<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ProductResource;
use App\Models\Product;
use App\Services\Content\RelatedContentService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    public function __construct(
        private RelatedContentService $relatedContent,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->validate([
            'category' => 'sometimes|string',
            'q' => 'sometimes|string|max:200',
        ]);

        $query = Product::query()->published()->with('terms');

        if (! empty($filters['category'])) {
            $query->whereHas('terms', function ($q) use ($filters): void {
                $q->where('slug', $filters['category'])
                    ->whereHas('taxonomy', fn ($t) => $t->where('slug', 'categories'));
            });
        }

        if (! empty($filters['q'])) {
            $needle = '%'.str_replace(['%', '_'], ['\\%', '\\_'], $filters['q']).'%';
            $query->where(function ($q) use ($needle): void {
                $q->where('title', 'like', $needle)
                    ->orWhere('summary', 'like', $needle)
                    ->orWhere('tagline', 'like', $needle);
            });
        }

        $query->orderBy('sort_order')->orderByDesc('featured')->orderByDesc('published_at');

        return ProductResource::collection($query->paginate(12));
    }

    public function show(string $slug): ProductResource
    {
        $product = Product::query()->where('slug', $slug)->with('terms')->firstOrFail();
        $this->authorize('view', $product);
        $product->increment('view_count');

        $related = $this->relatedContent->relatedProducts($product);
        $product->setRelation('relatedProducts', $related);

        return new ProductResource($product);
    }
}
