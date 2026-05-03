<?php

namespace App\Http\Controllers;

use App\Models\JadwalRequest;
use App\Models\KalenderAkademik;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

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

    // --- Web Methods for Inertia ---
    public function adminIndex()
    {
        $kalender = KalenderAkademik::all();
        $requests = JadwalRequest::with(['user', 'dosen'])->get();

        return Inertia::render('Admin/KalenderAkademik/Index', [
            'kalender' => $kalender,
            'requests' => $requests,
        ]);
    }

    public function adminStore(Request $request)
    {
        $validated = $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'tipe_kegiatan' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'jam_mulai' => 'nullable|date_format:H:i',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'deskripsi' => 'nullable|string',
        ]);
        $validated['status'] = 'Active';

        KalenderAkademik::create($validated);

        return redirect()->back()->with('success', 'Kegiatan berhasil ditambahkan.');
    }

    public function adminUpdate(Request $request, KalenderAkademik $kalenderAkademik)
    {
        $validated = $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'tipe_kegiatan' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'jam_mulai' => 'nullable|date_format:H:i',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'deskripsi' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        $kalenderAkademik->update($validated);

        return redirect()->back()->with('success', 'Kegiatan berhasil diperbarui.');
    }

    public function adminDestroy(KalenderAkademik $kalenderAkademik)
    {
        $kalenderAkademik->delete();

        return redirect()->back()->with('success', 'Kegiatan berhasil dihapus.');
    }

    public function dosenIndex()
    {
        // Mengambil ID Dosen dari Auth atau dari parameter URL (untuk kemudahan testing/demo)
        $dosenId = request('dosen_id', Auth::id() ?? 1);

        // Kalender global (null user_id) + Kalender pribadi dosen ini
        $kalender = KalenderAkademik::where(function ($query) use ($dosenId) {
            $query->whereNull('user_id')
                ->orWhere('user_id', $dosenId);
        })->get();

        $requests = JadwalRequest::with(['user'])
            ->where('dosen_id', $dosenId)
            ->get();

        // Cari user yang memiliki role 'dosen'. Jika ID yang diminta bukan dosen, ambil dosen pertama yang tersedia.
        $currentDosen = User::role('dosen')->find($dosenId)
                     ?? User::role('dosen')->first();

        return Inertia::render('Dosen/KalenderAkademik/Index', [
            'kalender' => $kalender,
            'requests' => $requests,
            'current_dosen' => $currentDosen,
        ]);
    }

    public function dosenStore(Request $request)
    {
        $validated = $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'tipe_kegiatan' => 'required|string|in:kuliah,rapat',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date',
            'jam_mulai' => 'nullable',
            'deskripsi' => 'nullable|string',
        ]);

        $validated['user_id'] = Auth::id() ?? $request->dosen_id ?? 1; // Mendukung ID dari form untuk testing tanpa login
        $validated['status'] = 'Active';

        KalenderAkademik::create($validated);

        return redirect()->back()->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function mahasiswaIndex()
    {
        $studentId = Auth::id() ?? 1;

        // Ambil semua ID Dosen yang pernah di-request oleh mahasiswa ini
        $dosenIds = JadwalRequest::where('user_id', $studentId)
            ->pluck('dosen_id')
            ->unique()
            ->toArray();

        // Jika belum ada request, coba ambil dari parameter URL
        if (empty($dosenIds) && request('dosen_id')) {
            $dosenIds[] = request('dosen_id');
        }

        $kalender = KalenderAkademik::where(function ($query) use ($dosenIds) {
            $query->whereNull('user_id') // Kalender Akademik Umum (Pusat)
                ->when(! empty($dosenIds), function ($q) use ($dosenIds) {
                    $q->orWhereIn('user_id', $dosenIds); // Kalender Pribadi Dosen-dosen terkait
                });
        })->where('status', 'Active')->get();

        // Ensure we load the 'dosen' relationship properly
        $requests = JadwalRequest::with(['dosen'])->where('user_id', $studentId)->get();

        foreach ($requests as $req) {
            // Priority: Actual relationship name -> then mock name
            $req->dosen_name = ($req->dosen && $req->dosen->name !== 'Administrator')
                ? $req->dosen->name
                : 'Dr. Ir. H. Rahmat Hidayat, M.T.';
        }

        $dosens = User::role('dosen')->get();

        return Inertia::render('Mahasiswa/KalenderAkademik/Index', [
            'kalender' => $kalender,
            'requests' => $requests,
            'dosens' => $dosens,
        ]);
    }
}
