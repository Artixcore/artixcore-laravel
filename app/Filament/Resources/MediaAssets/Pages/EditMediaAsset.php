<?php

namespace App\Filament\Resources\MediaAssets\Pages;

use App\Filament\Resources\MediaAssets\MediaAssetResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Arr;
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
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['upload'] = $data['path'] ?? null;

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $upload = $data['upload'] ?? null;
        unset($data['upload']);

        if (is_array($upload)) {
            $upload = Arr::first(array_filter($upload, fn (mixed $v): bool => is_string($v) && $v !== '')) ?: null;
        }

        $oldPath = $this->record->path;

        if (is_string($upload) && $upload !== '' && $upload !== $oldPath) {
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
            if ($oldPath !== '' && $oldPath !== $upload) {
                Storage::disk('public')->delete($oldPath);
            }
        } elseif ($upload instanceof TemporaryUploadedFile) {
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
