<?php

namespace App\Filament\Resources\SiteSettings\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SiteSettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('site_name'),
            ]);
    }
}
