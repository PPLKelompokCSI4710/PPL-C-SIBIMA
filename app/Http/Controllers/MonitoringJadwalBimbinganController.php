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
        $jadwalBimbingans = JadwalBimbingan::with(['dosen', 'mahasiswa', 'catatanKonsultasi'])
            ->orderBy('tanggal', 'desc')
            ->get();

        return Inertia::render('MonitoringJadwal/Index', [
            'jadwalBimbingans' => $jadwalBimbingans,
        ]);
    }

    public function cancel($id)
    {
        $jadwal = JadwalBimbingan::findOrFail($id);

        // Jika jadwal sebelumnya sudah disetujui, kembalikan kuota ketersediaan
        if ($jadwal->status === 'approved' && $jadwal->ketersediaanJadwal) {
            $jadwal->ketersediaanJadwal->increment('kuota');
        }

        $jadwal->update(['status' => 'canceled']);

        return redirect()->back()->with('success', 'Jadwal bimbingan berhasil dibatalkan.');
    }

    public function approve($id)
    {
        $jadwal = JadwalBimbingan::findOrFail($id);

        if ($jadwal->ketersediaanJadwal) {
            if ($jadwal->ketersediaanJadwal->kuota <= 0) {
                return redirect()->back()->withErrors(['error' => 'Gagal disetujui: Kuota jadwal ini sudah penuh.']);
            }
            $jadwal->ketersediaanJadwal->decrement('kuota');
        }

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
