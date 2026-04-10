<?php

namespace App\Filament\Resources\Pages\Schemas;

use App\Filament\Support\JsonTextarea;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('parent_id')
                    ->relationship('parent', 'title')
                    ->searchable()
                    ->preload(),
                TextInput::make('path')
                    ->required()
                    ->helperText('e.g. home, products/saas'),
                TextInput::make('title')
                    ->required(),
                TextInput::make('meta_title'),
                Textarea::make('meta_description')
                    ->columnSpanFull(),
                JsonTextarea::make('meta', 'Meta (JSON)'),
                Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'archived' => 'Archived',
                    ])
                    ->required()
                    ->default('draft'),
                DateTimePicker::make('published_at'),
                TextInput::make('primary_entity_type'),
                TextInput::make('primary_entity_id')
                    ->numeric(),
            ]);
    }
}
