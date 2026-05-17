<?php

namespace App\Filament\Resources\ReminderBimbingans;

use App\Filament\Resources\ReminderBimbingans\Pages\CreateReminderBimbingan;
use App\Filament\Resources\ReminderBimbingans\Pages\EditReminderBimbingan;
use App\Filament\Resources\ReminderBimbingans\Pages\ListReminderBimbingans;
use App\Filament\Resources\ReminderBimbingans\Schemas\ReminderBimbinganForm;
use App\Filament\Resources\ReminderBimbingans\Tables\ReminderBimbingansTable;
use App\Models\Bimbingan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ReminderBimbinganResource extends Resource
{
    protected static ?string $model = Bimbingan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'resource';

    public static function form(Schema $schema): Schema
    {
        return ReminderBimbinganForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReminderBimbingansTable::configure($table);
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
            'index' => ListReminderBimbingans::route('/'),
            'create' => CreateReminderBimbingan::route('/create'),
            'edit' => EditReminderBimbingan::route('/{record}/edit'),
        ];
    }
}
