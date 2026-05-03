<?php

namespace App\Filament\Resources\Leads\Schemas;

use App\Models\Lead;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LeadForm
{
    /**
     * @return array<string, string>
     */
    private static function statusOptions(): array
    {
        $opts = [];
        foreach (Lead::statuses() as $s) {
            $opts[$s] = ucfirst($s);
        }

        return $opts;
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->disabled(),
                TextInput::make('email')
                    ->disabled(),
                TextInput::make('phone')
                    ->disabled(),
                TextInput::make('service_type')
                    ->disabled(),
                Textarea::make('message')
                    ->disabled()
                    ->columnSpanFull()
                    ->rows(6),
                TextInput::make('source')
                    ->disabled(),
                DateTimePicker::make('submitted_at')
                    ->disabled(),
                Select::make('status')
                    ->options(self::statusOptions())
                    ->required(),
                Textarea::make('admin_notes')
                    ->columnSpanFull()
                    ->rows(4),
                DateTimePicker::make('reviewed_at'),
                Select::make('reviewed_by')
                    ->relationship('reviewedBy', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('assigned_to')
                    ->relationship('assignee', 'name')
                    ->searchable()
                    ->preload(),
                Textarea::make('internal_notes')
                    ->columnSpanFull()
                    ->rows(4),
            ]);
    }
}
