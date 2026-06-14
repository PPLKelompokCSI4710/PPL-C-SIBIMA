<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\KetersediaanJadwal;
use App\Models\JadwalBimbingan;
use App\Models\RescheduleRequest;
use Carbon\Carbon;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class RescheduleBimbinganPbi10Test extends DuskTestCase
{
    protected static array $createdIds = [
        'reschedule_requests' => [],
        'jadwal_bimbingans' => [],
        'ketersediaan_jadwals' => [],
    ];

    protected function mahasiswa(): User
    {
        // Pastikan role mahasiswa ada di DB
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'mahasiswa', 'guard_name' => 'web']);

        $user = User::where('email', 'mahasiswa@sibima.test')->first();
        if (!$user) {
            $user = User::create([
                'name' => 'Mahasiswa Test',
                'email' => 'mahasiswa@sibima.test',
                'password' => bcrypt('password'),
            ]);
            $user->assignRole('mahasiswa');
        }
        return $user;
    }

    protected function dosen(): User
    {
        // Pastikan role dosen ada di DB
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'dosen', 'guard_name' => 'web']);

        $user = User::where('email', 'dosen@sibima.test')->first();
        if (!$user) {
            $user = User::create([
                'name' => 'Dr. Budi Santoso, M.Kom.',
                'email' => 'dosen@sibima.test',
                'password' => bcrypt('password'),
            ]);
            $user->assignRole('dosen');
        }
        return $user;
    }

    protected function tearDown(): void
    {
        // Bersihkan data uji yang dibuat oleh test ini secara spesifik agar database tetap bersih
        foreach (self::$createdIds['reschedule_requests'] as $id) {
            RescheduleRequest::find($id)?->delete();
        }
        foreach (self::$createdIds['jadwal_bimbingans'] as $id) {
            JadwalBimbingan::find($id)?->delete();
        }
        foreach (self::$createdIds['ketersediaan_jadwals'] as $id) {
            KetersediaanJadwal::find($id)?->delete();
        }

        self::$createdIds = [
            'reschedule_requests' => [],
            'jadwal_bimbingans' => [],
            'ketersediaan_jadwals' => [],
        ];

        parent::tearDown();
    }

    /**
     * TC10-01: Pembatalan pengajuan reschedule bimbingan (refund kuota).
     */
    public function test_cancel_reschedule_success(): void
    {
        $mahasiswa = Mahasiswa::firstOrCreate(
            ['user_id' => $this->mahasiswa()->id],
            [
                'nim' => '2021001001',
                'nama_lengkap' => 'Ahmad Rizky Pratama',
                'program_studi' => 'Teknik Informatika',
                'fakultas' => 'FMIPA',
                'angkatan' => '2021',
                'semester' => '7',
                'status_akademik' => 'aktif'
            ]
        );

        $dosen = Dosen::firstOrCreate(
            ['user_id' => $this->dosen()->id],
            [
                'nidn' => '0012345678',
                'nama_lengkap' => 'Dr. Budi Santoso, M.Kom.',
                'program_studi' => 'Teknik Informatika',
                'fakultas' => 'FMIPA',
                'jabatan_fungsional' => 'Lektor Kepala',
                'gelar' => 'Dr., M.Kom.',
                'no_telepon' => '081234567890',
                'is_active' => true,
                'kuota_mahasiswa' => 10
            ]
        );

        $slotLama = KetersediaanJadwal::create([
            'dosen_id' => $dosen->id,
            'tanggal' => Carbon::now()->addDays(3)->toDateString(),
            'waktu_mulai' => '09:00:00',
            'waktu_selesai' => '10:00:00',
            'kuota' => 1,
            'tipe' => 'offline',
        ]);
        self::$createdIds['ketersediaan_jadwals'][] = $slotLama->id;

        $bimbingan = JadwalBimbingan::create([
            'ketersediaan_jadwal_id' => $slotLama->id,
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_id' => $dosen->id,
            'judul_ta' => 'Implementasi AI pada Sistem Rekomendasi',
            'topik_bimbingan' => 'Pengajuan Bab 1',
            'status' => 'approved',
            'tipe' => 'offline',
        ]);
        self::$createdIds['jadwal_bimbingans'][] = $bimbingan->id;

        $slotBaru = KetersediaanJadwal::create([
            'dosen_id' => $dosen->id,
            'tanggal' => Carbon::now()->addDays(4)->toDateString(),
            'waktu_mulai' => '13:00:00',
            'waktu_selesai' => '14:00:00',
            'kuota' => 2,
            'tipe' => 'online',
        ]);
        self::$createdIds['ketersediaan_jadwals'][] = $slotBaru->id;

        // Decrement kuota to represent the booked state of the pending reschedule request
        $slotBaru->decrement('kuota');

        $reschedule = RescheduleRequest::create([
            'jadwal_bimbingan_id' => $bimbingan->id,
            'ketersediaan_jadwal_lama_id' => $slotLama->id,
            'ketersediaan_jadwal_baru_id' => $slotBaru->id,
            'status' => 'pending',
            'alasan' => 'Topik: Topik Batal',
        ]);
        self::$createdIds['reschedule_requests'][] = $reschedule->id;

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->mahasiswa())
                ->visit('/mahasiswa/jadwal-bimbingan/riwayat-reschedule')
                ->script("
                    if (window.axios) {
                        const originalGet = window.axios.get;
                        window.axios.get = function(url, ...args) {
                            if (url && (url.includes('/api/ai-chat') || url.includes('/api/notifications'))) {
                                return Promise.resolve({ data: { sessions: [], notifications: [], quota: 20, max_quota: 20 } });
                            }
                            return originalGet.call(this, url, ...args);
                        };
                    }
                ");
            $browser->waitForText('MENUNGGU KONFIRMASI', 10)
                ->waitFor('button[title="Batalkan Pengajuan"]', 10)
                ->click('button[title="Batalkan Pengajuan"]')
                ->acceptDialog()
                ->waitForText('Pengajuan reschedule berhasil dibatalkan', 15)
                ->assertDontSee('MENUNGGU KONFIRMASI');
        });

        // Verifikasi kuota baru di-refund (+1)
        $this->assertEquals(2, $slotBaru->fresh()->kuota);
    }
}
