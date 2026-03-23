<?php

namespace App\Filament\Resources\MicroTools;

use App\Filament\Resources\MicroTools\Pages\CreateMicroTool;
use App\Filament\Resources\MicroTools\Pages\EditMicroTool;
use App\Filament\Resources\MicroTools\Pages\ListMicroTools;
use App\Filament\Resources\MicroTools\Schemas\MicroToolForm;
use App\Filament\Resources\MicroTools\Tables\MicroToolsTable;
use App\Models\MicroTool;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MicroToolResource extends Resource
{
    protected static ?string $model = MicroTool::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    protected static string|\UnitEnum|null $navigationGroup = 'Tools';

    public static function form(Schema $schema): Schema
    {
        return MicroToolForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MicroToolsTable::configure($table);
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
            'index' => ListMicroTools::route('/'),
            'create' => CreateMicroTool::route('/create'),
            'edit' => EditMicroTool::route('/{record}/edit'),
        ];
    }
}
