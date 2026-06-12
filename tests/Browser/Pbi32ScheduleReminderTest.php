<?php

namespace Tests\Browser;

use App\Models\Bimbingan;
use App\Models\BimbinganReminder;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\ReminderPreference;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\SeedsSpatieRoles;
use Tests\DuskTestCase;

/**
 * PBI 32 — Reminder jadwal bimbingan multi-tahap otomatis (H-3, H-1, H-2 jam).
 */
class Pbi32ScheduleReminderTest extends DuskTestCase
{
    use DatabaseMigrations;
    use SeedsSpatieRoles;

    public function test_multi_stage_reminder_can_send_and_can_be_disabled_per_stage(): void
    {
        // Freeze time so H-2 jam is exactly "now"; otherwise sub-second drift can mark H-2 as already past and skip creation.
        $this->travelTo(Carbon::parse('2026-05-03 10:00:00'));

        try {
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
            ]);

            $start = now()->addHours(2)->seconds(0);
            Bimbingan::create([
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

            $this->artisan('bimbingan:dispatch-schedule-reminders')->assertExitCode(0);

            $this->assertDatabaseHas('notifications', [
                'notifiable_id' => $mahasiswaUser->id,
            ]);
            $this->assertDatabaseHas('notifications', [
                'notifiable_id' => $dosenUser->id,
            ]);

            // AC 32.2: Konten reminder lengkap
            $notification = \DB::table('notifications')->where('notifiable_id', $mahasiswaUser->id)->first();
            $data = json_decode($notification->data, true);
            $this->assertEquals('Topik Test', $data['detail']['topik']);
            $this->assertEquals('Ruang 101', $data['detail']['lokasi']);
            $this->assertNotNull($data['detail']['mahasiswa']);
            $this->assertNotNull($data['detail']['dosen']);
            $this->assertNotNull($data['detail']['waktu_mulai']);

            ReminderPreference::forUser($mahasiswaUser->id)->update([
                'stage_h2_enabled' => false,
            ]);

            $start2 = now()->addHours(2)->seconds(0);
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
        } finally {
            $this->travelBack();
        }

        $this->browse(function (Browser $browser) use ($mahasiswaUser) {
            $browser->loginAs($mahasiswaUser)
                ->visitRoute('mahasiswa.bimbingan.reminder')
                ->assertSee('Reminder');
        });
    }

    public function test_updating_bimbingan_time_cancels_old_pending_reminders_and_reschedules(): void
    {
        $this->travelTo(Carbon::parse('2026-06-01 10:00:00'));

        try {
            $this->seedRoles();

            $mahasiswaUser = User::factory()->create();
            $mahasiswaUser->assignRole('mahasiswa');
            $mahasiswa = Mahasiswa::create([
                'user_id' => $mahasiswaUser->id,
                'nim' => 'MHS010',
                'nama_lengkap' => 'Mahasiswa Reschedule',
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
                'nidn' => 'D010',
                'nama_lengkap' => 'Dosen Reschedule',
                'program_studi' => 'Informatika',
                'fakultas' => 'FTI',
            ]);

            $bimbingan = Bimbingan::create([
                'mahasiswa_id' => $mahasiswa->id,
                'dosen_id' => $dosen->id,
                'waktu_mulai' => now()->addHours(2)->seconds(0),
                'waktu_selesai' => null,
                'topik' => 'Ubah Jadwal',
                'lokasi' => 'Lab',
                'tipe_pertemuan' => 'offline',
                'status' => 'disetujui',
            ]);

            $this->assertGreaterThan(
                0,
                BimbinganReminder::where('bimbingan_id', $bimbingan->id)->where('status', 'pending')->count()
            );

            $h2Before = BimbinganReminder::where('bimbingan_id', $bimbingan->id)
                ->where('stage', 'h2')
                ->where('user_id', $mahasiswaUser->id)
                ->first();
            $this->assertNotNull($h2Before);
            $previousSendAt = $h2Before->send_at->toDateTimeString();

            $bimbingan->update([
                'waktu_mulai' => now()->addHours(4)->seconds(0),
                'lokasi' => 'Ruang baru',
            ]);

            $h2After = BimbinganReminder::where('bimbingan_id', $bimbingan->id)
                ->where('stage', 'h2')
                ->where('user_id', $mahasiswaUser->id)
                ->first();
            $this->assertNotNull($h2After);
            $this->assertNotSame($previousSendAt, $h2After->send_at->toDateTimeString());
            $this->assertSame('Ruang baru', $h2After->payload['lokasi'] ?? null);
        } finally {
            $this->travelBack();
        }
    }

    public function test_dispatch_does_not_resend_already_sent_stage_reminders(): void
    {
        $this->travelTo(Carbon::parse('2026-06-02 10:00:00'));

        try {
            $this->seedRoles();

            $mahasiswaUser = User::factory()->create();
            $mahasiswaUser->assignRole('mahasiswa');
            $mahasiswa = Mahasiswa::create([
                'user_id' => $mahasiswaUser->id,
                'nim' => 'MHS011',
                'nama_lengkap' => 'Mahasiswa No Dup',
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
                'nidn' => 'D011',
                'nama_lengkap' => 'Dosen No Dup',
                'program_studi' => 'Informatika',
                'fakultas' => 'FTI',
            ]);

            Bimbingan::create([
                'mahasiswa_id' => $mahasiswa->id,
                'dosen_id' => $dosen->id,
                'waktu_mulai' => now()->addHours(2)->seconds(0),
                'waktu_selesai' => null,
                'topik' => 'Satu kali kirim',
                'lokasi' => 'A101',
                'tipe_pertemuan' => 'offline',
                'status' => 'disetujui',
            ]);

            $this->artisan('bimbingan:dispatch-schedule-reminders')->assertExitCode(0);
            $afterFirst = \DB::table('notifications')->count();

            $this->artisan('bimbingan:dispatch-schedule-reminders')->assertExitCode(0);
            $afterSecond = \DB::table('notifications')->count();

            $this->assertSame($afterFirst, $afterSecond, 'Sent reminders must not be duplicated on re-dispatch.');
        } finally {
            $this->travelBack();
        }
    }

    public function test_user_reminder_preferences_page_loads(): void
    {
        $this->seedRoles();

        $user = User::factory()->create();
        $user->assignRole('mahasiswa');

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visitRoute('mahasiswa.bimbingan.reminder')
                ->assertSee('Reminder');
        });
    }
}
