<?php

namespace App\Filament\Resources\BimbinganRelation\Pages;

use App\Filament\Resources\BimbinganRelation\BimbinganRelationResource;
use Filament\Resources\Pages\EditRecord;

class EditBimbinganRelation extends EditRecord
{
    protected static string $resource = BimbinganRelationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
