<?php

namespace App\Filament\Resources\Mahasiswas\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BimbingansRelationManager extends RelationManager
{
    protected static string $relationship = 'bimbingans';

    protected static ?string $title = 'Riwayat Bimbingan';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('topik')
            ->columns([
                TextColumn::make('waktu_mulai')
                    ->label('Waktu Mulai')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),

                TextColumn::make('waktu_selesai')
                    ->label('Waktu Selesai')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('topik')
                    ->label('Topik')
                    ->searchable()
                    ->limit(40),

                TextColumn::make('dosen.nama_lengkap')
                    ->label('Dosen Pembimbing')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tipe_pertemuan')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'online' => 'info',
                        'offline' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'menunggu' => 'warning',
                        'disetujui' => 'success',
                        'ditolak' => 'danger',
                        'selesai' => 'primary',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('waktu_mulai', 'desc');
    }
}
