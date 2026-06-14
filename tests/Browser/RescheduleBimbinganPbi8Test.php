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

class RescheduleBimbinganPbi8Test extends DuskTestCase
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

    protected function vueSelect(Browser $browser, string $selector, string $value): void
    {
        $browser->script("
            const el = document.querySelector('{$selector}');
            const nativeInputValueSetter = Object.getOwnPropertyDescriptor(
                window.HTMLSelectElement.prototype, 'value'
            ).set;
            nativeInputValueSetter.call(el, '{$value}');
            el.dispatchEvent(new Event('change', { bubbles: true }));
            el.dispatchEvent(new Event('input', { bubbles: true }));
        ");
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
     * TC08-01: Mahasiswa berhasil mengajukan reschedule bimbingan baru.
     * TC09-01: Verifikasi perbandingan jadwal lama & baru.
     */
    public function test_reschedule_flow_success(): void
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

        $this->browse(function (Browser $browser) use ($bimbingan, $slotBaru) {
            $browser->loginAs($this->mahasiswa())
                ->visit('/mahasiswa/jadwal-bimbingan')
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
            $browser->waitFor('a[href$="/jadwal-bimbingan/' . $bimbingan->id . '/reschedule"]', 15)
                ->click('a[href$="/jadwal-bimbingan/' . $bimbingan->id . '/reschedule"]')
                ->waitForLocation("/mahasiswa/jadwal-bimbingan/{$bimbingan->id}/reschedule", 15)
                ->assertSee('Reschedule Bimbingan')
                ->assertValue('#judul_ta', 'Implementasi AI pada Sistem Rekomendasi')
                ->waitFor('#ketersediaan_jadwal_id', 10);

            // TC08-01: Isi Form dan Submit
            $this->vueSelect($browser, '#ketersediaan_jadwal_id', (string)$slotBaru->id);
            $browser->type('#topik_bimbingan', 'Topik Baru Reschedule')
                ->press('Ajukan Reschedule')
                ->waitForLocation('/mahasiswa/jadwal-bimbingan/riwayat-reschedule', 15)
                ->assertPathIs('/mahasiswa/jadwal-bimbingan/riwayat-reschedule')
                ->assertSee('Pengajuan reschedule berhasil dikirim')
                
                // TC09-01: Verifikasi Tampilan Riwayat Perbandingan Jadwal Lama & Baru
                ->assertSee('MENUNGGU KONFIRMASI')
                ->assertSee('Implementasi AI pada Sistem Rekomendasi')
                ->assertSee('Topik Baru Reschedule');
        });

        // Simpan id reschedule request yang terbuat untuk pembersihan
        $req = RescheduleRequest::where('jadwal_bimbingan_id', $bimbingan->id)->first();
        if ($req) {
            self::$createdIds['reschedule_requests'][] = $req->id;
        }

        // Verifikasi kuota berkurang di DB
        $this->assertEquals(1, $slotBaru->fresh()->kuota);
    }

    /**
     * TC08-02: Validasi input kosong (required html5 validation).
     */
    public function test_reschedule_validation_empty(): void
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

        $this->browse(function (Browser $browser) use ($bimbingan) {
            $browser->loginAs($this->mahasiswa())
                ->visit("/mahasiswa/jadwal-bimbingan/{$bimbingan->id}/reschedule")
                ->waitFor('#ketersediaan_jadwal_id', 10)
                ->type('#topik_bimbingan', 'Topik Saja Tanpa Pilih Jadwal')
                ->press('Ajukan Reschedule')
                // Browser HTML5 validation should prevent submission, path remains the same
                ->assertPathIs("/mahasiswa/jadwal-bimbingan/{$bimbingan->id}/reschedule");
        });
    }
}
