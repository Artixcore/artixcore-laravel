<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResearchPaperResource extends JsonResource
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
            'summary' => $this->summary,
            'body' => $this->when($request->routeIs('api.v1.research-papers.show'), $this->body),
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'featured' => $this->featured,
            'published_at' => $this->published_at?->toIso8601String(),
            'terms' => TermResource::collection($this->whenLoaded('terms')),
            'related' => ResearchPaperResource::collection($this->whenLoaded('relatedPapers')),
        ];
    }
}
