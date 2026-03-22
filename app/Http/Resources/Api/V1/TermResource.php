<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Term */
class TermResource extends JsonResource
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
            'taxonomy' => $this->whenLoaded('taxonomy', fn () => [
                'slug' => $this->taxonomy->slug,
                'name' => $this->taxonomy->name,
            ]),
        ];
    }
}
