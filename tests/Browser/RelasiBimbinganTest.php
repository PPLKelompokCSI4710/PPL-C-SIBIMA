<?php

namespace Tests\Browser;

use App\Enums\AkademikStatus;
use App\Enums\UserRole;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Laravel\Dusk\Browser;
use Spatie\Permission\Models\Role;
use Tests\DuskTestCase;

class RelasiBimbinganTest extends DuskTestCase
{
    use DatabaseTruncation;
    use InteractsWithDatabase;

    // =========================================================================
    // HELPER: Setup Roles, Admin, Mahasiswa, Dosen
    // =========================================================================

    /**
     * Memastikan role admin, dosen, dan mahasiswa tersedia di database.
     */
    private function ensureRoles(): void
    {
        Role::findOrCreate(UserRole::ADMIN->value);
        Role::findOrCreate(UserRole::DOSEN->value);
        Role::findOrCreate(UserRole::MAHASISWA->value);
    }

    /**
     * Membuat user admin untuk login ke panel Filament.
     */
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

    /**
     * Membuat record mahasiswa beserta user-nya.
     */
    private function createMahasiswa(array $mahasiswaOverrides = [], array $userOverrides = []): Mahasiswa
    {
        $this->ensureRoles();

        $user = User::factory()->create(array_merge([
            'name' => $mahasiswaOverrides['nama_lengkap'] ?? 'Mahasiswa Dusk',
            'email' => 'mhs_' . uniqid() . '@example.test',
        ], $userOverrides));

        $user->assignRole(UserRole::MAHASISWA->value);

        $defaults = [
            'user_id' => $user->id,
            'nim' => '202400' . random_int(1000, 9999),
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

    /**
     * Membuat record dosen beserta user-nya.
     */
    private function createDosen(array $dosenOverrides = [], array $userOverrides = []): Dosen
    {
        $this->ensureRoles();

        $user = User::factory()->create(array_merge([
            'name' => $dosenOverrides['nama_lengkap'] ?? 'Dosen Dusk',
            'email' => 'dosen_' . uniqid() . '@example.test',
        ], $userOverrides));

        $user->assignRole(UserRole::DOSEN->value);

        $defaults = [
            'user_id' => $user->id,
            'nidn' => 'NIDN' . random_int(10000, 99999),
            'nama_lengkap' => 'Dosen Dusk',
            'program_studi' => 'Teknik Informatika',
            'fakultas' => 'Fakultas Ilmu Komputer',
            'is_active' => true,
        ];

        return Dosen::create(array_merge($defaults, $dosenOverrides));
    }

    /**
     * Helper JavaScript: klik dropdown Filament Select berdasarkan label field.
     */
    private function jsClickSelectDropdown(string $label): string
    {
        return '(() => {
            const main = document.querySelector("main") || document;
            const label = Array.from(main.querySelectorAll("label, span, div"))
                .find((el) => el.textContent?.trim() === "' . $label . '");
            const wrapper = label?.closest("[role=\'combobox\'], .fi-fo-field-wrp, .fi-fo-select, .fi-input-wrp")
                || label?.parentElement;
            const target = wrapper?.querySelector("button, [role=\'combobox\'], .fi-select-input-btn, .fi-input-wrp, .fi-fo-select")
                || wrapper;
            if (target) { target.click(); }
        })();';
    }

    /**
     * Helper JavaScript: pilih opsi dropdown Filament Select berdasarkan teks opsi.
     */
    private function jsSelectOption(string $optionText): string
    {
        return '(() => {
            const option = Array.from(document.querySelectorAll("[role=\'option\'], .fi-select-option, li, button"))
                .find((el) => el.textContent?.includes("' . $optionText . '"));
            if (option) { option.click(); }
        })();';
    }

    /**
     * Helper JavaScript: klik tombol di dalam modal dialog Filament.
     */
    private function jsClickModalButton(string $buttonText): string
    {
        return '(() => {
            const dialog = document.querySelector("[role=\'dialog\'], .fi-modal");
            const buttons = Array.from(dialog?.querySelectorAll("button") ?? []);
            const target = buttons.find((btn) => btn.textContent?.trim().includes("' . $buttonText . '"));
            if (target) { target.click(); }
        })();';
    }

    // =========================================================================
    // PBI #44 — Penetapan Dosen Pembimbing
    // =========================================================================

    /**
     * TC-BR-001: Penetapan dosen pembimbing berhasil.
     * Admin menetapkan dosen aktif sebagai pembimbing mahasiswa.
     */
    public function test_tc_br_001_penetapan_dosen_pembimbing_berhasil(): void
    {
        // Siapkan data awal
        $admin = $this->createAdmin();
        $dosen = $this->createDosen([
            'nidn' => 'NIDN001',
            'nama_lengkap' => 'Dr. Budi Santoso',
            'is_active' => true,
        ]);
        $mahasiswa = $this->createMahasiswa([
            'nim' => '2024010001',
            'nama_lengkap' => 'Andi Pratama',
            'dosen_id' => null, // belum ada pembimbing
        ]);

        $this->browse(function (Browser $browser) use ($admin, $mahasiswa, $dosen) {
            // Login sebagai admin dan buka halaman penetapan pembimbing
            $browser->loginAs($admin)
                ->visit('/admin/bimbingan-relation/bimbingan-relations')
                ->waitForText('Andi Pratama');

            // Klik tombol "Tetapkan" pada baris mahasiswa
            $browser->visit("/admin/bimbingan-relation/bimbingan-relations/{$mahasiswa->id}/edit")
                ->waitForText('Penetapan Dosen Pembimbing');

            // Klik dropdown "Dosen Pembimbing"
            $browser->script($this->jsClickSelectDropdown('Dosen Pembimbing'));

            // Tunggu opsi muncul lalu pilih dosen
            $browser->pause(500);
            $browser->script($this->jsSelectOption('Dr. Budi Santoso'));

            // Klik tombol simpan
            $browser->pause(1000)
                ->press('Save changes')
                ->waitForText('Saved');
        });

        // Verifikasi dosen_id tersimpan di database
        $this->assertDatabaseHas('mahasiswa', [
            'id' => $mahasiswa->id,
            'dosen_id' => $dosen->id,
        ]);
    }

    /**
     * TC-BR-002: Dropdown dosen hanya menampilkan dosen aktif.
     * Dosen non-aktif tidak muncul di pilihan dropdown.
     */
    public function test_tc_br_002_dropdown_hanya_menampilkan_dosen_aktif(): void
    {
        $admin = $this->createAdmin();

        // Buat dosen aktif dan non-aktif
        $dosenAktif = $this->createDosen([
            'nidn' => 'NIDN002',
            'nama_lengkap' => 'Dr. Siti Aminah',
            'is_active' => true,
        ]);
        $dosenNonaktif = $this->createDosen([
            'nidn' => 'NIDN003',
            'nama_lengkap' => 'Prof. Tanaka',
            'is_active' => false,
        ]);
        $mahasiswa = $this->createMahasiswa([
            'nim' => '2024010002',
            'nama_lengkap' => 'Budi Setiawan',
            'dosen_id' => null,
        ]);

        $this->browse(function (Browser $browser) use ($admin, $mahasiswa, $dosenAktif, $dosenNonaktif) {
            // Login dan buka halaman edit
            $browser->loginAs($admin)
                ->visit("/admin/bimbingan-relation/bimbingan-relations/{$mahasiswa->id}/edit")
                ->waitForText('Penetapan Dosen Pembimbing')
                ->pause(1000);

            // Klik dropdown "Dosen Pembimbing" untuk membuka pilihan
            $browser->click('.fi-select-input-btn')
                ->pause(1000);

            // Verifikasi dosen aktif muncul, dosen non-aktif tidak muncul
            $browser->waitForText('Dr. Siti Aminah', 10)
                ->assertSee('Dr. Siti Aminah')
                ->assertDontSee('Prof. Tanaka');
        });
    }

    /**
     * TC-BR-003: Submit form tanpa memilih dosen (nullable).
     * Dosen_id tetap null, badge "Belum Ditetapkan" tetap tampil.
     */
    public function test_tc_br_003_submit_tanpa_memilih_dosen_nullable(): void
    {
        $admin = $this->createAdmin();
        $mahasiswa = $this->createMahasiswa([
            'nim' => '2024010003',
            'nama_lengkap' => 'Citra Dewi',
            'dosen_id' => null,
        ]);

        $this->browse(function (Browser $browser) use ($admin, $mahasiswa) {
            // Login dan buka halaman edit tanpa memilih dosen
            $browser->loginAs($admin)
                ->visit("/admin/bimbingan-relation/bimbingan-relations/{$mahasiswa->id}/edit")
                ->waitForText('Penetapan Dosen Pembimbing');

            // Langsung klik simpan tanpa memilih dosen
            $browser->press('Save changes')
                ->waitForText('Saved');
        });

        // Verifikasi dosen_id tetap null di database
        $this->assertDatabaseHas('mahasiswa', [
            'id' => $mahasiswa->id,
            'dosen_id' => null,
        ]);

        // Verifikasi badge "Belum Ditetapkan" masih tampil di tabel
        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visit('/admin/bimbingan-relation/bimbingan-relations')
                ->waitForText('Belum Ditetapkan')
                ->assertSee('Belum Ditetapkan');
        });
    }

    // =========================================================================
    // PBI #45 — Pengelolaan Relasi (Ubah & Hapus)
    // =========================================================================

    /**
     * TC-BR-004: Ubah dosen pembimbing berhasil.
     * Admin mengubah dosen pembimbing dari dosen A ke dosen B.
     */
    public function test_tc_br_004_ubah_dosen_pembimbing_berhasil(): void
    {
        $admin = $this->createAdmin();
        $dosenA = $this->createDosen([
            'nidn' => 'NIDN004',
            'nama_lengkap' => 'Dr. Ahmad Fauzi',
            'is_active' => true,
        ]);
        $dosenB = $this->createDosen([
            'nidn' => 'NIDN005',
            'nama_lengkap' => 'Dr. Rina Wati',
            'is_active' => true,
        ]);
        $mahasiswa = $this->createMahasiswa([
            'nim' => '2024010004',
            'nama_lengkap' => 'Dian Permata',
            'dosen_id' => $dosenA->id, // sudah ada dosen A
        ]);

        $this->browse(function (Browser $browser) use ($admin, $mahasiswa, $dosenB) {
            // Login dan buka halaman edit
            $browser->loginAs($admin)
                ->visit("/admin/bimbingan-relation/bimbingan-relations/{$mahasiswa->id}/edit")
                ->waitForText('Penetapan Dosen Pembimbing');

            // Klik dropdown dosen dan pilih dosen B
            $browser->script($this->jsClickSelectDropdown('Dosen Pembimbing'));
            $browser->pause(500);
            $browser->script($this->jsSelectOption('Dr. Rina Wati'));

            // Simpan perubahan
            $browser->pause(1000)
                ->press('Save changes')
                ->waitForText('Saved');
        });

        // Verifikasi dosen_id berubah ke dosen B
        $this->assertDatabaseHas('mahasiswa', [
            'id' => $mahasiswa->id,
            'dosen_id' => $dosenB->id,
        ]);
    }

    /**
     * TC-BR-005: Field identitas mahasiswa terkunci pada mode edit.
     * NIM, Nama Lengkap, Program Studi, dan Angkatan berstatus disabled.
     */
    public function test_tc_br_005_field_identitas_mahasiswa_disabled_saat_edit(): void
    {
        $admin = $this->createAdmin();
        $dosen = $this->createDosen([
            'nidn' => 'NIDN006',
            'nama_lengkap' => 'Dr. Eko Prasetyo',
            'is_active' => true,
        ]);
        $mahasiswa = $this->createMahasiswa([
            'nim' => '2024010005',
            'nama_lengkap' => 'Eka Putri',
            'dosen_id' => $dosen->id,
        ]);

        $this->browse(function (Browser $browser) use ($admin, $mahasiswa) {
            // Login dan buka halaman edit
            $browser->loginAs($admin)
                ->visit("/admin/bimbingan-relation/bimbingan-relations/{$mahasiswa->id}/edit")
                ->waitForText('Identitas Mahasiswa');

            // Verifikasi semua field identitas mahasiswa disabled melalui JavaScript
            $results = $browser->script('
                return (() => {
                    const sections = document.querySelectorAll("section, [class*=\'fi-section\'], fieldset");
                    let identitasSection = null;
                    for (const s of sections) {
                        if (s.textContent?.includes("Identitas Mahasiswa")) {
                            identitasSection = s;
                            break;
                        }
                    }
                    if (!identitasSection) return [];
                    const inputs = identitasSection.querySelectorAll("input");
                    const states = [];
                    inputs.forEach((input) => {
                        states.push({
                            value: input.value,
                            disabled: input.disabled
                        });
                    });
                    return states;
                })();
            ');

            // Setidaknya 4 field identitas harus disabled (NIM, Nama, Prodi, Angkatan)
            $disabledInputs = collect($results[0] ?? [])
                ->filter(fn ($input) => $input['disabled'] === true);

            $this->assertGreaterThanOrEqual(4, $disabledInputs->count(),
                'Minimal 4 field identitas mahasiswa harus dalam kondisi disabled saat edit.'
            );
        });
    }

    /**
     * TC-BR-006: Hapus relasi — konfirmasi disetujui.
     * Admin memutus relasi dosen-mahasiswa, dosen_id menjadi null.
     */
    public function test_tc_br_006_hapus_relasi_konfirmasi_disetujui(): void
    {
        $admin = $this->createAdmin();
        $dosen = $this->createDosen([
            'nidn' => 'NIDN007',
            'nama_lengkap' => 'Dr. Fajar Hidayat',
            'is_active' => true,
        ]);
        $mahasiswa = $this->createMahasiswa([
            'nim' => '2024010006',
            'nama_lengkap' => 'Faisal Rahman',
            'dosen_id' => $dosen->id,
        ]);

        $this->browse(function (Browser $browser) use ($admin, $mahasiswa) {
            // Login dan buka halaman index
            $browser->loginAs($admin)
                ->visit('/admin/bimbingan-relation/bimbingan-relations')
                ->waitForText('Faisal Rahman');

            // Klik tombol "Hapus Relasi" pada baris mahasiswa
            $browser->script('(() => {
                const rows = document.querySelectorAll("table tbody tr");
                for (const row of rows) {
                    if (row.textContent.includes("Faisal Rahman")) {
                        const btn = Array.from(row.querySelectorAll("button"))
                            .find(b => b.textContent?.includes("Hapus Relasi"));
                        if (btn) { btn.click(); break; }
                    }
                }
            })();');

            // Tunggu modal konfirmasi muncul
            $browser->waitForText('Hapus Relasi Dosen Pembimbing');

            // Verifikasi deskripsi modal
            $browser->assertSee('mengosongkan dosen pembimbing');

            // Klik tombol konfirmasi "Ya, Hapus Relasi"
            $browser->script($this->jsClickModalButton('Ya, Hapus Relasi'));

            // Tunggu tabel diperbarui dan badge berubah
            $browser->pause(1000)
                ->waitForText('Belum Ditetapkan');
        });

        // Verifikasi dosen_id menjadi null di database
        $this->assertDatabaseHas('mahasiswa', [
            'id' => $mahasiswa->id,
            'dosen_id' => null,
        ]);
    }

    /**
     * TC-BR-007: Hapus relasi — konfirmasi dibatalkan.
     * Data dosen_id tidak berubah setelah admin membatalkan modal.
     */
    public function test_tc_br_007_hapus_relasi_konfirmasi_dibatalkan(): void
    {
        $admin = $this->createAdmin();
        $dosen = $this->createDosen([
            'nidn' => 'NIDN008',
            'nama_lengkap' => 'Dr. Gunawan',
            'is_active' => true,
        ]);
        $mahasiswa = $this->createMahasiswa([
            'nim' => '2024010007',
            'nama_lengkap' => 'Galih Prakoso',
            'dosen_id' => $dosen->id,
        ]);

        $this->browse(function (Browser $browser) use ($admin, $mahasiswa, $dosen) {
            // Login dan buka halaman index
            $browser->loginAs($admin)
                ->visit('/admin/bimbingan-relation/bimbingan-relations')
                ->waitForText('Galih Prakoso');

            // Klik tombol "Hapus Relasi"
            $browser->script('(() => {
                const rows = document.querySelectorAll("table tbody tr");
                for (const row of rows) {
                    if (row.textContent.includes("Galih Prakoso")) {
                        const btn = Array.from(row.querySelectorAll("button"))
                            .find(b => b.textContent?.includes("Hapus Relasi"));
                        if (btn) { btn.click(); break; }
                    }
                }
            })();');

            // Tunggu modal konfirmasi muncul
            $browser->waitForText('Hapus Relasi Dosen Pembimbing');

            // Klik tombol "Cancel" untuk membatalkan aksi
            $browser->script($this->jsClickModalButton('Cancel'));

            // Tunggu modal tertutup
            $browser->pause(500);

            // Verifikasi nama dosen masih tampil di tabel (relasi tidak berubah)
            $browser->assertSee('Dr. Gunawan');
        });

        // Verifikasi dosen_id TIDAK berubah di database
        $this->assertDatabaseHas('mahasiswa', [
            'id' => $mahasiswa->id,
            'dosen_id' => $dosen->id,
        ]);
    }

    /**
     * TC-BR-008: Isolasi data — hapus relasi tidak menghapus record.
     * Data mahasiswa, user, dan dosen tetap utuh setelah relasi diputus.
     */
    public function test_tc_br_008_isolasi_data_hapus_relasi_tidak_menghapus_record(): void
    {
        $admin = $this->createAdmin();
        $dosen = $this->createDosen([
            'nidn' => 'NIDN009',
            'nama_lengkap' => 'Dr. Hendra Wijaya',
            'is_active' => true,
        ]);
        $mahasiswa = $this->createMahasiswa([
            'nim' => '2024010008',
            'nama_lengkap' => 'Hana Safitri',
            'dosen_id' => $dosen->id,
        ]);
        $mahasiswaUserId = $mahasiswa->user_id;

        $this->browse(function (Browser $browser) use ($admin, $mahasiswa) {
            // Login dan buka halaman index
            $browser->loginAs($admin)
                ->visit('/admin/bimbingan-relation/bimbingan-relations')
                ->waitForText('Hana Safitri');

            // Klik tombol "Hapus Relasi"
            $browser->script('(() => {
                const rows = document.querySelectorAll("table tbody tr");
                for (const row of rows) {
                    if (row.textContent.includes("Hana Safitri")) {
                        const btn = Array.from(row.querySelectorAll("button"))
                            .find(b => b.textContent?.includes("Hapus Relasi"));
                        if (btn) { btn.click(); break; }
                    }
                }
            })();');

            // Tunggu modal dan konfirmasi
            $browser->waitForText('Hapus Relasi Dosen Pembimbing');
            $browser->script($this->jsClickModalButton('Ya, Hapus Relasi'));

            // Tunggu proses selesai
            $browser->pause(1000);
        });

        // Verifikasi dosen_id menjadi null
        $this->assertDatabaseHas('mahasiswa', [
            'id' => $mahasiswa->id,
            'dosen_id' => null,
        ]);

        // Verifikasi record mahasiswa TETAP ADA (tidak terhapus)
        $this->assertDatabaseHas('mahasiswa', [
            'id' => $mahasiswa->id,
            'nim' => '2024010008',
            'nama_lengkap' => 'Hana Safitri',
        ]);

        // Verifikasi record user mahasiswa TETAP ADA
        $this->assertDatabaseHas('users', [
            'id' => $mahasiswaUserId,
        ]);

        // Verifikasi record dosen TETAP ADA
        $this->assertDatabaseHas('dosen', [
            'id' => $dosen->id,
            'nama_lengkap' => 'Dr. Hendra Wijaya',
        ]);
    }

    /**
     * TC-BR-009: Tombol "Hapus Relasi" tersembunyi jika mahasiswa belum punya dosen.
     * Action hanya visible untuk mahasiswa dengan dosen_id != null.
     */
    public function test_tc_br_009_tombol_hapus_relasi_tersembunyi_tanpa_dosen(): void
    {
        $admin = $this->createAdmin();
        $mahasiswa = $this->createMahasiswa([
            'nim' => '2024010009',
            'nama_lengkap' => 'Irfan Maulana',
            'dosen_id' => null, // belum ada dosen
        ]);

        $this->browse(function (Browser $browser) use ($admin, $mahasiswa) {
            // Login dan buka halaman index
            $browser->loginAs($admin)
                ->visit('/admin/bimbingan-relation/bimbingan-relations')
                ->waitForText('Irfan Maulana');

            // Cek bahwa tombol "Hapus Relasi" TIDAK ada pada baris mahasiswa tanpa dosen
            $hasHapusRelasi = $browser->script('
                return (() => {
                    const rows = document.querySelectorAll("table tbody tr");
                    for (const row of rows) {
                        if (row.textContent.includes("Irfan Maulana")) {
                            const btn = Array.from(row.querySelectorAll("button"))
                                .find(b => b.textContent?.includes("Hapus Relasi"));
                            return btn !== undefined;
                        }
                    }
                    return false;
                })();
            ');

            // Tombol "Hapus Relasi" harus tidak ditemukan (false)
            $this->assertFalse(
                $hasHapusRelasi[0] ?? true,
                'Tombol "Hapus Relasi" seharusnya tidak muncul untuk mahasiswa tanpa dosen.'
            );

            // Tombol "Tetapkan" (edit) harus tetap ada
            $hasTetapkan = $browser->script('
                return (() => {
                    const rows = document.querySelectorAll("table tbody tr");
                    for (const row of rows) {
                        if (row.textContent.includes("Irfan Maulana")) {
                            const link = Array.from(row.querySelectorAll("a"))
                                .find(a => a.textContent?.includes("Tetapkan"));
                            return link !== undefined;
                        }
                    }
                    return false;
                })();
            ');

            $this->assertTrue(
                $hasTetapkan[0] ?? false,
                'Tombol "Tetapkan" harus tetap tersedia untuk mahasiswa tanpa dosen.'
            );
        });
    }

    // =========================================================================
    // PBI #46 — Tampilan & Pencarian Relasi
    // =========================================================================

    /**
     * TC-BR-010: Tabel menampilkan data identitas mahasiswa dan dosen pembimbing.
     * Verifikasi kolom NIM, Nama, Prodi, Angkatan, Dosen Pembimbing ditampilkan.
     */
    public function test_tc_br_010_tabel_menampilkan_data_lengkap(): void
    {
        $admin = $this->createAdmin();
        $dosen = $this->createDosen([
            'nidn' => 'NIDN010',
            'nama_lengkap' => 'Dr. Joko Widodo',
            'is_active' => true,
        ]);
        $mhsDenganDosen = $this->createMahasiswa([
            'nim' => '2024010010',
            'nama_lengkap' => 'Jihan Aulia',
            'program_studi' => 'Sistem Informasi',
            'angkatan' => '2023',
            'dosen_id' => $dosen->id,
        ]);
        $mhsTanpaDosen = $this->createMahasiswa([
            'nim' => '2024010011',
            'nama_lengkap' => 'Kevin Saputra',
            'program_studi' => 'Teknik Informatika',
            'angkatan' => '2024',
            'dosen_id' => null,
        ]);

        $this->browse(function (Browser $browser) use ($admin) {
            // Login dan buka halaman index
            $browser->loginAs($admin)
                ->visit('/admin/bimbingan-relation/bimbingan-relations')
                ->waitForText('Jihan Aulia');

            // Verifikasi data mahasiswa dengan dosen tampil
            $browser->assertSee('2024010010')       // NIM
                ->assertSee('Jihan Aulia')           // Nama Mahasiswa
                ->assertSee('Dr. Joko Widodo');      // Dosen Pembimbing

            // Verifikasi data mahasiswa tanpa dosen tampil
            $browser->assertSee('2024010011')        // NIM
                ->assertSee('Kevin Saputra')         // Nama Mahasiswa
                ->assertSee('Belum Ditetapkan');     // Badge merah
        });
    }

    /**
     * TC-BR-011: Badge merah "Belum Ditetapkan" muncul untuk mahasiswa tanpa dosen.
     * Verifikasi visual badge dengan warna danger (merah).
     */
    public function test_tc_br_011_badge_belum_ditetapkan_untuk_mahasiswa_tanpa_dosen(): void
    {
        $admin = $this->createAdmin();
        $mahasiswa = $this->createMahasiswa([
            'nim' => '2024010012',
            'nama_lengkap' => 'Lestari Ningrum',
            'dosen_id' => null,
        ]);

        $this->browse(function (Browser $browser) use ($admin) {
            // Login dan buka halaman index
            $browser->loginAs($admin)
                ->visit('/admin/bimbingan-relation/bimbingan-relations')
                ->waitForText('Lestari Ningrum');

            // Verifikasi teks badge "Belum Ditetapkan" tampil
            $browser->assertSee('Belum Ditetapkan');

            // Verifikasi badge memiliki class fi-badge
            $hasBadge = $browser->script('
                return (() => {
                    const allElements = document.querySelectorAll("*");
                    for (const el of allElements) {
                        if (el.textContent?.trim() === "Belum Ditetapkan") {
                            let current = el;
                            while (current && current !== document.body) {
                                const classes = current.className || "";
                                const classStr = typeof classes === "string" ? classes : (classes.animVal || "");
                                if (classStr.includes("fi-badge")) {
                                    return true;
                                }
                                current = current.parentElement;
                            }
                        }
                    }
                    return false;
                })();
            ');

            $this->assertTrue(
                $hasBadge[0] ?? false,
                'Badge "Belum Ditetapkan" harus ter-render sebagai komponen badge.'
            );
        });
    }

    /**
     * TC-BR-012: Pencarian global berdasarkan NIM.
     * Search field memfilter tabel berdasarkan NIM mahasiswa.
     */
    public function test_tc_br_012_pencarian_global_berdasarkan_nim(): void
    {
        $admin = $this->createAdmin();
        $target = $this->createMahasiswa([
            'nim' => '2024010013',
            'nama_lengkap' => 'Mira Susanti',
        ]);
        $other = $this->createMahasiswa([
            'nim' => '2024010014',
            'nama_lengkap' => 'Nanda Kurnia',
        ]);

        $this->browse(function (Browser $browser) use ($admin, $target, $other) {
            // Login dan buka halaman index
            $browser->loginAs($admin)
                ->visit('/admin/bimbingan-relation/bimbingan-relations')
                ->waitFor('.fi-ta-search-field input');

            // Ketik NIM target di kolom pencarian
            $browser->type('.fi-ta-search-field input', $target->nim)
                ->keys('.fi-ta-search-field input', '{enter}');

            // Tunggu tabel difilter — hanya target yang tampil
            $browser->waitUsing(5, 200, function () use ($browser, $target, $other) {
                $tableText = $browser->text('table');

                return str_contains($tableText, $target->nim)
                    && !str_contains($tableText, $other->nim);
            });

            // Verifikasi akhir
            $browser->assertSee($target->nim)
                ->assertDontSee($other->nim);
        });
    }

    /**
     * TC-BR-013: Pencarian global berdasarkan nama dosen pembimbing.
     * Search field memfilter tabel berdasarkan nama dosen.
     */
    public function test_tc_br_013_pencarian_global_berdasarkan_nama_dosen(): void
    {
        $admin = $this->createAdmin();
        $dosenA = $this->createDosen([
            'nidn' => 'NIDN011',
            'nama_lengkap' => 'Dr. Oscar Pratama',
            'is_active' => true,
        ]);
        $dosenB = $this->createDosen([
            'nidn' => 'NIDN012',
            'nama_lengkap' => 'Dr. Putri Handayani',
            'is_active' => true,
        ]);
        $mhsA = $this->createMahasiswa([
            'nim' => '2024010015',
            'nama_lengkap' => 'Pandu Wicaksono',
            'dosen_id' => $dosenA->id,
        ]);
        $mhsB = $this->createMahasiswa([
            'nim' => '2024010016',
            'nama_lengkap' => 'Qori Amelia',
            'dosen_id' => $dosenB->id,
        ]);

        $this->browse(function (Browser $browser) use ($admin, $mhsA, $mhsB, $dosenA) {
            // Login dan buka halaman index
            $browser->loginAs($admin)
                ->visit('/admin/bimbingan-relation/bimbingan-relations')
                ->waitFor('.fi-ta-search-field input');

            // Ketik nama dosen A di kolom pencarian
            $browser->type('.fi-ta-search-field input', 'Oscar Pratama')
                ->keys('.fi-ta-search-field input', '{enter}');

            // Tunggu tabel difilter — hanya mahasiswa bimbingan dosen A yang tampil
            $browser->waitUsing(5, 200, function () use ($browser, $mhsA, $mhsB) {
                $tableText = $browser->text('table');

                return str_contains($tableText, $mhsA->nim)
                    && !str_contains($tableText, $mhsB->nim);
            });

            // Verifikasi akhir
            $browser->assertSee($mhsA->nama_lengkap)
                ->assertDontSee($mhsB->nama_lengkap);
        });
    }

    /**
     * TC-BR-014: Filter "Belum Ada Pembimbing".
     * Toggle filter menampilkan hanya mahasiswa tanpa dosen pembimbing.
     */
    public function test_tc_br_014_filter_belum_ada_pembimbing(): void
    {
        $admin = $this->createAdmin();
        $dosen = $this->createDosen([
            'nidn' => 'NIDN013',
            'nama_lengkap' => 'Dr. Rahmat Hidayat',
            'is_active' => true,
        ]);
        $mhsDenganDosen = $this->createMahasiswa([
            'nim' => '2024010017',
            'nama_lengkap' => 'Rizki Aditya',
            'dosen_id' => $dosen->id,
        ]);
        $mhsTanpaDosen = $this->createMahasiswa([
            'nim' => '2024010018',
            'nama_lengkap' => 'Sari Indah',
            'dosen_id' => null,
        ]);

        $this->browse(function (Browser $browser) use ($admin, $mhsDenganDosen, $mhsTanpaDosen) {
            // Login dan buka halaman index
            $browser->loginAs($admin)
                ->visit('/admin/bimbingan-relation/bimbingan-relations')
                ->waitForText('Rizki Aditya')
                ->waitForText('Sari Indah')
                ->pause(1000);

            // Klik ikon filter di toolbar tabel
            $browser->script('(() => {
                const btn = document.querySelector("button[title=\'Filter\'], button[aria-label=\'Filter\']");
                if (btn) {
                    btn.dispatchEvent(new MouseEvent("mousedown", { bubbles: true }));
                    btn.click();
                    btn.dispatchEvent(new MouseEvent("mouseup", { bubbles: true }));
                } else {
                    const toolbar = document.querySelector(".fi-ta-header-toolbar, .fi-ta-header-ctn");
                    if (toolbar) {
                        const btns = toolbar.querySelectorAll("button");
                        for (const b of btns) {
                            if (b.querySelector("svg") && !b.querySelector("input")) {
                                b.dispatchEvent(new MouseEvent("mousedown", { bubbles: true }));
                                b.click();
                                b.dispatchEvent(new MouseEvent("mouseup", { bubbles: true }));
                                break;
                            }
                        }
                    }
                }
            })();');

            // Tunggu panel filter muncul
            $browser->pause(1000)
                ->waitForText('Belum Ada Pembimbing', 5);

            // Aktifkan toggle "Belum Ada Pembimbing"
            $browser->script('(() => {
                const toggle = document.getElementById("tableFiltersForm.belum_ada_pembimbing.isActive");
                if (toggle) {
                    toggle.click();
                } else {
                    const allText = document.querySelectorAll("label, span, div, p");
                    for (const el of allText) {
                        if (el.textContent?.trim() === "Belum Ada Pembimbing") {
                            let container = el.parentElement;
                            for (let i = 0; i < 5 && container; i++) {
                                const sw = container.querySelector("button[role=\'switch\']");
                                if (sw) { sw.click(); return; }
                                container = container.parentElement;
                            }
                        }
                    }
                }
            })();');

            // Tunggu tombol Apply filters siap lalu klik untuk menerapkan filter
            $browser->pause(500)
                ->press('Apply filters');

            // Tunggu tabel difilter — hanya mahasiswa tanpa dosen yang tampil
            $browser->waitUsing(10, 250, function () use ($browser, $mhsDenganDosen) {
                $tableText = $browser->text('table');

                return !str_contains($tableText, $mhsDenganDosen->nim);
            });

            // Verifikasi: mahasiswa tanpa dosen tampil, mahasiswa dengan dosen tidak tampil
            $browser->assertSee($mhsTanpaDosen->nim)
                ->assertSee('Sari Indah')
                ->assertDontSee($mhsDenganDosen->nim);
        });
    }
}
