<?php

namespace App\Filament\Resources\AiAgents\Schemas;

use App\Filament\Support\JsonTextarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AiAgentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->required()->maxLength(255),
                TextInput::make('slug')->required()->unique(ignoreRecord: true)->maxLength(255),
                Textarea::make('instructions')->rows(8)->columnSpanFull(),
                TextInput::make('model_id')->maxLength(255),
                Select::make('status')
                    ->options([
                        'active' => 'Active',
                        'disabled' => 'Disabled',
                    ])
                    ->required()
                    ->default('active'),
                JsonTextarea::make('tools_allowed', 'Tools allowed (JSON)'),
            ]);
    }
}
