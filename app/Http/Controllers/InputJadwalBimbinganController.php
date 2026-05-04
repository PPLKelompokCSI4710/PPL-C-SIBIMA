<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Models\Dosen;
use App\Models\JadwalBimbingan;
use App\Models\Mahasiswa;
use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

    // Mendapatkan jadwal dosen (dipanggil via axios/fetch API)
    public function getSchedules($dosenId)
    {
        $schedules = Schedule::where('dosen_id', $dosenId)
            ->where('tanggal', '>=', now()->toDateString())
            ->where('kuota', '>', 0)
            ->orderBy('tanggal', 'asc')
            ->orderBy('waktu_mulai', 'asc')
            ->get();

        return response()->json($schedules);
    }

    // Menyimpan jadwal bimbingan baru (Oleh Mahasiswa)
    public function store(StoreBookingRequest $request)
    {
        $validated = $request->validated();

        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->first();

        try {
            DB::beginTransaction();

            // Kunci baris jadwal agar tidak terjadi race condition
            $schedule = Schedule::where('id', $validated['schedule_id'])
                ->lockForUpdate()
                ->first();

            if (! $schedule || $schedule->kuota <= 0) {
                DB::rollBack();

                return redirect()->back()->withErrors([
                    'schedule_id' => 'Jadwal yang dipilih sudah penuh atau tidak tersedia.',
                ]);
            }

            // Kurangi kuota jadwal
            $schedule->decrement('kuota');

            // Simpan data booking ke jadwal_bimbingans
            JadwalBimbingan::create([
                'dosen_id' => $validated['dosen_id'],
                'mahasiswa_id' => $mahasiswa->id,
                'schedule_id' => $schedule->id,
                'tanggal' => $schedule->tanggal,
                'waktu' => $schedule->waktu_mulai,
                'topik_bimbingan' => $validated['topik_bimbingan'],
                'tipe' => $validated['tipe'],
                'status' => 'pending',
            ]);

            DB::commit();

            return redirect()->route('mahasiswa.jadwal.index')
                ->with('success', 'Jadwal bimbingan berhasil diajukan.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->withErrors([
                'error' => 'Terjadi kesalahan saat memproses pengajuan Anda. Silakan coba lagi.',
            ]);
        }
    }
}
