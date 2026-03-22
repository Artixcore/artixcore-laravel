<?php

namespace App\Filament\Resources\AiWorkflows\Pages;

use App\Filament\Resources\AiWorkflows\AiWorkflowResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAiWorkflow extends CreateRecord
{
    protected static string $resource = AiWorkflowResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        return $data;
    }
}
