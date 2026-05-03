<?php

namespace App\Filament\Resources\CaseStudies\Schemas;

use App\Models\CaseStudy;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CaseStudyForm
{
    public static function configure(Schema $schema): Schema
    {
        $jsonArray = static function (string $field, string $label): Textarea {
            return Textarea::make($field)
                ->label($label)
                ->columnSpanFull()
                ->rows(5)
                ->helperText('JSON array (e.g. `["Laravel","Redis"]`). Leave empty for none.')
                ->formatStateUsing(fn ($state): string => is_array($state)
                    ? (string) json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
                    : (string) ($state ?? ''))
                ->dehydrateStateUsing(function ($state): ?array {
                    $raw = trim((string) $state);
                    if ($raw === '') {
                        return null;
                    }
                    $decoded = json_decode($raw, true);

                    return is_array($decoded) ? $decoded : null;
                });
        };

        return $schema
            ->components([
                TextInput::make('slug')
                    ->required()
                    ->disabled(fn (?CaseStudy $record): bool => (bool) ($record?->slug_locked)),
                TextInput::make('title')
                    ->required(),
                Select::make('case_study_type')
                    ->label('Case study type')
                    ->options([
                        CaseStudy::TYPE_CONCEPT => 'Concept',
                        CaseStudy::TYPE_ANONYMIZED => 'Anonymized',
                        CaseStudy::TYPE_REAL => 'Real client',
                    ])
                    ->required()
                    ->default(CaseStudy::TYPE_CONCEPT),
                Toggle::make('client_verified')
                    ->label('Client verified')
                    ->default(false),
                TextInput::make('client_name'),
                TextInput::make('client_display_name'),
                TextInput::make('industry'),
                TextInput::make('project_type'),
                Textarea::make('summary')
                    ->label('Excerpt / summary')
                    ->columnSpanFull()
                    ->rows(3),
                Textarea::make('body')
                    ->columnSpanFull()
                    ->rows(12),
                Textarea::make('challenge')
                    ->columnSpanFull()
                    ->rows(5),
                Textarea::make('solution')
                    ->columnSpanFull()
                    ->rows(5),
                Textarea::make('implementation')
                    ->columnSpanFull()
                    ->rows(5),
                Textarea::make('lessons_learned')
                    ->columnSpanFull()
                    ->rows(4),
                $jsonArray('technology_stack', 'Technology stack (JSON array)'),
                $jsonArray('outcomes', 'Outcomes (JSON array of strings)'),
                $jsonArray('metrics', 'Metrics (JSON object or array)'),
                $jsonArray('gallery_paths', 'Gallery paths (JSON array of paths/URLs)'),
                TextInput::make('video_url')
                    ->maxLength(2048),
                FileUpload::make('main_image_path')
                    ->label('Main image')
                    ->image()
                    ->disk(config('media-library.disk_name', 'public'))
                    ->directory('case-studies/main')
                    ->visibility('public'),
                TextInput::make('reading_time_minutes')
                    ->numeric()
                    ->nullable(),
                Select::make('author_type')
                    ->options([
                        CaseStudy::AUTHOR_TYPE_AI => 'AI',
                        CaseStudy::AUTHOR_TYPE_HUMAN => 'Human',
                    ])
                    ->required()
                    ->default(CaseStudy::AUTHOR_TYPE_AI),
                TextInput::make('author_name')
                    ->default(fn (): string => (string) config('ai_content.author_name', config('ai_articles.author_name', 'Ali 1.0'))),
                TextInput::make('ai_model'),
                Select::make('status')
                    ->options([
                        CaseStudy::STATUS_DRAFT => 'Draft',
                        CaseStudy::STATUS_PENDING_REVIEW => 'Pending review',
                        CaseStudy::STATUS_SCHEDULED => 'Scheduled',
                        CaseStudy::STATUS_PUBLISHED => 'Published',
                        CaseStudy::STATUS_ARCHIVED => 'Archived',
                    ])
                    ->required()
                    ->default(CaseStudy::STATUS_DRAFT),
                Toggle::make('featured')
                    ->default(false),
                Toggle::make('slug_locked')
                    ->label('Lock slug')
                    ->default(false),
                Toggle::make('review_required')
                    ->default(false),
                TextInput::make('trending_score')
                    ->numeric()
                    ->default(0),
                DateTimePicker::make('scheduled_for'),
                DateTimePicker::make('published_at'),
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
                Textarea::make('fact_check_notes')
                    ->columnSpanFull()
                    ->rows(3),
                Textarea::make('source_topic')
                    ->columnSpanFull()
                    ->rows(2),
                Textarea::make('ai_prompt')
                    ->columnSpanFull()
                    ->rows(6)
                    ->disabled()
                    ->visible(fn (?CaseStudy $record): bool => filled($record?->ai_prompt)),
                Select::make('terms')
                    ->relationship('terms', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
            ]);
    }
}
