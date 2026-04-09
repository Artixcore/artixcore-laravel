<?php

namespace App\Filament\Resources\Pages\Pages;

use App\Filament\Resources\Pages\PageResource;
use App\Models\Page;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPage extends EditRecord
{
    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('visualBuilder')
                ->label('Visual builder')
                ->icon('heroicon-o-paint-brush')
                ->url(fn (Page $record): string => route('builder.pages.show', $record))
                ->openUrlInNewTab()
                ->visible(fn (): bool => auth()->user()?->can('builder.access') ?? false),
            DeleteAction::make(),
        ];
    }
}
