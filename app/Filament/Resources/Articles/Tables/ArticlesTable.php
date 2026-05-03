<?php

namespace App\Filament\Resources\Articles\Tables;

use App\Models\Article;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ArticlesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('slug')
                    ->searchable(),
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('author_name')
                    ->toggleable(),
                TextColumn::make('article_type')
                    ->toggleable(),
                TextColumn::make('summary')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('meta_title')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                        Article::STATUS_DRAFT => 'Draft',
                        Article::STATUS_PENDING_REVIEW => 'Pending review',
                        Article::STATUS_SCHEDULED => 'Scheduled',
                        Article::STATUS_PUBLISHED => 'Published',
                        Article::STATUS_ARCHIVED => 'Archived',
                    ]),
                SelectFilter::make('author_type')
                    ->options([
                        Article::AUTHOR_TYPE_AI => 'AI',
                        Article::AUTHOR_TYPE_HUMAN => 'Human',
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
