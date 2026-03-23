<?php

namespace App\Http\Controllers\Api\V1\Portal;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return response()->json([
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'aid' => $user->aid,
                    'name' => $user->name,
                    'email' => $user->email,
                    'user_kind' => $user->user_kind,
                    'phone' => $user->phone,
                    'bio' => $user->bio,
                    'designation' => $user->designation,
                ],
                'avatar_url' => $user->avatarUrl(),
                'roles' => $user->getRoleNames()->values()->all(),
                'permissions' => $user->getAllPermissions()->pluck('name')->values()->all(),
            ],
        ]);
    }
}
