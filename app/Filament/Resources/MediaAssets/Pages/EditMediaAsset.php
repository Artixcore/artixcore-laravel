<?php

namespace App\Filament\Resources\MediaAssets\Pages;

use App\Filament\Resources\MediaAssets\MediaAssetResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class EditMediaAsset extends EditRecord
{
    protected static string $resource = MediaAssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        /** @var TemporaryUploadedFile|null $upload */
        $upload = $data['upload'] ?? null;
        unset($data['upload']);

        if ($upload instanceof TemporaryUploadedFile) {
            $oldPath = $this->record->path;
            $path = $upload->store('media', 'public');
            $data['path'] = $path;
            $data['filename'] = $upload->getClientOriginalName();
            $data['mime_type'] = $upload->getClientMimeType();
            $data['size_bytes'] = $upload->getSize();
            if ($oldPath !== '' && $oldPath !== $path) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        $data['updated_by'] = auth()->id();

        return $data;
    }
}
