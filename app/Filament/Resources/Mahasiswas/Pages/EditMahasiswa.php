<?php

namespace App\Filament\Resources\Mahasiswas\Pages;

use App\Filament\Resources\Mahasiswas\MahasiswaResource;
use App\Models\Mahasiswa;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditMahasiswa extends EditRecord
{
    protected static string $resource = MahasiswaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make()
                ->after(function (Mahasiswa $record): void {
                    $record->user?->delete();
                }),
            ForceDeleteAction::make()
                ->after(function (Mahasiswa $record): void {
                    $record->user?->forceDelete();
                }),
            RestoreAction::make(),
        ];
    }
}
