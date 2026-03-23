<?php

namespace App\Filament\Resources\MicroTools\Pages;

use App\Filament\Resources\MicroTools\MicroToolResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMicroTool extends EditRecord
{
    protected static string $resource = MicroToolResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
