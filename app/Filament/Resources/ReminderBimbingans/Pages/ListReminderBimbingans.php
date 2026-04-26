<?php

namespace App\Filament\Resources\ReminderBimbingans\Pages;

use App\Filament\Resources\ReminderBimbingans\ReminderBimbinganResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListReminderBimbingans extends ListRecords
{
    protected static string $resource = ReminderBimbinganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
