<?php

namespace App\Filament\Resources\Dosens\Pages;

use App\Filament\Imports\DosenImporter;
use App\Filament\Resources\Dosens\DosenResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListDosens extends ListRecords
{
    protected static string $resource = DosenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()
                ->importer(DosenImporter::class)
                ->label('Import CSV')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('info')
                ->maxRows(1000)
                ->chunkSize(100)
                ->registerModalActions([
                    Action::make('downloadExample')
                        ->label(__('filament-actions::import.modal.actions.download_example.label'))
                        ->link()
                        ->url(route('import.template.download', 'dosen'))
                        ->openUrlInNewTab(),
                ]),
            CreateAction::make(),
        ];
    }
}
