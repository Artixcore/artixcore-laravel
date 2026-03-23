<?php

namespace App\Http\Resources\Api\V1;

use App\Services\Tools\ToolAccessService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MicroToolResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $access = app(ToolAccessService::class)->resolveForCatalog($this->resource, $request->user());

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'category' => $this->category,
            'category_slug' => $this->category_slug,
            'title' => $this->title,
            'description' => $this->description,
            'short_description' => $this->short_description,
            'icon_key' => $this->icon_key,
            'execution_mode' => $this->execution_mode,
            'input_schema' => $this->input_schema,
            'is_premium' => $this->is_premium,
            'is_popular' => $this->is_popular,
            'is_new' => $this->is_new,
            'is_featured' => $this->is_featured,
            'released_at' => $this->released_at?->toIso8601String(),
            'featured_score' => $this->featured_score,
            'access_type' => $this->access_type,
            'requires_auth' => $this->requires_auth,
            'route_path' => $this->route_path,
            'tool_type' => $this->tool_type,
            'locked' => ! $access->canExecute,
            'locked_for_guest' => $request->user() === null && ! $access->canExecute,
            'ads_expected' => $access->adsExpected,
        ];
    }
}
