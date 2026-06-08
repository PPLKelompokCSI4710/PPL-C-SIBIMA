<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

class DeleteDosenCancelTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected $seed = true;

    public function test_cancel_delete_data_dosen(): void
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
                ->press('Delete')
                ->press('Cancel')
                ->pause(1000)
                ->assertPathIs('/admin/dosens/' . $dosen->id . '/edit')
                ->assertSee('Budi Santoso');
        });
    }
}
