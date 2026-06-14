<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class DraftSkripsiCatatanTest extends DuskTestCase
{
    /**
     * PBI#24 TC24-01: Menguji penambahan catatan pada draft
     */
    public function test_mahasiswa_can_add_catatan_to_draft(): void
    {
        $this->artisan('migrate:fresh', ['--seed' => true]);

        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'mahasiswa@sibima.test')->first();
            $filePath = realpath(__DIR__ . '/fixtures/dummy.pdf');

            // Upload draft pertama secara otomatis untuk persiapan data pengujian
            $browser->loginAs($user)
                ->visit('/mahasiswa/draft-skripsi')
                ->waitForText('Manajemen Draft Skripsi', 15)
                ->press('Upload Draft Baru')
                ->waitForText('Upload Draft', 5)
                ->type('input[placeholder*="Contoh"]', 'Draft Bab 1 Final')
                ->select('select', 'Bab 1')
                ->attach('input[type="file"]', $filePath)
                ->type('textarea[placeholder*="Tambahkan catatan"]', 'Ini adalah draft awal')
                ->click('button[type="submit"]')
                ->waitForText('Draft Bab 1 Final', 15);

            // Tes inti penambahan catatan
            $browser->click('.bg-amber-50\\/50') // klik card catatan
                ->waitForText('Catatan Pribadi', 5)
                ->type('textarea[placeholder="Tuliskan hal-hal yang perlu diperbaiki, ide, atau pengingat..."]', 'Catatan tambahan dari Dusk Test')
                ->press('Simpan Catatan')
                ->waitForText('Catatan tambahan dari Dusk Test', 15)
                ->assertSee('Catatan tambahan dari Dusk Test');
        });
    }
}
