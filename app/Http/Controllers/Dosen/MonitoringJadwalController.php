<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\CatatanKonsultasi;
use App\Models\Dosen;
use App\Models\JadwalBimbingan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MonitoringJadwalController extends Controller
{
    public function index(Request $request)
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();

        if (!$dosen) {
            abort(403, 'Anda tidak terdaftar sebagai dosen.');
        }

        $status = $request->query('status');
        $search = $request->query('search');

        $query = JadwalBimbingan::with(['mahasiswa', 'ketersediaanJadwal', 'catatanKonsultasi'])
            ->where('dosen_id', $dosen->id);

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('topik_bimbingan', 'like', "%{$search}%")
                  ->orWhereHas('mahasiswa', function ($q2) use ($search) {
                      $q2->where('nama_lengkap', 'like', "%{$search}%")
                         ->orWhere('nim', 'like', "%{$search}%");
                  });
            });
        }

        // Urutkan berdasarkan yang terbaru masuk atau berdasarkan ketersediaan jadwal
        $jadwals = $query->latest()->get();

        return Inertia::render('Dosen/MonitoringJadwal/Index', [
            'jadwalBimbingans' => $jadwals,
            'filters' => [
                'status' => $status ?? 'all',
                'search' => $search ?? '',
            ]
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        $jadwal = JadwalBimbingan::findOrFail($id);

        if ($jadwal->dosen_id != $dosen->id) {
            abort(403, 'Anda tidak berhak mengubah status jadwal ini.');
        }

        $validated = $request->validate([
            'status' => 'required|in:approved,rejected,completed',
        ]);

        // If approving, check quota
        if ($validated['status'] === 'approved' && $jadwal->status !== 'approved') {
            $ketersediaan = $jadwal->ketersediaanJadwal;
            if ($ketersediaan) {
                if ($ketersediaan->kuota > 0) {
                    $ketersediaan->decrement('kuota');
                } else {
                    return redirect()->back()->with('error', 'Kuota untuk jadwal ini sudah habis.');
                }
            }
        }

        // If changing from approved to rejected, return quota
        if ($validated['status'] === 'rejected' && $jadwal->status === 'approved') {
            $ketersediaan = $jadwal->ketersediaanJadwal;
            if ($ketersediaan) {
                $ketersediaan->increment('kuota');
            }
        }

        $jadwal->update(['status' => $validated['status']]);

        return redirect()->back()->with('success', 'Status pengajuan bimbingan berhasil diperbarui.');
    }

    /**
     * Simpan catatan konsultasi baru untuk jadwal bimbingan yang sudah disetujui.
     */
    public function storeCatatan(Request $request, $id)
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        $jadwal = JadwalBimbingan::findOrFail($id);

        if ($jadwal->dosen_id != $dosen->id) {
            abort(403, 'Anda tidak berhak menambah catatan pada jadwal ini.');
        }

        if ($jadwal->status !== 'completed') {
            return redirect()->back()->with('error', 'Catatan hanya bisa ditambahkan pada bimbingan yang sudah selesai.');
        }

        $validated = $request->validate([
            'catatan' => 'required|string|min:5',
        ], [
            'catatan.required' => 'Catatan konsultasi wajib diisi.',
            'catatan.min' => 'Catatan konsultasi minimal 5 karakter.',
        ]);

        CatatanKonsultasi::updateOrCreate(
            ['jadwal_bimbingan_id' => $jadwal->id],
            ['catatan' => $validated['catatan']]
        );

        return redirect()->back()->with('success', 'Catatan konsultasi berhasil disimpan.');
    }

    /**
     * Update catatan konsultasi yang sudah ada.
     */
    public function updateCatatan(Request $request, $id)
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        $catatan = CatatanKonsultasi::findOrFail($id);
        $jadwal = $catatan->jadwalBimbingan;

        if ($jadwal->dosen_id != $dosen->id) {
            abort(403, 'Anda tidak berhak mengubah catatan ini.');
        }

        $validated = $request->validate([
            'catatan' => 'required|string|min:5',
        ], [
            'catatan.required' => 'Catatan konsultasi wajib diisi.',
            'catatan.min' => 'Catatan konsultasi minimal 5 karakter.',
        ]);

        $catatan->update([
            'catatan' => $validated['catatan'],
        ]);

        return redirect()->back()->with('success', 'Catatan konsultasi berhasil diperbarui.');
    }
}
