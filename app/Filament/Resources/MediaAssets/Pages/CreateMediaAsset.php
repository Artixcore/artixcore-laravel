<?php

namespace App\Filament\Resources\MediaAssets\Pages;

use App\Filament\Resources\MediaAssets\MediaAssetResource;
use Filament\Resources\Pages\CreateRecord;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class CreateMediaAsset extends CreateRecord
{
    protected static string $resource = MediaAssetResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        /** @var TemporaryUploadedFile|null $upload */
        $upload = $data['upload'] ?? null;
        unset($data['upload']);

        if ($upload instanceof TemporaryUploadedFile) {
            $path = $upload->store('media', 'public');
            $data['disk'] = 'public';
            $data['directory'] = 'media';
            $data['path'] = $path;
            $data['filename'] = $upload->getClientOriginalName();
            $data['mime_type'] = $upload->getClientMimeType();
            $data['size_bytes'] = $upload->getSize();
        }

        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        return $data;
    }
}
