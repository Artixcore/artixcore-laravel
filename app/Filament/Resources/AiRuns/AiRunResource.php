<?php

namespace App\Filament\Resources\AiRuns;

use App\Filament\Resources\AiRuns\Pages\CreateAiRun;
use App\Filament\Resources\AiRuns\Pages\EditAiRun;
use App\Filament\Resources\AiRuns\Pages\ListAiRuns;
use App\Filament\Resources\AiRuns\Schemas\AiRunForm;
use App\Filament\Resources\AiRuns\Tables\AiRunsTable;
use App\Models\AiRun;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AiRunResource extends Resource
{
    protected static ?string $model = AiRun::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPlay;

    protected static string|UnitEnum|null $navigationGroup = 'AI & automation';

    protected static ?int $navigationSort = 22;

    public static function form(Schema $schema): Schema
    {
        return AiRunForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AiRunsTable::configure($table);
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
            'index' => ListAiRuns::route('/'),
            'create' => CreateAiRun::route('/create'),
            'edit' => EditAiRun::route('/{record}/edit'),
        ];
    }
}
