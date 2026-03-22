<?php

namespace App\Filament\Resources\ResearchPapers\Pages;

use App\Filament\Resources\ResearchPapers\ResearchPaperResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditResearchPaper extends EditRecord
{
    protected static string $resource = ResearchPaperResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
