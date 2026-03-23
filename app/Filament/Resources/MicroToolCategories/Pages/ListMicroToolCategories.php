<?php

namespace App\Filament\Resources\MicroToolCategories\Pages;

use App\Filament\Resources\MicroToolCategories\MicroToolCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMicroToolCategories extends ListRecords
{
    protected static string $resource = MicroToolCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
