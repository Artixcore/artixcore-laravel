<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'tagline' => $this->tagline,
            'summary' => $this->summary,
            'body' => $this->when($request->routeIs('api.v1.products.show'), $this->body),
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'featured' => $this->featured,
            'sort_order' => $this->sort_order,
            'published_at' => $this->published_at?->toIso8601String(),
            'terms' => TermResource::collection($this->whenLoaded('terms')),
            'related' => ProductResource::collection($this->whenLoaded('relatedProducts')),
        ];
    }
}
