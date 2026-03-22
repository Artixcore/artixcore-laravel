<?php

namespace App\Filament\Resources\MediaAssets\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MediaAssetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('upload')
                    ->label('Replace file')
                    ->disk('public')
                    ->directory('media')
                    ->visibility('public')
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->columnSpanFull(),
                TextInput::make('alt_text')
                    ->maxLength(500),
                Textarea::make('caption')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
