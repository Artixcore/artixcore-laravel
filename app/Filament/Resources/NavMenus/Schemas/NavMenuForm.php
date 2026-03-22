<?php

namespace App\Filament\Resources\NavMenus\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class NavMenuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->required(),
                TextInput::make('name')
                    ->required(),
            ]);
    }
}
