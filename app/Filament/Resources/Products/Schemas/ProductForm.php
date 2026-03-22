<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
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
                TextInput::make('summary'),
                Textarea::make('body')
                    ->columnSpanFull()
                    ->rows(12),
                TextInput::make('meta_title'),
                Textarea::make('meta_description')
                    ->columnSpanFull(),
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
