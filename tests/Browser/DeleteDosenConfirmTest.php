<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

class DeleteDosenConfirmTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected $seed = true;

    public function test_delete_data_dosen_dengan_konfirmasi(): void
    {
        $this->browse(function (Browser $browser) {
            $admin = User::where('email', 'admin@sibima.test')->first();

            // Buat data dosen Budi Santoso
            $dosen = User::factory()->create([
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@example.com',
                'program_studi' => 'Teknik Informatika',
                'fakultas' => 'Fakultas Teknik',
                'kuota_pembimbingan' => 10,
            ]);
            $dosen->assignRole('dosen');

            $browser->loginAs($admin)
                ->visit('/admin/dosens')
                ->waitForText('Dosen Pembimbing')
                ->type('input[placeholder*="Search"]', 'Budi Santoso')
                ->waitForText('Budi Santoso')
                ->pause(1000)
                ->clickLink('Edit')
                ->waitFor('[wire\:model*="data.name"]')
                ->pause(1000)
                ->press('Delete')
                ->pause(1000)
                ->waitForText('Confirm')
                // Menekan tombol Delete di dalam modal konfirmasi
                ->click('button.fi-modal-footer button.fi-btn-color-danger')
                ->waitForLocation('/admin/dosens')
                ->assertDontSee('Budi Santoso');
        });
    }
}
