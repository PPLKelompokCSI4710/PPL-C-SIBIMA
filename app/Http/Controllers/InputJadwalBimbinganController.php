<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJadwalBimbinganRequest;
use App\Models\Dosen;
use App\Models\JadwalBimbingan;
use App\Models\KetersediaanJadwal;
use App\Models\Mahasiswa;
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
        $schedules = KetersediaanJadwal::where('dosen_id', $dosenId)
            ->where('tanggal', '>=', now()->toDateString())
            ->where('kuota', '>', 0)
            ->orderBy('tanggal', 'asc')
            ->orderBy('waktu_mulai', 'asc')
            ->get();

        return response()->json($schedules);
    }

    // Menyimpan jadwal bimbingan baru (Oleh Mahasiswa)
    public function store(StoreJadwalBimbinganRequest $request)
    {
        $validated = $request->validated();

        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->first();

        try {
            DB::beginTransaction();

            $ketersediaan = KetersediaanJadwal::where('id', $validated['ketersediaan_jadwal_id'])
                ->lockForUpdate()
                ->first();

            if (! $ketersediaan || $ketersediaan->kuota <= 0) {
                DB::rollBack();

                return redirect()->back()->withErrors([
                    'ketersediaan_jadwal_id' => 'Jadwal yang dipilih sudah penuh atau tidak tersedia.',
                ]);
            }

            // Kuota TIDAK dikurangi di sini (karena status masih pending)
            // Kuota akan dikurangi saat dosen meng-approve

            // Simpan data booking ke jadwal_bimbingans
            JadwalBimbingan::create([
                'ketersediaan_jadwal_id' => $validated['ketersediaan_jadwal_id'],
                'mahasiswa_id' => $mahasiswa->id,
                'dosen_id' => $ketersediaan->dosen_id,
                'judul_ta' => $validated['judul_ta'],
                'topik_bimbingan' => $validated['topik_bimbingan'],
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
