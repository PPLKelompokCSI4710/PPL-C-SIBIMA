<?php

namespace App\Enums;

enum AkademikStatus: string
{
    case AKTIF    = 'aktif';
    case CUTI     = 'cuti';
    case LULUS    = 'lulus';
    case DO       = 'drop_out';
    case MENGULANG = 'mengulang';

    public function label(): string
    {
        return match($this) {
            self::AKTIF     => 'Aktif',
            self::CUTI      => 'Cuti',
            self::LULUS     => 'Lulus',
            self::DO        => 'Drop Out',
            self::MENGULANG => 'Mengulang',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::AKTIF     => 'success',
            self::CUTI      => 'warning',
            self::LULUS     => 'info',
            self::DO        => 'danger',
            self::MENGULANG => 'gray',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
