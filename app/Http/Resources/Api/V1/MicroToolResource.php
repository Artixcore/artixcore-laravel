<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MicroToolResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'category' => $this->category,
            'title' => $this->title,
            'description' => $this->description,
            'icon_key' => $this->icon_key,
            'execution_mode' => $this->execution_mode,
            'input_schema' => $this->input_schema,
            'is_premium' => $this->is_premium,
            'is_popular' => $this->is_popular,
            'is_new' => $this->is_new,
            'released_at' => $this->released_at?->toIso8601String(),
            'featured_score' => $this->featured_score,
        ];
    }
}
