<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class JadwalBimbinganTest extends DuskTestCase
{
    protected function mahasiswa(): User
    {
        return User::where('email', 'mahasiswa@sibima.test')->firstOrFail();
    }

    /**
     * Helper: set value pada Vue v-model select via JS dispatch,
     * karena Dusk ->select() kadang tidak men-trigger Vue reactivity.
     */
    protected function vueSelect(Browser $browser, string $selector, string $value): void
    {
        $browser->script("
            const el = document.querySelector('{$selector}');
            const nativeInputValueSetter = Object.getOwnPropertyDescriptor(
                window.HTMLSelectElement.prototype, 'value'
            ).set;
            nativeInputValueSetter.call(el, '{$value}');
            el.dispatchEvent(new Event('change', { bubbles: true }));
            el.dispatchEvent(new Event('input', { bubbles: true }));
        ");
    }

    /**
     * TC-01: Mahasiswa berhasil mengajukan jadwal bimbingan baru.
     */
    public function test_ajukan_jadwal_berhasil(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->mahasiswa())
                ->visit('/mahasiswa/jadwal-bimbingan/create')
                ->waitFor('#dosen_id', 10);

            // Pilih dosen via JS untuk memastikan Vue mendeteksi perubahan
            $this->vueSelect($browser, '#dosen_id', '1');

            // Tunggu AJAX selesai: option selain placeholder muncul di DOM
            $browser->waitUsing(15, 200, function () use ($browser) {
                return $browser->script(
                    "return document.querySelectorAll('#schedule_id option[value]:not([value=\"\"])').length > 0"
                )[0];
            }, 'Schedule options tidak muncul setelah 15 detik');

            // Ambil value dari option pertama yang tersedia (tidak disabled)
            $firstScheduleValue = $browser->script("
                const opts = document.querySelectorAll('#schedule_id option[value]:not([value=\"\"]):not([disabled])');
                return opts.length > 0 ? opts[0].value : null;
            ")[0];

            $this->assertNotNull($firstScheduleValue, 'Tidak ada jadwal tersedia yang bisa dipilih');

            // Pilih jadwal via JS
            $this->vueSelect($browser, '#schedule_id', $firstScheduleValue);

            $browser
                ->type('#topik_bimbingan', 'Membahas Latar Belakang')
                ->press('Ajukan Jadwal Bimbingan')
                ->waitForLocation('/mahasiswa/jadwal-bimbingan', 15)
                ->assertPathIs('/mahasiswa/jadwal-bimbingan')
                ->assertSee('Jadwal bimbingan berhasil diajukan.')
                ->assertSee('Membahas Latar Belakang')
                ->assertSee('MENUNGGU KONFIRMASI');
        });
    }

    /**
     * TC-02: Validasi — submit dengan dosen terpilih tapi tanpa pilih jadwal.
     */
    public function test_ajukan_jadwal_tanpa_jadwal(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->mahasiswa())
                ->visit('/mahasiswa/jadwal-bimbingan/create')
                ->waitFor('#dosen_id', 10);

            $this->vueSelect($browser, '#dosen_id', '1');

            $browser->waitUsing(10, 200, function () use ($browser) {
                return $browser->script(
                    "return document.querySelectorAll('#schedule_id option[value]:not([value=\"\"])').length > 0"
                )[0];
            }, 'Schedule options tidak muncul')
                // Tidak pilih jadwal, langsung isi topik dan submit
                ->type('#topik_bimbingan', 'Topik Tanpa Jadwal')
                ->press('Ajukan Jadwal Bimbingan')
                // HTML5 required pada #schedule_id (value masih "") mencegah submit
                ->assertPathIs('/mahasiswa/jadwal-bimbingan/create');
        });
    }

    /**
     * TC-03: Mahasiswa berhasil membatalkan jadwal berstatus "pending".
     */
    public function test_batalkan_jadwal_menunggu_konfirmasi(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->mahasiswa())
                ->visit('/mahasiswa/jadwal-bimbingan')
                ->waitForText('MENUNGGU KONFIRMASI', 10)
                ->press('Batalkan')
                ->acceptDialog()
                ->waitUntilMissing('button.border-red-300', 10)
                ->assertPathIs('/mahasiswa/jadwal-bimbingan')
                ->assertDontSee('MENUNGGU KONFIRMASI')
                ->assertDontSee('Batalkan');
        });
    }

    /**
     * TC-04: Jadwal non-pending menampilkan "Terkunci" bukan tombol aktif.
     */
    public function test_tombol_terkunci_tampil_untuk_status_final(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->mahasiswa())
                ->visit('/mahasiswa/jadwal-bimbingan')
                ->waitForText('SELESAI', 10)
                ->assertSee('Terkunci')
                ->assertMissing('button[disabled]');
        });
    }

    /**
     * TC-05: Filter status — badge aktif muncul dan dropdown terseleksi.
     */
    public function test_filter_status_monitoring(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->mahasiswa())
                ->visit('/mahasiswa/jadwal-bimbingan')
                ->waitForText('Monitoring Jadwal Bimbingan', 10)
                ->select('#filter-status', 'approved')
                ->waitForText('Status: Disetujui', 10)
                ->assertSee('Status: Disetujui')
                ->assertSelected('#filter-status', 'approved')
                ->assertSee('Reset')
                ->press('Reset')
                ->waitUntilMissing('.text-indigo-800', 10)
                ->assertSelected('#filter-status', 'all')
                ->assertDontSee('Status: Disetujui');
        });
    }

    /**
     * TC-06: Fitur pencarian topik bimbingan.
     */
    public function test_cari_topik_bimbingan(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->mahasiswa())
                ->visit('/mahasiswa/jadwal-bimbingan')
                ->waitForText('Monitoring Jadwal Bimbingan', 10)
                ->type('#search-jadwal', 'Latar Belakang')
                ->pause(1500)
                ->assertSee('Latar Belakang')
                ->assertDontSee('Diskusi Metodologi Penelitian');
        });
    }
}
