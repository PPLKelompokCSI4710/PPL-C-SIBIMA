<?php

namespace Tests\Browser;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Spatie\Permission\Models\Role;
use Tests\DuskTestCase;

class MahasiswaDuplicateEmailTest extends DuskTestCase
{
    use DatabaseMigrations;
    use InteractsWithDatabase;

    private function ensureRoles(): void
    {
        Role::findOrCreate(UserRole::ADMIN->value);
        Role::findOrCreate(UserRole::MAHASISWA->value);
    }

    private function createAdmin(): User
    {
        $this->ensureRoles();

        $admin = User::factory()->create([
            'name' => 'Admin Dusk',
            'email' => 'admin_dusk@example.test',
        ]);

        $admin->assignRole(UserRole::ADMIN->value);

        return $admin;
    }

    public function test_tc_mhs_002_create_mahasiswa_duplicate_email(): void
    {
        $admin = $this->createAdmin();
        $email = 'duplikat@example.test';

        User::factory()->create([
            'email' => $email,
        ]);

        $this->browse(function (Browser $browser) use ($admin, $email) {
            $browser->loginAs($admin)
                ->visit('/admin/mahasiswas/create')
                ->waitFor('input[placeholder="contoh@email.com"]')
                ->assertPresent('input[placeholder="contoh@email.com"]')
                ->assertPresent('input[placeholder="Contoh: 2024001001"]')
                ->assertPresent('input[placeholder="Nama sesuai KTP"]')
                ->assertPresent('input[placeholder="Contoh: Teknik Informatika"]')
                ->assertPresent('input[placeholder="Contoh: 2024"]')
                ->type('input[placeholder="contoh@email.com"]', $email)
                ->type('input[placeholder="Contoh: 2024001001"]', '2024001002')
                ->type('input[placeholder="Nama sesuai KTP"]', 'Mahasiswa Duplikat')
                ->type('input[placeholder="Contoh: Teknik Informatika"]', 'Sistem Informasi')
                ->type('input[placeholder="Contoh: 2024"]', '2024')
                ->press('Create')
                ->waitForText('Email ini sudah terdaftar sebagai akun pengguna.');
        });

        $this->assertDatabaseMissing('mahasiswa', [
            'nim' => '2024001002',
        ]);
    }
}
