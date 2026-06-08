<?php

namespace App\Filament\Resources\JadwalBimbingans\Schemas;

use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class JadwalBimbinganForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('dosen_id')
                    ->label('Dosen')
                    ->options(User::role('dosen')->pluck('name', 'id'))
                    ->required(),
                Select::make('mahasiswa_id')
                    ->label('Mahasiswa')
                    ->options(User::role('mahasiswa')->pluck('name', 'id'))
                    ->required(),
                DatePicker::make('tanggal')
                    ->required(),
                TimePicker::make('waktu')
                    ->required(),
                TextInput::make('topik_bimbingan')
                    ->required()
                    ->maxLength(255),
                Select::make('tipe')
                    ->options([
                        'online' => 'Online',
                        'offline' => 'Offline',
                    ])
                    ->required(),
                Select::make('status')
                    ->options([
                        'menunggu' => 'Menunggu',
                        'disetujui' => 'Disetujui',
                        'ditolak' => 'Ditolak',
                    ])
                    ->default('menunggu')
                    ->required(),
            ]);
    }
}
