<?php

namespace App\Filament\Resources\PortfolioItems\Schemas;

use App\Models\PortfolioItem;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PortfolioItemForm
{
    public static function configure(Schema $schema): Schema
    {
        $techJson = Textarea::make('technology_stack')
            ->label('Technology stack (JSON array)')
            ->columnSpanFull()
            ->rows(4)
            ->helperText('JSON array of strings.')
            ->formatStateUsing(fn ($state): string => is_array($state)
                ? (string) json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                : (string) ($state ?? ''))
            ->dehydrateStateUsing(function ($state): ?array {
                $raw = trim((string) $state);
                if ($raw === '') {
                    return null;
                }
                $decoded = json_decode($raw, true);

                return is_array($decoded) ? $decoded : null;
            });

        return $schema
            ->components([
                TextInput::make('slug')
                    ->maxLength(255),
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                TextInput::make('client_name'),
                TextInput::make('project_type'),
                TextInput::make('industry'),
                Textarea::make('short_description')
                    ->maxLength(500)
                    ->rows(2),
                Textarea::make('body')
                    ->columnSpanFull()
                    ->rows(10),
                Textarea::make('challenge')
                    ->columnSpanFull()
                    ->rows(4),
                Textarea::make('solution')
                    ->columnSpanFull()
                    ->rows(4),
                Textarea::make('outcome')
                    ->columnSpanFull()
                    ->rows(4),
                $techJson,
                TextInput::make('main_image_media_id')
                    ->numeric()
                    ->label('Main image media ID'),
                TextInput::make('video_url')
                    ->label('Video URL (YouTube/Vimeo)')
                    ->maxLength(2048),
                Select::make('status')
                    ->options([
                        PortfolioItem::STATUS_DRAFT => 'Draft',
                        PortfolioItem::STATUS_PUBLISHED => 'Published',
                    ])
                    ->required()
                    ->default(PortfolioItem::STATUS_DRAFT),
                Toggle::make('featured')->default(false),
                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0),
                DateTimePicker::make('published_at'),
                TextInput::make('meta_title'),
                Textarea::make('meta_description')
                    ->columnSpanFull()
                    ->rows(2),
                TextInput::make('meta_keywords'),
                TextInput::make('canonical_url'),
                TextInput::make('robots')
                    ->default('index,follow'),
            ]);
    }
}
