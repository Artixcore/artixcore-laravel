<?php

namespace App\Filament\Resources\MicroTools\RelationManagers;

use App\Models\MicroToolAccessPlan;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AccessPlansRelationManager extends RelationManager
{
    protected static string $relationship = 'accessPlans';

    protected static bool $shouldSkipAuthorization = true;

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('plan_type')
                ->options([
                    MicroToolAccessPlan::PLAN_GUEST => 'Guest',
                    MicroToolAccessPlan::PLAN_FREE => 'Free',
                    MicroToolAccessPlan::PLAN_REGISTERED => 'Registered',
                    MicroToolAccessPlan::PLAN_PREMIUM => 'Premium',
                    MicroToolAccessPlan::PLAN_ENTERPRISE => 'Enterprise',
                ])
                ->required(),
            TextInput::make('usage_limit_daily')
                ->numeric()
                ->minValue(0),
            TextInput::make('usage_limit_monthly')
                ->numeric()
                ->minValue(0),
            Toggle::make('ads_enabled')
                ->default(true),
            Toggle::make('export_enabled')
                ->default(true),
            Toggle::make('saved_history_enabled')
                ->default(true),
            Toggle::make('priority_queue_enabled')
                ->default(false),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('plan_type')
                    ->badge(),
                TextColumn::make('usage_limit_daily'),
                TextColumn::make('usage_limit_monthly'),
                IconColumn::make('ads_enabled')
                    ->boolean(),
                IconColumn::make('export_enabled')
                    ->boolean(),
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
