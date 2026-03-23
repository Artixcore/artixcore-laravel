<?php

namespace App\Filament\Resources\MicroTools\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SettingsRelationManager extends RelationManager
{
    protected static string $relationship = 'settings';

    protected static bool $shouldSkipAuthorization = true;

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('key')
                ->required()
                ->maxLength(128),
            TextInput::make('value')
                ->maxLength(65535),
            TextInput::make('type')
                ->maxLength(32),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('key')
            ->columns([
                TextColumn::make('key')
                    ->searchable(),
                TextColumn::make('value')
                    ->limit(60),
                TextColumn::make('type'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
