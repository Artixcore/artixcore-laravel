<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Article */
class ArticleResource extends JsonResource
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
            'body' => $this->when($request->routeIs('api.v1.articles.show'), $this->body),
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'featured' => $this->featured,
            'published_at' => $this->published_at?->toIso8601String(),
            'terms' => TermResource::collection($this->whenLoaded('terms')),
            'related' => ArticleResource::collection($this->whenLoaded('relatedArticles')),
        ];
    }
}
