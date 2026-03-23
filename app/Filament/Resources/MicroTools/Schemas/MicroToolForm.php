<?php

namespace App\Filament\Resources\MicroTools\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class MicroToolForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                TextInput::make('category')
                    ->required()
                    ->maxLength(64),
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull()
                    ->rows(4),
                TextInput::make('icon_key')
                    ->maxLength(255),
                Select::make('execution_mode')
                    ->options([
                        'client' => 'Client',
                        'server' => 'Server',
                    ])
                    ->required(),
                Textarea::make('limits')
                    ->label('Limits (JSON object)')
                    ->helperText('Optional: {"guest_per_minute":20,"auth_per_minute":100}')
                    ->columnSpanFull()
                    ->rows(3)
                    ->formatStateUsing(fn ($state): string => is_array($state) && $state !== []
                        ? (string) json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
                        : '')
                    ->dehydrateStateUsing(function (?string $state): ?array {
                        if ($state === null || trim($state) === '') {
                            return null;
                        }
                        $decoded = json_decode($state, true);

                        return is_array($decoded) ? $decoded : null;
                    }),
                Textarea::make('input_schema')
                    ->label('Input schema (JSON)')
                    ->columnSpanFull()
                    ->rows(6)
                    ->formatStateUsing(fn ($state): string => is_array($state) && $state !== []
                        ? (string) json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
                        : '')
                    ->dehydrateStateUsing(function (?string $state): ?array {
                        if ($state === null || trim($state) === '') {
                            return null;
                        }
                        $decoded = json_decode($state, true);

                        return is_array($decoded) ? $decoded : null;
                    }),
                Toggle::make('is_active')
                    ->default(true),
                Toggle::make('is_premium')
                    ->default(false),
                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0),
                DateTimePicker::make('released_at'),
                TextInput::make('featured_score')
                    ->numeric()
                    ->default(0),
                Toggle::make('is_popular')
                    ->default(false),
                Toggle::make('is_new')
                    ->default(false),
            ]);
    }
}
