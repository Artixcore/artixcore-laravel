<?php

namespace App\Filament\Resources\Articles\Pages;

use App\Filament\Resources\Articles\ArticleResource;
use App\Models\Article;
use App\Services\Ai\ArticleGenerationService;
use App\Services\Ai\Exceptions\LlmTransportException;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditArticle extends EditRecord
{
    protected static string $resource = ArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('preview')
                ->label('Preview')
                ->url(fn (Article $record): string => route('admin.articles.preview', $record))
                ->openUrlInNewTab(),
            Action::make('regenerateMeta')
                ->label('Regenerate SEO only')
                ->requiresConfirmation()
                ->visible(fn (): bool => auth()->user()?->can('ai_articles.generate') ?? false)
                ->action(function (ArticleGenerationService $service): void {
                    try {
                        $service->regenerateMeta($this->record);
                        Notification::make()->title('Meta regenerated')->success()->send();
                        $this->redirect(ArticleResource::getUrl('edit', ['record' => $this->record]));
                    } catch (LlmTransportException $e) {
                        Notification::make()->title($e->getMessage())->danger()->send();
                    }
                }),
            Action::make('regenerateBody')
                ->label('Regenerate body')
                ->requiresConfirmation()
                ->visible(fn (): bool => auth()->user()?->can('ai_articles.generate') ?? false)
                ->action(function (ArticleGenerationService $service): void {
                    try {
                        $service->regenerateBody($this->record);
                        Notification::make()->title('Body regenerated')->success()->send();
                        $this->redirect(ArticleResource::getUrl('edit', ['record' => $this->record]));
                    } catch (LlmTransportException $e) {
                        Notification::make()->title($e->getMessage())->danger()->send();
                    }
                }),
            DeleteAction::make(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = auth()->id();

        if (($data['status'] ?? '') === Article::STATUS_PUBLISHED) {
            abort_unless(auth()->user()?->can('articles.publish'), 403);
        }

        return $data;
    }
}
