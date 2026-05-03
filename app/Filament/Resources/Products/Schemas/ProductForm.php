<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('slug')
                    ->required(),
                TextInput::make('title')
                    ->required(),
                TextInput::make('tagline'),
                TextInput::make('platform_type')
                    ->label('Platform type'),
                Textarea::make('features')
                    ->label('Features (JSON array)')
                    ->helperText('JSON array of strings or objects with title/body keys.')
                    ->columnSpanFull()
                    ->rows(6)
                    ->formatStateUsing(fn ($state): string => is_array($state) ? (string) json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : (string) $state)
                    ->dehydrateStateUsing(function (?string $state): ?array {
                        if ($state === null || trim($state) === '') {
                            return null;
                        }
                        $decoded = json_decode($state, true);

                        return is_array($decoded) ? $decoded : null;
                    }),
                Textarea::make('use_cases')
                    ->label('Use cases (JSON array)')
                    ->columnSpanFull()
                    ->rows(5)
                    ->formatStateUsing(fn ($state): string => is_array($state) ? (string) json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : (string) $state)
                    ->dehydrateStateUsing(function (?string $state): ?array {
                        if ($state === null || trim($state) === '') {
                            return null;
                        }
                        $decoded = json_decode($state, true);

                        return is_array($decoded) ? $decoded : null;
                    }),
                Textarea::make('target_audience')
                    ->columnSpanFull()
                    ->rows(3),
                Textarea::make('pricing_note')
                    ->columnSpanFull()
                    ->rows(3),
                TextInput::make('summary'),
                Textarea::make('body')
                    ->columnSpanFull()
                    ->rows(12),
                TextInput::make('video_url')
                    ->label('Video URL (YouTube/Vimeo)')
                    ->maxLength(2048),
                TextInput::make('main_image_media_id')
                    ->numeric()
                    ->label('Main image media asset ID'),
                TextInput::make('meta_title'),
                Textarea::make('meta_description')
                    ->columnSpanFull(),
                TextInput::make('meta_keywords'),
                TextInput::make('canonical_url'),
                TextInput::make('robots')
                    ->default('index,follow'),
                Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                    ])
                    ->required()
                    ->default('draft'),
                Toggle::make('featured')
                    ->default(false),
                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0),
                TextInput::make('trending_score')
                    ->numeric()
                    ->default(0),
                DateTimePicker::make('published_at'),
                Select::make('terms')
                    ->relationship('terms', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
            ]);
    }
}
