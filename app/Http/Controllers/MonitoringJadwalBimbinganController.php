<?php

namespace App\Http\Controllers;

use App\Models\JadwalBimbingan;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MonitoringJadwalBimbinganController extends Controller
{
    // Menampilkan daftar jadwal milik mahasiswa yang sedang login
    public function index(Request $request)
    {
        // Ambil profil mahasiswa yang sedang login
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->first();

        // Ambil query filter dari request
        $status = $request->query('status');
        $search = $request->query('search');

        // Query jadwal milik mahasiswa ini dengan filter opsional
        $query = JadwalBimbingan::select('jadwal_bimbingans.*')
            ->join('ketersediaan_jadwals', 'jadwal_bimbingans.ketersediaan_jadwal_id', '=', 'ketersediaan_jadwals.id')
            ->with(['dosen', 'mahasiswa', 'ketersediaanJadwal'])
            ->where('jadwal_bimbingans.mahasiswa_id', $mahasiswa?->id);

        // Filter berdasarkan status jika dipilih
        if ($status && $status !== 'all') {
            $query->where('jadwal_bimbingans.status', $status);
        }

        // Filter berdasarkan kata kunci topik atau nama dosen
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('jadwal_bimbingans.topik_bimbingan', 'like', "%{$search}%")
                    ->orWhere('jadwal_bimbingans.judul_ta', 'like', "%{$search}%")
                    ->orWhereHas('dosen', function ($q2) use ($search) {
                        $q2->where('nama_lengkap', 'like', "%{$search}%");
                    });
            });
        }

        $jadwalBimbingans = $query->orderBy('ketersediaan_jadwals.tanggal', 'desc')->get();

        // Progress reminder history (PBI 33 – displayed as section on this page)
        $progressReminderHistory = Auth::user()
            ->notifications()
            ->where('type', 'App\\Notifications\\AcademicProgressNotification')
            ->latest()
            ->take(10)
            ->get()
            ->map(fn ($n) => [
                'id' => $n->id,
                'title' => data_get($n->data, 'title', 'Reminder Progres'),
                'message' => data_get($n->data, 'message', ''),
                'progress_summary' => data_get($n->data, 'progress_summary', []),
                'days_since_last_bimbingan' => data_get($n->data, 'days_since_last_bimbingan'),
                'read_at' => $n->read_at,
                'created_at' => $n->created_at->diffForHumans(),
            ]);

        return Inertia::render('MonitoringJadwal/Index', [
            'jadwalBimbingans' => $jadwalBimbingans,
            'progressReminderHistory' => $progressReminderHistory,
            'filters' => [
                'status' => $status ?? 'all',
                'search' => $search ?? '',
            ],
        ]);
    }

    // Membatalkan jadwal (Oleh Mahasiswa)
    public function cancel($id)
    {
        $jadwal = JadwalBimbingan::findOrFail($id);

        // Pastikan mahasiswa yang login adalah pemilik jadwal
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->first();
        if ($jadwal->mahasiswa_id !== $mahasiswa?->id) {
            abort(403, 'Anda tidak memiliki akses untuk membatalkan jadwal ini.');
        }

        $jadwal->update(['status' => 'canceled']);

        return redirect()->back()->with('success', 'Jadwal bimbingan berhasil dibatalkan.');
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
