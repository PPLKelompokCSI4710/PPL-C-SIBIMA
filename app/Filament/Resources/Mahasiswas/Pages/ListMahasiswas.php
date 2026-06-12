<?php

namespace App\Filament\Resources\Mahasiswas\Pages;

use App\Filament\Imports\MahasiswaImporter;
use App\Filament\Resources\Mahasiswas\MahasiswaResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListMahasiswas extends ListRecords
{
    protected static string $resource = MahasiswaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()
                ->importer(MahasiswaImporter::class)
                ->label('Import CSV')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('info')
                ->maxRows(1000)
                ->chunkSize(100)
                ->registerModalActions([
                    Action::make('downloadExample')
                        ->label(__('filament-actions::import.modal.actions.download_example.label'))
                        ->link()
                        ->url(route('import.template.download', 'mahasiswa'))
                        ->openUrlInNewTab(),
                ]),
            CreateAction::make()
                ->label('Tambah Mahasiswa'),
        ];
    }
}
