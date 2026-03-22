<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ArticleResource;
use App\Models\Article;
use App\Services\Content\RelatedContentService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ArticleController extends Controller
{
    public function __construct(
        private RelatedContentService $relatedContent,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->validate([
            'category' => 'sometimes|string',
            'topic' => 'sometimes|string',
            'tag' => 'sometimes|string',
            'q' => 'sometimes|string|max:200',
        ]);

        $query = Article::query()->published()->with('terms');

        if (! empty($filters['category'])) {
            $query->whereHas('terms', function ($q) use ($filters): void {
                $q->where('slug', $filters['category'])
                    ->whereHas('taxonomy', fn ($t) => $t->where('slug', 'categories'));
            });
        }

        if (! empty($filters['topic'])) {
            $query->whereHas('terms', function ($q) use ($filters): void {
                $q->where('slug', $filters['topic'])
                    ->whereHas('taxonomy', fn ($t) => $t->where('slug', 'topics'));
            });
        }

        if (! empty($filters['tag'])) {
            $query->whereHas('terms', function ($q) use ($filters): void {
                $q->where('slug', $filters['tag'])
                    ->whereHas('taxonomy', fn ($t) => $t->where('slug', 'tags'));
            });
        }

        if (! empty($filters['q'])) {
            $needle = '%'.str_replace(['%', '_'], ['\\%', '\\_'], $filters['q']).'%';
            $query->where(function ($q) use ($needle): void {
                $q->where('title', 'like', $needle)
                    ->orWhere('summary', 'like', $needle);
            });
        }

        $interest = collect(explode(',', (string) $request->header('X-Interest-Topics', '')))
            ->map(fn (string $s) => trim($s))
            ->filter();

        $this->relatedContent->applyArticleInterestBoost($query, $interest);

        $query->orderByDesc('featured')->orderByDesc('published_at');

        return ArticleResource::collection($query->paginate(12));
    }

    public function show(string $slug): ArticleResource
    {
        $article = Article::query()->where('slug', $slug)->with('terms')->firstOrFail();
        $this->authorize('view', $article);
        $article->increment('view_count');

        $related = $this->relatedContent->relatedArticles($article);
        $article->setRelation('relatedArticles', $related);

        return new ArticleResource($article);
    }
}
