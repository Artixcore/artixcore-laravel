<?php

namespace App\Actions\Media;

use App\Models\MediaAsset;
use App\Models\User;
use Illuminate\Http\UploadedFile;

class CreateMediaAssetFromUpload
{
    public function execute(UploadedFile $uploaded, ?User $user, ?string $altText = null): MediaAsset
    {
        $path = $uploaded->store('media', 'public');
        $userId = $user?->id;

        return MediaAsset::query()->create([
            'disk' => 'public',
            'directory' => 'media',
            'path' => $path,
            'filename' => $uploaded->getClientOriginalName(),
            'mime_type' => $uploaded->getClientMimeType(),
            'size_bytes' => $uploaded->getSize(),
            'alt_text' => $altText,
            'created_by' => $userId,
            'updated_by' => $userId,
        ]);
    }
}
