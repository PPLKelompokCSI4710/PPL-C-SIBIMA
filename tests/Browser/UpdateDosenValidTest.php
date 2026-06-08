<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

class UpdateDosenValidTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected $seed = true;

    public function test_edit_data_dosen_berhasil(): void
    {
        $this->browse(function (Browser $browser) {
            $admin = User::where('email', 'admin@sibima.test')->first();

            $browser->loginAs($admin)
                ->visit('/admin/dosens')
                ->waitForText('Dosen Pembimbing')
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
