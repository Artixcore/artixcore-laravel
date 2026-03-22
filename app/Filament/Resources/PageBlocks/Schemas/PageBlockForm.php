<?php

namespace App\Filament\Resources\PageBlocks\Schemas;

use App\Filament\Support\JsonTextarea;
use App\Support\Content\PageBlockType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PageBlockForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('page_id')
                    ->relationship('page', 'title')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0)
                    ->required(),
                Select::make('type')
                    ->options(PageBlockType::filamentOptions())
                    ->required(),
                JsonTextarea::make('data', 'Block data (JSON)'),
            ]);
    }
}
