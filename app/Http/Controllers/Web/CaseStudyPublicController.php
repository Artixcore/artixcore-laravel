<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CaseStudy;
use App\Models\Taxonomy;
use App\Models\Term;
use App\Services\Content\RelatedContentService;
use App\Services\HtmlSanitizer;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CaseStudyPublicController extends Controller
{
    public function __construct(
        private RelatedContentService $relatedContent,
        private HtmlSanitizer $htmlSanitizer,
    ) {}

    public function index(Request $request): View
    {
        $query = CaseStudy::query()
            ->published()
            ->with(['terms.taxonomy'])
            ->orderByDesc('featured')
            ->orderByDesc('published_at');

        if ($request->filled('industry')) {
            $query->where('industry', $request->string('industry')->toString());
        }

        if ($request->filled('technology')) {
            $needle = '%'.$request->string('technology')->toString().'%';
            $query->where('technology_stack', 'like', $needle);
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
            $query->search($request->string('q')->toString());
        }

        $caseStudies = $query->paginate(12)->withQueryString();

        return view('pages.case-studies.index', [
            'caseStudies' => $caseStudies,
            'categoriesNav' => $this->categoryTermsForNav(),
            'filters' => [
                'industry' => $request->string('industry')->toString(),
                'technology' => $request->string('technology')->toString(),
                'category' => $request->string('category')->toString(),
                'q' => $request->string('q')->toString(),
            ],
        ]);
    }

    public function category(string $categorySlug): View
    {
        $category = $this->resolveCategoryTerm($categorySlug);
        abort_if($category === null, 404);

        $query = CaseStudy::query()
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

        return view('pages.case-studies.index', [
            'caseStudies' => $query->paginate(12),
            'categoriesNav' => $this->categoryTermsForNav(),
            'activeCategory' => $category,
            'filters' => [
                'industry' => '',
                'technology' => '',
                'category' => '',
                'q' => '',
            ],
        ]);
    }

    public function tag(string $tagSlug): View
    {
        $tag = Term::query()
            ->where('slug', $tagSlug)
            ->whereHas('taxonomy', fn ($t) => $t->where('slug', 'tags'))
            ->firstOrFail();

        $query = CaseStudy::query()
            ->published()
            ->with(['terms.taxonomy'])
            ->whereHas('terms', fn ($q) => $q->where('terms.id', $tag->id))
            ->orderByDesc('featured')
            ->orderByDesc('published_at');

        return view('pages.case-studies.index', [
            'caseStudies' => $query->paginate(12),
            'categoriesNav' => $this->categoryTermsForNav(),
            'activeTag' => $tag,
            'filters' => [
                'industry' => '',
                'technology' => '',
                'category' => '',
                'q' => '',
            ],
        ]);
    }

    public function show(string $slug): View
    {
        $study = CaseStudy::query()
            ->published()
            ->where('slug', $slug)
            ->with(['terms.taxonomy', 'terms.parent'])
            ->firstOrFail();

        $study->increment('view_count');

        $challenge = $this->htmlSanitizer->sanitizeForPublic((string) ($study->challenge ?? ''));
        $solution = $this->htmlSanitizer->sanitizeForPublic((string) ($study->solution ?? ''));
        $implementation = $this->htmlSanitizer->sanitizeForPublic((string) ($study->implementation ?? ''));
        $lessons = $this->htmlSanitizer->sanitizeForPublic((string) ($study->lessons_learned ?? ''));
        $bodyExtra = $this->htmlSanitizer->sanitizeForPublic((string) ($study->body ?? ''));

        $related = $this->relatedContent->relatedCaseStudies($study);

        return view('pages.case-studies.show', [
            'study' => $study,
            'challengeHtml' => $this->htmlSanitizer->hardenLinks($challenge),
            'solutionHtml' => $this->htmlSanitizer->hardenLinks($solution),
            'implementationHtml' => $this->htmlSanitizer->hardenLinks($implementation),
            'lessonsHtml' => $this->htmlSanitizer->hardenLinks($lessons),
            'bodyHtml' => $this->htmlSanitizer->hardenLinks($bodyExtra),
            'relatedCaseStudies' => $related,
            'videoEmbed' => $study->video_embed,
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
