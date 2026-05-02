<?php

namespace App\Console\Commands;

use App\Models\AppSetting;
use App\Models\Bimbingan;
use App\Models\Mahasiswa;
use App\Notifications\AcademicProgressNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckAcademicProgressCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bimbingan:check-progress';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and send reminder to students who have not had bimbingan in a while, and CC their dosens.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting academic progress check...');

        $inactiveThresholdDays = (int) (AppSetting::get('progress_reminder_inactive_days', 14) ?? 14);

        $mahasiswas = Mahasiswa::where('progress_reminder_enabled', true)->with(['user', 'dosens', 'bimbingans' => function ($query) {
            $query->where('status', 'selesai')->orderBy('waktu_selesai', 'desc');
        }])->get();

        foreach ($mahasiswas as $mahasiswa) {
            $lastBimbingan = $mahasiswa->bimbingans->first();

            if ($lastBimbingan) {
                $daysSinceLast = Carbon::now()->diffInDays($lastBimbingan->waktu_selesai);
            } else {
                // If no bimbingan ever, check against creation or semester start (for simplicity we use created_at)
                $daysSinceLast = Carbon::now()->diffInDays($mahasiswa->created_at);
            }

            $cooldownDays = ($mahasiswa->progress_reminder_frequency ?? 'biweekly') === 'weekly' ? 7 : 14;
            $lastSentAt = $mahasiswa->last_progress_reminder_sent_at;
            $eligibleByCooldown = ! $lastSentAt || $lastSentAt->copy()->addDays($cooldownDays)->isPast();

            if ($daysSinceLast >= $inactiveThresholdDays && $eligibleByCooldown) {
                $progressSummary = [
                    'sks_lulus' => $mahasiswa->sks_lulus,
                    'sks_total' => $mahasiswa->sks_total,
                    'ipk' => $mahasiswa->ipk,
                    'semester' => $mahasiswa->semester,
                ];

                // Send reminder to Mahasiswa
                if ($mahasiswa->user) {
                    $mahasiswa->user->notify(new AcademicProgressNotification($daysSinceLast, false, '', $progressSummary));
                }

                // Send CC to Dosen(s)
                foreach ($mahasiswa->dosens as $dosen) {
                    if ($dosen->user) {
                        $dosen->user->notify(new AcademicProgressNotification($daysSinceLast, true, $mahasiswa->nama_lengkap, $progressSummary));
                    }
                }

                $mahasiswa->forceFill([
                    'last_progress_reminder_sent_at' => now(),
                ])->save();

                $this->info("Reminder sent to Mahasiswa NIM {$mahasiswa->nim} and CC'd Dosen.");
            }
        }

        $this->info('Academic progress check completed.');
    }
}
