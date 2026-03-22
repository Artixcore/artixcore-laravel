<?php

namespace App\Filament\Resources\MediaAssets\Pages;

use App\Filament\Resources\MediaAssets\MediaAssetResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
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
        $upload = $data['upload'] ?? null;
        unset($data['upload']);

        if (is_array($upload)) {
            $upload = Arr::first(array_filter($upload, fn (mixed $v): bool => is_string($v) && $v !== '')) ?: null;
        }

        if (is_string($upload) && $upload !== '') {
            $disk = 'public';
            $data['disk'] = $disk;
            $data['path'] = $upload;
            $dir = dirname($upload);
            $data['directory'] = ($dir === '.' || $dir === '') ? 'media' : $dir;
            $data['filename'] = basename($upload);
            $storage = Storage::disk($disk);
            if ($storage->exists($upload)) {
                $data['mime_type'] = $storage->mimeType($upload);
                $data['size_bytes'] = $storage->size($upload);
            }
        } elseif ($upload instanceof TemporaryUploadedFile) {
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

        if (empty($data['path'] ?? null)) {
            throw ValidationException::withMessages([
                'upload' => ['File upload failed or is missing.'],
            ]);
        }

        return $data;
    }
}
