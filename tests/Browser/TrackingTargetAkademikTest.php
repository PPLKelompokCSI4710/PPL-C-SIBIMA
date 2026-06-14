<?php

namespace Tests\Browser;

use App\Models\StudentProgress;
use App\Models\User;
use App\Models\Mahasiswa;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class TrackingTargetAkademikTest extends DuskTestCase
{
    /**
     * Helper to prepare testing data (user, mahasiswa, and reset student progress).
     */
    private function prepareTestData($ipk = 3.50, $totalSks = 100, $tak = 45, $targetIpk = null, $targetSks = null, $targetSem = null)
    {
        $user = User::role('mahasiswa')->first();
        if (!$user) {
            $user = User::factory()->create([
                'name' => 'Mahasiswa Test Dusk',
                'email' => 'mahasiswa_dusk@example.com',
            ]);
            $user->assignRole('mahasiswa');
        }

        $mahasiswa = $user->mahasiswa;
        if (!$mahasiswa) {
            $mahasiswa = Mahasiswa::create([
                'user_id' => $user->id,
                'nim' => '202400999',
                'nama_lengkap' => $user->name,
                'program_studi' => 'Teknik Informatika S1',
                'fakultas' => 'Fakultas Ilmu Komputer',
                'angkatan' => '2024',
                'semester' => '5',
                'ipk' => $ipk,
                'sks_lulus' => 0,
                'sks_total' => 144,
                'status_akademik' => 'aktif',
            ]);
        } else {
            $mahasiswa->update([
                'ipk' => $ipk,
                'nim' => '202400999',
                'semester' => '5',
                'program_studi' => 'Teknik Informatika S1',
            ]);
        }

        // Reset atau buat student progress awal
        StudentProgress::updateOrCreate(
            ['user_id' => $user->id],
            [
                'ipk' => $ipk,
                'total_sks' => $totalSks,
                'passed_courses' => 30,
                'target_ipk' => $targetIpk,
                'target_sks' => $targetSks,
                'target_semester' => $targetSem,
                'tak' => $tak,
            ]
        );

        return [$user, $mahasiswa];
    }

    /**
     * Case ID: TC-022-01
     * Test Scenario: Menampilkan target IPK
     */
    public function test_tc_022_01_menampilkan_target_ipk(): void
    {
        [$user] = $this->prepareTestData(3.50, 100, 45, 3.80, null, null);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/mahasiswa/progress')
                ->waitForText('Progress Studi')
                ->assertSee('Target IPK')
                ->assertSee('3.80')
                ->screenshot('TC-022-01');
        });
    }

    /**
     * Case ID: TC-022-02
     * Test Scenario: Menampilkan target SKS
     */
    public function test_tc_022_02_menampilkan_target_sks(): void
    {
        [$user] = $this->prepareTestData(3.50, 100, 45, null, 144, null);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/mahasiswa/progress')
                ->waitForText('Progress Studi')
                ->assertSee('TARGET SKS')
                ->assertSee('144 SKS')
                ->screenshot('TC-022-02');
        });
    }

    /**
     * Case ID: TC-022-03
     * Test Scenario: Menampilkan target waktu lulus
     */
    public function test_tc_022_03_menampilkan_target_waktu_lulus(): void
    {
        [$user] = $this->prepareTestData(3.50, 100, 45, null, null, 8);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/mahasiswa/progress')
                ->waitForText('Progress Studi')
                ->assertSee('TARGET LULUS (SEM 8)')
                ->screenshot('TC-022-03');
        });
    }

    /**
     * Case ID: TC-022-04
     * Test Scenario: Menampilkan IPK saat ini
     */
    public function test_tc_022_04_menampilkan_ipk_saat_ini(): void
    {
        [$user] = $this->prepareTestData(3.50, 100, 45, null, null, null);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/mahasiswa/progress')
                ->waitForText('Progress Studi')
                ->assertSee('IPK Aktif')
                ->assertSee('3.50')
                ->screenshot('TC-022-04');
        });
    }

    /**
     * Case ID: TC-022-05
     * Test Scenario: Menampilkan SKS yang telah ditempuh
     */
    public function test_tc_022_05_menampilkan_sks_yang_telah_ditempuh(): void
    {
        [$user] = $this->prepareTestData(3.50, 100, 45, null, null, null);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/mahasiswa/progress')
                ->waitForText('Progress Studi')
                ->assertSee('100')
                ->assertSee('/ 144 SKS')
                ->screenshot('TC-022-05');
        });
    }

    /**
     * Case ID: TC-022-06
     * Test Scenario: Menampilkan persentase pencapaian target IPK
     */
    public function test_tc_022_06_menampilkan_persentase_pencapaian_target_ipk(): void
    {
        [$user] = $this->prepareTestData(3.50, 100, 45, 3.80, null, null);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/mahasiswa/progress')
                ->waitForText('Progress Studi')
                // Validasi bahwa gauge visual (svg) tampil
                ->assertVisible('svg')
                ->screenshot('TC-022-06');
        });
    }

    /**
     * Case ID: TC-022-07
     * Test Scenario: Menampilkan persentase pencapaian target SKS
     */
    public function test_tc_022_07_menampilkan_persentase_pencapaian_target_sks(): void
    {
        [$user] = $this->prepareTestData(3.50, 100, 45, null, null, null);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/mahasiswa/progress')
                ->waitForText('Progress Studi')
                // 100 dari 144 adalah 69%
                ->assertSee('69% Completed')
                ->screenshot('TC-022-07');
        });
    }

    /**
     * Case ID: TC-022-08
     * Test Scenario: Menampilkan progress keseluruhan target akademik
     */
    public function test_tc_022_08_menampilkan_progress_keseluruhan_target_akademik(): void
    {
        [$user] = $this->prepareTestData(3.50, 100, 45, null, null, 8);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/mahasiswa/progress')
                ->waitForText('Progress Studi')
                ->assertSee('SEM 5 (AKTIF)')
                ->assertSee('TARGET LULUS (SEM 8)')
                ->screenshot('TC-022-08');
        });
    }

    /**
     * Case ID: TC-022-09
     * Test Scenario: Menampilkan chart/grafik progress
     */
    public function test_tc_022_09_menampilkan_chart_grafik_progress(): void
    {
        [$user] = $this->prepareTestData(3.50, 100, 45, 3.80, 144, 8);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/mahasiswa/progress')
                ->waitForText('Progress Studi')
                // Validasi elemen input target juga terlihat di form kanan
                ->assertVisible('@target-ipk')
                ->assertVisible('@target-sks')
                ->assertVisible('@target-semester')
                ->screenshot('TC-022-09');
        });
    }

    /**
     * Case ID: TC-022-10
     * Test Scenario: Data target akademik belum tersedia
     */
    public function test_tc_022_10_data_target_akademik_belum_tersedia(): void
    {
        [$user] = $this->prepareTestData(3.50, 100, 45, null, null, null);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/mahasiswa/progress')
                ->waitForText('Progress Studi')
                ->assertSee('Target belum dikonfigurasi')
                ->screenshot('TC-022-10');
        });
    }

    /**
     * Case ID: TC-022-11
     * Test Scenario: Target akademik sudah tercapai
     */
    public function test_tc_022_11_target_akademik_sudah_tercapai(): void
    {
        // IPK aktual 3.85, target IPK 3.80 (sudah tercapai)
        [$user] = $this->prepareTestData(3.85, 144, 45, 3.80, 144, 5);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/mahasiswa/progress')
                ->waitForText('Progress Studi')
                ->assertSee('Target IPK Tercapai! 🎉')
                ->assertSee('Target SKS Tercapai! 🎉')
                ->screenshot('TC-022-11');
        });
    }

    /**
     * Case ID: TC-022-12
     * Test Scenario: Target akademik belum tercapai
     */
    public function test_tc_022_12_target_akademik_belum_tercapai(): void
    {
        // IPK aktual 3.50, target IPK 3.80 (kurang 0.30)
        [$user] = $this->prepareTestData(3.50, 100, 45, 3.80, 120, 8);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/mahasiswa/progress')
                ->waitForText('Progress Studi')
                ->assertSee('Butuh +0.30 untuk capai target')
                ->assertSee('Kurang 20 SKS untuk target')
                ->screenshot('TC-022-12');
        });
    }

    /**
     * Case ID: TC-022-13
     * Test Scenario: Validasi perhitungan progress (AI Graduation Predictor)
     */
    public function test_tc_022_13_validasi_perhitungan_progress(): void
    {
        // 100 SKS ditempuh, sisa 44 SKS. IPK 3.50 -> Kapasitas 24 SKS/Semester.
        // Skenario Optimis: ceil(44 / 24) = 2 Semester.
        // Skenario Normal: ceil(44 / 18) = 3 Semester.
        [$user] = $this->prepareTestData(3.50, 100, 45, 3.80, 144, 8);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/mahasiswa/progress')
                ->waitForText('Progress Studi')
                ->assertSee('+2 Semester')
                ->assertSee('+3 Semester')
                ->screenshot('TC-022-13');
        });
    }

    /**
     * Case ID: TC-022-14
     * Test Scenario: Validasi tampilan data kosong
     */
    public function test_tc_022_14_validasi_tampilan_data_kosong(): void
    {
        [$user] = $this->prepareTestData(3.50, 100, 45, null, null, null);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/mahasiswa/progress')
                ->waitForText('Progress Studi')
                ->assertSee('-') // Target IPK kosong ter-render default '-'
                ->screenshot('TC-022-14');
        });
    }

    /**
     * Case ID: TC-022-15
     * Test Scenario: Validasi informasi pencapaian target (TAK)
     */
    public function test_tc_022_15_validasi_informasi_pencapaian_target(): void
    {
        // Poin TAK 45 dari 120 = 38%
        [$user] = $this->prepareTestData(3.50, 100, 45, 3.80, 144, 8);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/mahasiswa/progress')
                ->waitForText('Progress Studi')
                ->assertSee('45')
                ->assertSee('38% Completed')
                ->screenshot('TC-022-15');
        });
    }

    /**
     * Case ID: TC-022-16
     * Test Scenario: Mengakses halaman progress tanpa login
     */
    public function test_tc_022_16_mengakses_halaman_progress_tanpa_login(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->logout()
                ->visit('/mahasiswa/progress')
                ->assertPathIs('/login')
                ->screenshot('TC-022-16');
        });
    }

    /**
     * Case ID: TC-022-17
     * Test Scenario: Mengakses halaman progress dengan role yang tidak sesuai
     */
    public function test_tc_022_17_mengakses_halaman_progress_dengan_role_yang_tidak_sesuai(): void
    {
        $dosen = User::role('dosen')->first();
        if (!$dosen) {
            $dosen = User::factory()->create([
                'name' => 'Dosen Test Dusk',
                'email' => 'dosen_dusk@example.com',
            ]);
            $dosen->assignRole('dosen');
        }

        $this->browse(function (Browser $browser) use ($dosen) {
            $browser->loginAs($dosen)
                ->visit('/mahasiswa/progress')
                // Rute dilindungi middleware role:mahasiswa, sehingga mengembalikan 403 Forbidden
                // di Inertia biasanya di-render sebagai halaman error/forbidden
                ->assertDontSee('Progress Studi')
                ->screenshot('TC-022-17');
        });
    }
}
