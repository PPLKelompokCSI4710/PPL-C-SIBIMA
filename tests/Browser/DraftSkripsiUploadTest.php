<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class DraftSkripsiUploadTest extends DuskTestCase
{
    /**
     * PBI#23 TC23-01: Upload draft skripsi format PDF/Word
     */
    public function test_mahasiswa_can_upload_draft_skripsi(): void
    {
        $this->artisan('migrate:fresh', ['--seed' => true]);

        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'mahasiswa@sibima.test')->first();
            $filePath = realpath(__DIR__ . '/fixtures/dummy.pdf');

            $browser->loginAs($user)
                ->visit('/mahasiswa/draft-skripsi')
                ->waitForText('Manajemen Draft Skripsi', 15)
                ->press('Upload Draft Baru')
                ->waitForText('Upload Draft', 5)
                ->type('input[placeholder*="Contoh"]', 'Draft Bab 1 Final')
                ->select('select', 'Bab 1')
                ->attach('input[type="file"]', $filePath)
                ->type('textarea[placeholder*="Tambahkan catatan"]', 'Ini adalah draft untuk pengujian positif')
                ->click('button[type="submit"]') // More specific than press
                ->waitForText('Draft Bab 1 Final', 15)
                ->assertSee('Draft Bab 1 Final')
                ->assertSee('BAB 1'); // "Bab 1" displays as BAB 1 (uppercase) due to uppercase class
        });
    }

    /**
     * PBI#23 TC23-02: Menguji upload draft tanpa melampirkan file
     */
    public function test_mahasiswa_cannot_upload_draft_without_file(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'mahasiswa@sibima.test')->first();

            $browser->loginAs($user)
                ->visit('/mahasiswa/draft-skripsi')
                ->waitForText('Manajemen Draft Skripsi', 15)
                ->press('Upload Draft Baru')
                ->waitForText('Upload Draft', 5)
                ->type('input[placeholder*="Contoh"]', 'Draft Bab 2 Tanpa File')
                ->select('select', 'Bab 2')
                // Jangan melampirkan file
                ->click('button[type="submit"]');
            
            // Karena file adalah required, form tidak akan tersubmit dan modal masih terbuka.
            $browser->pause(1000)
                ->assertSee('Upload Draft');
        });
    }
}
