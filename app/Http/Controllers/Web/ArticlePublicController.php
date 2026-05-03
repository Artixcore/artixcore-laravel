<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Taxonomy;
use App\Models\Term;
use App\Services\Content\RelatedContentService;
use App\Services\HtmlSanitizer;
use App\Support\Content\ArticleTocExtractor;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class ArticlePublicController extends Controller
{
    public function __construct(
        private RelatedContentService $relatedContent,
        private HtmlSanitizer $htmlSanitizer,
    ) {}

    public function index(Request $request): View
    {
        $query = Article::query()
            ->published()
            ->with(['terms.taxonomy', 'media'])
            ->orderByDesc('featured')
            ->orderByDesc('published_at');

        $category = null;
        $tag = null;

        if ($request->filled('category')) {
            $slug = $request->string('category')->toString();
            $category = $this->resolveCategoryTerm($slug);
            if ($category) {
                $query->whereHas('terms', function ($q) use ($category): void {
                    $q->where(function ($q2) use ($category): void {
                        $q2->where('terms.id', $category->id);
                        $childIds = Term::query()->where('parent_id', $category->id)->pluck('id');
                        if ($childIds->isNotEmpty()) {
                            $q2->orWhereIn('terms.id', $childIds);
                        }
                    });
                });
            }
        }

        if ($request->filled('tag')) {
            $slug = $request->string('tag')->toString();
            $tag = $this->resolveTagTerm($slug);
            if ($tag) {
                $query->whereHas('terms', fn ($q) => $q->where('terms.id', $tag->id));
            }
        }

        if ($request->filled('q')) {
            $query->search($request->string('q')->toString());
        }

        $articles = $query->paginate(12)->withQueryString();

        $featured = Article::query()
            ->published()
            ->where('featured', true)
            ->with(['terms.taxonomy', 'media'])
            ->orderByDesc('published_at')
            ->first();

        return view('pages.articles.index', [
            'articles' => $articles,
            'featuredArticle' => $featured,
            'category' => $category,
            'tag' => $tag,
            'categoriesNav' => $this->categoryTermsForNav(),
            'popularTags' => $this->popularTags(),
        ]);
    }

    public function show(string $slug): View
    {
        $article = Article::query()
            ->published()
            ->where('slug', $slug)
            ->with(['terms.taxonomy', 'terms.parent', 'media'])
            ->firstOrFail();

        $article->increment('view_count');

        $sanitized = $this->htmlSanitizer->sanitizeForPublic($article->body);
        $sanitized = $this->htmlSanitizer->hardenLinks($sanitized);
        $tocData = ArticleTocExtractor::injectAnchorIds($sanitized);

        $related = $this->relatedContent->relatedArticles($article);
        $related->load(['media']);

        return view('pages.articles.show', [
            'article' => $article,
            'articleBodyHtml' => $tocData['html'],
            'toc' => $tocData['toc'],
            'relatedArticles' => $related,
        ]);
    }

    public function category(string $categorySlug): View
    {
        request()->merge(['category' => $categorySlug]);

        return $this->index(request());
    }

    public function tag(string $tagSlug): View
    {
        request()->merge(['tag' => $tagSlug]);

        return $this->index(request());
    }

    /**
     * @return Collection<int, Term>
     */
    private function categoryTermsForNav()
    {
        $tax = Taxonomy::query()->where('slug', 'categories')->first();
        if (! $tax) {
            return collect();
        }

        return Term::query()
            ->where('taxonomy_id', $tax->id)
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    /**
     * @return Collection<int, Term>
     */
    private function popularTags(int $limit = 24)
    {
        $tax = Taxonomy::query()->where('slug', 'tags')->first();
        if (! $tax) {
            return collect();
        }

        return Term::query()
            ->where('taxonomy_id', $tax->id)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->limit($limit)
            ->get();
    }

    private function resolveCategoryTerm(string $slug): ?Term
    {
        $taxonomy = Taxonomy::query()->where('slug', 'categories')->first();
        if (! $taxonomy) {
            return null;
        }

        return Term::query()
            ->where('taxonomy_id', $taxonomy->id)
            ->where('slug', $slug)
            ->first();
    }

    private function resolveTagTerm(string $slug): ?Term
    {
        $taxonomy = Taxonomy::query()->where('slug', 'tags')->first();
        if (! $taxonomy) {
            return null;
        }

        return Term::query()
            ->where('taxonomy_id', $taxonomy->id)
            ->where('slug', $slug)
            ->first();
    }
}
