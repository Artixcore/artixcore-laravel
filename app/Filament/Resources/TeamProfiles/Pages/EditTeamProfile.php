<?php

namespace App\Filament\Resources\TeamProfiles\Pages;

use App\Filament\Resources\TeamProfiles\TeamProfileResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTeamProfile extends EditRecord
{
    protected static string $resource = TeamProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
