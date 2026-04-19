<?php

namespace App\Http\Controllers;

use App\Models\JadwalBimbingan;
use Inertia\Inertia;

class MonitoringJadwalBimbinganController extends Controller
{
    // Menampilkan daftar jadwal (Read)
    public function index()
    {
        // Mengambil semua jadwal berserta data dosen & mahasiswa
        $jadwalBimbingans = JadwalBimbingan::with(['dosen', 'mahasiswa'])
            ->orderBy('tanggal', 'desc')
            ->get();

        return Inertia::render('MonitoringJadwal/Index', [
            'jadwalBimbingans' => $jadwalBimbingans,
        ]);
    }

    // Membatalkan jadwal (Oleh Mahasiswa)
    public function cancel($id)
    {
        $jadwal = JadwalBimbingan::findOrFail($id);
        $jadwal->update(['status' => 'canceled']);

        return redirect()->back()->with('success', 'Jadwal bimbingan berhasil dibatalkan.');
    }

    // Menyetujui jadwal (Khusus Dosen)
    public function approve($id)
    {
        $jadwal = JadwalBimbingan::findOrFail($id);
        $jadwal->update(['status' => 'approved']);

        return redirect()->back()->with('success', 'Jadwal bimbingan berhasil disetujui.');
    }

    // Menolak jadwal (Khusus Dosen)
    public function reject($id)
    {
        $jadwal = JadwalBimbingan::findOrFail($id);
        $jadwal->update(['status' => 'rejected']);

        return redirect()->back()->with('success', 'Jadwal bimbingan telah ditolak.');
    }
}
