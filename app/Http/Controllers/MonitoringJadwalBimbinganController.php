<?php

namespace App\Http\Controllers;

use App\Models\JadwalBimbingan;
use App\Models\Mahasiswa;
use App\Models\KetersediaanJadwal;
use App\Http\Requests\RescheduleJadwalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Carbon\Carbon;

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
        
        // Append can_reschedule boolean
        $jadwalBimbingans->each(function ($jadwal) {
            $jadwal->can_reschedule = ($jadwal->status === 'approved') 
                && Carbon::parse($jadwal->ketersediaanJadwal->tanggal)->startOfDay()->greaterThan(now()->startOfDay());
        });

        return Inertia::render('MonitoringJadwal/Index', [
            'jadwalBimbingans' => $jadwalBimbingans,
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

    // Menampilkan halaman form reschedule (Jalur Demo Asistensi)
    public function pilihReschedule()
    {
        // MOCK DATA: Memasukkan data palsu agar UI form Reschedule langsung terbuka tanpa database
        $mockJadwal = [
            'id' => 9999,
            'mahasiswa_id' => auth()->id(),
            'dosen_id' => 2, // Asumsi ID dosen
            'ketersediaan_jadwal_id' => 8888,
            'judul_ta' => 'Sistem Informasi Manajemen Jadwal Bimbingan (Demo)',
            'topik_bimbingan' => 'Pembahasan Progres UI/UX Fase 2',
            'status' => 'approved',
            'tipe' => 'offline',
            'dosen' => [
                'id' => 2,
                'nama_lengkap' => 'Dr. Ir. Rahmat Hidayat, M.T.',
                'email' => 'rahmat@sibima.test',
            ],
            'ketersediaan_jadwal' => [
                'id' => 8888,
                'dosen_id' => 2,
                'tanggal' => now()->addDays(1)->toDateString(),
                'waktu_mulai' => '09:00:00',
                'waktu_selesai' => '11:00:00',
                'kuota' => 1,
                'ruangan' => 'Ruang Dosen Gedung A',
                'status' => 'tersedia',
            ]
        ];

        $mockKetersediaanJadwals = [
            [
                'id' => 8889,
                'dosen_id' => 2,
                'tanggal' => now()->addDays(3)->toDateString(),
                'waktu_mulai' => '13:00:00',
                'waktu_selesai' => '15:00:00',
                'kuota' => 5,
                'ruangan' => 'Laboratorium Komputer Dasar',
                'status' => 'tersedia',
            ],
            [
                'id' => 8890,
                'dosen_id' => 2,
                'tanggal' => now()->addDays(5)->toDateString(),
                'waktu_mulai' => '10:00:00',
                'waktu_selesai' => '12:00:00',
                'kuota' => 3,
                'ruangan' => 'Ruang Rapat Program Studi',
                'status' => 'tersedia',
            ]
        ];

        return Inertia::render('MonitoringJadwal/Reschedule', [
            'jadwal' => $mockJadwal,
            'ketersediaanJadwals' => $mockKetersediaanJadwals
        ]);
    }

    public function editReschedule($id)
    {
        $jadwal = JadwalBimbingan::with(['dosen', 'ketersediaanJadwal'])->findOrFail($id);
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->first();

        // Validasi Kepemilikan
        if ($jadwal->mahasiswa_id !== $mahasiswa?->id) {
            abort(403, 'Anda tidak memiliki akses untuk mereschedule jadwal ini.');
        }

        // Validasi Status
        if ($jadwal->status !== 'approved') {
            return redirect()->back()->with('error', 'Hanya jadwal yang sudah disetujui yang dapat di-reschedule.');
        }

        // Validasi H-1 (Jadwal Lama)
        $tanggalLama = Carbon::parse($jadwal->ketersediaanJadwal->tanggal)->startOfDay();
        if (!$tanggalLama->greaterThan(now()->startOfDay())) {
            return redirect()->back()->with('error', 'Reschedule hanya dapat dilakukan maksimal H-1 sebelum jadwal berlangsung.');
        }

        // Ambil ketersediaan jadwal dosen pengganti (yang >= H+2)
        $ketersediaanJadwals = KetersediaanJadwal::where('dosen_id', $jadwal->ketersediaanJadwal->dosen_id)
            ->where('kuota', '>', 0)
            ->whereDate('tanggal', '>=', now()->addDays(2)->toDateString())
            ->orderBy('tanggal', 'asc')
            ->orderBy('waktu_mulai', 'asc')
            ->get();

        return Inertia::render('MonitoringJadwal/Reschedule', [
            'jadwal' => $jadwal,
            'ketersediaanJadwals' => $ketersediaanJadwals
        ]);
    }

    // Melakukan reschedule jadwal bimbingan (Oleh Mahasiswa)
    public function updateReschedule(RescheduleJadwalRequest $request, $id)
    {
        // 0. Bypass untuk Mode Demo Asistensi
        if ($id == 9999) {
            return redirect()->route('mahasiswa.jadwal.index')
                ->with('success', '[MODE DEMO] Jadwal berhasil direschedule dan menunggu persetujuan dosen.');
        }

        $jadwal = JadwalBimbingan::with('ketersediaanJadwal')->findOrFail($id);
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->first();

        // 1. Validasi Kepemilikan
        if ($jadwal->mahasiswa_id !== $mahasiswa?->id) {
            abort(403, 'Anda tidak memiliki akses untuk mereschedule jadwal ini.');
        }

        // 2. Validasi Status
        if ($jadwal->status !== 'approved') {
            return redirect()->back()->with('error', 'Hanya jadwal yang sudah disetujui yang dapat di-reschedule.');
        }

        // 3. Validasi H-1 (Jadwal Lama)
        $tanggalLama = Carbon::parse($jadwal->ketersediaanJadwal->tanggal)->startOfDay();
        if (!$tanggalLama->greaterThan(now()->startOfDay())) {
            return redirect()->back()->with('error', 'Reschedule hanya dapat dilakukan maksimal H-1 sebelum jadwal berlangsung.');
        }

        $newKetersediaan = KetersediaanJadwal::findOrFail($request->ketersediaan_jadwal_id);

        // 4. Validasi H+2 (Jadwal Baru)
        $tanggalBaru = Carbon::parse($newKetersediaan->tanggal)->startOfDay();
        if ($tanggalBaru->lessThan(now()->addDays(2)->startOfDay())) {
            return redirect()->back()->with('error', 'Jadwal pengganti minimal harus 2 hari dari sekarang (H+2).');
        }

        // 5. Validasi Dosen
        if ($newKetersediaan->dosen_id !== $jadwal->ketersediaanJadwal->dosen_id) {
            return redirect()->back()->with('error', 'Jadwal baru harus dengan dosen pembimbing yang sama.');
        }

        // 6. Validasi Kuota
        if ($newKetersediaan->kuota <= 0) {
            return redirect()->back()->with('error', 'Kuota untuk jadwal yang dipilih sudah penuh.');
        }

        // 7. Refund Kuota
        if ($jadwal->status === 'approved') {
            $jadwal->ketersediaanJadwal->increment('kuota');
        }

        // 8. Update Data
        $jadwal->update([
            'ketersediaan_jadwal_id' => $newKetersediaan->id,
            'topik_bimbingan' => $request->topik_bimbingan,
            'status' => 'pending'
        ]);

        return redirect()->route('mahasiswa.jadwal.index')->with('success', 'Jadwal berhasil direschedule dan menunggu persetujuan dosen.');
    }
}
