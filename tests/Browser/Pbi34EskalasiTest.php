<?php

namespace Tests\Browser;

use App\Models\AppSetting;
use App\Models\Bimbingan;
use App\Models\Dosen;
use App\Models\Eskalasi;
use App\Models\Mahasiswa;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\SeedsSpatieRoles;
use Tests\DuskTestCase;

/**
 * PBI 34 — Eskalasi Reminder Progres ke Koordinator/Admin
 */
class Pbi34EskalasiTest extends DuskTestCase
{
    use DatabaseMigrations;
    use SeedsSpatieRoles;

    public function test_pbi_34_scenarios_escalation_workflow(): void
    {
        $this->seedRoles();

        AppSetting::set('progress_reminder_inactive_days', 1);
        AppSetting::set('escalation_reminder_threshold', 3);

        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');

        $mahasiswaUser = User::factory()->create();
        $mahasiswaUser->assignRole('mahasiswa');
        $mahasiswa = Mahasiswa::create([
            'user_id' => $mahasiswaUser->id,
            'nim' => 'MHS003',
            'nama_lengkap' => 'Mahasiswa Eskalasi',
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
            'consecutive_progress_reminders' => 2, // Already got 2 reminders
        ]);

        $dosenUser = User::factory()->create();
        $dosenUser->assignRole('dosen');
        $dosen = Dosen::create([
            'user_id' => $dosenUser->id,
            'nidn' => 'D003',
            'nama_lengkap' => 'Dosen Eskalasi',
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

        // Trigger the 3rd reminder (reaches threshold of 3)
        // Set last sent at to null to bypass cooldown
        $mahasiswa->forceFill(['last_progress_reminder_sent_at' => null])->save();

        $this->artisan('bimbingan:check-progress')->assertExitCode(0);

        // AC 34.1: Deteksi reminder tidak direspons (Eskalasi record created)
        $this->assertDatabaseHas('eskalasis', [
            'mahasiswa_id' => $mahasiswa->id,
            'status' => 'active',
        ]);

        // AC 34.2: Notifikasi eskalasi ke koordinator/admin
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $adminUser->id,
        ]);
        $notification = \DB::table('notifications')->where('notifiable_id', $adminUser->id)->first();
        $data = json_decode($notification->data, true);
        $this->assertEquals('eskalasi_progress', $data['type']);
        $this->assertEquals('Mahasiswa Eskalasi', $data['detail']['mahasiswa']);
        $this->assertArrayHasKey('jumlah_sesi_bimbingan_selesai', $data['detail']);
        $this->assertArrayHasKey('terakhir_bimbingan_pada', $data['detail']);

        // AC 34.5: Tidak ada eskalasi duplikat
        // Run again, should not create another eskalasi active row
        $mahasiswa->forceFill(['last_progress_reminder_sent_at' => null])->save();
        $this->artisan('bimbingan:check-progress')->assertExitCode(0);
        $this->assertEquals(1, Eskalasi::where('mahasiswa_id', $mahasiswa->id)->where('status', 'active')->count());

        // AC 34.3: Daftar mahasiswa dalam status eskalasi (Admin page)
        $this->browse(function (Browser $browser) use ($adminUser, $mahasiswa) {
            $browser->loginAs($adminUser)
                ->visitRoute('admin.eskalasi.index')
                ->assertSee('Monitoring Eskalasi Bimbingan')
                ->assertSee($mahasiswa->nama_lengkap);
        });

        // AC 34.4: Eskalasi ditutup otomatis setelah booking baru
        Bimbingan::create([
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_id' => $dosen->id,
            'waktu_mulai' => Carbon::now()->addDays(2),
            'waktu_selesai' => null,
            'topik' => 'Bab 1',
            'lokasi' => 'Ruang 101',
            'tipe_pertemuan' => 'offline',
            'status' => 'menunggu',
        ]);

        $this->assertDatabaseHas('eskalasis', [
            'mahasiswa_id' => $mahasiswa->id,
            'status' => 'resolved',
        ]);
        $mahasiswa->refresh();
        $this->assertEquals(0, $mahasiswa->consecutive_progress_reminders);
    }
}
