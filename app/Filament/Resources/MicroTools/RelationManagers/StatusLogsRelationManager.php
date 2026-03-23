<?php

namespace App\Filament\Resources\MicroTools\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StatusLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'statusLogs';

    protected static bool $shouldSkipAuthorization = true;

    public function isReadOnly(): bool
    {
        return true;
    }

    public function form(Schema $schema): Schema
    {
        return $schema;
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('action')
                    ->badge(),
                TextColumn::make('old_value')
                    ->limit(40)
                    ->wrap(),
                TextColumn::make('new_value')
                    ->limit(40)
                    ->wrap(),
                TextColumn::make('changedByUser.name')
                    ->label('By')
                    ->placeholder('—'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
