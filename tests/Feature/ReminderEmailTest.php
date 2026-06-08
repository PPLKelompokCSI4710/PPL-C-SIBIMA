<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\AcademicProgressNotification;
use App\Notifications\BimbinganScheduleReminderNotification;
use App\Notifications\EskalasiNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ReminderEmailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::findOrCreate('mahasiswa');
        Role::findOrCreate('dosen');
        Role::findOrCreate('admin');
    }

    public function test_bimbingan_schedule_reminder_email_content_for_mahasiswa()
    {
        $user = User::factory()->create();
        $user->assignRole('mahasiswa');

        $payload = [
            'waktu_mulai' => '2026-06-01 10:00:00',
            'topik' => 'Review Progress Bab 3',
            'lokasi' => 'https://zoom.us/j/123456',
            'tipe_pertemuan' => 'online',
            'mahasiswa' => 'Mahasiswa Test',
            'dosen' => 'Dosen Test',
        ];

        $notification = new BimbinganScheduleReminderNotification($payload, 'h2');
        $mail = $notification->toMail($user);

        $this->assertStringContainsString('⏰ Reminder Bimbingan H-2 Jam: Review Progress Bab 3', $mail->subject);
        $this->assertStringContainsString('Halo, rekan mahasiswa!', $mail->greeting);

        $body = implode("\n", $mail->introLines);
        $this->assertStringContainsString('Review Progress Bab 3', $body);
        $this->assertStringContainsString('Online', $body);
        $this->assertStringContainsString('Link Pertemuan:', $body);
    }

    public function test_bimbingan_schedule_reminder_email_content_for_dosen()
    {
        $user = User::factory()->create();
        $user->assignRole('dosen');

        $payload = [
            'waktu_mulai' => '2026-06-01 10:00:00',
            'topik' => 'Review Progress Bab 3',
            'lokasi' => 'Ruang Rapat FTI',
            'tipe_pertemuan' => 'offline',
            'mahasiswa' => 'Mahasiswa Test',
            'dosen' => 'Dosen Test',
        ];

        $notification = new BimbinganScheduleReminderNotification($payload, 'h1');
        $mail = $notification->toMail($user);

        $this->assertStringContainsString('⏰ Reminder Bimbingan H-1: Review Progress Bab 3', $mail->subject);
        $this->assertStringContainsString('Yth. Bapak/Ibu Dosen Pembimbing,', $mail->greeting);

        $body = implode("\n", $mail->introLines);
        $this->assertStringContainsString('Review Progress Bab 3', $body);
        $this->assertStringContainsString('Offline', $body);
        $this->assertStringContainsString('Ruang Rapat FTI', $body);
    }

    public function test_academic_progress_reminder_email_content_for_mahasiswa()
    {
        $user = User::factory()->create();
        $user->assignRole('mahasiswa');

        $progressSummary = [
            'sks_lulus' => 110,
            'sks_total' => 144,
            'ipk' => '3.80',
            'semester' => 8,
        ];

        $notification = new AcademicProgressNotification(15, false, '', $progressSummary);
        $mail = $notification->toMail($user);

        $this->assertStringContainsString('📈 Pantau Progres Akademikmu: Waktunya Bimbingan!', $mail->subject);
        $this->assertStringContainsString('Halo, rekan mahasiswa!', $mail->greeting);

        $body = implode("\n", $mail->introLines);
        $this->assertStringContainsString('15 hari', $body);
        $this->assertStringContainsString('3.80', $body);
        $this->assertStringContainsString('110', $body);
    }

    public function test_academic_progress_reminder_email_content_for_dosen_cc()
    {
        $user = User::factory()->create();
        $user->assignRole('dosen');

        $progressSummary = [
            'sks_lulus' => 110,
            'sks_total' => 144,
            'ipk' => '3.80',
            'semester' => 8,
        ];

        $notification = new AcademicProgressNotification(15, true, 'Budi Mahasiswa', $progressSummary);
        $mail = $notification->toMail($user);

        $this->assertStringContainsString('📢 CC Reminder: Progres Akademik Mahasiswa Budi Mahasiswa', $mail->subject);
        $this->assertStringContainsString('Yth. Bapak/Ibu Dosen Pembimbing,', $mail->greeting);

        $body = implode("\n", $mail->introLines);
        $this->assertStringContainsString('Budi Mahasiswa', $body);
        $this->assertStringContainsString('3.80', $body);
        $this->assertStringContainsString('15 hari', $body);
    }

    public function test_eskalasi_reminder_email_content_for_admin()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $progressSummary = [
            'sks_lulus' => 100,
            'sks_total' => 144,
            'ipk' => '3.20',
            'semester' => 9,
        ];

        $notification = new EskalasiNotification($progressSummary, 'Andi Mahasiswa', 5, '2026-05-10 14:00:00');
        $mail = $notification->toMail($user);

        $this->assertStringContainsString('⚠️ Eskalasi Bimbingan: Mahasiswa Andi Mahasiswa Membutuhkan Tindakan', $mail->subject);
        $this->assertStringContainsString('Yth. Koordinator / Administrator SIBIMA,', $mail->greeting);

        $body = implode("\n", $mail->introLines);
        $this->assertStringContainsString('Andi Mahasiswa', $body);
        $this->assertStringContainsString('5 Sesi', $body);
        $this->assertStringContainsString('3.20', $body);
    }
}
