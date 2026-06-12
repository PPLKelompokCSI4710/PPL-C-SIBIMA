<?php

namespace Tests\Browser;

use App\Models\Bimbingan;
use App\Models\BimbinganReminder;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\DuskTestCase;
use Illuminate\Support\Facades\Artisan;
use Tests\Browser\Concerns\SeedsSpatieRoles;
use App\Models\User;
use App\Models\Dosen;
use App\Models\Mahasiswa;

class ReminderJadwalTest extends DuskTestCase
{
    use DatabaseMigrations;
    use SeedsSpatieRoles;

    /**
     * TC.Reminder.33.001 & TC.Reminder.33.002
     * Combined into one test because DatabaseMigrations resets DB between test methods.
     *
     * Strategy: use travelTo so that "now + 2 hours" is the bimbingan start time,
     * which causes the h2 stage (startTime - 2h = now) to be immediately due.
     * We then call dispatch-schedule-reminders which picks up the due reminder.
     */
    public function test_reminder_jadwal_bimbingan_workflow(): void
    {
        $this->travelTo(Carbon::parse('2026-06-12 10:00:00'));

        try {
            $this->seedRoles();

            // Create dosen user
            $dosenUser = User::factory()->create();
            $dosenUser->assignRole('dosen');
            $dosen = Dosen::create([
                'user_id'       => $dosenUser->id,
                'nidn'          => 'D099',
                'nama_lengkap'  => 'Dosen Reminder Test',
                'program_studi' => 'Informatika',
                'fakultas'      => 'FTI',
            ]);

            // Create mahasiswa user
            $mahasiswaUser = User::factory()->create();
            $mahasiswaUser->assignRole('mahasiswa');
            $mahasiswa = Mahasiswa::create([
                'user_id'         => $mahasiswaUser->id,
                'nim'             => 'MHS099',
                'nama_lengkap'    => 'Mahasiswa Reminder Test',
                'program_studi'   => 'Informatika',
                'fakultas'        => 'FTI',
                'angkatan'        => '2022',
                'semester'        => '8',
                'status_akademik' => 'aktif',
            ]);

            // Create bimbingan with waktu_mulai exactly 2 hours from now
            // This means h2 stage (start - 2h = now) will be due immediately
            $start = now()->addHours(2)->seconds(0);

            $bimbingan = Bimbingan::create([
                'mahasiswa_id'     => $mahasiswa->id,
                'dosen_id'         => $dosen->id,
                'waktu_mulai'      => $start,
                'waktu_selesai'    => null,
                'topik'            => 'Dusk Test Topic',
                'lokasi'           => 'Ruang Dosen',
                'tipe_pertemuan'   => 'offline',
                'status'           => 'disetujui',
            ]);

            // BimbinganReminder records are auto-created by Bimbingan::booted()
            // The h2 stage send_at = start - 2h = now (due immediately)
            $this->assertGreaterThan(
                0,
                BimbinganReminder::where('bimbingan_id', $bimbingan->id)->where('status', 'pending')->count(),
                'BimbinganReminder records should be auto-created on Bimbingan save'
            );

            // Dispatch reminders - should send the h2 stage that is due now
            Artisan::call('bimbingan:dispatch-schedule-reminders');

            // TC.Reminder.33.001: Assert notification was created
            $this->assertDatabaseHas('notifications', [
                'notifiable_id' => $mahasiswaUser->id,
            ]);

            // TC.Reminder.33.002: Canceling bimbingan should have no pending reminders
            // 'batal' is the valid enum value (not 'canceled')
            $bimbingan->update(['status' => 'batal']);

            $this->assertDatabaseMissing('bimbingan_reminders', [
                'bimbingan_id' => $bimbingan->id,
                'status'       => 'pending',
            ]);
        } finally {
            $this->travelBack();
        }
    }
}
