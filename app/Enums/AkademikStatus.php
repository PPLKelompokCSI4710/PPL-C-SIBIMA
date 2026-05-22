<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum AkademikStatus: string implements HasColor, HasLabel
{
    case AKTIF = 'aktif';
    case CUTI = 'cuti';
    case LULUS = 'lulus';
    case DO = 'drop_out';
    case MENGULANG = 'mengulang';

    /**
     * Label yang ditampilkan di UI Filament (form, table badge, filter, dsb.)
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::AKTIF => 'Aktif',
            self::CUTI => 'Cuti',
            self::LULUS => 'Lulus',
            self::DO => 'Drop Out',
            self::MENGULANG => 'Mengulang',
        };
    }

    /**
     * Warna badge yang digunakan oleh Filament Table & Form.
     */
    public function getColor(): string
    {
        return match ($this) {
            self::AKTIF => 'success',
            self::CUTI => 'warning',
            self::LULUS => 'info',
            self::DO => 'danger',
            self::MENGULANG => 'gray',
        };
    }

    /**
     * Helper untuk keperluan non-Filament (misal: seeder, test).
     */
    public function label(): string
    {
        return $this->getLabel();
    }

    /**
     * Helper untuk keperluan non-Filament.
     */
    public function color(): string
    {
        return $this->getColor();
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
