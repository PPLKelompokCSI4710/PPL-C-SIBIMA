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
            ->with(['dosen', 'mahasiswa', 'ketersediaanJadwal', 'rescheduleRequests'])
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
        
        // Append can_reschedule dan has_pending_reschedule boolean
        $jadwalBimbingans->each(function ($jadwal) {
            $hasPending = $jadwal->rescheduleRequests->where('status', 'pending')->first();
            $hasApproved = $jadwal->rescheduleRequests->where('status', 'approved')->first();
            $jadwal->has_pending_reschedule = (bool) $hasPending;
            $jadwal->can_reschedule = ($jadwal->status === 'approved') 
                && Carbon::parse($jadwal->ketersediaanJadwal->tanggal)->startOfDay()->greaterThan(now()->startOfDay())
                && !$hasPending
                && !$hasApproved;
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


    public function editReschedule($id)
    {
        $jadwal = JadwalBimbingan::with(['dosen', 'ketersediaanJadwal'])->findOrFail($id);
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->first();

        // Validasi Kepemilikan
        if ($jadwal->mahasiswa_id !== $mahasiswa?->id) {
            abort(403, 'Anda tidak memiliki akses untuk mereschedule jadwal ini.');
        }

        // Validasi jika sudah ada request reschedule pending
        if ($jadwal->rescheduleRequests()->where('status', 'pending')->exists()) {
            return redirect()->route('mahasiswa.jadwal.index')
                ->with('error', 'Anda sudah memiliki pengajuan reschedule yang sedang menunggu persetujuan.');
        }

        // Validasi jika reschedule sudah disetujui sebelumnya
        if ($jadwal->rescheduleRequests()->where('status', 'approved')->exists()) {
            return redirect()->route('mahasiswa.jadwal.index')
                ->with('error', 'Jadwal ini sudah pernah di-reschedule dan tidak dapat di-reschedule kembali.');
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

    public function riwayatReschedule()
    {
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->first();

        // Ambil data asli dari database jika ada
        $riwayat = \App\Models\RescheduleRequest::with([
            'jadwalBimbingan',
            'ketersediaanJadwalLama',
            'ketersediaanJadwalBaru.dosen'
        ])
        ->whereHas('jadwalBimbingan', function($q) use ($mahasiswa) {
            $q->where('mahasiswa_id', $mahasiswa->id);
        })
        ->orderBy('created_at', 'desc')
        ->get();



        return Inertia::render('MonitoringJadwal/RiwayatReschedule', [
            'riwayat' => $riwayat
        ]);
    }

    // Melakukan reschedule jadwal bimbingan (Oleh Mahasiswa)
    public function updateReschedule(RescheduleJadwalRequest $request, $id)
    {


        $jadwal = JadwalBimbingan::with('ketersediaanJadwal')->findOrFail($id);
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->first();

        // 1. Validasi Kepemilikan
        if ($jadwal->mahasiswa_id !== $mahasiswa?->id) {
            abort(403, 'Anda tidak memiliki akses untuk mereschedule jadwal ini.');
        }

        // Validasi jika sudah ada request reschedule pending
        if ($jadwal->rescheduleRequests()->where('status', 'pending')->exists()) {
            return redirect()->route('mahasiswa.jadwal.index')
                ->with('error', 'Anda sudah memiliki pengajuan reschedule yang sedang menunggu persetujuan.');
        }

        // Validasi jika reschedule sudah disetujui sebelumnya
        if ($jadwal->rescheduleRequests()->where('status', 'approved')->exists()) {
            return redirect()->route('mahasiswa.jadwal.index')
                ->with('error', 'Jadwal ini sudah pernah di-reschedule dan tidak dapat di-reschedule kembali.');
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

        // 7. Booking Kuota Jadwal Baru (Kurangi kuota)
        $newKetersediaan->decrement('kuota');

        // 8. Buat Request Reschedule (Data Asli Tidak Berubah)
        \App\Models\RescheduleRequest::create([
            'jadwal_bimbingan_id' => $jadwal->id,
            'ketersediaan_jadwal_lama_id' => $jadwal->ketersediaan_jadwal_id,
            'ketersediaan_jadwal_baru_id' => $newKetersediaan->id,
            'status' => 'pending',
            'alasan' => 'Topik: ' . $request->topik_bimbingan, // Kita simpan topik baru di alasan sementara
        ]);

        return redirect()->route('mahasiswa.jadwal.riwayat-reschedule')
            ->with('success', 'Pengajuan reschedule berhasil dikirim dan menunggu persetujuan dosen.');
    }

    // Membatalkan (menghapus) pengajuan reschedule yang masih pending
    public function destroyReschedule($id)
    {


        $rescheduleRequest = \App\Models\RescheduleRequest::with(['jadwalBimbingan', 'ketersediaanJadwalBaru'])->findOrFail($id);
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->first();

        // 1. Validasi Kepemilikan
        if ($rescheduleRequest->jadwalBimbingan->mahasiswa_id !== $mahasiswa?->id) {
            abort(403, 'Anda tidak memiliki akses untuk membatalkan pengajuan ini.');
        }

        // 2. Validasi Status (Hanya pending yang bisa dihapus)
        if ($rescheduleRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'Hanya pengajuan dengan status pending yang dapat dibatalkan.');
        }

        // 3. Refund Kuota
        if ($rescheduleRequest->ketersediaanJadwalBaru) {
            $rescheduleRequest->ketersediaanJadwalBaru->increment('kuota');
        }

        // 4. Hapus Pengajuan
        $rescheduleRequest->delete();

        return redirect()->back()->with('success', 'Pengajuan reschedule berhasil dibatalkan dan kuota jadwal telah dikembalikan.');
    }
}
