<?php

namespace App\Http\Controllers;

use App\Models\CatatanKonsultasi;
use App\Models\Dosen;
use App\Models\JadwalBimbingan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CatatanKonsultasiController extends Controller
{
    public function index()
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        if (! $dosen) {
            abort(403, 'Profil Dosen tidak ditemukan.');
        }

        // Ambil daftar catatan berdasarkan jadwal bimbingan yang sudah selesai
        $catatanList = CatatanKonsultasi::with(['mahasiswa', 'jadwalBimbingan'])
            ->where('dosen_id', $dosen->id)
            ->orderBy('tanggal', 'desc')
            ->get();

        return Inertia::render('CatatanKonsultasi/Index', [
            'catatanList' => $catatanList,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'jadwal_bimbingan_id' => 'required|exists:jadwal_bimbingans,id',
            'catatan' => 'required|string',
        ]);

        $dosen = Dosen::where('user_id', Auth::id())->first();
        $jadwal = JadwalBimbingan::findOrFail($request->jadwal_bimbingan_id);

        CatatanKonsultasi::create([
            'jadwal_bimbingan_id' => $jadwal->id,
            'dosen_id' => $dosen->id,
            'mahasiswa_id' => $jadwal->mahasiswa_id,
            'tanggal' => $jadwal->tanggal,
            'topik' => $jadwal->topik_bimbingan,
            'catatan' => $request->catatan,
        ]);

        return redirect()->back()->with('success', 'Catatan konsultasi berhasil disimpan.');
    }

    public function destroy($id)
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        $catatan = CatatanKonsultasi::where('dosen_id', $dosen->id)->findOrFail($id);
        $catatan->delete();

        return redirect()->back()->with('success', 'Catatan berhasil dihapus.');
    }
}
