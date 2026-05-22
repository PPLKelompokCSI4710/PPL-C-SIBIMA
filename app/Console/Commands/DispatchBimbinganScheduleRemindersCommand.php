<?php

namespace App\Console\Commands;

use App\Models\BimbinganReminder;
use App\Models\ReminderPreference;
use App\Notifications\BimbinganScheduleReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DispatchBimbinganScheduleRemindersCommand extends Command
{
    protected $signature = 'bimbingan:dispatch-schedule-reminders {--limit=200}';

    protected $description = 'Dispatch due bimbingan schedule reminders (H-3, H-1, H-2 jam).';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');

        $due = BimbinganReminder::query()
            ->where('status', 'pending')
            ->where('send_at', '<=', now())
            ->orderBy('send_at')
            ->limit($limit)
            ->get();

        if ($due->isEmpty()) {
            $this->info('No due reminders.');

            return self::SUCCESS;
        }

        $sent = 0;

        foreach ($due as $reminder) {
            DB::transaction(function () use ($reminder, &$sent) {
                $reminder->refresh();
                if ($reminder->status !== 'pending' || $reminder->send_at->isFuture()) {
                    return;
                }

                $pref = ReminderPreference::forUser($reminder->user_id);
                if (! $pref->schedule_reminder_enabled) {
                    $reminder->update([
                        'status' => 'canceled',
                        'canceled_at' => now(),
                    ]);

                    return;
                }

                $stageEnabled = match ($reminder->stage) {
                    'h3' => (bool) $pref->stage_h3_enabled,
                    'h1' => (bool) $pref->stage_h1_enabled,
                    'h2' => (bool) $pref->stage_h2_enabled,
                    default => false,
                };

                if (! $stageEnabled) {
                    $reminder->update([
                        'status' => 'canceled',
                        'canceled_at' => now(),
                    ]);

                    return;
                }

                $user = $reminder->user;
                if (! $user) {
                    $reminder->update([
                        'status' => 'canceled',
                        'canceled_at' => now(),
                    ]);

                    return;
                }

                $user->notify(new BimbinganScheduleReminderNotification($reminder->payload ?? [], $reminder->stage));

                $reminder->update([
                    'status' => 'sent',
                    'sent_at' => now(),
                ]);

                $sent++;
            });
        }

        $this->info("Sent {$sent} reminder(s).");

        return self::SUCCESS;
    }
}
