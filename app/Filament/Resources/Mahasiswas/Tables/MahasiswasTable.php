<?php

namespace App\Filament\Resources\Mahasiswas\Tables;

use App\Enums\AkademikStatus;
use App\Models\Mahasiswa;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class MahasiswasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // ─── Foto ─────────────────────────────────────────────────────
                ImageColumn::make('foto')
                    ->label('Foto')
                    ->circular()
                    ->defaultImageUrl(fn (Mahasiswa $record) => 'https://ui-avatars.com/api/?name='.urlencode($record->nama_lengkap).'&background=random&length=1'),

                // ─── Identitas Utama ──────────────────────────────────────────
                TextColumn::make('nim')
                    ->label('NIM')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight(FontWeight::Medium),

                TextColumn::make('nama_lengkap')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable(),

                // ─── Info Akademik ────────────────────────────────────────────
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

                TextColumn::make('semester')
                    ->label('Semester')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('status_akademik')
                    ->label('Status Akademik')
                    ->badge()
                    ->sortable(),

                // ─── Info Tambahan (default hidden) ───────────────────────────
                IconColumn::make('status_kelulusan_bimbingan')
                    ->label('Lulus Bimbingan')
                    ->boolean()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('user.email')
                    ->label('Akun Email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Terakhir Diperbarui')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Filter berdasarkan Status Akademik
                SelectFilter::make('status_akademik')
                    ->label('Status Akademik')
                    ->options(AkademikStatus::class)
                    ->native()
                    ->preload()
                    ->modifyFormFieldUsing(fn ($field) => $field->extraInputAttributes([
                        'dusk' => 'status-akademik',
                    ])),

                // Filter berdasarkan Angkatan
                SelectFilter::make('angkatan')
                    ->label('Angkatan')
                    ->options(
                        fn () => Mahasiswa::query()
                            ->distinct()
                            ->orderBy('angkatan', 'desc')
                            ->pluck('angkatan', 'angkatan')
                            ->toArray()
                    )
                    ->multiple(),

                // Filter tampilkan yang sudah di-soft delete
                TrashedFilter::make(),
            ], FiltersLayout::Modal)
            ->filtersTriggerAction(fn (Action $action) => $action->extraAttributes([
                'dusk' => 'filter-button',
            ]))
            ->filtersApplyAction(fn (Action $action) => $action->extraAttributes([
                'dusk' => 'apply-filter',
            ]))
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->after(function (Collection $records): void {
                            $records->each(function (Mahasiswa $record): void {
                                $record->user?->delete();
                            });
                        }),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make()
                        ->after(function (Collection $records): void {
                            $records->each(function (Mahasiswa $record): void {
                                $record->user?->forceDelete();
                            });
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped();
    }
}
