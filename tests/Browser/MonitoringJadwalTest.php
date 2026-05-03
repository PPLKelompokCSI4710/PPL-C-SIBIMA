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
     * Test alur pembuatan ketersediaan jadwal oleh dosen.
     */
    public function test_dosen_buat_ketersediaan_jadwal()
    {
        $this->browse(function (Browser $browser) {

            // Ambil data user dosen
            $dosenUser = User::where('email', 'dosen@sibima.test')->first();

            // Tanggal besok untuk test
            $tanggalBesok = now()->addDays(1)->format('mdY'); // format m/d/Y untuk input date HTML5 di beberapa browser
            $tanggalAssert = now()->addDays(1)->format('Y-m-d'); // format standar Y-m-d untuk text

            $browser->loginAs($dosenUser)
                ->visit('/dosen/ketersediaan-jadwal')
                ->assertSee('Kelola Ketersediaan Jadwal Bimbingan')

                    // Mengisi form
                ->keys('input[type="date"]', $tanggalBesok)
                ->keys('input[type="time"]:nth-of-type(1)', '0900AM')
                ->keys('input[type="time"]:nth-of-type(2)', '1100AM')
                ->clear('input[type="number"]')
                ->type('input[type="number"]', '5')

                    // Submit form
                ->press('Tambahkan Jadwal')

                    // Verifikasi flash message sukses
                ->pause(1500)
                ->assertSee('Jadwal ketersediaan berhasil ditambahkan')

                    // Verifikasi data masuk ke dalam tabel
                ->assertSee('5 Orang')
                ->assertSee('09:00 - 11:00');
        });
    }

    /**
     * Test alur persetujuan/penolakan jadwal oleh dosen.
     */
    public function test_dosen_reject_jadwal()
    {
        $this->browse(function (Browser $browserDosen) {

            // Ambil data user
            $dosenUser = User::where('email', 'dosen@sibima.test')->first();
            $mahasiswaUser = User::where('email', 'mahasiswa@sibima.test')->first();

            $dosenProfile = Dosen::where('user_id', $dosenUser->id)->first();
            $mahasiswaProfile = Mahasiswa::where('user_id', $mahasiswaUser->id)->first();

            // Buat satu jadwal bimbingan baru dengan status "pending" agar tes selalu bisa dijalankan
            $jadwal = JadwalBimbingan::create([
                'dosen_id' => $dosenProfile->id,
                'mahasiswa_id' => $mahasiswaProfile->id,
                'tanggal' => now()->addDays(2)->toDateString(),
                'waktu' => '10:00:00',
                'topik_bimbingan' => 'Testing Penolakan Oleh Dosen',
                'tipe' => 'online',
                'status' => 'pending',
            ]);

            // Skenario Dosen: Login dan menolak jadwal
            $browserDosen->loginAs($dosenUser)
                ->visit('/dosen/jadwal-bimbingan')
                ->assertSee('Monitoring Jadwal Bimbingan')
                ->assertSee('Testing Penolakan Oleh Dosen')
                         // Dosen melihat jadwal "Menunggu Konfirmasi"
                ->assertSee('Menunggu Konfirmasi')
                         // Dosen menekan tombol "Tolak"
                ->press('Tolak')
                         // Menerima dialog konfirmasi browser
                ->acceptDialog()
                         // Tunggu proses update selesai
                ->pause(1500)
                         // Memastikan jadwal sekarang sudah "Ditolak"
                ->assertSee('Ditolak');

            // Bersihkan data tes setelah selesai
            $jadwal->delete();
        });
    }
}
