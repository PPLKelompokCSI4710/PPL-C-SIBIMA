<?php

namespace App\Filament\Resources\JadwalBimbingans;

use App\Filament\Resources\JadwalBimbingans\Pages\CreateJadwalBimbingan;
use App\Filament\Resources\JadwalBimbingans\Pages\ListJadwalBimbingans;
use App\Filament\Resources\JadwalBimbingans\Schemas\JadwalBimbinganForm;
use App\Filament\Resources\JadwalBimbingans\Tables\JadwalBimbingansTable;
use App\Models\JadwalBimbingan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class JadwalBimbinganResource extends Resource
{
    protected static ?string $model = JadwalBimbingan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return JadwalBimbinganForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return JadwalBimbingansTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListJadwalBimbingans::route('/'),
            'create' => CreateJadwalBimbingan::route('/create'),
        ];
    }
}
