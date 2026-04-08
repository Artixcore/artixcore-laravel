<?php

namespace App\Filament\Resources\NavItems\Schemas;

use App\Filament\Support\JsonTextarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class NavItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('nav_menu_id')
                    ->relationship('menu', 'name')
                    ->required()
                    ->preload(),
                Select::make('parent_id')
                    ->relationship('parent', 'label')
                    ->searchable()
                    ->preload(),
                TextInput::make('label')
                    ->required(),
                TextInput::make('url')
                    ->url()
                    ->helperText('External or internal path; leave blank if using page'),
                Select::make('page_id')
                    ->relationship('page', 'title')
                    ->searchable()
                    ->preload(),
                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0)
                    ->required(),
                JsonTextarea::make('feature_payload', 'Feature payload (JSON)')
                    ->helperText('Optional. For header mega menus use: {"mega":"services"} or {"mega":"portfolio"}. Other keys are reserved for future use.'),
                JsonTextarea::make('visibility', 'Visibility (JSON)')
                    ->helperText('Default: public. Hide from the marketing site: {"contexts":["internal"]}. Show publicly: {"contexts":["public"]} or omit.'),
            ]);
    }
}
