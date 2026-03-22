<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class NavigationCollection extends ResourceCollection
{
    public $collects = NavigationItemResource::class;

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'menu' => $this->additional['menu_key'] ?? 'primary',
            'items' => $this->collection,
        ];
    }
}
