<?php

namespace App\Filament\Resources\AiAgents;

use App\Filament\Resources\AiAgents\Pages\CreateAiAgent;
use App\Filament\Resources\AiAgents\Pages\EditAiAgent;
use App\Filament\Resources\AiAgents\Pages\ListAiAgents;
use App\Filament\Resources\AiAgents\Schemas\AiAgentForm;
use App\Filament\Resources\AiAgents\Tables\AiAgentsTable;
use App\Models\AiAgent;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AiAgentResource extends Resource
{
    protected static ?string $model = AiAgent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCpuChip;

    protected static string|UnitEnum|null $navigationGroup = 'AI & automation';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return AiAgentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AiAgentsTable::configure($table);
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
            'index' => ListAiAgents::route('/'),
            'create' => CreateAiAgent::route('/create'),
            'edit' => EditAiAgent::route('/{record}/edit'),
        ];
    }
}
