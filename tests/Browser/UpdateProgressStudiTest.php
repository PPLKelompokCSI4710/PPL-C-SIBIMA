<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class UpdateProgressStudiTest extends DuskTestCase
{
    /**
     * PBI-18: Mahasiswa Mengelola Progres Studi
     * Skenario: Mahasiswa melihat pencapaian akademik dan memperbarui data SKS/IPK.
     */
    public function test_mahasiswa_dapat_mengelola_progres_studi()
    {
        // 1. Persiapan: Ambil akun mahasiswa yang memiliki data bimbingan
        $user = User::where('name', 'Atina Wildannur')->first();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/mahasiswa/progress')
                ->waitForText('Progress Studi')

                    // Gunakan waitFor() sebelum mengetik
                ->waitFor('#ipk', 10)
                ->type('#ipk', '3.99')
                ->waitFor('#total_sks', 10)
                ->type('#total_sks', '97')
                ->waitFor('#passed_courses', 10)
                ->type('#passed_courses', '39')
                ->press('Simpan Perubahan')

                    // --- TAHAP 3: VERIFIKASI HASIL ---
                    // Memberi waktu sistem memproses (Inertia reload)
                ->pause(2000)

                    // Verifikasi apakah statistik di sisi kiri sudah terupdate
                ->assertSee('4')             // Cek angka IPK terbaru
                ->assertSee('122')           // Cek angka SKS terbaru
                ->assertSee('/ 144 SKS')
                ->assertSee('42')            // Cek jumlah mata kuliah terbaru
                ->assertSee('85%')           // Cek persentase progres terbaru

                    // Ambil bukti untuk laporan PBI-18
                ->screenshot('PBI-18_Update_Progres_Sukses');
        });
    }
}
