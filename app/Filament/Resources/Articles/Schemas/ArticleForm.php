<?php

namespace App\Filament\Resources\Articles\Schemas;

use App\Models\Article;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ArticleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('slug')
                    ->required()
                    ->disabled(fn (?Article $record): bool => (bool) ($record?->slug_locked)),
                TextInput::make('title')
                    ->required()
                    ->live(onBlur: true),
                Textarea::make('summary')
                    ->label('Excerpt / summary')
                    ->rows(3),
                Textarea::make('body')
                    ->columnSpanFull()
                    ->rows(14),
                Select::make('article_type')
                    ->options([
                        'latest_discovery' => 'Latest discovery',
                        'today_trends' => 'Today trends',
                        'latest_tech' => 'Latest tech',
                        'company_update' => 'Company update',
                        'tutorial' => 'Tutorial',
                        'insight' => 'Insight',
                    ]),
                Select::make('author_type')
                    ->options([
                        Article::AUTHOR_TYPE_AI => 'AI',
                        Article::AUTHOR_TYPE_HUMAN => 'Human',
                    ])
                    ->default(Article::AUTHOR_TYPE_AI),
                TextInput::make('author_name')
                    ->default(fn (): string => (string) config('ai_articles.author_name', 'Ali 1.0')),
                Select::make('status')
                    ->options([
                        Article::STATUS_DRAFT => 'Draft',
                        Article::STATUS_PENDING_REVIEW => 'Pending review',
                        Article::STATUS_SCHEDULED => 'Scheduled',
                        Article::STATUS_PUBLISHED => 'Published',
                        Article::STATUS_ARCHIVED => 'Archived',
                    ])
                    ->required()
                    ->default(Article::STATUS_DRAFT),
                Toggle::make('featured')
                    ->default(false),
                Toggle::make('review_required')
                    ->default(false),
                TextInput::make('trending_score')
                    ->numeric()
                    ->default(0),
                DateTimePicker::make('published_at'),
                DateTimePicker::make('scheduled_for'),
                TextInput::make('video_url')
                    ->maxLength(2048),
                FileUpload::make('main_image_path')
                    ->label('Main image')
                    ->image()
                    ->disk(config('media-library.disk_name', 'public'))
                    ->directory('articles/main')
                    ->visibility('public'),
                TextInput::make('meta_title'),
                Textarea::make('meta_description')
                    ->columnSpanFull()
                    ->rows(3),
                TextInput::make('meta_keywords'),
                TextInput::make('canonical_url'),
                TextInput::make('robots')
                    ->default('index,follow'),
                Textarea::make('originality_notes')
                    ->columnSpanFull()
                    ->rows(3),
                TextInput::make('plagiarism_score')
                    ->numeric()
                    ->step(0.01)
                    ->minValue(0)
                    ->maxValue(100),
                Textarea::make('ai_prompt')
                    ->columnSpanFull()
                    ->rows(6)
                    ->disabled()
                    ->visible(fn (?Article $record): bool => filled($record?->ai_prompt)),
                Select::make('terms')
                    ->relationship('terms', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
            ]);
    }
}
