<?php

namespace App\Http\Controllers\Api\V1\Tools;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\MicroToolResource;
use App\Models\MicroTool;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ToolCatalogController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->validate([
            'category' => 'sometimes|string|max:64',
            'q' => 'sometimes|string|max:200',
            'sort' => 'sometimes|string|in:popular,new,default',
        ]);

        $query = MicroTool::query()->active();

        if (! empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (! empty($filters['q'])) {
            $needle = '%'.str_replace(['%', '_'], ['\\%', '\\_'], $filters['q']).'%';
            $query->where(function ($q) use ($needle): void {
                $q->where('title', 'like', $needle)
                    ->orWhere('description', 'like', $needle)
                    ->orWhere('slug', 'like', $needle);
            });
        }

        $sort = $filters['sort'] ?? 'default';
        match ($sort) {
            'popular' => $query->orderByDesc('is_popular')->orderByDesc('featured_score')->orderBy('sort_order'),
            'new' => $query->orderByDesc('is_new')->orderByDesc('released_at')->orderBy('sort_order'),
            default => $query->orderBy('sort_order')->orderBy('title'),
        };

        return MicroToolResource::collection($query->get());
    }

    public function show(string $slug): MicroToolResource
    {
        $tool = MicroTool::query()->active()->where('slug', $slug)->firstOrFail();

        return new MicroToolResource($tool);
    }
}
