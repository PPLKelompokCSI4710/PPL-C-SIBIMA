<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\JadwalBimbingan;
use App\Models\Mahasiswa;
use Carbon\Carbon;
use Inertia\Inertia;

class BimbinganReminderController extends Controller
{
    public function index()
    {
        $mahasiswa = Mahasiswa::where('user_id', auth()->id())->first();

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
                $k = $jadwal->ketersediaanJadwal;
                $tanggal = Carbon::parse($k->tanggal);
                $waktuMulai = Carbon::parse($k->tanggal.' '.$k->waktu_mulai);
                $waktuSelesai = Carbon::parse($k->tanggal.' '.$k->waktu_selesai);

                $upcoming = [
                    'id' => $jadwal->id,
                    'dosen' => $jadwal->dosen?->nama_lengkap,
                    'topic' => $jadwal->topik_bimbingan ?? $jadwal->judul_ta ?? 'Bimbingan Tugas Akhir',
                    'date' => $tanggal->toDateString(),
                    'dateFormatted' => $tanggal->translatedFormat('d F Y'),
                    'timeFormatted' => $waktuMulai->format('H:i').' - '.$waktuSelesai->format('H:i').' WIB',
                    'location' => $jadwal->tipe === 'offline'
                        ? 'Ruang Bimbingan Dosen'
                        : 'Online (link akan diinformasikan)',
                    'type' => $jadwal->tipe === 'online' ? 'Online' : 'Offline',
                    'preparationNotes' => [
                        'Siapkan draft atau dokumen yang akan dibahas',
                        'Catat pertanyaan yang ingin ditanyakan',
                        'Isi logbook progres sebelumnya',
                    ],
                ];
            }
        }

        return Inertia::render('Mahasiswa/Bimbingan/Reminder', [
            'upcomingBimbingan' => $upcoming,
        ]);
    }
}
