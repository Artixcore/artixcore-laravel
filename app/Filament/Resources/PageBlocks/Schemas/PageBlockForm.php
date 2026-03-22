<?php

namespace App\Filament\Resources\PageBlocks\Schemas;

use App\Filament\Support\JsonTextarea;
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
                    ->options([
                        'hero' => 'Hero',
                        'feature_grid' => 'Feature grid',
                        'product_showcase' => 'Product showcase',
                        'research_highlight' => 'Research highlight',
                        'article_rail' => 'Article rail',
                        'cta' => 'CTA',
                        'rich_text' => 'Rich text',
                    ])
                    ->required(),
                JsonTextarea::make('data', 'Block data (JSON)'),
            ]);
    }
}
