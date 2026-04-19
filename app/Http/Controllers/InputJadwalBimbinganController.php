<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\JadwalBimbingan;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class InputJadwalBimbinganController extends Controller
{
    // Menampilkan form input jadwal bimbingan (Mahasiswa)
    public function create()
    {
        // Ambil profil mahasiswa yang sedang login
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->first();

        // Ambil daftar dosen untuk dropdown
        $dosenList = Dosen::all();

        return Inertia::render('InputJadwalBimbingan/Create', [
            'dosenList' => $dosenList,
            'mahasiswa' => $mahasiswa,
        ]);
    }

    // Menyimpan jadwal bimbingan baru (Oleh Mahasiswa)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'dosen_id' => 'required|exists:dosen,id',
            'tanggal' => 'required|date|after_or_equal:today',
            'waktu' => 'required',
            'topik_bimbingan' => 'required|string|max:500',
            'tipe' => 'required|in:online,offline',
        ]);

        // Ambil profil mahasiswa yang sedang login
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->first();

        JadwalBimbingan::create([
            'dosen_id' => $validated['dosen_id'],
            'mahasiswa_id' => $mahasiswa->id,
            'tanggal' => $validated['tanggal'],
            'waktu' => $validated['waktu'],
            'topik_bimbingan' => $validated['topik_bimbingan'],
            'tipe' => $validated['tipe'],
            'status' => 'pending',
        ]);

        return redirect()->route('mahasiswa.jadwal.index')
            ->with('success', 'Jadwal bimbingan berhasil diajukan.');
    }
}
