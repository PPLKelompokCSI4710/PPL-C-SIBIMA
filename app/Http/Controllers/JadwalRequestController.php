<?php

namespace App\Http\Controllers;

use App\Models\JadwalRequest;
use App\Models\KalenderAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JadwalRequestController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'tipe_request' => 'required|string',
            'judul' => 'required|string',
            'tanggal' => 'required|date',
            'jam' => 'required',
            'dosen_id' => 'nullable|exists:users,id',
            'deskripsi' => 'nullable|string',
        ]);

        JadwalRequest::create([
            'user_id' => Auth::id() ?? 1, // Mocking Auth::id() since we're using preview routes for now without login
            'dosen_id' => $request->dosen_id,
            'tipe_request' => $request->tipe_request,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'tanggal' => $request->tanggal,
            'jam' => $request->jam,
            'status' => 'pending_dosen',
        ]);

        return redirect()->back()->with('success', 'Request jadwal berhasil dikirim.');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:pending_dosen,approved_dosen,rejected_dosen,approved_admin,rejected_admin',
            'alasan_penolakan' => 'nullable|string',
        ]);

        $jadwalRequest = JadwalRequest::findOrFail($id);
        $jadwalRequest->update([
            'status' => $request->status,
            'alasan_penolakan' => $request->alasan_penolakan,
        ]);

        // If approved by admin, insert to Kalender Akademik
        if ($request->status === 'approved_admin') {
            KalenderAkademik::create([
                'user_id' => $jadwalRequest->dosen_id, // Hubungkan ke kalender pribadi dosen tersebut
                'nama_kegiatan' => $jadwalRequest->judul,
                'tipe_kegiatan' => $jadwalRequest->tipe_request,
                'tanggal_mulai' => $jadwalRequest->tanggal,
                'tanggal_selesai' => $jadwalRequest->tanggal,
                'jam_mulai' => $jadwalRequest->jam,
                'deskripsi' => $jadwalRequest->deskripsi,
                'status' => 'Active',
            ]);
        }

        return redirect()->back()->with('success', 'Status jadwal berhasil diubah.');
    }
}
