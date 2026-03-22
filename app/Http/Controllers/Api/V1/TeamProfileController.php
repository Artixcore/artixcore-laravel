<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\TeamProfileResource;
use App\Models\TeamProfile;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TeamProfileController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $profiles = TeamProfile::query()
            ->published()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return TeamProfileResource::collection($profiles);
    }

    public function show(string $slug): TeamProfileResource
    {
        $profile = TeamProfile::query()->where('slug', $slug)->firstOrFail();
        $this->authorize('view', $profile);

        return new TeamProfileResource($profile);
    }
}
