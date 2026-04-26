<?php

namespace App\Http\Controllers;

use App\Models\JadwalBimbingan;
use App\Models\Mahasiswa;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MonitoringJadwalBimbinganController extends Controller
{
    // Menampilkan daftar jadwal milik mahasiswa yang sedang login
    public function index()
    {
        // Ambil profil mahasiswa yang sedang login
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->first();

        // Mengambil jadwal milik mahasiswa ini saja, berserta data dosen
        $jadwalBimbingans = JadwalBimbingan::with(['dosen', 'mahasiswa'])
            ->where('mahasiswa_id', $mahasiswa?->id)
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

    // Menghapus jadwal (Oleh Mahasiswa, hanya jika status masih pending)
    public function destroy($id)
    {
        $jadwal = JadwalBimbingan::findOrFail($id);

        // Pastikan hanya bisa dihapus jika statusnya masih pending
        if ($jadwal->status !== 'pending') {
            return redirect()->back()->with('error', 'Hanya pengajuan dengan status pending yang dapat dihapus.');
        }

        // Pastikan mahasiswa yang login adalah pemilik jadwal
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->first();
        if ($jadwal->mahasiswa_id !== $mahasiswa?->id) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus jadwal ini.');
        }

        $jadwal->delete();

        return redirect()->route('mahasiswa.jadwal.index')
            ->with('success', 'Pengajuan bimbingan berhasil dihapus.');
    }
}
