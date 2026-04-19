<?php

namespace App\Filament\Resources\JadwalBimbingans\Pages;

use App\Filament\Resources\JadwalBimbingans\JadwalBimbinganResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListJadwalBimbingans extends ListRecords
{
    protected static string $resource = JadwalBimbinganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
