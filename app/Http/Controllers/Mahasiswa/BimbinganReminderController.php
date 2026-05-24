<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Bimbingan;
use App\Models\Mahasiswa;
use Inertia\Inertia;

class BimbinganReminderController extends Controller
{
    public function index()
    {
        $mahasiswa = Mahasiswa::where('user_id', auth()->id())->first();

        $upcoming = null;
        if ($mahasiswa) {
            $bimbingan = Bimbingan::query()
                ->where('mahasiswa_id', $mahasiswa->id)
                ->where('status', 'disetujui')
                ->where('waktu_mulai', '>=', now())
                ->with('dosen')
                ->orderBy('waktu_mulai')
                ->first();

            if ($bimbingan) {
                $upcoming = [
                    'id' => $bimbingan->id,
                    'dosen' => $bimbingan->dosen?->nama_lengkap,
                    'topic' => $bimbingan->topik,
                    'date' => $bimbingan->waktu_mulai->toDateString(),
                    'dateFormatted' => $bimbingan->waktu_mulai->translatedFormat('d F Y'),
                    'timeFormatted' => $bimbingan->waktu_mulai->translatedFormat('H:i').' WIB',
                    'location' => $bimbingan->lokasi ?? ($bimbingan->tipe_pertemuan === 'online' ? 'Online (link akan diinformasikan)' : '-'),
                    'type' => $bimbingan->tipe_pertemuan === 'online' ? 'Online' : 'Offline',
                    'preparationNotes' => $bimbingan->catatan_persiapan ?? [],
                ];
            }
        }

        return Inertia::render('Mahasiswa/Bimbingan/Reminder', [
            'upcomingBimbingan' => $upcoming,
        ]);
    }
}
