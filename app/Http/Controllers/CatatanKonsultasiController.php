<?php

namespace App\Http\Controllers;

use App\Models\CatatanKonsultasi;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CatatanKonsultasiController extends Controller
{
    public function index()
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        if (! $dosen) {
            abort(403, 'Profil Dosen tidak ditemukan.');
        }

        // Ambil daftar catatan yang sudah dibuat oleh dosen ini
        $catatanList = CatatanKonsultasi::with('mahasiswa')
            ->where('dosen_id', $dosen->id)
            ->orderBy('tanggal', 'desc')
            ->get();

        // Ambil daftar mahasiswa bimbingan dosen ini
        $mahasiswaList = DB::table('dosen_mahasiswa')
            ->join('mahasiswa', 'dosen_mahasiswa.mahasiswa_id', '=', 'mahasiswa.id')
            ->where('dosen_mahasiswa.dosen_id', $dosen->id)
            ->where('dosen_mahasiswa.is_active', true)
            ->select('mahasiswa.id', 'mahasiswa.nama_lengkap', 'mahasiswa.nim')
            ->get();

        return Inertia::render('CatatanKonsultasi/Index', [
            'catatanList' => $catatanList,
            'mahasiswaList' => $mahasiswaList,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'mahasiswa_id' => 'required|exists:mahasiswa,id',
            'tanggal' => 'required|date',
            'topik' => 'required|string|max:255',
            'catatan' => 'required|string',
        ]);

        $dosen = Dosen::where('user_id', Auth::id())->first();

        CatatanKonsultasi::create([
            'dosen_id' => $dosen->id,
            'mahasiswa_id' => $request->mahasiswa_id,
            'tanggal' => $request->tanggal,
            'topik' => $request->topik,
            'catatan' => $request->catatan,
        ]);

        return redirect()->back()->with('success', 'Catatan hasil konsultasi berhasil disimpan.');
    }

    public function destroy($id)
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        $catatan = CatatanKonsultasi::where('dosen_id', $dosen->id)->findOrFail($id);
        $catatan->delete();

        return redirect()->back()->with('success', 'Catatan berhasil dihapus.');
    }
}
