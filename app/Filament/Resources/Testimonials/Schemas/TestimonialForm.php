<?php

namespace App\Filament\Resources\Testimonials\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TestimonialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('author_name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('role')
                    ->maxLength(255),
                TextInput::make('company')
                    ->maxLength(255),
                Textarea::make('body')
                    ->required()
                    ->columnSpanFull()
                    ->rows(6),
                TextInput::make('rating')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(5),
                TextInput::make('avatar_media_id')
                    ->numeric()
                    ->label('Avatar media asset ID'),
                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0),
                Toggle::make('is_published')
                    ->label('Published')
                    ->default(true),
                Toggle::make('featured')
                    ->default(false),
            ]);
    }
}
