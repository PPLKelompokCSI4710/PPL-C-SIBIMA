<?php

namespace Tests\Browser;

use App\Models\StudentProgress;
use App\Models\User;
use App\Models\Mahasiswa;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class TargetAkademikTest extends DuskTestCase
{
    /**
     * Helper to prepare testing data (user, mahasiswa, and reset student progress).
     */
    private function prepareTestData($ipk = 3.50, $totalSks = 100, $tak = 45)
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
                'program_studi' => 'Teknik Informatika',
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
                'program_studi' => 'Teknik Informatika',
            ]);
        }

        // Reset atau buat student progress awal
        StudentProgress::updateOrCreate(
            ['user_id' => $user->id],
            [
                'ipk' => $ipk,
                'total_sks' => $totalSks,
                'passed_courses' => 30,
                'target_ipk' => null,
                'target_sks' => null,
                'target_semester' => null,
                'tak' => $tak,
            ]
        );

        return [$user, $mahasiswa];
    }

    /**
     * Case ID: TC-021-01
     * Test Scenario: Input target akademik berhasil
     */
    public function test_tc_021_01_input_target_akademik_berhasil(): void
    {
        [$user] = $this->prepareTestData(3.50, 100, 45);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/mahasiswa/progress')
                ->waitForText('Progress Studi')
                ->waitFor('@target-ipk')
                ->type('@target-ipk', '3.80')
                ->type('@target-sks', '144')
                ->select('@target-semester', '8')
                ->press('@submit-button')
                ->waitForText('Berhasil disimpan!', 15)
                // Verifikasi nilai target ter-render di UI
                ->assertSee('3.80')
                ->assertSee('144 SKS')
                ->assertSee('Semester 8')
                ->screenshot('TC-021-01');
        });
    }

    /**
     * Case ID: TC-021-02
     * Test Scenario: Input target IPK valid
     */
    public function test_tc_021_02_input_target_ipk_valid(): void
    {
        [$user] = $this->prepareTestData(3.50, 100, 45);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/mahasiswa/progress')
                ->waitForText('Progress Studi')
                ->waitFor('@target-ipk')
                ->type('@target-ipk', '3.75')
                ->press('@submit-button')
                ->waitForText('Berhasil disimpan!', 15)
                ->assertSee('3.75')
                ->screenshot('TC-021-02');
        });
    }

    /**
     * Case ID: TC-021-03
     * Test Scenario: Input target SKS valid
     */
    public function test_tc_021_03_input_target_sks_valid(): void
    {
        [$user] = $this->prepareTestData(3.50, 100, 45);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/mahasiswa/progress')
                ->waitForText('Progress Studi')
                ->waitFor('@target-sks')
                ->type('@target-sks', '144')
                ->press('@submit-button')
                ->waitForText('Berhasil disimpan!', 15)
                ->assertSee('144 SKS')
                ->screenshot('TC-021-03');
        });
    }

    /**
     * Case ID: TC-021-04
     * Test Scenario: Input target waktu lulus valid
     */
    public function test_tc_021_04_input_target_waktu_lulus_valid(): void
    {
        [$user] = $this->prepareTestData(3.50, 100, 45);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/mahasiswa/progress')
                ->waitForText('Progress Studi')
                ->waitFor('@target-semester')
                ->select('@target-semester', '8')
                ->press('@submit-button')
                ->waitForText('Berhasil disimpan!', 15)
                ->assertSee('Semester 8')
                ->screenshot('TC-021-04');
        });
    }

    /**
     * Case ID: TC-021-05
     * Test Scenario: Target IPK kosong
     */
    public function test_tc_021_05_target_ipk_kosong(): void
    {
        [$user] = $this->prepareTestData(3.50, 100, 45);
        StudentProgress::where('user_id', $user->id)->update(['target_ipk' => 3.80]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/mahasiswa/progress')
                ->waitForText('Progress Studi')
                ->waitFor('@target-ipk')
                ->type('@target-ipk', '')
                ->press('@submit-button')
                ->waitForText('Berhasil disimpan!', 15)
                ->screenshot('TC-021-05');
        });
    }

    /**
     * Case ID: TC-021-06
     * Test Scenario: Target IPK lebih dari 4.00
     */
    public function test_tc_021_06_target_ipk_lebih_dari_4(): void
    {
        [$user] = $this->prepareTestData(3.50, 100, 45);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/mahasiswa/progress')
                ->waitForText('Progress Studi')
                ->waitFor('@target-ipk')
                ->type('@target-ipk', '4.50')
                ->press('@submit-button')
                ->pause(1000) // Form submission diblokir di sisi browser oleh atribut max="4"
                ->screenshot('TC-021-06');
        });
    }

    /**
     * Case ID: TC-021-07
     * Test Scenario: Target IPK kurang dari 0
     */
    public function test_tc_021_07_target_ipk_kurang_dari_0(): void
    {
        [$user] = $this->prepareTestData(3.50, 100, 45);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/mahasiswa/progress')
                ->waitForText('Progress Studi')
                ->waitFor('@target-ipk')
                ->type('@target-ipk', '-1.00')
                ->press('@submit-button')
                ->pause(1000) // Form submission diblokir di sisi browser oleh atribut min="ipk"
                ->screenshot('TC-021-07');
        });
    }

    /**
     * Case ID: TC-021-08
     * Test Scenario: Target SKS kosong
     */
    public function test_tc_021_08_target_sks_kosong(): void
    {
        [$user] = $this->prepareTestData(3.50, 100, 45);
        StudentProgress::where('user_id', $user->id)->update(['target_sks' => 120]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/mahasiswa/progress')
                ->waitForText('Progress Studi')
                ->waitFor('@target-sks')
                ->type('@target-sks', '')
                ->press('@submit-button')
                ->waitForText('Berhasil disimpan!', 15)
                ->screenshot('TC-021-08');
        });
    }

    /**
     * Case ID: TC-021-09
     * Test Scenario: Target SKS bernilai negatif
     */
    public function test_tc_021_09_target_sks_bernilai_negatif(): void
    {
        [$user] = $this->prepareTestData(3.50, 100, 45);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/mahasiswa/progress')
                ->waitForText('Progress Studi')
                ->waitFor('@target-sks')
                ->type('@target-sks', '-50')
                ->press('@submit-button')
                ->pause(1000) // Form submission diblokir oleh atribut min SKS
                ->screenshot('TC-021-09');
        });
    }

    /**
     * Case ID: TC-021-10
     * Test Scenario: Target waktu lulus kosong
     */
    public function test_tc_021_10_target_waktu_lulus_kosong(): void
    {
        [$user] = $this->prepareTestData(3.50, 100, 45);
        StudentProgress::where('user_id', $user->id)->update(['target_semester' => 8]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/mahasiswa/progress')
                ->waitForText('Progress Studi')
                ->waitFor('@target-semester')
                ->select('@target-semester', '')
                ->press('@submit-button')
                ->waitForText('Berhasil disimpan!', 15)
                ->screenshot('TC-021-10');
        });
    }

    /**
     * Case ID: TC-021-11
     * Test Scenario: Target waktu lulus bernilai 0
     */
    public function test_tc_021_11_target_waktu_lulus_bernilai_0(): void
    {
        [$user] = $this->prepareTestData(3.50, 100, 45);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/mahasiswa/progress')
                ->waitForText('Progress Studi')
                ->waitFor('@target-semester')
                // Menggunakan JS untuk menyisipkan nilai 0 agar memicu validasi backend
                ->script("document.getElementById('target_semester').innerHTML += '<option value=\"0\">Semester 0</option>';");
            
            $browser->select('@target-semester', '0')
                ->press('@submit-button')
                ->pause(1000)
                ->screenshot('TC-021-11');
        });
    }

    /**
     * Case ID: TC-021-12
     * Test Scenario: Update target akademik yang sudah ada
     */
    public function test_tc_021_12_update_target_akademik_yang_sudah_ada(): void
    {
        [$user] = $this->prepareTestData(3.50, 100, 45);
        StudentProgress::where('user_id', $user->id)->update([
            'target_ipk' => 3.70,
            'target_sks' => 120,
            'target_semester' => 8,
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/mahasiswa/progress')
                ->waitForText('Progress Studi')
                ->waitFor('@target-ipk')
                ->type('@target-ipk', '3.95')
                ->type('@target-sks', '144')
                ->select('@target-semester', '7')
                ->press('@submit-button')
                ->waitForText('Berhasil disimpan!', 15)
                ->assertSee('3.95')
                ->assertSee('144 SKS')
                ->assertSee('Semester 7')
                ->screenshot('TC-021-12');
        });
    }

    /**
     * Case ID: TC-021-13
     * Test Scenario: Validasi pesan sukses
     */
    public function test_tc_021_13_validasi_pesan_sukses(): void
    {
        [$user] = $this->prepareTestData(3.50, 100, 45);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/mahasiswa/progress')
                ->waitForText('Progress Studi')
                ->waitFor('@target-ipk')
                ->type('@target-ipk', '3.80')
                ->press('@submit-button')
                ->waitForText('Berhasil disimpan!', 15)
                ->screenshot('TC-021-13');
        });
    }

    /**
     * Case ID: TC-021-14
     * Test Scenario: Validasi pesan error
     */
    public function test_tc_021_14_validasi_pesan_error(): void
    {
        [$user] = $this->prepareTestData(3.50, 100, 45);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/mahasiswa/progress')
                ->waitForText('Progress Studi')
                ->waitFor('@target-ipk')
                // Hapus batasan min HTML5 sementara dengan JS agar form bisa di-submit
                ->script("document.getElementById('target_ipk').removeAttribute('min')");

            $browser->type('@target-ipk', '3.20') // Lebih rendah dari IPK saat ini 3.50
                ->press('@submit-button')
                ->waitForText('Target IPK tidak boleh lebih rendah dari IPK saat ini.', 15)
                ->screenshot('TC-021-14');
        });
    }
}
