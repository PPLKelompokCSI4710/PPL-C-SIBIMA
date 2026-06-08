<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

class CreateDosenValidTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected $seed = true;

    public function test_input_data_dosen_valid(): void
    {
        $this->browse(function (Browser $browser) {
            $admin = User::where('email', 'admin@sibima.test')->first();

            $browser->loginAs($admin)
                ->visit('/admin/dosens/create')
                ->pause(500)
                ->type('[wire\:model*="data.name"]', 'Budi Santoso')
                ->type('[wire\:model*="data.email"]', 'budi@example.com')
                ->type('[wire\:model*="data.password"]', 'password123')
                ->type('[wire\:model*="data.program_studi"]', 'Informatika')
                ->type('[wire\:model*="data.fakultas"]', 'Fasilkom')
                ->press('Create')
                ->assertPathIs('/admin/dosens')
                ->assertSee('Budi Santoso');
        });
    }
}
