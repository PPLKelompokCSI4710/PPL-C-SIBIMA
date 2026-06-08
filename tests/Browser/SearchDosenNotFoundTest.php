<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

class SearchDosenNotFoundTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected $seed = true;

    public function test_search_data_dosen_tidak_ditemukan(): void
    {
        $this->browse(function (Browser $browser) {
            $admin = User::where('email', 'admin@sibima.test')->first();

            User::factory()->create(['name' => 'Dosen Tersedia']);

            $browser->loginAs($admin)
                ->visit('/admin/dosens')
                ->pause(1000)
                ->type('input[wire\:model\.live\.debounce\.500ms="tableSearch"]', 'KeywordAcakTidakAda')
                ->pause(1500)
                ->assertDontSee('Dosen Tersedia');
        });
    }
}
