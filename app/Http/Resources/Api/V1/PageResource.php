<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Page;
use App\Support\PagePath;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Page */
class PageResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Page $page */
        $page = $this->resource;

        $page->loadMissing('metaOgMedia');

        return [
            'path' => $page->path,
            'href' => PagePath::toHref($page->path),
            'title' => $page->title,
            'meta_title' => $page->meta_title,
            'meta_description' => $page->meta_description,
            'meta_og_image_url' => $page->metaOgMedia?->absoluteUrl(),
            'meta' => $page->meta,
            'blocks' => PageBlockResource::collection($page->blocks),
        ];
    }
}
