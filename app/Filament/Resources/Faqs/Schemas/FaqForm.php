<?php

namespace App\Filament\Resources\Faqs\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class FaqForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('question')
                    ->required()
                    ->columnSpanFull()
                    ->rows(2),
                Textarea::make('answer')
                    ->required()
                    ->columnSpanFull()
                    ->rows(6),
                TextInput::make('category')
                    ->maxLength(255),
                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0),
                Toggle::make('is_published')
                    ->label('Published')
                    ->default(true),
                Toggle::make('show_on_general_faq')
                    ->label('Show on general FAQ page')
                    ->default(false),
                Toggle::make('show_on_saas_page')
                    ->label('Show on SaaS page')
                    ->default(false),
            ]);
    }
}
