<?php

namespace Tests\Browser;

use App\Models\Dosen;
use App\Models\JadwalBimbingan;
use App\Models\Mahasiswa;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class MonitoringJadwalTest extends DuskTestCase
{
    /**
     * Dosen dapat melihat dan menyetujui jadwal bimbingan.
     * Command: php artisan dusk --filter=MonitoringJadwalTest
     */
    public function test_dosen_kelola_monitoring_jadwal()
    {
        $dosenUser = User::where('email', 'dosen@sibima.test')->first();
        $mahasiswaUser = User::where('email', 'mahasiswa@sibima.test')->first();
        $dosenProfile = Dosen::where('user_id', $dosenUser->id)->first();
        $mahasiswaProfile = Mahasiswa::where('user_id', $mahasiswaUser->id)->first();

        $topik = 'Test Dusk Approve '.now()->timestamp;
        $jadwal = JadwalBimbingan::create([
            'dosen_id' => $dosenProfile->id,
            'mahasiswa_id' => $mahasiswaProfile->id,
            'tanggal' => now()->addDays(5)->toDateString(),
            'waktu' => '09:00:00',
            'topik_bimbingan' => $topik,
            'tipe' => 'online',
            'status' => 'pending',
        ]);

        $this->browse(function (Browser $browser) use ($dosenUser, $topik) {
            $browser->loginAs($dosenUser)
                ->visit('/dosen/jadwal-bimbingan')
                ->waitForText($topik, 15);

            $safeTopik = addslashes($topik);
            $browser->script("
                window.confirm = () => true;
                const rows = Array.from(document.querySelectorAll('tbody tr'));
                const targetRow = rows.find(row => row.textContent.includes('{$safeTopik}'));
                if (targetRow) {
                    const btn = targetRow.querySelector('button');
                    if (btn) btn.click();
                }
            ");

            $browser->pause(8000);
        });

        $jadwal->refresh();
        $this->assertEquals('approved', $jadwal->status, 'Status jadwal harus berubah menjadi approved.');

        $jadwal->delete();
    }
}
