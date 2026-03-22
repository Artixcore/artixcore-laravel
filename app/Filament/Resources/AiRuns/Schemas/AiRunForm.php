<?php

namespace App\Filament\Resources\AiRuns\Schemas;

use App\Filament\Support\JsonTextarea;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class AiRunForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('ai_workflow_id')
                    ->label('Workflow')
                    ->relationship('workflow', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('ai_agent_id')
                    ->label('Agent')
                    ->relationship('agent', 'name')
                    ->searchable()
                    ->preload(),
                JsonTextarea::make('input', 'Input (JSON)'),
                JsonTextarea::make('output', 'Output (JSON)')
                    ->disabled()
                    ->dehydrated(false)
                    ->visible(fn (string $operation): bool => $operation === 'edit'),
            ]);
    }
}
