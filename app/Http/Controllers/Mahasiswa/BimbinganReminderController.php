<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\JadwalBimbingan;
use App\Models\Mahasiswa;
use App\Models\ReminderPreference;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BimbinganReminderController extends Controller
{
    public function index()
    {
        $userId    = auth()->id();
        $mahasiswa = Mahasiswa::where('user_id', $userId)->with('bimbingans')->first();

        // ── Jadwal reminder (PBI 32) ─────────────────────────────────────────
        $upcoming = null;
        if ($mahasiswa) {
            $jadwal = JadwalBimbingan::query()
                ->where('mahasiswa_id', $mahasiswa->id)
                ->where('status', 'approved')
                ->whereHas('ketersediaanJadwal', function ($q) {
                    $q->where('tanggal', '>=', now()->toDateString());
                })
                ->with(['dosen', 'ketersediaanJadwal'])
                ->get()
                ->sortBy(function ($j) {
                    $k = $j->ketersediaanJadwal;

                    return $k ? $k->tanggal.' '.$k->waktu_mulai : '9999-12-31';
                })
                ->first();

            if ($jadwal && $jadwal->ketersediaanJadwal) {
                $k           = $jadwal->ketersediaanJadwal;
                $tanggal     = Carbon::parse($k->tanggal);
                $waktuMulai  = Carbon::parse($k->tanggal.' '.$k->waktu_mulai);
                $waktuSelesai = Carbon::parse($k->tanggal.' '.$k->waktu_selesai);

                $upcoming = [
                    'id'               => $jadwal->id,
                    'dosen'            => $jadwal->dosen?->nama_lengkap,
                    'topic'            => $jadwal->topik_bimbingan ?? $jadwal->judul_ta ?? 'Bimbingan Tugas Akhir',
                    'date'             => $tanggal->toDateString(),
                    'dateFormatted'    => $tanggal->translatedFormat('d F Y'),
                    'timeFormatted'    => $waktuMulai->format('H:i').' - '.$waktuSelesai->format('H:i').' WIB',
                    'location'         => $jadwal->tipe === 'offline'
                        ? 'Ruang Bimbingan Dosen'
                        : 'Online (link akan diinformasikan)',
                    'type'             => $jadwal->tipe === 'online' ? 'Online' : 'Offline',
                    'preparationNotes' => [
                        'Siapkan draft atau dokumen yang akan dibahas',
                        'Catat pertanyaan yang ingin ditanyakan',
                        'Isi logbook progres sebelumnya',
                    ],
                ];
            }
        }

        // Schedule reminder preferences (PBI 32 – AC 32.3)
        $pref = ReminderPreference::forUser($userId);
        $schedulePreferences = [
            'schedule_reminder_enabled' => (bool) $pref->schedule_reminder_enabled,
            'stage_h3_enabled'          => (bool) $pref->stage_h3_enabled,
            'stage_h1_enabled'          => (bool) $pref->stage_h1_enabled,
            'stage_h2_enabled'          => (bool) $pref->stage_h2_enabled,
        ];

        // ── Progress reminder (PBI 33) ────────────────────────────────────────
        $inactiveThresholdDays = (int) (AppSetting::get('progress_reminder_inactive_days', 14) ?? 14);
        $frequency             = $mahasiswa?->progress_reminder_frequency ?? 'biweekly';
        $frequencyDays         = $mahasiswa?->progress_reminder_frequency_days ?? 14;
        $isEnabled             = $mahasiswa ? (bool) $mahasiswa->progress_reminder_enabled : true;

        $daysSinceLast = 0;
        $lastBimbinganDate = null;
        if ($mahasiswa && $mahasiswa->bimbingans->count() > 0) {
            $lastBimbingan = $mahasiswa->bimbingans
                ->where('status', 'selesai')
                ->sortByDesc('waktu_selesai')
                ->first();
            if ($lastBimbingan) {
                $daysSinceLast     = Carbon::now()->diffInDays($lastBimbingan->waktu_selesai);
                $lastBimbinganDate = Carbon::parse($lastBimbingan->waktu_selesai)->translatedFormat('d F Y');
            }
        }

        $progressData = [
            'daysSinceLastBimbingan' => $daysSinceLast,
            'status'                 => $daysSinceLast >= $inactiveThresholdDays ? 'Warning' : 'Good',
            'lastBimbinganDate'      => $lastBimbinganDate ?? '-',
        ];

        $progressSettings = [
            'inactive_threshold_days' => $inactiveThresholdDays,
            'frequency'               => $frequency,
            'frequency_days'          => $frequencyDays,
            'enabled'                 => $isEnabled,
        ];

        return Inertia::render('Mahasiswa/Bimbingan/Reminder', [
            'upcomingBimbingan'   => $upcoming,
            'schedulePreferences' => $schedulePreferences,
            'progressData'        => $progressData,
            'progressSettings'    => $progressSettings,
        ]);
    }

    /**
     * Update schedule reminder preferences (H-3, H-1, H-2 jam) – PBI 32 AC 32.3
     */
    public function updateSchedulePreferences(Request $request)
    {
        $data = $request->validate([
            'schedule_reminder_enabled' => ['required', 'boolean'],
            'stage_h3_enabled'          => ['required', 'boolean'],
            'stage_h1_enabled'          => ['required', 'boolean'],
            'stage_h2_enabled'          => ['required', 'boolean'],
        ]);

        $pref = ReminderPreference::forUser(auth()->id());
        $pref->update($data);

        return redirect()->back()->with('success', 'Pengaturan reminder jadwal berhasil diperbarui.');
    }

    /**
     * Update progress reminder frequency – PBI 33 AC 33.4
     */
    public function updateProgressSettings(Request $request)
    {
        $request->validate([
            'frequency'   => 'required|in:2_days,3_days,weekly,biweekly,custom',
            'custom_days' => 'required_if:frequency,custom|nullable|integer|min:1|max:365',
            'enabled'     => 'required|boolean',
        ]);

        $mahasiswa = Mahasiswa::where('user_id', auth()->id())->first();
        if ($mahasiswa) {
            $days = match ($request->frequency) {
                '2_days'  => 2,
                '3_days'  => 3,
                'weekly'  => 7,
                'custom'  => (int) $request->custom_days,
                default   => 14,
            };

            $mahasiswa->update([
                'progress_reminder_frequency'      => $request->frequency,
                'progress_reminder_frequency_days' => $days,
                'progress_reminder_enabled'        => $request->enabled,
            ]);
        }

        return redirect()->back()->with('success', 'Pengaturan reminder progres berhasil diperbarui.');
    }
}
