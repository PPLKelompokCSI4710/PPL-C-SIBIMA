<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class DraftSkripsiRiwayatTest extends DuskTestCase
{
    /**
     * PBI#25 TC25-01: Menguji tampilan riwayat versi draft
     */
    public function test_mahasiswa_can_see_draft_history(): void
    {
        $this->artisan('migrate:fresh', ['--seed' => true]);

        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'mahasiswa@sibima.test')->first();
            $filePath = realpath(__DIR__ . '/fixtures/dummy.pdf');

            // Upload draft pertama
            $browser->loginAs($user)
                ->visit('/mahasiswa/draft-skripsi')
                ->waitForText('Manajemen Draft Skripsi', 15)
                ->press('Upload Draft Baru')
                ->waitForText('Upload Draft', 5)
                ->type('input[placeholder*="Contoh"]', 'Draft Bab 1 Final')
                ->select('select', 'Bab 1')
                ->attach('input[type="file"]', $filePath)
                ->type('textarea[placeholder*="Tambahkan catatan"]', 'Draft pertama')
                ->click('button[type="submit"]')
                ->waitForText('Draft Bab 1 Final', 15);

            // Upload draft kedua (history)
            $browser->press('Upload Draft Baru')
                ->waitForText('Upload Draft', 5)
                ->type('input[placeholder*="Contoh"]', 'Draft Bab 1 V2')
                ->select('select', 'Bab 1')
                ->attach('input[type="file"]', $filePath)
                ->type('textarea[placeholder*="Tambahkan catatan"]', 'Ini adalah revisi kedua')
                ->click('button[type="submit"]')
                ->waitForText('Draft Bab 1 V2', 15);

            // Verifikasi bahwa ada 2 draft yang tampil (riwayat draft)
            $browser->assertSee('Draft Bab 1 Final')
                    ->assertSee('Draft Bab 1 V2');
        });
    }

    /**
     * PBI#25 TC255-02: Menguji unduhan file draft dari riwayat
     */
    public function test_mahasiswa_can_download_draft(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'mahasiswa@sibima.test')->first();

            $browser->loginAs($user)
                ->visit('/mahasiswa/draft-skripsi')
                ->waitForText('Draft Bab 1 V2', 15)
                // Temukan tombol download dengan atribut title='Download File' pada riwayat
                ->assertPresent('a[title="Download File"]')
                // Verifikasi bahwa link download ada dan memiliki atribut href yang valid
                ->assertAttributeContains('a[title="Download File"]', 'href', '/storage/draft_skripsi/');
        });
    }
}
