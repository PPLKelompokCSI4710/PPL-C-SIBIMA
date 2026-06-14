<?php

namespace Tests\Browser;

use App\Models\Course;
use App\Models\Mahasiswa;
use App\Models\StudyPlan;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class RencanaStudiTest extends DuskTestCase
{
    /**
     * Helper to prepare testing data (user, courses, and fresh study plan).
     */
    private function prepareTestData()
    {
        // Temukan atau buat user dengan role mahasiswa
        $user = User::role('mahasiswa')->first();
        if (!$user) {
            $user = User::factory()->create([
                'name' => 'Mahasiswa Test Dusk',
                'email' => 'mahasiswa_dusk@example.com',
            ]);
            $user->assignRole('mahasiswa');
        }

        // Pastikan relasi mahasiswa ada
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
                'ipk' => 3.50,
                'sks_lulus' => 0,
                'sks_total' => 144,
                'status_akademik' => 'aktif',
            ]);
        }

        // Hapus rencana studi sebelumnya untuk menghindari duplikasi / limitasi SKS
        $mahasiswa->studyPlans()->delete();

        // Buat atau temukan data Mata Kuliah A
        $courseA = Course::firstOrCreate(
            ['code' => 'CS101'],
            ['name' => 'Basis Data', 'credits' => 3, 'semester' => 5]
        );

        // Buat atau temukan data Mata Kuliah B
        $courseB = Course::firstOrCreate(
            ['code' => 'CS102'],
            ['name' => 'Jaringan Komputer', 'credits' => 3, 'semester' => 5]
        );

        return [$user, $mahasiswa, $courseA, $courseB];
    }

    /**
     * Case ID: TC-020-01
     * Test Scenario: Edit data berhasil
     */
    public function test_tc_020_01_edit_data_berhasil(): void
    {
        [$user, $mahasiswa, $courseA, $courseB] = $this->prepareTestData();

        // Siapkan data rencana studi awal dengan Mata Kuliah A
        StudyPlan::create([
            'mahasiswa_id' => $mahasiswa->id,
            'course_id' => $courseA->id,
            'semester' => 5,
            'status' => 'approved',
        ]);

        $this->browse(function (Browser $browser) use ($user, $courseB) {
            try {
                $browser->loginAs($user)
                    ->visit('/mahasiswa/study-plans')
                    ->waitForText('Study Plans (KRS)')
                    // Membuka modal edit dengan klik edit-button
                    ->click('@edit-button')
                    ->waitForText('Edit Mata Kuliah')
                    ->pause(1000)
                    ->waitFor('@course-select')
                    // Mengubah pilihan mata kuliah ke Course B
                    ->select('@course-select', $courseB->id)
                    ->waitUntilMissing('button[dusk="submit-button"][disabled]', 10)
                    // Menyimpan perubahan
                    ->click('@submit-button')
                    ->waitForText('Rencana studi berhasil diperbarui.', 15)
                    // Assertion: mematikan data terupdate di tabel
                    ->assertSee('Jaringan Komputer')
                    // Screenshot evidence
                    ->screenshot('TC-020-01');
            } catch (\Throwable $e) {
                $consoleLogs = $browser->driver->manage()->getLog('browser');
                fwrite(STDERR, "\nBROWSER CONSOLE LOGS:\n" . json_encode($consoleLogs, JSON_PRETTY_PRINT) . "\n");
                $html = $browser->driver->getPageSource();
                fwrite(STDERR, "\nPAGE HTML:\n" . $html . "\n");
                throw $e;
            }
        });
    }

    /**
     * Case ID: TC-020-02
     * Test Scenario: Edit data dengan input tidak valid
     */
    public function test_tc_020_02_edit_data_input_tidak_valid(): void
    {
        [$user, $mahasiswa, $courseA, $courseB] = $this->prepareTestData();

        // Buat 2 rencana studi untuk memicu validasi duplikasi mata kuliah
        StudyPlan::create([
            'mahasiswa_id' => $mahasiswa->id,
            'course_id' => $courseA->id,
            'semester' => 5,
            'status' => 'approved',
        ]);

        StudyPlan::create([
            'mahasiswa_id' => $mahasiswa->id,
            'course_id' => $courseB->id,
            'semester' => 5,
            'status' => 'approved',
        ]);

        $this->browse(function (Browser $browser) use ($user, $courseB) {
            $browser->loginAs($user)
                ->visit('/mahasiswa/study-plans')
                ->waitForText('Study Plans (KRS)')
                // Klik tombol edit di baris pertama (Mata Kuliah A)
                ->click('@edit-button')
                ->waitForText('Edit Mata Kuliah')
                ->pause(1000)
                ->waitFor('@course-select')
                // Ubah mata kuliah menjadi Course B yang sudah terdaftar
                ->select('@course-select', $courseB->id)
                ->waitUntilMissing('button[dusk="submit-button"][disabled]', 10)
                // Klik Simpan / Update
                ->click('@submit-button')
                ->waitForText('Mata kuliah sudah ada di KRS Anda.', 15)
                // Screenshot evidence
                ->screenshot('TC-020-02');
        });
    }

    /**
     * Case ID: TC-020-03
     * Test Scenario: Batal edit data
     */
    public function test_tc_020_03_batal_edit_data(): void
    {
        [$user, $mahasiswa, $courseA, $courseB] = $this->prepareTestData();

        StudyPlan::create([
            'mahasiswa_id' => $mahasiswa->id,
            'course_id' => $courseA->id,
            'semester' => 5,
            'status' => 'approved',
        ]);

        $this->browse(function (Browser $browser) use ($user, $courseB) {
            $browser->loginAs($user)
                ->visit('/mahasiswa/study-plans')
                ->waitForText('Study Plans (KRS)')
                ->click('@edit-button')
                ->waitForText('Edit Mata Kuliah')
                ->pause(1000)
                ->waitFor('@course-select')
                // Ubah pilihan tapi batalkan
                ->select('@course-select', $courseB->id)
                ->click('@cancel-button')
                ->waitUntilMissingText('Edit Mata Kuliah')
                // Assertion: memastikan data lama masih tampil dan data baru tidak tersimpan
                ->assertSee('Basis Data')
                ->assertDontSee('Jaringan Komputer')
                // Screenshot evidence
                ->screenshot('TC-020-03');
        });
    }

    /**
     * Case ID: TC-020-04
     * Test Scenario: Edit data yang tidak ditemukan
     */
    public function test_tc_020_04_edit_data_tidak_ditemukan(): void
    {
        [$user] = $this->prepareTestData();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                // Mengakses URL edit dengan ID data yang tidak ada
                ->visit('/mahasiswa/study-plans/9999/edit')
                // Assertion: validasi halaman error / 404
                ->assertSee('404')
                // Screenshot evidence
                ->screenshot('TC-020-04');
        });
    }

    /**
     * Case ID: TC-020-05
     * Test Scenario: Hapus data berhasil
     */
    public function test_tc_020_05_hapus_data_berhasil(): void
    {
        [$user, $mahasiswa, $courseA] = $this->prepareTestData();

        StudyPlan::create([
            'mahasiswa_id' => $mahasiswa->id,
            'course_id' => $courseA->id,
            'semester' => 5,
            'status' => 'approved',
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/mahasiswa/study-plans')
                ->waitForText('Study Plans (KRS)')
                // Klik tombol hapus
                ->click('@delete-button')
                ->waitForText('Apakah Anda yakin ingin menghapus')
                ->pause(1000)
                ->waitFor('@confirm-delete')
                // Klik konfirmasi hapus
                ->click('@confirm-delete')
                ->waitForText('Mata kuliah berhasil dihapus dari KRS.', 15)
                // Assertion: pastikan mata kuliah tidak lagi terlihat di tabel
                ->assertDontSee('Basis Data')
                // Screenshot evidence
                ->screenshot('TC-020-05');
        });
    }

    /**
     * Case ID: TC-020-06
     * Test Scenario: Batal hapus data
     */
    public function test_tc_020_06_batal_hapus_data(): void
    {
        [$user, $mahasiswa, $courseA] = $this->prepareTestData();

        StudyPlan::create([
            'mahasiswa_id' => $mahasiswa->id,
            'course_id' => $courseA->id,
            'semester' => 5,
            'status' => 'approved',
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/mahasiswa/study-plans')
                ->waitForText('Study Plans (KRS)')
                ->click('@delete-button')
                ->waitForText('Apakah Anda yakin ingin menghapus')
                ->pause(1000)
                ->waitFor('@cancel-delete')
                // Membatalkan hapus
                ->click('@cancel-delete')
                ->waitUntilMissingText('Apakah Anda yakin ingin menghapus')
                // Assertion: pastikan data masih ada di tabel
                ->assertSee('Basis Data')
                // Screenshot evidence
                ->screenshot('TC-020-06');
        });
    }

    /**
     * Case ID: TC-020-07
     * Test Scenario: Hapus data yang tidak ditemukan
     */
    public function test_tc_020_07_hapus_data_tidak_ditemukan(): void
    {
        [$user] = $this->prepareTestData();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                // Mengakses URL hapus dengan ID data yang tidak ada
                ->visit('/mahasiswa/study-plans/9999/delete')
                // Assertion: validasi halaman error / 404
                ->assertSee('404')
                // Screenshot evidence
                ->screenshot('TC-020-07');
        });
    }
}
