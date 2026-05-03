<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class UpdateStudyPlanTest extends DuskTestCase
{
    /**
     * PBI-19: Mahasiswa Menginput/Menambahkan Rencana Studi (KRS)
     */
    public function test_mahasiswa_dapat_menginput_krs()
    {
        $user = User::role('mahasiswa')->first();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/mahasiswa/study-plans')
                ->waitForText('Study Plans (KRS)')

                    // 1. Langkah Membuka Form Input
                ->press('Tambah Mata Kuliah')
                ->waitForText('Tambah Mata Kuliah') // Menunggu modal muncul

                    // 2. Mengisi Data Mata Kuliah
                    // (Pilih mata kuliah yang belum pernah Anda ambil sebelumnya)
                ->select('course_id')
                ->select('semester', '5')

                    // 3. Menyimpan Data
                ->press('Tambah')

                    // 4. Verifikasi: Apakah data berhasil masuk ke tabel?
                ->pause(2000)
                ->assertSee('Sem 5') // Memastikan semester yang dipilih tampil di tabel
                ->assertSee('Pending') // Status awal biasanya Pending
                ->screenshot('PBI-19_Input_KRS_Sukses');
        });
    }
}
