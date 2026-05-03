<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\SyncsAdminContentGraph;
use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\CaseStudy;
use App\Models\ContentRelation;
use App\Models\Faq;
use App\Models\Taxonomy;
use App\Models\Term;
use App\Models\Testimonial;
use App\Services\Content\VideoEmbedResolver;
use App\Services\HtmlSanitizer;
use App\Support\Slug\UniqueSlugGenerator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CaseStudyAdminController extends Controller
{
    use SyncsAdminContentGraph;

    public function index(Request $request): View
    {
        $this->authorize('viewAny', CaseStudy::class);

        $query = CaseStudy::query()->with(['terms.taxonomy'])->orderByDesc('updated_at');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }
        if ($request->filled('case_study_type')) {
            $query->where('case_study_type', $request->string('case_study_type')->toString());
        }
        if ($request->filled('industry')) {
            $query->where('industry', $request->string('industry')->toString());
        }
        if ($request->filled('author_type')) {
            $query->where('author_type', $request->string('author_type')->toString());
        }
        if ($request->filled('category_term_id')) {
            $tid = (int) $request->input('category_term_id');
            $query->whereHas('terms', fn ($q) => $q->where('terms.id', $tid));
        }
        if ($request->filled('q')) {
            $query->search($request->string('q')->toString());
        }

        return view('admin.case-studies.index', [
            'caseStudies' => $query->paginate(20)->withQueryString(),
            'categoryParents' => $this->categoryParents(),
            'filters' => $request->only(['status', 'case_study_type', 'industry', 'author_type', 'category_term_id', 'q']),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', CaseStudy::class);

        return view('admin.case-studies.form', [
            'caseStudy' => new CaseStudy,
            'mode' => 'create',
            'categoryParents' => $this->categoryParents(),
            'categoryChildren' => $this->categoryChildrenGrouped(),
            'tagTerms' => $this->tagTerms(),
            'pickArticles' => Article::query()->orderBy('title')->limit(500)->get(['id', 'title']),
            'pickFaqs' => Faq::query()->orderBy('sort_order')->orderBy('question')->get(['id', 'question']),
            'pickTestimonials' => Testimonial::query()->orderBy('sort_order')->orderBy('author_name')->get(['id', 'author_name']),
            'relatedArticleIds' => old('related_article_ids', []),
            'faqIds' => old('faq_ids', []),
            'testimonialIds' => old('testimonial_ids', []),
        ]);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $this->authorize('create', CaseStudy::class);
        $data = $this->validated($request, null);
        $termIds = $this->mergeTermIds($data);
        unset($data['category_term_ids'], $data['tag_term_ids']);

        if (($data['status'] ?? '') === CaseStudy::STATUS_PUBLISHED) {
            abort_unless($request->user()?->can('case_studies.publish'), 403);
        }

        $data['slug'] = app(UniqueSlugGenerator::class)->unique(
            'case_studies',
            'slug',
            Str::slug($data['slug'] ?? $data['title']) ?: 'case-study',
            null
        );
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        $study = CaseStudy::query()->create($data);
        $study->terms()->sync($termIds);
        $this->syncMainImage($request, $study);
        $this->syncCaseStudyGraph($request, $study);

        return $this->respond($request, 'Case study created.', route('admin.case-studies.index'));
    }

    public function edit(CaseStudy $caseStudy): View
    {
        $this->authorize('update', $caseStudy);
        $caseStudy->load(['terms.taxonomy']);

        return view('admin.case-studies.form', [
            'caseStudy' => $caseStudy,
            'mode' => 'edit',
            'categoryParents' => $this->categoryParents(),
            'categoryChildren' => $this->categoryChildrenGrouped(),
            'tagTerms' => $this->tagTerms(),
            'pickArticles' => Article::query()->orderBy('title')->limit(500)->get(['id', 'title']),
            'pickFaqs' => Faq::query()->orderBy('sort_order')->orderBy('question')->get(['id', 'question']),
            'pickTestimonials' => Testimonial::query()->orderBy('sort_order')->orderBy('author_name')->get(['id', 'author_name']),
            'relatedArticleIds' => old(
                'related_article_ids',
                ContentRelation::query()
                    ->where('source_type', CaseStudy::class)
                    ->where('source_id', $caseStudy->id)
                    ->where('related_type', Article::class)
                    ->where('relation_type', ContentRelation::RELATED_ARTICLE)
                    ->orderBy('sort_order')
                    ->pluck('related_id')
                    ->all()
            ),
            'faqIds' => old('faq_ids', $caseStudy->faqs()->orderByPivot('sort_order')->pluck('faqs.id')->all()),
            'testimonialIds' => old('testimonial_ids', $caseStudy->testimonials()->orderByPivot('sort_order')->pluck('testimonials.id')->all()),
        ]);
    }

    public function update(Request $request, CaseStudy $caseStudy): JsonResponse|RedirectResponse
    {
        $this->authorize('update', $caseStudy);
        $data = $this->validated($request, $caseStudy);
        $termIds = $this->mergeTermIds($data);
        unset($data['category_term_ids'], $data['tag_term_ids']);

        if (($data['status'] ?? '') === CaseStudy::STATUS_PUBLISHED) {
            abort_unless($request->user()?->can('case_studies.publish'), 403);
        }

        if (! ($caseStudy->slug_locked && ! $request->boolean('unlock_slug'))) {
            $base = ($data['slug'] ?? '') !== '' ? Str::slug($data['slug']) : Str::slug($data['title']);
            $data['slug'] = app(UniqueSlugGenerator::class)->unique('case_studies', 'slug', $base ?: 'case-study', $caseStudy->id);
        } else {
            unset($data['slug']);
        }

        if ($request->boolean('unlock_slug')) {
            $data['slug_locked'] = false;
        }

        $data['updated_by'] = Auth::id();
        $caseStudy->update($data);
        $caseStudy->terms()->sync($termIds);
        $this->syncMainImage($request, $caseStudy);
        $this->syncCaseStudyGraph($request, $caseStudy);

        return $this->respond($request, 'Case study updated.', route('admin.case-studies.index'));
    }

    public function preview(CaseStudy $caseStudy): View
    {
        $this->authorize('view', $caseStudy);
        $s = app(HtmlSanitizer::class);

        return view('admin.case-studies.preview', [
            'study' => $caseStudy->load(['terms.taxonomy', 'terms.parent']),
            'challengeHtml' => $s->hardenLinks($s->sanitizeForPublic((string) ($caseStudy->challenge ?? ''))),
            'solutionHtml' => $s->hardenLinks($s->sanitizeForPublic((string) ($caseStudy->solution ?? ''))),
            'implementationHtml' => $s->hardenLinks($s->sanitizeForPublic((string) ($caseStudy->implementation ?? ''))),
            'lessonsHtml' => $s->hardenLinks($s->sanitizeForPublic((string) ($caseStudy->lessons_learned ?? ''))),
            'bodyHtml' => $s->hardenLinks($s->sanitizeForPublic((string) ($caseStudy->body ?? ''))),
            'relatedCaseStudies' => collect(),
            'videoEmbed' => $caseStudy->video_embed,
        ]);
    }

    public function destroy(Request $request, CaseStudy $caseStudy): JsonResponse|RedirectResponse
    {
        $this->authorize('delete', $caseStudy);
        $caseStudy->terms()->detach();
        $caseStudy->faqs()->detach();
        $caseStudy->testimonials()->detach();
        ContentRelation::query()
            ->where(function ($q) use ($caseStudy): void {
                $q->where(fn ($q2) => $q2->where('source_type', CaseStudy::class)->where('source_id', $caseStudy->id))
                    ->orWhere(fn ($q2) => $q2->where('related_type', CaseStudy::class)->where('related_id', $caseStudy->id));
            })
            ->delete();
        $caseStudy->delete();

        return $this->respond($request, 'Case study deleted.', route('admin.case-studies.index'));
    }

    private function syncCaseStudyGraph(Request $request, CaseStudy $study): void
    {
        $this->syncOutgoingRelationIds($study, ContentRelation::RELATED_ARTICLE, Article::class, $request->input('related_article_ids', []));
        $this->syncMorphPivotOrdered($study, 'faqs', $request->input('faq_ids', []));
        $this->syncMorphPivotOrdered($study, 'testimonials', $request->input('testimonial_ids', []));
    }

    private function syncMainImage(Request $request, CaseStudy $study): void
    {
        if ($request->hasFile('main_image')) {
            $path = $request->file('main_image')->store('case-studies', 'public');
            $study->update(['main_image_path' => $path]);
        }
    }

    /**
     * @return Collection<int, Term>
     */
    private function categoryParents(): Collection
    {
        $tax = Taxonomy::query()->where('slug', 'categories')->first();
        if (! $tax) {
            return collect();
        }

        return Term::query()->where('taxonomy_id', $tax->id)->whereNull('parent_id')->orderBy('sort_order')->orderBy('name')->get();
    }

    /**
     * @return Collection<string, Collection<int, Term>>
     */
    private function categoryChildrenGrouped(): Collection
    {
        $tax = Taxonomy::query()->where('slug', 'categories')->first();
        if (! $tax) {
            return collect();
        }

        return Term::query()
            ->where('taxonomy_id', $tax->id)
            ->whereNotNull('parent_id')
            ->with('parent')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->groupBy(fn (Term $t): string => (string) $t->parent_id);
    }

    /**
     * @return Collection<int, Term>
     */
    private function tagTerms(): Collection
    {
        $tax = Taxonomy::query()->where('slug', 'tags')->first();
        if (! $tax) {
            return collect();
        }

        return Term::query()->where('taxonomy_id', $tax->id)->orderBy('sort_order')->orderBy('name')->get();
    }

    /**
     * @param  array<string, mixed>  $data
     * @return list<int>
     */
    private function mergeTermIds(array $data): array
    {
        $cats = $data['category_term_ids'] ?? [];
        $tags = $data['tag_term_ids'] ?? [];
        if (! is_array($cats)) {
            $cats = [];
        }
        if (! is_array($tags)) {
            $tags = [];
        }

        return array_values(array_unique(array_map(static fn ($id): int => (int) $id, array_merge($cats, $tags))));
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?CaseStudy $caseStudy): array
    {
        $resolver = app(VideoEmbedResolver::class);

        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('case_studies', 'slug')->ignore($caseStudy?->id),
            ],
            'summary' => ['nullable', 'string', 'max:500'],
            'body' => ['nullable', 'string', 'max:200000'],
            'status' => ['required', 'string', Rule::in([
                CaseStudy::STATUS_DRAFT,
                CaseStudy::STATUS_PENDING_REVIEW,
                CaseStudy::STATUS_SCHEDULED,
                CaseStudy::STATUS_PUBLISHED,
                CaseStudy::STATUS_ARCHIVED,
            ])],
            'case_study_type' => ['required', 'string', Rule::in([
                CaseStudy::TYPE_REAL,
                CaseStudy::TYPE_ANONYMIZED,
                CaseStudy::TYPE_CONCEPT,
            ])],
            'client_verified' => ['sometimes', 'boolean'],
            'client_name' => ['nullable', 'string', 'max:255'],
            'client_display_name' => ['nullable', 'string', 'max:255'],
            'industry' => ['nullable', 'string', 'max:255'],
            'project_type' => ['nullable', 'string', 'max:255'],
            'challenge' => ['nullable', 'string', 'max:200000'],
            'solution' => ['nullable', 'string', 'max:200000'],
            'implementation' => ['nullable', 'string', 'max:200000'],
            'lessons_learned' => ['nullable', 'string', 'max:200000'],
            'technology_stack_text' => ['nullable', 'string', 'max:10000'],
            'outcomes_text' => ['nullable', 'string', 'max:10000'],
            'metrics_json' => ['nullable', 'string', 'max:10000'],
            'gallery_urls_text' => ['nullable', 'string', 'max:10000'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'canonical_url' => ['nullable', 'string', 'max:500'],
            'robots' => ['nullable', 'string', 'max:120'],
            'video_url' => ['nullable', 'string', 'max:2048', function (string $attribute, mixed $value, \Closure $fail) use ($resolver): void {
                if ($value === null || $value === '') {
                    return;
                }
                if ($resolver->resolve((string) $value) === null) {
                    $fail('Video URL must be a supported YouTube or Vimeo link.');
                }
            }],
            'author_name' => ['nullable', 'string', 'max:120'],
            'author_type' => ['nullable', 'string', Rule::in([CaseStudy::AUTHOR_TYPE_AI, CaseStudy::AUTHOR_TYPE_HUMAN])],
            'featured' => ['sometimes', 'boolean'],
            'published_at' => ['nullable', 'date'],
            'scheduled_for' => ['nullable', 'date'],
            'source_topic' => ['nullable', 'string', 'max:500'],
            'originality_notes' => ['nullable', 'string', 'max:5000'],
            'fact_check_notes' => ['nullable', 'string', 'max:5000'],
            'ai_prompt' => ['nullable', 'string', 'max:50000'],
            'review_required' => ['sometimes', 'boolean'],
            'unlock_slug' => ['sometimes', 'boolean'],
            'category_term_ids' => ['sometimes', 'array'],
            'category_term_ids.*' => ['integer', 'exists:terms,id'],
            'tag_term_ids' => ['sometimes', 'array'],
            'tag_term_ids.*' => ['integer', 'exists:terms,id'],
            'main_image' => ['nullable', 'image', 'max:8192'],
            'clear_main_image' => ['sometimes', 'boolean'],
            'related_article_ids' => ['sometimes', 'array'],
            'related_article_ids.*' => ['integer', 'exists:articles,id'],
            'faq_ids' => ['sometimes', 'array'],
            'faq_ids.*' => ['integer', 'exists:faqs,id'],
            'testimonial_ids' => ['sometimes', 'array'],
            'testimonial_ids.*' => ['integer', 'exists:testimonials,id'],
        ];

        $data = $request->validate($rules) + [
            'featured' => $request->boolean('featured'),
            'review_required' => $request->boolean('review_required'),
            'client_verified' => $request->boolean('client_verified'),
            'category_term_ids' => $request->input('category_term_ids', []),
            'tag_term_ids' => $request->input('tag_term_ids', []),
        ];

        foreach (['challenge', 'solution', 'implementation', 'lessons_learned', 'body'] as $htmlField) {
            if (isset($data[$htmlField]) && is_string($data[$htmlField])) {
                $data[$htmlField] = app(HtmlSanitizer::class)->sanitize($data[$htmlField]);
            }
        }

        $data['technology_stack'] = $this->linesToArray($request->string('technology_stack_text')->toString());
        unset($data['technology_stack_text']);

        $data['outcomes'] = $this->linesToArray($request->string('outcomes_text')->toString());
        unset($data['outcomes_text']);

        $data['metrics'] = $this->decodeMetricsJson($request->string('metrics_json')->toString());
        unset($data['metrics_json']);

        $data['gallery_paths'] = $this->linesToArray($request->string('gallery_urls_text')->toString());
        unset($data['gallery_urls_text']);

        if ($request->boolean('clear_main_image')) {
            $data['main_image_path'] = null;
        }

        unset($data['unlock_slug'], $data['clear_main_image'], $data['main_image']);

        return $data;
    }

    /**
     * @return list<string>
     */
    private function linesToArray(string $raw): array
    {
        $lines = preg_split('/\r\n|\r|\n/', $raw) ?: [];
        $out = [];
        foreach ($lines as $line) {
            $s = trim((string) $line);
            if ($s !== '') {
                $out[] = $s;
            }
        }

        return $out;
    }

    /**
     * @return list<array{label: string, note: string}>|null
     */
    private function decodeMetricsJson(string $raw): ?array
    {
        $raw = trim($raw);
        if ($raw === '') {
            return null;
        }
        $decoded = json_decode($raw, true);
        if (! is_array($decoded)) {
            return null;
        }
        $out = [];
        foreach ($decoded as $row) {
            if (! is_array($row)) {
                continue;
            }
            $label = trim((string) ($row['label'] ?? ''));
            $note = trim((string) ($row['note'] ?? ''));
            if ($label === '' && $note === '') {
                continue;
            }
            $out[] = ['label' => $label !== '' ? $label : 'Metric', 'note' => $note];
        }

        return $out !== [] ? $out : null;
    }

    private function respond(Request $request, string $message, string $redirect): JsonResponse|RedirectResponse
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return redirect()->to($redirect)->with('status', $message);
    }
}
