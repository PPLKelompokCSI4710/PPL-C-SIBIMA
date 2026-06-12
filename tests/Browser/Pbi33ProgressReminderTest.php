<?php

namespace Tests\Browser;

use App\Models\AppSetting;
use App\Models\Bimbingan;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\SeedsSpatieRoles;
use Tests\DuskTestCase;

/**
 * PBI 33 — Reminder progres & target akademik periodik (+ konfigurasi admin terkait).
 */
class Pbi33ProgressReminderTest extends DuskTestCase
{
    use DatabaseMigrations;
    use SeedsSpatieRoles;

    public function test_progress_reminder_sends_to_mahasiswa_and_cc_dosen_with_admin_threshold(): void
    {
        $this->seedRoles();

        AppSetting::set('progress_reminder_inactive_days', 1);

        $mahasiswaUser = User::factory()->create();
        $mahasiswaUser->assignRole('mahasiswa');
        $mahasiswa = Mahasiswa::create([
            'user_id' => $mahasiswaUser->id,
            'nim' => 'MHS002',
            'nama_lengkap' => 'Mahasiswa Progress',
            'program_studi' => 'Informatika',
            'fakultas' => 'FTI',
            'angkatan' => '2022',
            'semester' => '8',
            'status_akademik' => 'aktif',
            'sks_lulus' => 100,
            'sks_total' => 144,
            'ipk' => 3.25,
            'progress_reminder_enabled' => true,
            'progress_reminder_frequency' => 'weekly',
        ]);

        $dosenUser = User::factory()->create();
        $dosenUser->assignRole('dosen');
        $dosen = Dosen::create([
            'user_id' => $dosenUser->id,
            'nidn' => 'D002',
            'nama_lengkap' => 'Dosen Pembimbing',
            'program_studi' => 'Informatika',
            'fakultas' => 'FTI',
        ]);

        \DB::table('dosen_mahasiswa')->insert([
            'dosen_id' => $dosen->id,
            'mahasiswa_id' => $mahasiswa->id,
            'tanggal_penugasan' => now()->toDateString(),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Bimbingan::create([
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_id' => $dosen->id,
            'waktu_mulai' => Carbon::now()->subDays(10)->subHours(1),
            'waktu_selesai' => Carbon::now()->subDays(10),
            'topik' => 'Review',
            'lokasi' => 'Online',
            'tipe_pertemuan' => 'online',
            'status' => 'selesai',
        ]);

        $this->artisan('bimbingan:check-progress')->assertExitCode(0);

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $mahasiswaUser->id,
        ]);
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $dosenUser->id,
        ]);

        // AC 33.3: Ringkasan progres dalam notifikasi
        $notification = \DB::table('notifications')->where('notifiable_id', $mahasiswaUser->id)->first();
        $data = json_decode($notification->data, true);
        $this->assertEquals(100, $data['progress_summary']['sks_lulus']);
        $this->assertEquals(144, $data['progress_summary']['sks_total']);
        $this->assertEquals(3.25, $data['progress_summary']['ipk']);
        $this->assertEquals('8', $data['progress_summary']['semester']);

        // AC 33.4: Frekuensi reminder dapat diatur (Cooldown)
        // Since we just sent one, running command again shouldn't send another one immediately
        $countBefore = \DB::table('notifications')->where('notifiable_id', $mahasiswaUser->id)->count();
        $this->artisan('bimbingan:check-progress')->assertExitCode(0);
        $countAfter = \DB::table('notifications')->where('notifiable_id', $mahasiswaUser->id)->count();
        $this->assertEquals($countBefore, $countAfter, 'Cooldown prevents immediate duplicate reminder');

        $this->browse(function (Browser $browser) use ($mahasiswaUser) {
            $browser->loginAs($mahasiswaUser)
                ->visitRoute('mahasiswa.bimbingan.progress_reminder')
                ->pause(2000)
                ->assertSee('Konfigurasi Reminder Progres');
        });
    }
}
