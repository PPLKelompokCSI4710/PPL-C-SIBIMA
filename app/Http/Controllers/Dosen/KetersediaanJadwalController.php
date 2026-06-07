<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\KetersediaanJadwal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class KetersediaanJadwalController extends Controller
{
    /**
     * Menampilkan daftar ketersediaan jadwal dosen.
     */
    public function index()
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();

        if (!$dosen) {
            abort(403, 'Anda tidak terdaftar sebagai dosen.');
        }

        $jadwals = KetersediaanJadwal::where('dosen_id', $dosen->id)
            ->orderBy('tanggal', 'desc')
            ->orderBy('waktu_mulai', 'asc')
            ->get();

        return Inertia::render('Dosen/KetersediaanJadwal/Index', [
            'jadwals' => $jadwals,
        ]);
    }

    /**
     * Menyimpan ketersediaan jadwal baru.
     */
    public function store(Request $request)
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();

        if (!$dosen) {
            abort(403, 'Anda tidak terdaftar sebagai dosen.');
        }

        $validated = $request->validate([
            'tanggal' => ['required', 'date', 'after_or_equal:today'],
            'waktu_mulai' => ['required', 'date_format:H:i'],
            'waktu_selesai' => ['required', 'date_format:H:i', 'after:waktu_mulai'],
            'kuota' => ['required', 'integer', 'min:1'],
            'tipe' => ['required', 'in:online,offline'],
        ], [
            'tanggal.after_or_equal' => 'Tanggal tidak boleh di masa lalu.',
            'waktu_selesai.after' => 'Waktu selesai harus setelah waktu mulai.',
        ]);

        KetersediaanJadwal::create([
            'dosen_id' => $dosen->id,
            'tanggal' => $validated['tanggal'],
            'waktu_mulai' => $validated['waktu_mulai'],
            'waktu_selesai' => $validated['waktu_selesai'],
            'kuota' => $validated['kuota'],
            'tipe' => $validated['tipe'],
        ]);

        return redirect()->back()->with('success', 'Jadwal ketersediaan berhasil ditambahkan.');
    }

    /**
     * Menghapus ketersediaan jadwal.
     */
    public function destroy($id)
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        $jadwal = KetersediaanJadwal::findOrFail($id);

        if ($jadwal->dosen_id != $dosen->id) {
            abort(403, 'Anda tidak berhak menghapus jadwal ini.');
        }

        $jadwal->delete();

        return redirect()->back()->with('success', 'Jadwal ketersediaan berhasil dihapus.');
    }
}
