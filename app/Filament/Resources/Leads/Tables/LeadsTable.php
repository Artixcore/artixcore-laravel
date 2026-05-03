<?php

namespace App\Filament\Resources\Leads\Tables;

use App\Models\Lead;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LeadsTable
{
    public static function configure(Table $table): Table
    {
        $statusOptions = collect(Lead::statuses())
            ->mapWithKeys(fn (string $s): array => [$s => ucfirst($s)])
            ->all();

        $serviceOptions = collect(Lead::SERVICE_TYPES)
            ->mapWithKeys(fn (string $s): array => [$s => $s])
            ->all();

        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('phone')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('service_type')
                    ->label('Service')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('submitted_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options($statusOptions),
                SelectFilter::make('service_type')
                    ->label('Service type')
                    ->options($serviceOptions),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
