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

class MahasiswaFilterTest extends DuskTestCase
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

    public function test_tc_mhs_006_filter_mahasiswa_status_cuti(): void
    {
        $admin = $this->createAdmin();
        $cuti = $this->createMahasiswa([
            'nim' => '2024003001',
            'nama_lengkap' => 'Mahasiswa Cuti',
            'status_akademik' => AkademikStatus::CUTI->value,
        ]);
        $aktif = $this->createMahasiswa([
            'nim' => '2024003002',
            'nama_lengkap' => 'Mahasiswa Aktif',
            'status_akademik' => AkademikStatus::AKTIF->value,
        ]);

        $this->browse(function (Browser $browser) use ($admin, $cuti, $aktif) {
            $browser->loginAs($admin)
                ->visit('/admin/mahasiswas')
                ->click('@filter-button')
                ->waitForText('Status Akademik', 10)
                ->waitFor('@status-akademik', 10)
                ->select('@status-akademik', 'cuti')
                ->click('@apply-filter')
                ->waitUsing(10, 250, fn () => ! str_contains($browser->text('table'), $aktif->nim))
                ->assertSee($cuti->nim)
                ->assertDontSee($aktif->nim);
        });
    }
}
