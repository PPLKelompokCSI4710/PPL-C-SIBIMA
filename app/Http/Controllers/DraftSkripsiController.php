<?php

namespace App\Http\Controllers;

use App\Models\DraftSkripsi;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class DraftSkripsiController extends Controller
{
    public function index()
    {
        $studentId = Auth::id() ?? 1; // Assuming default for testing
        $mahasiswa = Mahasiswa::where('user_id', $studentId)->first();
        
        $drafts = DraftSkripsi::where('mahasiswa_id', $mahasiswa?->id)->latest()->get();

        return Inertia::render('Mahasiswa/DraftSkripsi/Index', [
            'drafts' => $drafts
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'bab' => 'required|string|max:50',
            'file' => 'required|file|mimes:pdf,doc,docx|max:10240', // Max 10MB
            'catatan' => 'nullable|string'
        ]);

        $studentId = Auth::id() ?? 1;
        $mahasiswa = Mahasiswa::where('user_id', $studentId)->first();

        if (!$mahasiswa) {
            return redirect()->back()->withErrors('Data mahasiswa tidak ditemukan.');
        }

        $filePath = $request->file('file')->store('draft_skripsi', 'public');

        $existingCount = DraftSkripsi::where('mahasiswa_id', $mahasiswa->id)
            ->where('bab', $request->bab)
            ->count();
            
        $judul = $request->judul;
        if ($existingCount > 0) {
            $version = $existingCount + 1;
            // Clean up any existing version suffix if user typed it manually
            $judul = preg_replace('/(?:\s*(?:-\s*)?\(?Versi\s+\d+\)?)$/i', '', $judul);
            $judul .= " (Versi {$version})";
        }

        DraftSkripsi::create([
            'mahasiswa_id' => $mahasiswa->id,
            'judul' => $judul,
            'bab' => $request->bab,
            'file_path' => $filePath,
            'catatan' => $request->catatan,
        ]);

        return redirect()->back()->with('success', 'Draft skripsi berhasil diupload.');
    }

    public function update(Request $request, DraftSkripsi $draft)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'bab' => 'required|string|max:50',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:10240', // Max 10MB
            'catatan' => 'nullable|string'
        ]);

        $studentId = Auth::id() ?? 1;
        $mahasiswa = Mahasiswa::where('user_id', $studentId)->first();
        
        if ($draft->mahasiswa_id !== $mahasiswa?->id) {
            abort(403, 'Unauthorized action.');
        }

        $data = [
            'judul' => $request->judul,
            'bab' => $request->bab,
            'catatan' => $request->catatan,
        ];

        if ($request->hasFile('file')) {
            if ($draft->file_path && Storage::disk('public')->exists($draft->file_path)) {
                Storage::disk('public')->delete($draft->file_path);
            }
            $data['file_path'] = $request->file('file')->store('draft_skripsi', 'public');
        }

        $draft->update($data);

        return redirect()->back()->with('success', 'Draft skripsi berhasil diperbarui.');
    }

    public function updateCatatan(Request $request, DraftSkripsi $draft)
    {
        $request->validate([
            'catatan' => 'nullable|string'
        ]);

        // Authorization check
        $studentId = Auth::id() ?? 1;
        $mahasiswa = Mahasiswa::where('user_id', $studentId)->first();
        
        if ($draft->mahasiswa_id !== $mahasiswa?->id) {
            abort(403, 'Unauthorized action.');
        }

        $draft->update([
            'catatan' => $request->catatan
        ]);

        return redirect()->back()->with('success', 'Catatan berhasil diperbarui.');
    }

    public function destroy(DraftSkripsi $draft)
    {
        $studentId = Auth::id() ?? 1;
        $mahasiswa = Mahasiswa::where('user_id', $studentId)->first();
        
        if ($draft->mahasiswa_id !== $mahasiswa?->id) {
            abort(403, 'Unauthorized action.');
        }

        if (Storage::disk('public')->exists($draft->file_path)) {
            Storage::disk('public')->delete($draft->file_path);
        }

        $draft->delete();

        return redirect()->back()->with('success', 'Draft skripsi berhasil dihapus.');
    }
}
