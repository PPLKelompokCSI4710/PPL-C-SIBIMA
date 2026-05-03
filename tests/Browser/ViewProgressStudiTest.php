<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ViewProgressStudiTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     */
    public function test_dosen_dapat_memonitoring_progres_mahasiswa()
    {
        $dosen = User::role('dosen')->first();

        $this->browse(function (Browser $browser) use ($dosen) {
            $browser->loginAs($dosen)
                ->visit('/staff/progress')

                    // Menunggu judul halaman muncul
                ->waitForText('Progres Mahasiswa')

                    // Cek teks yang benar-benar ada di layar (huruf besar/kecil berpengaruh)
                ->assertSee('MAHASISWA')
                ->assertSee('IPK')
                ->assertSee('SKS LULUS')
                ->assertSee('MK LULUS')

                    // Cek apakah data mahasiswa tampil (Atina Wildannur)
                ->assertSee('Atina Wildannur')

                    // Bukti screenshot
                ->screenshot('dosen-monitoring-sukses');
        });
    }
}
