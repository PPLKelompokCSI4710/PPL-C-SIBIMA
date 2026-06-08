<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

class SearchDosenFoundTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected $seed = true;

    public function test_search_data_dosen_ditemukan(): void
    {
        $this->browse(function (Browser $browser) {
            $admin = User::where('email', 'admin@sibima.test')->first();

            User::factory()->create(['name' => 'Dosen Target']);
            User::factory()->create(['name' => 'Dosen Lainnya']);

            $browser->loginAs($admin)
                ->visit('/admin/dosens')
                ->waitForText('Dosen Pembimbing')
                ->type('input[placeholder*="Search"]', 'Target')
                ->pause(2000)
                ->assertSee('Dosen Pembimbing');
        });
    }
}
