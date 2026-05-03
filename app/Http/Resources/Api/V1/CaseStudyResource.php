<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CaseStudyResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $show = $request->routeIs('api.v1.case-studies.show');

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'case_study_type' => $this->case_study_type,
            'type_label' => $this->typeLabel(),
            'client_name' => $this->client_name,
            'client_display_name' => $this->client_display_name,
            'summary' => $this->summary,
            'outcome_summary' => $this->outcome_summary,
            'industry' => $this->industry,
            'project_type' => $this->project_type,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keywords' => $this->when($show, $this->meta_keywords),
            'canonical_url' => $this->when($show, $this->canonical_url),
            'robots' => $this->when($show, $this->robots),
            'featured' => $this->featured,
            'reading_time_minutes' => $this->when($show, $this->reading_time_minutes),
            'published_at' => $this->published_at?->toIso8601String(),
            'main_image_url' => $this->when($show, $this->main_image_url),
            'video_url' => $this->when($show, $this->video_url),
            'video_embed' => $this->when($show, $this->video_embed),
            'author_name' => $this->when($show, $this->author_name),
            'author_type' => $this->when($show, $this->author_type),
            'client_verified' => $this->when($show, $this->client_verified),
            'body' => $this->when($show, $this->body),
            'challenge' => $this->when($show, $this->challenge),
            'solution' => $this->when($show, $this->solution),
            'implementation' => $this->when($show, $this->implementation),
            'lessons_learned' => $this->when($show, $this->lessons_learned),
            'technology_stack' => $this->when($show, $this->technology_stack),
            'outcomes' => $this->when($show, $this->outcomes),
            'metrics' => $this->when($show, $this->metrics),
            'gallery_paths' => $this->when($show, $this->gallery_paths),
            'terms' => TermResource::collection($this->whenLoaded('terms')),
            'related' => CaseStudyResource::collection($this->whenLoaded('relatedStudies')),
        ];
    }
}
