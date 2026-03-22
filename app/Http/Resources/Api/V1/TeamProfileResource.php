<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamProfileResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'role' => $this->role,
            'bio' => $this->when($request->routeIs('api.v1.team.show'), $this->bio),
            'avatar_url' => $this->avatar_url,
            'sort_order' => $this->sort_order,
            'published_at' => $this->published_at?->toIso8601String(),
        ];
    }
}
