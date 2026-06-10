<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class UpdateProgressStudiTest extends DuskTestCase
{
    /**
     * PBI-18: Mahasiswa Mengelola Progres Studi
     * Skenario: Mahasiswa melihat pencapaian akademik dan memperbarui data SKS/IPK/TAK serta target.
     */
    public function test_mahasiswa_dapat_mengelola_progres_studi()
    {
        // 1. Persiapan: Ambil akun mahasiswa yang memiliki data bimbingan
        $user = User::role('mahasiswa')->first();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/mahasiswa/progress')
                ->waitForText('Progress Studi')

                // Gunakan waitFor() sebelum mengetik
                ->waitFor('#ipk', 10)
                ->type('#ipk', '3.75')
                ->waitFor('#total_sks', 10)
                ->type('#total_sks', '120')
                ->waitFor('#passed_courses', 10)
                ->type('#passed_courses', '38')
                ->waitFor('#tak', 10)
                ->type('#tak', '45')
                ->waitFor('#target_ipk', 10)
                ->type('#target_ipk', '3.90')
                ->waitFor('#target_sks', 10)
                ->type('#target_sks', '144')
                ->waitFor('#target_semester', 10)
                ->select('#target_semester', '8')

                // Verify client-side min attributes are dynamically bound
                ->assertAttribute('#target_ipk', 'min', '3.75')
                ->assertAttribute('#target_sks', 'min', '120')

                ->press('Simpan Perubahan')

                // --- TAHAP 3: VERIFIKASI HASIL ---
                // Memberi waktu sistem memproses (Inertia reload)
                ->waitForText('3.75', 15)    // Cek angka IPK terbaru
                ->assertSee('120')           // Cek angka SKS terbaru
                ->assertSee('/ 144 SKS')
                ->assertSee('38')            // Cek jumlah mata kuliah terbaru
                ->assertSee('45')            // Cek poin TAK terbaru
                ->assertSee('/ 120 Poin')    // Cek target TAK
                ->assertSee('83% Completed') // Cek persentase SKS (120/144 = 83%)
                ->assertSee('38% Completed') // Cek persentase TAK (45/120 = 38%)
                ->assertSee('3.90')          // Cek target IPK terbaru
                ->assertSee('144 SKS')       // Cek target SKS terbaru
                ->assertSee('Semester 8')    // Cek target Semester terbaru

                // Ambil bukti untuk laporan PBI-18
                ->screenshot('PBI-18_Update_Progres_Sukses');
        });
    }
}
