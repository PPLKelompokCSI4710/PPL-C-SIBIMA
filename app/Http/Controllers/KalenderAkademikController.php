<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\JadwalBimbingan;
use App\Models\JadwalRequest;
use App\Models\KalenderAkademik;
use App\Models\Mahasiswa;
use App\Models\User;
use App\Services\GoogleCalendarService;
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
            'tanggal_mulai' => 'required|date',
            'jam_mulai' => 'nullable|string',
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
            'tanggal_mulai' => 'required|date',
            'jam_mulai' => 'nullable|string',
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

        $dosenModel = Dosen::where('user_id', $dosenId)->first();

        $bimbinganRequests = JadwalBimbingan::with(['mahasiswa.user', 'ketersediaanJadwal'])
            ->where('dosen_id', $dosenModel?->id)
            ->where('status', 'pending')
            ->get()
            ->map(function ($b) {
                return [
                    'id' => $b->id,
                    'is_real_bimbingan' => true,
                    'judul' => 'Bimbingan: '.($b->judul_ta ?? 'Tugas Akhir'),
                    'deskripsi' => $b->topik_bimbingan,
                    'tanggal' => $b->ketersediaanJadwal?->tanggal ?? '',
                    'jam' => ($b->ketersediaanJadwal?->waktu_mulai ? substr($b->ketersediaanJadwal?->waktu_mulai, 0, 5) : '').' - '.($b->ketersediaanJadwal?->waktu_selesai ? substr($b->ketersediaanJadwal?->waktu_selesai, 0, 5) : ''),
                    'status' => 'pending_dosen',
                    'user' => [
                        'name' => $b->mahasiswa->nama_lengkap ?? 'Mahasiswa',
                        'email' => $b->mahasiswa->user->email ?? '',
                    ],
                ];
            });

        // Fallback to JadwalRequest if no real bimbingan requests exist (for demo purposes)
        if ($bimbinganRequests->isEmpty()) {
            $requests = JadwalRequest::with(['user'])
                ->where('dosen_id', $dosenId)
                ->get()
                ->map(function ($r) {
                    return [
                        'id' => $r->id,
                        'is_real_bimbingan' => false,
                        'judul' => $r->judul,
                        'deskripsi' => $r->deskripsi,
                        'tanggal' => $r->tanggal,
                        'jam' => $r->jam,
                        'status' => $r->status,
                        'user' => [
                            'name' => $r->user->name ?? 'Mahasiswa',
                            'email' => $r->user->email ?? '',
                        ],
                    ];
                });
        } else {
            $requests = $bimbinganRequests;
        }

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
        $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'tipe_kegiatan' => 'required|string|in:kuliah,rapat',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date',
            'jam_mulai' => 'nullable',
            'deskripsi' => 'nullable|string',
        ]);

        $kalenderItem = KalenderAkademik::create([
            'user_id' => $request->dosen_id ?? Auth::id() ?? 1,
            'nama_kegiatan' => $request->nama_kegiatan,
            'tipe_kegiatan' => $request->tipe_kegiatan,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'jam_mulai' => $request->jam_mulai,
            'deskripsi' => $request->deskripsi,
            'status' => 'Active',
        ]);

        // Attempt Google Calendar Sync for Dosen
        $user = Auth::user();
        if ($user && $user->google_access_token) {
            $googleService = new GoogleCalendarService;
            $googleService->syncEvent($user, $kalenderItem);
        }

        return redirect()->back()->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function mahasiswaIndex()
    {
        $studentId = Auth::id() ?? 1;

        // Ambil semua ID Dosen
        $dosenIds = JadwalRequest::where('user_id', $studentId)
            ->pluck('dosen_id')
            ->unique()
            ->toArray();

        if (empty($dosenIds) && request('dosen_id')) {
            $dosenIds[] = request('dosen_id');
        }

        // Get global academic calendar events (where user_id is null)
        $kalenderGlobal = KalenderAkademik::whereNull('user_id')
            ->where('status', 'Active')
            ->get();

        // Get student's own approved bimbingan events
        $mahasiswa = Mahasiswa::where('user_id', $studentId)->first();
        
        $myBimbingansQuery = JadwalBimbingan::with(['dosen', 'ketersediaanJadwal'])
            ->where('mahasiswa_id', $mahasiswa?->id)
            ->where('status', 'approved')
            ->get();

        $dosenUserIds = $myBimbingansQuery->pluck('dosen.user_id')->filter()->unique()->toArray();

        $allBimbinganKalender = KalenderAkademik::whereIn('user_id', $dosenUserIds)
            ->where('tipe_kegiatan', 'bimbingan')
            ->get();

        $myBimbingans = $myBimbingansQuery->map(function ($b) use ($mahasiswa, $allBimbinganKalender) {
                // Find matching KalenderAkademik to reflect Admin edits
                $kalenderItem = $allBimbinganKalender->first(function ($item) use ($mahasiswa, $b) {
                    if ($item->user_id !== ($b->dosen->user_id ?? null)) return false;
                    
                    $expectedDescFragment = $b->topik_bimbingan.' (Lokasi: '.($b->lokasi ?? '-').', Tipe: '.($b->tipe ?? 'offline').')';
                    if (stripos($item->deskripsi ?? '', $expectedDescFragment) !== false) {
                        return true;
                    }
                    
                    $topik = substr($b->topik_bimbingan ?? '', 0, 15);
                    if ($topik && stripos($item->deskripsi ?? '', $topik) !== false) {
                        return true;
                    }
                    
                    return false;
                });

                return (object) [
                    'id' => 'bimbingan-'.$b->id,
                    'user_id' => $b->mahasiswa->user_id ?? null,
                    'nama_kegiatan' => $kalenderItem->nama_kegiatan ?? 'Bimbingan: '.($b->dosen->nama_lengkap ?? 'Dosen'),
                    'tipe_kegiatan' => 'bimbingan',
                    'tanggal_mulai' => $kalenderItem->tanggal_mulai ?? $b->ketersediaanJadwal?->tanggal ?? '',
                    'tanggal_selesai' => $kalenderItem->tanggal_selesai ?? $b->ketersediaanJadwal?->tanggal ?? '',
                    'jam_mulai' => $kalenderItem->jam_mulai ?? $b->ketersediaanJadwal?->waktu_mulai ?? '',
                    'deskripsi' => $kalenderItem->deskripsi ?? $b->topik_bimbingan.' (Lokasi: '.($b->lokasi ?? '-').', Tipe: '.($b->tipe ?? 'offline').')',
                    'status' => 'Active',
                ];
            });

        $kalender = $kalenderGlobal->concat($myBimbingans);

        // Fetch bimbingan requests (pending, approved, rejected, completed)
        $requests = JadwalBimbingan::with(['dosen', 'ketersediaanJadwal'])
            ->where('mahasiswa_id', $mahasiswa?->id)
            ->get()
            ->map(function ($b) {
                return [
                    'id' => $b->id,
                    'judul' => 'Bimbingan: '.($b->judul_ta ?? 'Tugas Akhir'),
                    'deskripsi' => $b->topik_bimbingan,
                    'tanggal' => $b->ketersediaanJadwal?->tanggal ?? '',
                    'jam' => ($b->ketersediaanJadwal?->waktu_mulai ? substr($b->ketersediaanJadwal?->waktu_mulai, 0, 5) : '').' - '.($b->ketersediaanJadwal?->waktu_selesai ? substr($b->ketersediaanJadwal?->waktu_selesai, 0, 5) : ''),
                    'status' => $b->status,
                    'tipe' => $b->tipe,
                    'lokasi' => $b->lokasi,
                    'dosen_name' => $b->dosen->nama_lengkap ?? 'Dosen',
                ];
            });

        $dosenList = Dosen::all();

        return Inertia::render('Mahasiswa/AcademicCalendar', [
            'kalender' => $kalender,
            'requests' => $requests,
            'dosenList' => $dosenList,
        ]);
    }

    public function approveBimbingan(Request $request, $id)
    {
        $request->validate([
            'tipe' => 'required|in:online,offline',
            'lokasi' => 'required|string|max:255',
        ]);

        $bimbingan = JadwalBimbingan::with(['mahasiswa.user', 'dosen.user', 'ketersediaanJadwal'])->findOrFail($id);
        $bimbingan->update([
            'status' => 'approved',
            'tipe' => $request->tipe,
            'lokasi' => $request->lokasi,
        ]);

        // Decrement quota
        if ($bimbingan->ketersediaanJadwal) {
            $bimbingan->ketersediaanJadwal->decrement('kuota');
        }

        // Add to Kalender Akademik so both see it (use updateOrCreate to prevent duplicates if clicked twice)
        $kalenderItem = KalenderAkademik::updateOrCreate([
            'user_id' => $bimbingan->dosen->user_id ?? Auth::id() ?? 1,
            'nama_kegiatan' => 'Bimbingan: '.($bimbingan->mahasiswa->nama_lengkap ?? 'Mahasiswa'),
            'tanggal_mulai' => $bimbingan->ketersediaanJadwal?->tanggal ?? date('Y-m-d'),
            'jam_mulai' => $bimbingan->ketersediaanJadwal?->waktu_mulai ?? '08:00:00',
        ], [
            'tipe_kegiatan' => 'bimbingan',
            'tanggal_selesai' => $bimbingan->ketersediaanJadwal?->tanggal ?? date('Y-m-d'),
            'deskripsi' => $bimbingan->topik_bimbingan.' (Lokasi: '.$request->lokasi.', Tipe: '.$request->tipe.')',
            'status' => 'Active',
        ]);

        // Google Calendar Sync for Dosen
        if ($bimbingan->dosen && $bimbingan->dosen->user && $bimbingan->dosen->user->google_access_token) {
            $googleService = new GoogleCalendarService;
            $googleService->syncEvent($bimbingan->dosen->user, $kalenderItem);
        }

        // Google Calendar Sync for Mahasiswa
        if ($bimbingan->mahasiswa && $bimbingan->mahasiswa->user && $bimbingan->mahasiswa->user->google_access_token) {
            $googleService = new GoogleCalendarService;
            $googleService->syncEvent($bimbingan->mahasiswa->user, $kalenderItem);
        }

        return redirect()->back()->with('success', 'Request bimbingan berhasil disetujui.');
    }

    public function rejectBimbingan(Request $request, $id)
    {
        $bimbingan = JadwalBimbingan::findOrFail($id);
        $bimbingan->update([
            'status' => 'rejected',
        ]);

        return redirect()->back()->with('success', 'Request bimbingan berhasil ditolak.');
    }
}
