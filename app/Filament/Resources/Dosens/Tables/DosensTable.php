<?php

namespace App\Filament\Resources\Dosens\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DosensTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // ─── Penomoran ────────────────────────────────────────────────
                TextColumn::make('index')
                    ->label('#')
                    ->rowIndex(),

                // ─── Identitas ────────────────────────────────────────────────
                TextColumn::make('name')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                // ─── Akademik ─────────────────────────────────────────────────
                TextColumn::make('program_studi')
                    ->label('Prodi')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('fakultas')
                    ->label('Unit Kerja')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('kuota_pembimbingan')
                    ->label('Kapasitas')
                    ->numeric()
                    ->alignCenter()
                    ->sortable(),
            ])
            ->filters([
                // Filter tambahan jika dibutuhkan
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
