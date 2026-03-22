<?php

namespace App\Http\Controllers\Api\V1\Portal;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Portal\UpdatePasswordRequest;
use App\Http\Requests\Api\V1\Portal\UpdateProfileRequest;
use App\Http\Requests\Api\V1\Portal\UploadAvatarRequest;
use App\Http\Requests\Api\V1\Portal\UploadPhotoRequest;
use App\Http\Resources\Api\V1\Portal\UserProfileResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return response()->json([
            'data' => UserProfileResource::make($user)->resolve(),
        ]);
    }

    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $user->fill($request->validated());
        $user->save();

        return response()->json([
            'data' => UserProfileResource::make($user->fresh())->resolve(),
        ]);
    }

    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $user->password = $request->validated()['password'];
        $user->save();

        return response()->json([
            'data' => ['message' => 'Password updated.'],
        ]);
    }

    public function uploadAvatar(UploadAvatarRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $user->addMediaFromRequest('avatar')->toMediaCollection('avatar');

        return response()->json([
            'data' => UserProfileResource::make($user->fresh())->resolve(),
        ]);
    }

    public function removeAvatar(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $user->clearMediaCollection('avatar');

        return response()->json([
            'data' => UserProfileResource::make($user->fresh())->resolve(),
        ]);
    }

    public function listPhotos(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $photos = $user->media()
            ->where('collection_name', 'photos')
            ->orderBy('order_column')
            ->get()
            ->map(fn (Media $media): array => UserProfileResource::photoArray($media))
            ->values()
            ->all();

        return response()->json([
            'data' => ['photos' => $photos],
        ]);
    }

    public function uploadPhoto(UploadPhotoRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $media = $user->addMediaFromRequest('photo')->toMediaCollection('photos');

        return response()->json([
            'data' => [
                'photo' => UserProfileResource::photoArray($media),
            ],
        ]);
    }

    public function deletePhoto(Request $request, int $media): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $mediaModel = Media::query()
            ->whereKey($media)
            ->where('model_type', $user->getMorphClass())
            ->where('model_id', $user->getKey())
            ->where('collection_name', 'photos')
            ->first();

        if ($mediaModel === null) {
            abort(404);
        }

        $mediaModel->delete();

        return response()->json([
            'data' => ['message' => 'Photo removed.'],
        ]);
    }
}
