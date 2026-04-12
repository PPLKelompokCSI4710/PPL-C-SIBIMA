<?php

namespace App\Enums;

enum BookingStatus: string
{
    case PENDING   = 'pending';
    case APPROVED  = 'approved';
    case REJECTED  = 'rejected';
    case CANCELLED = 'cancelled';
    case COMPLETED = 'completed';
    case RESCHEDULED = 'rescheduled';

    public function label(): string
    {
        return match($this) {
            self::PENDING     => 'Menunggu Konfirmasi',
            self::APPROVED    => 'Disetujui',
            self::REJECTED    => 'Ditolak',
            self::CANCELLED   => 'Dibatalkan',
            self::COMPLETED   => 'Selesai',
            self::RESCHEDULED => 'Dijadwal Ulang',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING     => 'warning',
            self::APPROVED    => 'success',
            self::REJECTED    => 'danger',
            self::CANCELLED   => 'gray',
            self::COMPLETED   => 'info',
            self::RESCHEDULED => 'primary',
        };
    }

    public function canApprove(): bool
    {
        return $this === self::PENDING;
    }

    public function canCancel(): bool
    {
        return in_array($this, [self::PENDING, self::APPROVED]);
    }

    public function canReschedule(): bool
    {
        return in_array($this, [self::PENDING, self::APPROVED]);
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
