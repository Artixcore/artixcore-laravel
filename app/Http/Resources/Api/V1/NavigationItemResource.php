<?php

namespace App\Http\Resources\Api\V1;

use App\Models\NavItem;
use App\Support\PagePath;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin NavItem */
class NavigationItemResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var NavItem $item */
        $item = $this->resource;

        $href = $item->url
            ?? ($item->page ? PagePath::toHref($item->page->path) : null);

        return [
            'id' => $item->id,
            'label' => $item->label,
            'href' => $href,
            'feature' => $item->feature_payload,
            'children' => NavigationItemResource::collection(
                $item->children->loadMissing(['page', 'children.page'])
            ),
        ];
    }
}
