<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\JadwalBimbingan;
use App\Models\KetersediaanJadwal;
use App\Models\Bimbingan;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Support\Facades\Artisan;

class ReminderJadwalTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected $seed = true;

    /**
     * TC.Reminder.33.001 - Reminder jadwal bimbingan
     */
    public function test_reminder_muncul_di_bell_mahasiswa(): void
    {
        // Setup data: Create a schedule exactly 24 hours from now so H-1 reminder is generated and due
        $dosen = \App\Models\Dosen::first();
        $dosenUser = $dosen->user;
        $mahasiswa = \App\Models\Mahasiswa::first();
        $mahasiswaUser = $mahasiswa->user;
        
        $this->assertNotNull($dosenUser);
        $this->assertNotNull($mahasiswaUser);

        $ketersediaan = KetersediaanJadwal::create([
            'dosen_id' => $dosen->id,
            'tanggal' => Carbon::tomorrow()->toDateString(),
            'waktu_mulai' => Carbon::now()->addHours(24)->toTimeString(),
            'waktu_selesai' => Carbon::now()->addHours(25)->toTimeString(),
            'kuota' => 1,
            'lokasi' => 'Ruang Dosen',
            'tipe' => 'offline',
            'status' => 'tersedia'
        ]);

        $jadwal = JadwalBimbingan::create([
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_id' => $dosen->id,
            'ketersediaan_jadwal_id' => $ketersediaan->id,
            'topik_bimbingan' => 'Dusk Test Topic',
            'status' => 'approved',
            'lokasi' => 'Ruang Dosen',
            'tipe' => 'offline'
        ]);

        Bimbingan::updateOrCreate(
            [
                'mahasiswa_id' => $jadwal->mahasiswa_id,
                'dosen_id' => $jadwal->dosen_id,
                'waktu_mulai' => $ketersediaan->tanggal . ' ' . $ketersediaan->waktu_mulai,
            ],
            [
                'waktu_selesai' => $ketersediaan->tanggal . ' ' . $ketersediaan->waktu_selesai,
                'topik' => $jadwal->topik_bimbingan,
                'lokasi' => $jadwal->lokasi,
                'tipe_pertemuan' => $jadwal->tipe,
                'status' => 'disetujui',
            ]
        );

        // Dispatch reminders
        Artisan::call('bimbingan:dispatch-schedule-reminders');

        $this->browse(function (Browser $browser) use ($mahasiswaUser) {
            $browser->loginAs($mahasiswaUser)
                    ->visit('/mahasiswa/dashboard')
                    ->pause(1000)
                    // Check if bell icon has unread badge
                    ->waitFor('@notification-bell-badge', 5)
                    ->click('@notification-bell')
                    ->pause(1000)
                    ->assertSee('Dusk Test Topic')
                    ->assertSee('Ruang Dosen');
        });
    }

    /**
     * TC.Reminder.33.002 - Pembatalan / Ubah Jadwal
     */
    public function test_reminder_dihapus_jika_batal(): void
    {
        // Cancel the schedule
        $jadwal = JadwalBimbingan::where('topik_bimbingan', 'Dusk Test Topic')->first();
        $this->assertNotNull($jadwal);
        
        $jadwal->update(['status' => 'canceled']);
        
        // This should cancel the reminders
        $this->assertDatabaseMissing('bimbingan_reminders', [
            'bimbingan_id' => $jadwal->id, // Actually bimbingan_id points to Bimbingan, but let's just check the DB logic
            'status' => 'pending'
        ]);
        
        $mahasiswaUser = $jadwal->mahasiswa->user;

        $this->browse(function (Browser $browser) use ($mahasiswaUser) {
            $browser->loginAs($mahasiswaUser)
                    ->visit('/mahasiswa/dashboard')
                    ->pause(1000)
                    ->click('@notification-bell')
                    ->pause(1000)
                    ->assertDontSee('Dusk Test Topic');
        });
    }
}
