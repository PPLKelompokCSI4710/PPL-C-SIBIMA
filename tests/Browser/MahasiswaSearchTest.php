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

class MahasiswaSearchTest extends DuskTestCase
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

    public function test_tc_mhs_005_search_mahasiswa_by_nim(): void
    {
        $admin = $this->createAdmin();
        $target = $this->createMahasiswa([
            'nim' => '2024002001',
            'nama_lengkap' => 'Mahasiswa Target',
        ]);
        $other = $this->createMahasiswa([
            'nim' => '2024002002',
            'nama_lengkap' => 'Mahasiswa Lain',
        ]);

        $this->browse(function (Browser $browser) use ($admin, $target, $other) {
            $browser->loginAs($admin)
                ->visit('/admin/mahasiswas')
                ->waitFor('.fi-ta-search-field input')
                ->assertPresent('.fi-ta-search-field input')
                ->type('.fi-ta-search-field input', $target->nim)
                ->keys('.fi-ta-search-field input', '{enter}');

            $browser->waitUsing(5, 200, function () use ($browser, $target, $other) {
                $tableText = $browser->text('table');

                return str_contains($tableText, $target->nim)
                    && ! str_contains($tableText, $other->nim);
            });
        });
    }
}
