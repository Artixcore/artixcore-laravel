<?php

namespace App\Filament\Resources\AiAgents\Pages;

use App\Filament\Resources\AiAgents\AiAgentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAiAgent extends EditRecord
{
    protected static string $resource = AiAgentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = auth()->id();

        return $data;
    }
}
