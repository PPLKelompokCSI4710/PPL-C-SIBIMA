<?php

namespace App\Console\Commands;

use App\Models\Mahasiswa;
use Illuminate\Console\Command;

class CleanOrphanedUsers extends Command
{
    protected $signature = 'mahasiswa:clean-orphaned-users';

    protected $description = 'Hapus akun User yang Mahasiswanya sudah di-soft-delete (agar import ulang bisa dilakukan)';

    public function handle(): int
    {
        $trashed = Mahasiswa::onlyTrashed()->with('user')->get();

        $count = 0;
        foreach ($trashed as $mahasiswa) {
            if ($mahasiswa->user) {
                $mahasiswa->user->delete();
                $count++;
                $this->line("Deleted user: {$mahasiswa->user->email}");
            }
        }

        $this->info("Selesai. {$count} akun user dihapus.");

        return self::SUCCESS;
    }
}
