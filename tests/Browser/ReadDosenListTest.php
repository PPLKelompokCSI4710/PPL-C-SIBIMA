<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

class ReadDosenListTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected $seed = true;

    public function test_menampilkan_daftar_dosen(): void
    {
        $this->browse(function (Browser $browser) {
            $admin = User::where('email', 'admin@sibima.test')->first();

            User::factory()->create(['name' => 'Siti Aminah']);
            User::factory()->create(['name' => 'Bambang Pamungkas']);

            $browser->loginAs($admin)
                ->visit('/admin/dosens')
                ->pause(1000)
                ->assertPresent('table')
                ->assertSee('Siti Aminah')
                ->assertSee('Bambang Pamungkas');
        });
    }
}
