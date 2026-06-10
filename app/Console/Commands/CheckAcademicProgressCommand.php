<?php

namespace App\Console\Commands;

use App\Models\AppSetting;
use App\Models\Eskalasi;
use App\Models\JadwalBimbingan;
use App\Models\Mahasiswa;
use App\Models\User;
use App\Notifications\AcademicProgressNotification;
use App\Notifications\EskalasiNotification;
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

        $inactiveThresholdDays = max(1, (int) (AppSetting::get('progress_reminder_inactive_days') ?? 14));
        $escalationThreshold = max(1, (int) (AppSetting::get('escalation_reminder_threshold') ?? 3));

        $mahasiswas = Mahasiswa::where('progress_reminder_enabled', true)->with(['user', 'dosens', 'bimbingans' => function ($query) {
            $query->where('status', 'selesai')->orderBy('waktu_selesai', 'desc');
        }])->get();

        foreach ($mahasiswas as $mahasiswa) {
            $lastBimbingan = $mahasiswa->bimbingans->first();

            if ($lastBimbingan) {
                $daysSinceLast = (int) $lastBimbingan->waktu_selesai->copy()->startOfDay()->diffInDays(now()->startOfDay());
            } else {
                $daysSinceLast = (int) $mahasiswa->created_at->copy()->startOfDay()->diffInDays(now()->startOfDay());
            }

            $daysSinceLast = max(0, $daysSinceLast);

            $cooldownDays = (int) ($mahasiswa->progress_reminder_frequency_days ?? 14);
            $lastSentAt = $mahasiswa->last_progress_reminder_sent_at;
            $eligibleByCooldown = ! $lastSentAt || $lastSentAt->copy()->addDays($cooldownDays)->isPast();

            if ($daysSinceLast >= $inactiveThresholdDays && $eligibleByCooldown) {
                $progressSummary = [
                    'sks_lulus' => $mahasiswa->sks_lulus,
                    'sks_total' => $mahasiswa->sks_total,
                    'ipk' => $mahasiswa->ipk,
                    'semester' => $mahasiswa->semester,
                ];

                if ($mahasiswa->user) {
                    $mahasiswa->user->notify(new AcademicProgressNotification($daysSinceLast, false, '', $progressSummary));
                }

                foreach ($mahasiswa->dosens as $dosen) {
                    if ($dosen->user) {
                        $dosen->user->notify(new AcademicProgressNotification($daysSinceLast, true, $mahasiswa->nama_lengkap, $progressSummary));
                    }
                }

                $nextConsecutive = (int) ($mahasiswa->consecutive_progress_reminders ?? 0) + 1;

                $mahasiswa->forceFill([
                    'last_progress_reminder_sent_at' => now(),
                    'consecutive_progress_reminders' => $nextConsecutive,
                ])->save();

                $this->info("Reminder sent to Mahasiswa NIM {$mahasiswa->nim} and CC'd Dosen.");

                $mahasiswa->refresh();
            }

            // Check if we should escalate to admin
            // Condition 1: reached escalation threshold (consecutive reminders)
            $condition1 = $mahasiswa->consecutive_progress_reminders >= $escalationThreshold;

            // Condition 2: daysSinceLast >= 7 AND no pending/approved upcoming schedule
            $hasUpcoming = JadwalBimbingan::where('mahasiswa_id', $mahasiswa->id)
                ->whereIn('status', ['pending', 'approved'])
                ->exists();
            $condition2 = ($daysSinceLast >= 7 && !$hasUpcoming);

            if ($condition1 || $condition2) {
                $escalationDelayDays = (int) (AppSetting::get('escalation_delay_days', 3) ?? 3);
                $lastSent = $mahasiswa->last_progress_reminder_sent_at;

                // If escalated due to condition 2, we might not wait for delay if it hasn't been sent yet
                // But let's just apply the delay if it's based on reminders, or immediate if condition2
                $eligibleForEscalation = $condition2 || ($escalationDelayDays === 0) ||
                    ($lastSent && $lastSent->copy()->startOfDay()->addDays($escalationDelayDays)->isPast());

                if ($eligibleForEscalation) {
                    $existingActive = Eskalasi::where('mahasiswa_id', $mahasiswa->id)
                        ->where('status', 'active')
                        ->exists();

                    if (! $existingActive) {
                        Eskalasi::create([
                            'mahasiswa_id' => $mahasiswa->id,
                            'status' => 'active',
                        ]);

                        $progressSummary = [
                            'sks_lulus' => $mahasiswa->sks_lulus,
                            'sks_total' => $mahasiswa->sks_total,
                            'ipk' => $mahasiswa->ipk,
                            'semester' => $mahasiswa->semester,
                        ];

                        $admins = User::role('admin')->get();
                        $sesiSelesai = $mahasiswa->bimbingans->count();
                        $terakhir = $lastBimbingan?->waktu_selesai?->toDateTimeString();
                        foreach ($admins as $admin) {
                            $admin->notify(new EskalasiNotification(
                                $progressSummary,
                                $mahasiswa->nama_lengkap,
                                $sesiSelesai,
                                $terakhir,
                            ));
                        }

                        $this->info("Eskalasi created for Mahasiswa NIM {$mahasiswa->nim} and notified Admins.");
                    }
                }
            }
        }

        $this->info('Academic progress check completed.');
    }
}
