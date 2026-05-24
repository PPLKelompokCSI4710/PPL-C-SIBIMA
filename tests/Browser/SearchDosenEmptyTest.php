<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

class SearchDosenEmptyTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected $seed = true;

    public function test_search_dengan_input_kosong(): void
    {
        $this->browse(function (Browser $browser) {
            $admin = User::where('email', 'admin@sibima.test')->first();

            User::factory()->create(['name' => 'Dosen Pertama']);
            User::factory()->create(['name' => 'Dosen Kedua']);

            $browser->loginAs($admin)
                ->visit('/admin/dosens')
                ->pause(1000)
                ->type('input[wire\:model\.live\.debounce\.500ms="tableSearch"]', ' ')
                ->pause(500)
                ->keys('input[wire\:model\.live\.debounce\.500ms="tableSearch"]', '{backspace}')
                ->pause(1500)
                ->assertSee('Dosen Pertama')
                ->assertSee('Dosen Kedua');
        });
    }
}
