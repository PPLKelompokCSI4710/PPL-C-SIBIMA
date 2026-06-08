<?php

namespace Tests\Browser;

use App\Models\JadwalRequest;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class DosenKalenderAkademikTest extends DuskTestCase
{
    /**
     * Test 1: Dosen Siti dapat melihat halaman kalender.
     */
    public function test_dosen_can_view_kalender_page(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'siti@sibima.test')->first();

            $browser->loginAs($user)
                ->visit('/preview/kalender-dosen')
                ->waitForText('Siti Aminah', 10)
                ->assertSee('Siti Aminah')
                ->assertSee('Dosen');
        });
    }

    /**
     * Test 2: Dosen Siti dapat menambah jadwal rapat baru.
     */
    public function test_dosen_can_add_rapat_event(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'siti@sibima.test')->first();

            $browser->loginAs($user)
                ->visit('/preview/kalender-dosen')
                ->waitForText('Tambah Jadwal', 10)
                ->click('button.bg-\\[\\#1F4C7A\\]') // Tombol buka modal
                ->waitForText('Tipe Kegiatan', 5)
                ->click('#opt-rapat')
                ->type('#add-nama-kegiatan', 'Rapat Koordinasi Prodi Dusk')
                ->type('#add-tanggal-mulai', '2026-05-15') // Tanggal lebih dekat agar muncul di list
                ->type('#add-jam-mulai', '10:00')
                ->press('Simpan Jadwal')
                ->waitForText('Rapat Koordinasi Prodi Dusk', 20)
                ->assertSee('Rapat Koordinasi Prodi Dusk');
        });
    }

    /**
     * Test 3: Dosen Siti dapat menyetujui (ACC) permintaan jadwal.
     */
    public function test_dosen_can_approve_request(): void
    {
        // Pastikan ada data pending
        $this->artisan('db:seed', ['--class' => 'KalenderTestSeeder']);

        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'siti@sibima.test')->first();
            $request = JadwalRequest::where('judul', 'Permintaan Baru: Konsultasi Bab 4')->first();

            $browser->loginAs($user)
                ->visit('/preview/kalender-dosen')
                ->waitForText('Permintaan Baru: Konsultasi Bab 4', 10)
                ->click('#btn-acc-'.$request->id)
                ->waitUntilMissingText('Permintaan Baru: Konsultasi Bab 4', 15)
                ->assertDontSee('Permintaan Baru: Konsultasi Bab 4');
        });
    }

    /**
     * Test 4: Dosen Siti dapat menolak permintaan jadwal dengan alasan.
     */
    public function test_dosen_can_reject_request(): void
    {
        // Re-seed to ensure the request is pending again
        $this->artisan('db:seed', ['--class' => 'KalenderTestSeeder']);

        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'siti@sibima.test')->first();
            $request = JadwalRequest::where('judul', 'Permintaan Baru: Konsultasi Bab 4')->first();

            $browser->loginAs($user)
                ->visit('/preview/kalender-dosen')
                ->waitForText('Permintaan Baru: Konsultasi Bab 4', 10)
                ->click('#btn-reject-'.$request->id)
                ->waitForText('Alasan Penolakan', 5)
                ->type('textarea', 'Maaf, saya ada rapat di jam tersebut.')
                ->press('Kirim Penolakan')
                ->waitUntilMissingText('Permintaan Baru: Konsultasi Bab 4', 15)
                ->assertDontSee('Permintaan Baru: Konsultasi Bab 4');
        });
    }
}
