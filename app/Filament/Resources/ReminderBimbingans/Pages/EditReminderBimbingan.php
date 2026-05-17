<?php

namespace App\Filament\Resources\ReminderBimbingans\Pages;

use App\Filament\Resources\ReminderBimbingans\ReminderBimbinganResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditReminderBimbingan extends EditRecord
{
    protected static string $resource = ReminderBimbinganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
