<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\RescheduleRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class RescheduleController extends Controller
{
    public function index()
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();

        if (!$dosen) {
            abort(403, 'Anda tidak terdaftar sebagai dosen.');
        }

        // Ambil permohonan yang masih pending
        $pendingRequests = RescheduleRequest::with([
            'jadwalBimbingan.mahasiswa',
            'ketersediaanJadwalLama',
            'ketersediaanJadwalBaru'
        ])
        ->whereHas('jadwalBimbingan', function ($q) use ($dosen) {
            $q->where('dosen_id', $dosen->id);
        })
        ->where('status', 'pending')
        ->latest()
        ->get();

        // Ambil riwayat permohonan (yang sudah disetujui / ditolak)
        $historyRequests = RescheduleRequest::with([
            'jadwalBimbingan.mahasiswa',
            'ketersediaanJadwalLama',
            'ketersediaanJadwalBaru'
        ])
        ->whereHas('jadwalBimbingan', function ($q) use ($dosen) {
            $q->where('dosen_id', $dosen->id);
        })
        ->whereIn('status', ['approved', 'rejected'])
        ->latest('updated_at')
        ->get();

        return Inertia::render('Dosen/Reschedule/Index', [
            'pendingRequests' => $pendingRequests,
            'historyRequests' => $historyRequests
        ]);
    }

    public function respond(Request $request, $id)
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();

        if (!$dosen) {
            abort(403, 'Anda tidak terdaftar sebagai dosen.');
        }

        $rescheduleRequest = RescheduleRequest::with([
            'jadwalBimbingan',
            'ketersediaanJadwalLama',
            'ketersediaanJadwalBaru'
        ])->findOrFail($id);

        // Validasi kepemilikan bimbingan
        if ($rescheduleRequest->jadwalBimbingan->dosen_id !== $dosen->id) {
            abort(403, 'Anda tidak berhak memproses pengajuan reschedule ini.');
        }

        // Pastikan status request masih pending
        if ($rescheduleRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'Pengajuan reschedule ini sudah diproses sebelumnya.');
        }

        $validated = $request->validate([
            'response' => 'required|in:approved,rejected'
        ]);

        if ($validated['response'] === 'approved') {
            // 1. Setujui pengajuan reschedule
            $rescheduleRequest->update(['status' => 'approved']);

            // 2. Kembalikan kuota pada jadwal lama
            $oldKetersediaan = $rescheduleRequest->ketersediaanJadwalLama;
            if ($oldKetersediaan) {
                $oldKetersediaan->increment('kuota');
            }

            // 3. Update ketersediaan_jadwal_id pada jadwal bimbingan utama ke jadwal baru
            $rescheduleRequest->jadwalBimbingan->update([
                'ketersediaan_jadwal_id' => $rescheduleRequest->ketersediaan_jadwal_baru_id
            ]);

            $message = 'Pengajuan reschedule bimbingan berhasil disetujui.';
        } else {
            // 1. Tolak pengajuan reschedule
            $rescheduleRequest->update(['status' => 'rejected']);

            // 2. Kembalikan kuota pada jadwal baru (karena batal dipesan)
            $newKetersediaan = $rescheduleRequest->ketersediaanJadwalBaru;
            if ($newKetersediaan) {
                $newKetersediaan->increment('kuota');
            }

            $message = 'Pengajuan reschedule bimbingan berhasil ditolak.';
        }

        return redirect()->back()->with('success', $message);
    }
}
