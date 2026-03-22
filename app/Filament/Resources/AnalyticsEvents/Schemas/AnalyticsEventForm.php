<?php

namespace App\Filament\Resources\AnalyticsEvents\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AnalyticsEventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('session_id'),
                TextInput::make('event_type')
                    ->required(),
                TextInput::make('payload'),
            ]);
    }
}
