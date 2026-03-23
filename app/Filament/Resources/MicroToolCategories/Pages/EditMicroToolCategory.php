<?php

namespace App\Filament\Resources\MicroToolCategories\Pages;

use App\Filament\Resources\MicroToolCategories\MicroToolCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMicroToolCategory extends EditRecord
{
    protected static string $resource = MicroToolCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
