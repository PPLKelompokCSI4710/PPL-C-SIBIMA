<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJadwalBimbinganRequest;
use App\Models\Dosen;
use App\Models\JadwalBimbingan;
use App\Models\KalenderAkademik;
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
        $dosen = Dosen::findOrFail($dosenId);

        $schedules = KetersediaanJadwal::where('dosen_id', $dosenId)
            ->where('tanggal', '>=', now()->toDateString())
            ->orderBy('tanggal', 'asc')
            ->orderBy('waktu_mulai', 'asc')
            ->get()
            ->map(function ($s) use ($dosen) {
                $s->has_clash = $this->checkClash($dosen->user_id, $s);

                return $s;
            });

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

            // Verify if there is a conflict/clash with Dosen's busy schedule on that day
            $dosen = Dosen::findOrFail($ketersediaan->dosen_id);
            if ($this->checkClash($dosen->user_id, $ketersediaan)) {
                DB::rollBack();

                return redirect()->back()->withErrors([
                    'ketersediaan_jadwal_id' => 'Maaf dosen sedang ada kegiatan, mohon pilih jadwal lain.',
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

    /**
     * Check if a specific slot clashes with the lecturer's academic calendar events.
     */
    private function checkClash($dosenUserId, $slot)
    {
        $clashingEvents = KalenderAkademik::where('user_id', $dosenUserId)
            ->where('tanggal_mulai', '<=', $slot->tanggal)
            ->where('tanggal_selesai', '>=', $slot->tanggal)
            ->get();

        foreach ($clashingEvents as $event) {
            // Jika kegiatan seharian penuh (tidak ada jam_mulai), maka bentrok dengan semua slot pada hari itu
            if (empty($event->jam_mulai)) {
                return true;
            }

            // Samakan format waktu untuk perbandingan yang konsisten
            $eventTime = date('H:i:s', strtotime($event->jam_mulai));
            $slotStart = date('H:i:s', strtotime($slot->waktu_mulai));
            $slotEnd = date('H:i:s', strtotime($slot->waktu_selesai));

            // Bentrok terjadi jika waktu mulai kegiatan berada di dalam rentang slot bimbingan
            if ($eventTime >= $slotStart && $eventTime < $slotEnd) {
                return true;
            }
        }

        return false;
    }
}
