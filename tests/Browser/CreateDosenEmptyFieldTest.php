<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

class CreateDosenEmptyFieldTest extends DuskTestCase
{
    use DatabaseMigrations;
    
    protected $seed = true;

    public function test_input_dengan_field_kosong(): void
    {
        $this->browse(function (Browser $browser) {
            $admin = User::where('email', 'admin@sibima.test')->first();

            $browser->loginAs($admin)
                    ->visit('/admin/dosens/create')
                    ->pause(1000)
                    ->press('Create')
                    ->pause(1000)
                    ->assertPathIs('/admin/dosens/create');
        });
    }
}
