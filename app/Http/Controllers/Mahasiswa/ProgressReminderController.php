<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\Mahasiswa;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProgressReminderController extends Controller
{
    public function index()
    {
        $mahasiswa = Mahasiswa::where('user_id', auth()->id())->with('bimbingans')->first();

        $inactiveThresholdDays = (int) (AppSetting::get('progress_reminder_inactive_days', 14) ?? 14);
        $frequency = $mahasiswa?->progress_reminder_frequency ?? 'biweekly';
        $isEnabled = $mahasiswa ? (bool) $mahasiswa->progress_reminder_enabled : true;

        $daysSinceLast = 0;
        if ($mahasiswa && $mahasiswa->bimbingans->count() > 0) {
            $lastBimbingan = $mahasiswa->bimbingans->where('status', 'selesai')->sortByDesc('waktu_selesai')->first();
            if ($lastBimbingan) {
                $daysSinceLast = Carbon::now()->diffInDays($lastBimbingan->waktu_selesai);
            }
        } else {
            $daysSinceLast = 18; // Mock value for display if no DB matches
        }

        return Inertia::render('Mahasiswa/Bimbingan/ProgressReminder', [
            'progressData' => [
                'daysSinceLastBimbingan' => $daysSinceLast,
                'status' => $daysSinceLast >= $inactiveThresholdDays ? 'Warning' : 'Good',
                'lastBimbinganDate' => Carbon::now()->subDays($daysSinceLast)->translatedFormat('d F Y'),
            ],
            'reminderSettings' => [
                'inactive_threshold_days' => $inactiveThresholdDays,
                'frequency' => $frequency,
                'enabled' => (bool) $isEnabled,
            ],
        ]);
    }

    public function updateFrequency(Request $request)
    {
        $request->validate([
            'frequency' => 'required|in:weekly,biweekly',
            'enabled' => 'required|boolean',
        ]);

        $mahasiswa = Mahasiswa::where('user_id', auth()->id())->first();
        if ($mahasiswa) {
            $days = $request->frequency === 'weekly' ? 7 : 14;
            $mahasiswa->update([
                'progress_reminder_frequency' => $request->frequency,
                // keep legacy column in sync
                'progress_reminder_frequency_days' => $days,
                'progress_reminder_enabled' => $request->enabled,
            ]);
        }

        return redirect()->back()->with('success', 'Pengaturan reminder berhasil diperbarui.');
    }
}
