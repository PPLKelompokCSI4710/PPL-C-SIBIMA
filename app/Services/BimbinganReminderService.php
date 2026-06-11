<?php

namespace App\Services;

use App\Models\Bimbingan;
use App\Models\BimbinganReminder;
use App\Models\JadwalBimbingan;
use App\Models\ReminderPreference;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BimbinganReminderService
{
    /**
     * Sync reminder records for a bimbingan based on its current schedule.
     */
    public function syncForBimbingan(Bimbingan $bimbingan): void
    {
        // Only create reminders when a schedule is approved and has a start time in the future.
        if (! $bimbingan->waktu_mulai) {
            $this->cancelForBimbingan($bimbingan);

            return;
        }

        if (($bimbingan->status ?? null) !== 'disetujui') {
            $this->cancelForBimbingan($bimbingan);

            return;
        }

        $start = Carbon::parse($bimbingan->waktu_mulai);
        if ($start->isPast()) {
            $this->cancelForBimbingan($bimbingan);

            return;
        }

        $mahasiswaUserId = $bimbingan->mahasiswa?->user_id;
        $dosenUserId = $bimbingan->dosen?->user_id;
        if (! $mahasiswaUserId || ! $dosenUserId) {
            return;
        }

        $payload = [
            'bimbingan_id' => $bimbingan->id,
            'topik' => $bimbingan->topik,
            'waktu_mulai' => $start->toDateTimeString(),
            'lokasi' => $bimbingan->lokasi,
            'tipe_pertemuan' => $bimbingan->tipe_pertemuan,
            'mahasiswa' => $bimbingan->mahasiswa?->nama_lengkap,
            'dosen' => $bimbingan->dosen?->nama_lengkap,
        ];

        $stages = [
            'h3' => $start->copy()->subDays(3),
            'h1' => $start->copy()->subDays(1),
            'h3jam' => $start->copy()->subHours(3),
            'h2' => $start->copy()->subHours(2),
        ];

        DB::transaction(function () use ($bimbingan, $payload, $stages, $mahasiswaUserId, $dosenUserId) {
            // Remove pending rows so updateOrCreate does not resurrect rows we just marked canceled (unique key).
            BimbinganReminder::query()
                ->where('bimbingan_id', $bimbingan->id)
                ->where('status', 'pending')
                ->delete();

            $this->upsertStagesForUser($bimbingan->id, $mahasiswaUserId, $payload, $stages);
            $this->upsertStagesForUser($bimbingan->id, $dosenUserId, $payload, $stages);
        });
    }

    public function syncForJadwalBimbingan(JadwalBimbingan $bimbingan): void
    {
        $ketersediaan = $bimbingan->ketersediaanJadwal;
        
        if (! $ketersediaan || ! $ketersediaan->tanggal || ! $ketersediaan->waktu_mulai) {
            $this->cancelForJadwalBimbingan($bimbingan);
            return;
        }

        if (($bimbingan->status ?? null) !== 'approved') {
            $this->cancelForJadwalBimbingan($bimbingan);
            return;
        }

        $start = Carbon::parse($ketersediaan->tanggal . ' ' . $ketersediaan->waktu_mulai);
        if ($start->isPast()) {
            $this->cancelForJadwalBimbingan($bimbingan);
            return;
        }

        $mahasiswaUserId = $bimbingan->mahasiswa?->user_id;
        $dosenUserId = $bimbingan->dosen?->user_id;
        if (! $mahasiswaUserId || ! $dosenUserId) {
            return;
        }

        $payload = [
            'bimbingan_id' => $bimbingan->id,
            'topik' => $bimbingan->topik_bimbingan,
            'waktu_mulai' => $start->toDateTimeString(),
            'lokasi' => $bimbingan->lokasi,
            'tipe_pertemuan' => $bimbingan->tipe,
            'mahasiswa' => $bimbingan->mahasiswa?->nama_lengkap,
            'dosen' => $bimbingan->dosen?->nama_lengkap,
        ];

        $stages = [
            'h3' => $start->copy()->subDays(3),
            'h1' => $start->copy()->subDays(1),
            'h3jam' => $start->copy()->subHours(3),
            'h2' => $start->copy()->subHours(2),
        ];

        DB::transaction(function () use ($bimbingan, $payload, $stages, $mahasiswaUserId, $dosenUserId) {
            BimbinganReminder::query()
                ->where('bimbingan_id', $bimbingan->id)
                ->where('status', 'pending')
                ->delete();

            $this->upsertStagesForUser($bimbingan->id, $mahasiswaUserId, $payload, $stages);
            $this->upsertStagesForUser($bimbingan->id, $dosenUserId, $payload, $stages);
        });
    }

    public function cancelForBimbingan(Bimbingan $bimbingan): void
    {
        BimbinganReminder::query()
            ->where('bimbingan_id', $bimbingan->id)
            ->where('status', 'pending')
            ->update([
                'status' => 'canceled',
                'canceled_at' => now(),
                'updated_at' => now(),
            ]);
    }

    public function cancelForJadwalBimbingan(JadwalBimbingan $bimbingan): void
    {
        BimbinganReminder::query()
            ->where('bimbingan_id', $bimbingan->id)
            ->where('status', 'pending')
            ->update([
                'status' => 'canceled',
                'canceled_at' => now(),
                'updated_at' => now(),
            ]);
    }

    private function upsertStagesForUser(int $bimbinganId, int $userId, array $payload, array $stages): void
    {
        // Ensure preference row exists (defaults all enabled).
        ReminderPreference::forUser($userId);

        foreach ($stages as $stage => $sendAt) {
            // If stage time already passed, don't create it.
            if ($sendAt->isPast()) {
                continue;
            }

            BimbinganReminder::query()->updateOrCreate(
                [
                    'bimbingan_id' => $bimbinganId,
                    'user_id' => $userId,
                    'stage' => $stage,
                ],
                [
                    'send_at' => $sendAt,
                    'status' => 'pending',
                    'sent_at' => null,
                    'canceled_at' => null,
                    'payload' => $payload,
                ],
            );
        }
    }
}
