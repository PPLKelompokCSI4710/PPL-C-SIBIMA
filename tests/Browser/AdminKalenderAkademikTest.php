<?php

namespace Tests\Browser;

use App\Models\JadwalRequest;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminKalenderAkademikTest extends DuskTestCase
{
    /**
     * Test 1: Admin dapat menambah event baru ke kalender.
     */
    public function test_admin_can_add_new_event(): void
    {
        // Jalankan migrasi dan seed di awal suite agar state bersih
        $this->artisan('migrate:fresh', ['--seed' => true]);
        $this->artisan('db:seed', ['--class' => 'KalenderTestSeeder']);

        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'admin@sibima.test')->first();

            $browser->loginAs($user)
                ->visit('/preview/kalender-admin')
                ->waitFor('#btn-add-event', 15)
                ->click('#btn-add-event')
                ->waitForText('Add New Event', 5)
                ->type('#admin-nama-kegiatan', 'Libur Akhir Semester Dusk')
                ->script([
                    "document.getElementById('admin-tanggal-mulai').value = '2026-06-01'",
                    "document.getElementById('admin-tanggal-mulai').dispatchEvent(new Event('input'))",
                    "document.getElementById('admin-tanggal-selesai').value = '2026-06-05'",
                    "document.getElementById('admin-tanggal-selesai').dispatchEvent(new Event('input'))",
                ]);

            $browser->press('Create Event')
                ->pause(3000)
                ->type('#search-input', 'Libur Akhir Semester Dusk')
                ->waitForText('Libur Akhir Semester Dusk', 20)
                ->assertSee('Libur Akhir Semester Dusk');
        });
    }

    /**
     * Test 2: Admin dapat menerbitkan request bimbingan yang sudah di-ACC dosen.
     */
    public function test_admin_can_approve_dosen_request(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'admin@sibima.test')->first();

            $request = JadwalRequest::where('judul', 'Bimbingan ACC Dosen - Tunggu Admin')
                ->where('status', 'approved_dosen')
                ->first();

            $browser->loginAs($user)
                ->visit('/preview/kalender-admin')
                ->waitForText('Bimbingan ACC Dosen - Tunggu Admin', 15)
                ->assertPresent('#btn-publish-'.$request->id)
                ->click('#btn-publish-'.$request->id)
                ->pause(3000)
                ->waitUntilMissing('#btn-publish-'.$request->id, 15)
                ->assertMissing('#btn-publish-'.$request->id);
        });
    }

    /**
     * Test 3: Admin dapat menghapus event.
     */
    public function test_admin_can_delete_event(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'admin@sibima.test')->first();

            $browser->loginAs($user)
                ->visit('/preview/kalender-admin')
                ->type('#search-input', 'Libur Akhir Semester Dusk')
                ->waitForText('Libur Akhir Semester Dusk', 15)
                ->click('button.bg-rose-500') // Tombol hapus
                ->waitForText('Hapus event?', 5)
                ->press('Hapus')
                ->pause(1000)
                ->waitUntilMissingText('Libur Akhir Semester Dusk', 20)
                ->assertDontSee('Libur Akhir Semester Dusk');
        });
    }
}
