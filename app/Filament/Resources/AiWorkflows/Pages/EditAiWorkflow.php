<?php

namespace App\Filament\Resources\AiWorkflows\Pages;

use App\Filament\Resources\AiWorkflows\AiWorkflowResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAiWorkflow extends EditRecord
{
    protected static string $resource = AiWorkflowResource::class;

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
