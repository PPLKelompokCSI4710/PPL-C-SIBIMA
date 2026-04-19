<?php

namespace App\Filament\Resources\JadwalBimbingans\Schemas;

use Filament\Schemas\Schema;

class JadwalBimbinganForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Select::make('dosen_id')
                    ->label('Dosen')
                    ->options(\App\Models\User::role('dosen')->pluck('name', 'id'))
                    ->required(),
                \Filament\Forms\Components\Select::make('mahasiswa_id')
                    ->label('Mahasiswa')
                    ->options(\App\Models\User::role('mahasiswa')->pluck('name', 'id'))
                    ->required(),
                \Filament\Forms\Components\DatePicker::make('tanggal')
                    ->required(),
                \Filament\Forms\Components\TimePicker::make('waktu')
                    ->required(),
                \Filament\Forms\Components\TextInput::make('topik_bimbingan')
                    ->required()
                    ->maxLength(255),
                \Filament\Forms\Components\Select::make('tipe')
                    ->options([
                        'online' => 'Online',
                        'offline' => 'Offline',
                    ])
                    ->required(),
                \Filament\Forms\Components\Select::make('status')
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
