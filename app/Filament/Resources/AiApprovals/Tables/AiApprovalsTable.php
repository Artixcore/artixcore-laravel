<?php

namespace App\Filament\Resources\AiApprovals\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AiApprovalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('status')->badge(),
                TextColumn::make('run.correlation_id')->label('Run')->toggleable(),
                TextColumn::make('reviewer.name')->label('Reviewer'),
                TextColumn::make('resolved_at')->dateTime()->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
