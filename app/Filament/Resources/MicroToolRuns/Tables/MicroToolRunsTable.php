<?php

namespace App\Filament\Resources\MicroToolRuns\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MicroToolRunsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('tool.slug')
                    ->label('Tool')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                IconColumn::make('is_guest')
                    ->boolean()
                    ->label('Guest'),
                IconColumn::make('is_paid_user')
                    ->boolean()
                    ->label('Paid'),
                TextColumn::make('user.email')
                    ->label('User')
                    ->toggleable(),
                TextColumn::make('duration_ms')
                    ->label('ms')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'success' => 'Success',
                        'failed' => 'Failed',
                        'blocked' => 'Blocked',
                        'rate_limited' => 'Rate limited',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->defaultSort('id', 'desc');
    }
}
