<?php

namespace App\Filament\Resources\MicroToolRuns\Pages;

use App\Filament\Resources\MicroToolRuns\MicroToolRunResource;
use Filament\Resources\Pages\ListRecords;

class ListMicroToolRuns extends ListRecords
{
    protected static string $resource = MicroToolRunResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
