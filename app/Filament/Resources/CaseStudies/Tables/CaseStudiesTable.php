<?php

namespace App\Filament\Resources\CaseStudies\Tables;

use App\Models\CaseStudy;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CaseStudiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('slug')
                    ->searchable(),
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('case_study_type')
                    ->label('Type')
                    ->searchable(),
                TextColumn::make('client_name')
                    ->searchable(),
                TextColumn::make('industry')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('summary')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('meta_title')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('author_name')
                    ->toggleable(),
                TextColumn::make('status')
                    ->searchable(),
                IconColumn::make('featured')
                    ->boolean(),
                TextColumn::make('view_count')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('trending_score')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        CaseStudy::STATUS_DRAFT => 'Draft',
                        CaseStudy::STATUS_PENDING_REVIEW => 'Pending review',
                        CaseStudy::STATUS_SCHEDULED => 'Scheduled',
                        CaseStudy::STATUS_PUBLISHED => 'Published',
                        CaseStudy::STATUS_ARCHIVED => 'Archived',
                    ]),
                SelectFilter::make('case_study_type')
                    ->label('Type')
                    ->options([
                        CaseStudy::TYPE_CONCEPT => 'Concept',
                        CaseStudy::TYPE_ANONYMIZED => 'Anonymized',
                        CaseStudy::TYPE_REAL => 'Real client',
                    ]),
                SelectFilter::make('author_type')
                    ->options([
                        CaseStudy::AUTHOR_TYPE_AI => 'AI',
                        CaseStudy::AUTHOR_TYPE_HUMAN => 'Human',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('updated_at', 'desc');
    }
}
