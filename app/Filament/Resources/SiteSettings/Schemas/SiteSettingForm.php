<?php

namespace App\Filament\Resources\SiteSettings\Schemas;

use App\Filament\Support\JsonTextarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SiteSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('site_name')
                    ->maxLength(255),
                TextInput::make('default_meta_title')
                    ->maxLength(255),
                TextInput::make('default_meta_description')
                    ->maxLength(500),
                TextInput::make('contact_email')
                    ->email()
                    ->maxLength(255),
                Select::make('logo_media_id')
                    ->label('Logo')
                    ->relationship('logoMedia', 'filename')
                    ->searchable()
                    ->preload(),
                Select::make('favicon_media_id')
                    ->label('Favicon')
                    ->relationship('faviconMedia', 'filename')
                    ->searchable()
                    ->preload(),
                Select::make('og_default_media_id')
                    ->label('Default Open Graph image')
                    ->relationship('ogDefaultMedia', 'filename')
                    ->searchable()
                    ->preload(),
                Select::make('theme_default')
                    ->options([
                        'system' => 'System',
                        'light' => 'Light',
                        'dark' => 'Dark',
                    ])
                    ->required()
                    ->default('system'),
                JsonTextarea::make('social_links', 'Social links (JSON)'),
                JsonTextarea::make('design_tokens', 'Design tokens (JSON)'),
            ]);
    }
}
