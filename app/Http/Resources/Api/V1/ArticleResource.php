<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Article;
use App\Services\HtmlSanitizer;
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
        $sanitizer = app(HtmlSanitizer::class);
        $safeBody = $sanitizer->hardenLinks($sanitizer->sanitizeForPublic((string) $this->body));

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'summary' => $this->summary,
            'body' => $this->when($request->routeIs('api.v1.articles.show'), $safeBody),
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keywords' => $this->meta_keywords,
            'canonical_url' => $this->canonical_url,
            'robots' => $this->robots,
            'featured' => $this->featured,
            'published_at' => $this->published_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'author_name' => $this->author_name,
            'author_type' => $this->author_type,
            'article_type' => $this->article_type,
            'reading_time_minutes' => $this->reading_time_minutes,
            'main_image_url' => $this->main_image_url,
            'video_embed' => $this->video_embed,
            'terms' => TermResource::collection($this->whenLoaded('terms')),
            'related' => ArticleResource::collection($this->whenLoaded('relatedArticles')),
        ];
    }
}
