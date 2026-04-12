<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN      = 'admin';
    case DOSEN      = 'dosen';
    case MAHASISWA  = 'mahasiswa';

    public function label(): string
    {
        return match($this) {
            self::ADMIN     => 'Administrator',
            self::DOSEN     => 'Dosen Pembimbing',
            self::MAHASISWA => 'Mahasiswa',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::ADMIN     => 'danger',
            self::DOSEN     => 'warning',
            self::MAHASISWA => 'success',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
