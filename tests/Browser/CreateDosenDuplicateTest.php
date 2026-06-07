<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

class CreateDosenDuplicateTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected $seed = true;

    public function test_input_data_duplikat_email(): void
    {
        $this->browse(function (Browser $browser) {
            $admin = User::where('email', 'admin@sibima.test')->first();

            User::factory()->create([
                'email' => 'dosen1@example.com'
            ]);

            $browser->loginAs($admin)
                ->visit('/admin/dosens/create')
                ->pause(500)
                ->type('Nama Lengkap', 'Budi Santoso')
                ->type('Alamat email', 'budi@example.com')
                ->type('data.password', '12345678')
                ->type('data.program_studi', 'Sistem Informasi')
                ->type('data.fakultas', 'FRi')
                ->click('button[type="submit"]')
                ->pause(1000)
                ->assertPathIs('/admin/dosens/create')
                ->assertSee('The alamat Email has already been taken.');
        });
    }
}
