<?php

namespace App\Filament\Resources\MicroTools\Pages;

use App\Filament\Resources\MicroTools\MicroToolResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMicroTools extends ListRecords
{
    protected static string $resource = MicroToolResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
