<?php

namespace Tests\Browser;

use App\Models\Dosen;
use App\Models\KetersediaanJadwal;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class KetersediaanJadwalTest extends DuskTestCase
{
    /**
     * Dosen dapat menambah ketersediaan jadwal bimbingan.
     * Command: php artisan dusk --filter=KetersediaanJadwalTest
     */
    public function test_dosen_tambah_ketersediaan()
    {
        $this->browse(function (Browser $browser) {
            $dosenUser = User::where('email', 'dosen@sibima.test')->first();
            $dosen = Dosen::where('user_id', $dosenUser->id)->first();

            KetersediaanJadwal::where('dosen_id', $dosen->id)->delete();
            $tanggal = now()->addDays(3)->format('mdY');

            $browser->loginAs($dosenUser)
                ->visit('/dosen/ketersediaan-jadwal')
                ->waitForText('Kelola Ketersediaan Jadwal', 15)
                ->keys('input[type="date"]', $tanggal)
                ->type('div.grid-cols-2 div:nth-child(1) input[type="time"]', '09:00')
                ->type('div.grid-cols-2 div:nth-child(2) input[type="time"]', '11:00')
                ->clear('input[type="number"]')
                ->type('input[type="number"]', '5')
                ->press('Tambahkan Jadwal')
                ->waitForText('5 Orang', 15);
        });
    }
}
