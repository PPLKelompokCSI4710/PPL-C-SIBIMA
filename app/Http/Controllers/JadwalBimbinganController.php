<?php

namespace App\Http\Controllers;

use App\Models\JadwalBimbingan;
use Illuminate\Http\Request;

class JadwalBimbinganController extends Controller
{
    public function create()
    {
        return view('jadwal-bimbingan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'waktu' => 'required|date_format:H:i',
            'topik_bimbingan' => 'nullable|string|max:255',
        ]);

        JadwalBimbingan::create($validated);

        return redirect()->route('jadwal-bimbingan.index')->with('success', 'Jadwal bimbingan berhasil dibuat.');
    }
}