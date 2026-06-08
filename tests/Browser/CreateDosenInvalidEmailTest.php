<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

class CreateDosenInvalidEmailTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected $seed = true;

    public function test_input_dengan_format_email_tidak_valid(): void
    {
        $this->browse(function (Browser $browser) {
            $admin = User::where('email', 'admin@sibima.test')->first();

            $browser->loginAs($admin)
                ->visit('/admin/dosens/create')
                ->pause(500)
                ->type('data.name', 'Andi')
                ->type('data.email', 'bukan-format-email')
                ->type('data.password', 'password123')
                ->type('data.program_studi', 'Informatika')
                ->type('data.fakultas', 'Fasilkom')
                ->click('button[type="submit"]')
                ->pause(1000)
                ->assertPathIs('/admin/dosens/create');
        });
    }
}
