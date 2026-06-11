<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\StudentProgress;
use App\Models\Eskalasi;
use Carbon\Carbon;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Support\Facades\Artisan;

class ReminderProgressTest extends DuskTestCase
{
    use \Illuminate\Foundation\Testing\DatabaseMigrations;

    protected $seed = true;

    /**
     * TC.Reminder.34.001 & TC.Reminder.34.002
     */
    public function test_reminder_progress_berkala(): void
    {
        $dosen = Dosen::first();
        $mahasiswa = Mahasiswa::first();
        
        $mahasiswa->update(['dosen_id' => $dosen->id]);
        
        $mahasiswaUser = $mahasiswa->user;
        $dosenUser = $dosen->user;

        // Set the last bimbingan progress to way past the threshold (e.g. 15 days ago)
        StudentProgress::updateOrCreate(
            ['mahasiswa_id' => $mahasiswa->id],
            [
                'last_bimbingan_date' => Carbon::now()->subDays(15),
                'status' => 'inactive',
                'target_lulus' => Carbon::now()->addMonths(6),
            ]
        );

        // Reset consecutive reminders
        $mahasiswa->update([
            'consecutive_progress_reminders' => 0,
            'progress_reminder_frequency' => 7 // Every 7 days
        ]);

        // Run the command to dispatch progress reminders
        Artisan::call('bimbingan:check-progress');

        // Test Mahasiswa receives it
        $this->browse(function (Browser $browser) use ($mahasiswaUser) {
            $browser->loginAs($mahasiswaUser)
                    ->visit('/mahasiswa/dashboard')
                    ->pause(1000)
                    ->click('@notification-bell')
                    ->pause(1000)
                    ->assertSee('Reminder Progres Bimbingan')
                    ->assertSee('Sudah 15 hari sejak bimbingan terakhir');
        });

        // Test Dosen receives it
        $this->browse(function (Browser $browser) use ($dosenUser, $mahasiswa) {
            $browser->loginAs($dosenUser)
                    ->visit('/dosen/dashboard')
                    ->pause(1000)
                    ->click('@notification-bell')
                    ->pause(1000)
                    ->assertSee('Perhatian: Progres Mahasiswa')
                    ->assertSee($mahasiswa->nama_lengkap);
        });
    }

    /**
     * TC.Reminder.35.001 - Eskalasi koordinator aktif
     */
    public function test_eskalasi_koordinator_aktif_setelah_batas_terlampaui(): void
    {
        $mahasiswa = Mahasiswa::first();
        $mahasiswa->update(['dosen_id' => Dosen::first()->id]);
        
        // Simulate missing 3 consecutive progress reminders
        $mahasiswa->update([
            'consecutive_progress_reminders' => 3
        ]);

        // Next check should trigger eskalasi
        Artisan::call('bimbingan:check-progress');

        // Verify Eskalasi is created
        $this->assertDatabaseHas('eskalasis', [
            'mahasiswa_id' => $mahasiswa->id,
            'status' => 'active'
        ]);

        $adminUser = User::whereHas('roles', fn($q) => $q->where('name', 'admin'))->first();

        // The notification in Filament might require visiting filament page
        $this->browse(function (Browser $browser) use ($adminUser, $mahasiswa) {
            $browser->loginAs($adminUser)
                    ->visit('/admin/dashboard') // Adjust if filament route is different
                    ->pause(2000)
                    // We assume there's a database notification bell in Filament
                    // Testing Filament notifications directly in Dusk can be tricky,
                    // but we verify the DB record is there at least.
                    ->assertSee($mahasiswa->nama_lengkap) // Check if the student appears in eskalasi table
                    ->assertSee('Eskalasi');
        });
    }
}
