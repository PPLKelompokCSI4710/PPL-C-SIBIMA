<?php

namespace App\Filament\Resources\BimbinganRelation\Tables;

use App\Models\Mahasiswa;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BimbinganRelationTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // ─── Penomoran ────────────────────────────────────────────
                TextColumn::make('index')
                    ->label('#')
                    ->rowIndex(),

                // ─── Identitas Mahasiswa ──────────────────────────────────
                TextColumn::make('nim')
                    ->label('NIM')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight(FontWeight::Medium),

                TextColumn::make('nama_lengkap')
                    ->label('Nama Mahasiswa')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('program_studi')
                    ->label('Program Studi')
                    ->toggleable(),

                TextColumn::make('angkatan')
                    ->label('Angkatan')
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                // ─── Dosen Pembimbing ─────────────────────────────────────
                TextColumn::make('dosen.nama_lengkap')
                    ->label('Dosen Pembimbing')
                    ->searchable()
                    ->sortable()
                    ->default('Belum Ditetapkan')
                    ->icon('heroicon-o-user')
                    ->badge()
                    ->color(fn (?string $state): string => $state ? 'success' : 'danger'),
            ])
            ->filters([
                // Filter berdasarkan Program Studi
                SelectFilter::make('program_studi')
                    ->label('Program Studi')
                    ->options(
                        fn () => Mahasiswa::query()
                            ->distinct()
                            ->pluck('program_studi', 'program_studi')
                            ->toArray()
                    )
                    ->searchable()
                    ->preload(),

                // Filter mahasiswa yang belum memiliki pembimbing
                Filter::make('belum_ada_pembimbing')
                    ->label('Belum Ada Pembimbing')
                    ->query(fn ($query) => $query->whereNull('dosen_id'))
                    ->toggle(),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Tetapkan'),

                Action::make('putus_relasi')
                    ->label('Hapus Relasi')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Relasi Dosen Pembimbing')
                    ->modalDescription('Aksi ini hanya akan mengosongkan dosen pembimbing pada mahasiswa ini. Data mahasiswa tidak akan dihapus.')
                    ->modalSubmitActionLabel('Ya, Hapus Relasi')
                    ->action(fn ($record) => $record->update(['dosen_id' => null]))
                    ->visible(fn ($record): bool => $record->dosen_id !== null),
            ])
            ->toolbarActions([
                BulkActionGroup::make([]),
            ])
            ->defaultSort('nama_lengkap', 'asc')
            ->striped();
    }
}
