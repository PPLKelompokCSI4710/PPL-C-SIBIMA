<?php

namespace App\Filament\Resources\Mahasiswas\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DosensRelationManager extends RelationManager
{
    protected static string $relationship = 'dosens';

    protected static ?string $title = 'Dosen Pembimbing';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama_lengkap')
            ->columns([
                TextColumn::make('nidn')
                    ->label('NIDN')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nama_lengkap')
                    ->label('Nama Dosen')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('program_studi')
                    ->label('Program Studi')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('tanggal_penugasan')
                    ->label('Tanggal Penugasan')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->defaultSort('tanggal_penugasan', 'desc');
    }
}
