<?php

namespace App\Filament\Resources\BimbinganRelation\Tables;

use App\Models\Dosen;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
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
                    ->searchable()
                    ->sortable()
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
                    ->placeholder('Belum ditetapkan')
                    ->icon('heroicon-o-user')
                    ->badge()
                    ->color(fn (?string $state): string => $state ? 'success' : 'danger'),
            ])
            ->filters([
                // Filter berdasarkan Dosen Pembimbing
                SelectFilter::make('dosen_id')
                    ->label('Dosen Pembimbing')
                    ->relationship(
                        name: 'dosen',
                        titleAttribute: 'nama_lengkap',
                        modifyQueryUsing: fn ($query) => $query->where('is_active', true),
                    )
                    ->searchable()
                    ->preload(),

                // Filter mahasiswa tanpa pembimbing
                TernaryFilter::make('has_dosen')
                    ->label('Status Penetapan')
                    ->placeholder('Semua')
                    ->trueLabel('Sudah ditetapkan')
                    ->falseLabel('Belum ditetapkan')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('dosen_id'),
                        false: fn ($query) => $query->whereNull('dosen_id'),
                    ),
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
