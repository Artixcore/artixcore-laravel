<?php

namespace App\Filament\Resources\AiAgents\Pages;

use App\Filament\Resources\AiAgents\AiAgentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAiAgents extends ListRecords
{
    protected static string $resource = AiAgentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
