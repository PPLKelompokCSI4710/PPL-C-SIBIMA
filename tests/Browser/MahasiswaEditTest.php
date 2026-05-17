<?php

namespace Tests\Browser;

use App\Enums\AkademikStatus;
use App\Enums\UserRole;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Spatie\Permission\Models\Role;
use Tests\DuskTestCase;

class MahasiswaEditTest extends DuskTestCase
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

    private function createMahasiswa(array $mahasiswaOverrides = [], array $userOverrides = []): Mahasiswa
    {
        $this->ensureRoles();

        $user = User::factory()->create(array_merge([
            'name' => $mahasiswaOverrides['nama_lengkap'] ?? 'Mahasiswa Dusk',
            'email' => 'mhs_'.uniqid().'@example.test',
        ], $userOverrides));

        $user->assignRole(UserRole::MAHASISWA->value);

        $defaults = [
            'user_id' => $user->id,
            'nim' => '202400'.random_int(100, 999),
            'nama_lengkap' => 'Mahasiswa Dusk',
            'program_studi' => 'Teknik Informatika',
            'fakultas' => 'Fakultas Ilmu Komputer',
            'angkatan' => '2024',
            'semester' => '1',
            'ipk' => 3.25,
            'sks_lulus' => 0,
            'sks_total' => 144,
            'status_akademik' => AkademikStatus::AKTIF->value,
        ];

        return Mahasiswa::create(array_merge($defaults, $mahasiswaOverrides));
    }

    public function test_tc_mhs_003_edit_ipk_and_status(): void
    {
        $admin = $this->createAdmin();
        $mahasiswa = $this->createMahasiswa([
            'nim' => '2024001003',
            'nama_lengkap' => 'Mahasiswa Edit',
            'status_akademik' => AkademikStatus::AKTIF->value,
        ]);

        $this->browse(function (Browser $browser) use ($admin, $mahasiswa) {
            $browser->loginAs($admin)
                ->visit("/admin/mahasiswas/{$mahasiswa->id}/edit")
                ->waitFor('input[placeholder="0.00"]')
                ->assertPresent('input[placeholder="0.00"]')
                ->type('input[placeholder="0.00"]', '3.75');

            $browser->script('(() => {
                const label = Array.from(document.querySelectorAll("label, span, div"))
                    .find((el) => el.textContent?.trim() === "Status Akademik");
                const wrapper = label?.closest("[role=\"combobox\"], .fi-fo-field-wrp, .fi-fo-select, .fi-input-wrp")
                    || label?.parentElement;
                const target = wrapper?.querySelector("[role=\"combobox\"], button, .fi-input-wrp, .fi-fo-select")
                    || wrapper;
                if (target) {
                    target.click();
                }
            })();');

            $browser->script('(() => {
                const option = Array.from(document.querySelectorAll("[role=\"option\"], .fi-select-option, li, button"))
                    .find((el) => el.textContent?.trim() === "Cuti");
                if (option) {
                    option.click();
                }
            })();');

            $browser->press('Save changes')
                ->visit('/admin/mahasiswas')
                ->assertSee('Cuti');
        });

        $this->assertDatabaseHas('mahasiswa', [
            'id' => $mahasiswa->id,
            'ipk' => '3.75',
            'status_akademik' => AkademikStatus::CUTI->value,
        ]);
    }
}
