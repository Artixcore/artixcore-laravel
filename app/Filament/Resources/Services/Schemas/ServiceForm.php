<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        $jsonBlocks = static function (string $field, string $label): Textarea {
            return Textarea::make($field)
                ->label($label)
                ->columnSpanFull()
                ->rows(6)
                ->helperText('JSON array (objects or strings). Leave empty for none.')
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
        };

        return $schema
            ->components([
                TextInput::make('slug')
                    ->maxLength(255),
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Textarea::make('summary')
                    ->maxLength(500)
                    ->rows(3),
                Textarea::make('body')
                    ->columnSpanFull()
                    ->rows(12),
                $jsonBlocks('benefits', 'Benefits (JSON)'),
                $jsonBlocks('process', 'Process (JSON)'),
                $jsonBlocks('technologies', 'Technologies (JSON)'),
                TextInput::make('icon')
                    ->maxLength(100),
                TextInput::make('featured_image_media_id')
                    ->numeric()
                    ->label('Featured image media ID'),
                Toggle::make('featured')
                    ->default(false),
                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0),
                Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                    ])
                    ->required()
                    ->default('draft'),
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
