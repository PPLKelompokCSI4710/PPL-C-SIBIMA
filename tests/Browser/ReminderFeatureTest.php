<?php

namespace Tests\Browser;

use App\Models\AppSetting;
use App\Models\Bimbingan;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\ReminderPreference;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Spatie\Permission\Models\Role;
use Tests\DuskTestCase;

class ReminderFeatureTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_pbi_15_multi_stage_reminder_can_send_and_can_be_disabled_per_stage(): void
    {
        $this->seedRoles();

        $mahasiswaUser = User::factory()->create();
        $mahasiswaUser->assignRole('mahasiswa');
        $mahasiswa = Mahasiswa::create([
            'user_id' => $mahasiswaUser->id,
            'nim' => 'MHS001',
            'nama_lengkap' => 'Mahasiswa Test',
            'program_studi' => 'Informatika',
            'fakultas' => 'FTI',
            'angkatan' => '2022',
            'semester' => '8',
            'status_akademik' => 'aktif',
        ]);

        $dosenUser = User::factory()->create();
        $dosenUser->assignRole('dosen');
        $dosen = Dosen::create([
            'user_id' => $dosenUser->id,
            'nidn' => 'D001',
            'nama_lengkap' => 'Dosen Test',
            'program_studi' => 'Informatika',
            'fakultas' => 'FTI',
            'jabatan_akademik' => 'Lektor',
            'keahlian' => 'AI',
        ]);

        // Create approved bimbingan exactly 2 hours from now => stage h2 is due immediately.
        $start = Carbon::now()->addHours(2)->seconds(0);
        $bimbingan = Bimbingan::create([
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_id' => $dosen->id,
            'waktu_mulai' => $start,
            'waktu_selesai' => null,
            'topik' => 'Topik Test',
            'lokasi' => 'Ruang 101',
            'tipe_pertemuan' => 'offline',
            'catatan_persiapan' => ['Bawa draft'],
            'status' => 'disetujui',
        ]);

        // Trigger dispatch due reminders.
        $this->artisan('bimbingan:dispatch-schedule-reminders')->assertExitCode(0);

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $mahasiswaUser->id,
        ]);
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $dosenUser->id,
        ]);

        // Disable stage h2 for mahasiswa, recreate a new bimbingan for immediate due reminder.
        ReminderPreference::forUser($mahasiswaUser->id)->update([
            'stage_h2_enabled' => false,
        ]);

        $start2 = Carbon::now()->addHours(2)->seconds(0);
        Bimbingan::create([
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_id' => $dosen->id,
            'waktu_mulai' => $start2,
            'waktu_selesai' => null,
            'topik' => 'Topik Test 2',
            'lokasi' => 'Ruang 102',
            'tipe_pertemuan' => 'offline',
            'catatan_persiapan' => ['Bawa logbook'],
            'status' => 'disetujui',
        ]);

        $before = \DB::table('notifications')->where('notifiable_id', $mahasiswaUser->id)->count();
        $this->artisan('bimbingan:dispatch-schedule-reminders')->assertExitCode(0);
        $after = \DB::table('notifications')->where('notifiable_id', $mahasiswaUser->id)->count();
        $this->assertSame($before, $after, 'Mahasiswa should not receive h2 reminder when disabled.');

        $this->browse(function (Browser $browser) use ($mahasiswaUser) {
            $browser->loginAs($mahasiswaUser)
                ->visitRoute('mahasiswa.bimbingan.reminder')
                ->assertSee('Reminder Bimbingan');
        });
    }

    public function test_pbi_16_progress_reminder_sends_to_mahasiswa_and_cc_dosen_with_admin_threshold(): void
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
            'jabatan_akademik' => 'Lektor',
            'keahlian' => 'SE',
        ]);

        // Link dosen-mahasiswa
        \DB::table('dosen_mahasiswa')->insert([
            'dosen_id' => $dosen->id,
            'mahasiswa_id' => $mahasiswa->id,
            'tanggal_penugasan' => now()->toDateString(),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Last bimbingan was 10 days ago => exceeds threshold.
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

        $this->browse(function (Browser $browser) use ($mahasiswaUser) {
            $browser->loginAs($mahasiswaUser)
                ->visitRoute('mahasiswa.bimbingan.progress_reminder')
                ->assertSee('Monitoring Progres Akademik');
        });
    }

    private function seedRoles(): void
    {
        foreach (['admin', 'dosen', 'mahasiswa'] as $name) {
            Role::findOrCreate($name);
        }
    }
}
