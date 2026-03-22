<?php

namespace App\Http\Resources\Api\V1;

use App\Models\PageBlock;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin PageBlock */
class PageBlockResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'sort_order' => $this->sort_order,
            'data' => $this->data ?? [],
        ];
    }
}
