<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Mahasiswa;
use App\Models\Bimbingan;
use Carbon\Carbon;
use App\Notifications\AcademicProgressNotification;

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
        
        $mahasiswas = Mahasiswa::where('progress_reminder_enabled', true)->with(['user', 'dosens', 'bimbingans' => function($query) {
            $query->where('status', 'selesai')->orderBy('waktu_selesai', 'desc');
        }])->get();

        foreach ($mahasiswas as $mahasiswa) {
            $frequencyDays = $mahasiswa->progress_reminder_frequency_days ?? 14; // Default 14 days
            
            $lastBimbingan = $mahasiswa->bimbingans->first();
            
            if ($lastBimbingan) {
                $daysSinceLast = Carbon::now()->diffInDays($lastBimbingan->waktu_selesai);
            } else {
                // If no bimbingan ever, check against creation or semester start (for simplicity we use created_at)
                $daysSinceLast = Carbon::now()->diffInDays($mahasiswa->created_at);
            }

            if ($daysSinceLast >= $frequencyDays) {
                // Send reminder to Mahasiswa
                if ($mahasiswa->user) {
                    $mahasiswa->user->notify(new AcademicProgressNotification($daysSinceLast, false));
                }
                
                // Send CC to Dosen(s)
                foreach ($mahasiswa->dosens as $dosen) {
                    if ($dosen->user) {
                        $dosen->user->notify(new AcademicProgressNotification($daysSinceLast, true, $mahasiswa->nama_lengkap));
                    }
                }
                
                $this->info("Reminder sent to Mahasiswa NIM {$mahasiswa->nim} and CC'd Dosen.");
            }
        }

        $this->info('Academic progress check completed.');
    }
}
