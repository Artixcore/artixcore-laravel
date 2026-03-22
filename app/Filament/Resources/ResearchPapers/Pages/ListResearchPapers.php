<?php

namespace App\Filament\Resources\ResearchPapers\Pages;

use App\Filament\Resources\ResearchPapers\ResearchPaperResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListResearchPapers extends ListRecords
{
    protected static string $resource = ResearchPaperResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
