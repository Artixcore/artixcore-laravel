<?php

namespace App\Filament\Resources\AiApprovals\Schemas;

use App\Filament\Support\JsonTextarea;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class AiApprovalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('ai_run_id')
                    ->label('Run')
                    ->relationship('run', 'correlation_id')
                    ->searchable()
                    ->preload(),
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->required()
                    ->default('pending'),
                Select::make('reviewer_id')
                    ->label('Reviewer')
                    ->relationship('reviewer', 'name')
                    ->searchable()
                    ->preload(),
                DateTimePicker::make('resolved_at'),
                JsonTextarea::make('payload', 'Payload (JSON)'),
            ]);
    }
}
