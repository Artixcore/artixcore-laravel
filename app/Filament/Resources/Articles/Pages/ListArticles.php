<?php

namespace App\Filament\Resources\Articles\Pages;

use App\Filament\Resources\Articles\ArticleResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListArticles extends ListRecords
{
    protected static string $resource = ArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('aliGenerator')
                ->label('Generate with Ali 1.0')
                ->url(route('admin.ai-article-generator.index'))
                ->visible(fn (): bool => auth()->user()?->can('ai_articles.generate') ?? false),
            CreateAction::make(),
        ];
    }
}
