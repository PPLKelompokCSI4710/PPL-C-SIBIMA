<?php

namespace Tests\Browser;

use App\Models\CatatanKonsultasi;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class CatatanKonsultasiTest extends DuskTestCase
{
    /**
     * Dosen dapat membuat catatan konsultasi baru.
     * Command: php artisan dusk --filter=CatatanKonsultasiTest
     */
    public function test_dosen_buat_catatan_konsultasi()
    {
        $this->browse(function (Browser $browser) {
            $dosenUser = User::where('email', 'dosen@sibima.test')->first();
            $dosenProfile = Dosen::where('user_id', $dosenUser->id)->first();
            $mahasiswaUser = User::where('email', 'mahasiswa@sibima.test')->first();
            $mahasiswaProfile = Mahasiswa::where('user_id', $mahasiswaUser->id)->first();

            DB::table('dosen_mahasiswa')->updateOrInsert(
                ['dosen_id' => $dosenProfile->id, 'mahasiswa_id' => $mahasiswaProfile->id],
                ['tanggal_penugasan' => now()->toDateString(), 'is_active' => true]
            );

            $topik = 'Catatan Dusk '.now()->timestamp;

            $browser->loginAs($dosenUser)
                ->visit('/dosen/catatan-konsultasi')
                ->waitForText('Catatan Hasil Konsultasi', 15)
                ->click('#btn-buat-catatan')
                ->waitFor('#mahasiswa_id', 10)
                ->select('#mahasiswa_id', $mahasiswaProfile->id)
                ->keys('#tanggal', now()->format('mdY'))
                ->type('#topik', $topik)
                ->type('#catatan', 'Revisi BAB 1-3.')
                ->press('Simpan Catatan')
                ->waitForText($topik, 15);

            CatatanKonsultasi::where('topik', $topik)->delete();
        });
    }
}
