<?php

namespace App\Filament\Resources\AiWorkflows\Schemas;

use App\Filament\Support\JsonTextarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AiWorkflowForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->required()->maxLength(255),
                TextInput::make('slug')->required()->unique(ignoreRecord: true)->maxLength(255),
                Textarea::make('description')->rows(4)->columnSpanFull(),
                Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'active' => 'Active',
                        'archived' => 'Archived',
                    ])
                    ->required()
                    ->default('draft'),
                JsonTextarea::make('config', 'Workflow config (JSON)'),
            ]);
    }
}
