<?php

namespace App\Filament\Resources\AiWorkflows;

use App\Filament\Resources\AiWorkflows\Pages\CreateAiWorkflow;
use App\Filament\Resources\AiWorkflows\Pages\EditAiWorkflow;
use App\Filament\Resources\AiWorkflows\Pages\ListAiWorkflows;
use App\Filament\Resources\AiWorkflows\Schemas\AiWorkflowForm;
use App\Filament\Resources\AiWorkflows\Tables\AiWorkflowsTable;
use App\Models\AiWorkflow;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AiWorkflowResource extends Resource
{
    protected static ?string $model = AiWorkflow::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static string|UnitEnum|null $navigationGroup = 'AI & automation';

    protected static ?int $navigationSort = 21;

    public static function form(Schema $schema): Schema
    {
        return AiWorkflowForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AiWorkflowsTable::configure($table);
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
            'index' => ListAiWorkflows::route('/'),
            'create' => CreateAiWorkflow::route('/create'),
            'edit' => EditAiWorkflow::route('/{record}/edit'),
        ];
    }
}
