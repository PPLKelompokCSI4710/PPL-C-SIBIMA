<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\KetersediaanJadwal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class KetersediaanJadwalController extends Controller
{
    public function index()
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        if (! $dosen) {
            abort(403, 'Profil Dosen tidak ditemukan.');
        }

        $ketersediaan = KetersediaanJadwal::where('dosen_id', $dosen->id)
            ->orderBy('tanggal', 'desc')
            ->orderBy('waktu_mulai', 'asc')
            ->get();

        return Inertia::render('KetersediaanJadwal/Index', [
            'ketersediaan' => $ketersediaan,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
            'kuota' => 'required|integer|min:1',
        ]);

        $dosen = Dosen::where('user_id', Auth::id())->first();

        KetersediaanJadwal::create([
            'dosen_id' => $dosen->id,
            'tanggal' => $request->tanggal,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'kuota' => $request->kuota,
        ]);

        return redirect()->back()->with('success', 'Jadwal ketersediaan berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        $jadwal = KetersediaanJadwal::where('dosen_id', $dosen->id)->findOrFail($id);
        $jadwal->delete();

        return redirect()->back()->with('success', 'Jadwal ketersediaan berhasil dihapus.');
    }
}
