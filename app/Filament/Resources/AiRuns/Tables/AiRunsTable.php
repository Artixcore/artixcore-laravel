<?php

namespace App\Filament\Resources\AiRuns\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AiRunsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('correlation_id')->copyable()->searchable(),
                TextColumn::make('status')->badge(),
                TextColumn::make('workflow.name')->label('Workflow'),
                TextColumn::make('agent.name')->label('Agent'),
                TextColumn::make('created_at')->dateTime()->sortable(),
                TextColumn::make('finished_at')->dateTime()->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
