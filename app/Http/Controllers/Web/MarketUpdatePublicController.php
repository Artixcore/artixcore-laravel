<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\MarketUpdate;
use App\Models\Taxonomy;
use App\Models\Term;
use App\Services\Content\RelatedContentService;
use App\Services\HtmlSanitizer;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MarketUpdatePublicController extends Controller
{
    public function __construct(
        private RelatedContentService $relatedContent,
        private HtmlSanitizer $htmlSanitizer,
    ) {}

    public function index(Request $request): View
    {
        $query = MarketUpdate::query()
            ->published()
            ->with(['terms.taxonomy'])
            ->orderByDesc('featured')
            ->orderByDesc('published_at');

        if ($request->filled('market_area')) {
            $query->where('market_area', $request->string('market_area')->toString());
        }

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

        if ($request->filled('q')) {
            $needle = '%'.str_replace(['%', '_'], ['\\%', '\\_'], trim($request->string('q')->toString())).'%';
            $query->where(function ($q) use ($needle): void {
                $q->where('title', 'like', $needle)
                    ->orWhere('excerpt', 'like', $needle)
                    ->orWhere('body', 'like', $needle);
            });
        }

        return view('pages.market-updates.index', [
            'marketUpdates' => $query->paginate(12)->withQueryString(),
            'categoriesNav' => $this->categoryTermsForNav(),
            'filters' => [
                'market_area' => $request->string('market_area')->toString(),
                'category' => $request->string('category')->toString(),
                'q' => $request->string('q')->toString(),
            ],
        ]);
    }

    public function category(string $categorySlug): View
    {
        $category = $this->resolveCategoryTerm($categorySlug);
        abort_if($category === null, 404);

        $query = MarketUpdate::query()
            ->published()
            ->with(['terms.taxonomy'])
            ->whereHas('terms', function ($q) use ($category): void {
                $q->where(function ($q2) use ($category): void {
                    $q2->where('terms.id', $category->id);
                    $childIds = Term::query()->where('parent_id', $category->id)->pluck('id');
                    if ($childIds->isNotEmpty()) {
                        $q2->orWhereIn('terms.id', $childIds);
                    }
                });
            })
            ->orderByDesc('featured')
            ->orderByDesc('published_at');

        return view('pages.market-updates.index', [
            'marketUpdates' => $query->paginate(12),
            'categoriesNav' => $this->categoryTermsForNav(),
            'activeCategory' => $category,
            'filters' => ['market_area' => '', 'category' => '', 'q' => ''],
        ]);
    }

    public function tag(string $tagSlug): View
    {
        $tag = Term::query()
            ->where('slug', $tagSlug)
            ->whereHas('taxonomy', fn ($t) => $t->where('slug', 'tags'))
            ->firstOrFail();

        $query = MarketUpdate::query()
            ->published()
            ->with(['terms.taxonomy'])
            ->whereHas('terms', fn ($q) => $q->where('terms.id', $tag->id))
            ->orderByDesc('featured')
            ->orderByDesc('published_at');

        return view('pages.market-updates.index', [
            'marketUpdates' => $query->paginate(12),
            'categoriesNav' => $this->categoryTermsForNav(),
            'activeTag' => $tag,
            'filters' => ['market_area' => '', 'category' => '', 'q' => ''],
        ]);
    }

    public function show(string $slug): View
    {
        $update = MarketUpdate::query()
            ->published()
            ->where('slug', $slug)
            ->with(['terms.taxonomy', 'terms.parent'])
            ->firstOrFail();

        $update->increment('view_count');

        $bodyHtml = $this->htmlSanitizer->hardenLinks(
            $this->htmlSanitizer->sanitizeForPublic((string) ($update->body ?? ''))
        );

        $sections = [
            'trend_summary' => $update->trend_summary,
            'business_impact' => $update->business_impact,
            'technology_impact' => $update->technology_impact,
            'opportunities' => $update->opportunities,
            'risks' => $update->risks,
            'what_next' => $update->what_next,
        ];

        $sanitizedSections = [];
        foreach ($sections as $key => $html) {
            $sanitizedSections[$key] = $this->htmlSanitizer->hardenLinks(
                $this->htmlSanitizer->sanitizeForPublic((string) $html)
            );
        }

        $related = $this->relatedContent->relatedMarketUpdates($update);

        return view('pages.market-updates.show', [
            'update' => $update,
            'bodyHtml' => $bodyHtml,
            'sectionsHtml' => $sanitizedSections,
            'relatedMarketUpdates' => $related,
            'videoEmbed' => $update->video_embed,
        ]);
    }

    private function resolveCategoryTerm(string $slug): ?Term
    {
        return Term::query()
            ->where('slug', $slug)
            ->whereHas('taxonomy', fn ($t) => $t->where('slug', 'categories'))
            ->first();
    }

    /**
     * @return \Illuminate\Support\Collection<int, Term>
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
}
