<?php

namespace App\Http\Controllers;

use App\Models\KalenderAkademik;
use Illuminate\Http\Request;

class KalenderAkademikController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kalender = KalenderAkademik::all();

        return response()->json($kalender);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'jam_mulai' => 'nullable|date_format:H:i',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'deskripsi' => 'nullable|string',
        ]);

        $kalender = KalenderAkademik::create($validated);

        return response()->json($kalender, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(KalenderAkademik $kalenderAkademik)
    {
        return response()->json($kalenderAkademik);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, KalenderAkademik $kalenderAkademik)
    {
        $validated = $request->validate([
            'nama_kegiatan' => 'sometimes|required|string|max:255',
            'tanggal_mulai' => 'sometimes|required|date',
            'jam_mulai' => 'nullable|date_format:H:i',
            'tanggal_selesai' => 'sometimes|required|date|after_or_equal:tanggal_mulai',
            'deskripsi' => 'nullable|string',
        ]);

        $kalenderAkademik->update($validated);

        return response()->json($kalenderAkademik);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KalenderAkademik $kalenderAkademik)
    {
        $kalenderAkademik->delete();

        return response()->json(null, 204);
    }
}
