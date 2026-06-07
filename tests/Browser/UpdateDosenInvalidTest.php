<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

class UpdateDosenInvalidTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected $seed = true;

    public function test_edit_dengan_data_tidak_valid(): void
    {
        $this->browse(function (Browser $browser) {
            $admin = User::where('email', 'admin@sibima.test')->first();

            $browser->loginAs($admin)
                ->visit('/admin/dosens')
                ->waitForText('Budi Santoso')
                ->type('input[placeholder*="Search"]', 'Budi Santoso')
                ->waitForText('Budi Santoso')
                ->pause(1000)
                ->clickLink('Edit')
                ->waitFor('[wire\:model*="data.name"]')
                ->clear('[wire\:model*="data.name"]')
                ->type('[wire\:model*="data.name"]', 'Budi Santoso Updated')
                ->press('Save changes')
                ->waitForLocation('/admin/dosens')
                ->assertSee('Budi Santoso Updated');
        });
    }
}
