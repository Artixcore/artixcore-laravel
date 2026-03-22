<?php

namespace App\Http\Resources\Api\V1\Portal;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/** @mixin User */
class UserProfileResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var User $user */
        $user = $this->resource;

        $avatarUrl = $user->avatarUrl();
        $avatarThumb = $user->getFirstMediaUrl('avatar', 'thumb');

        $photos = $user->media()
            ->where('collection_name', 'photos')
            ->orderBy('order_column')
            ->get();

        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'user_kind' => $user->user_kind,
                'phone' => $user->phone,
                'bio' => $user->bio,
                'designation' => $user->designation,
            ],
            'avatar_url' => $avatarUrl,
            'avatar_thumb_url' => $avatarThumb !== '' ? $avatarThumb : $avatarUrl,
            'photos' => $photos->map(fn (Media $media): array => self::photoArray($media))->values()->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function photoArray(Media $media): array
    {
        return [
            'id' => $media->id,
            'url' => $media->getFullUrl(),
            'thumb_url' => $media->getFullUrl('thumb'),
            'name' => $media->name,
            'file_name' => $media->file_name,
            'mime_type' => $media->mime_type,
        ];
    }
}
