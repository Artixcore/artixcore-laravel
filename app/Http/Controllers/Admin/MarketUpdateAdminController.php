<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Responses\AjaxFormEnvelope;
use App\Http\Support\AjaxRequestExpectations;
use App\Models\MarketUpdate;
use App\Models\Taxonomy;
use App\Models\Term;
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

class MarketUpdateAdminController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', MarketUpdate::class);

        $query = MarketUpdate::query()->with(['terms.taxonomy'])->orderByDesc('updated_at');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }
        if ($request->filled('author_type')) {
            $query->where('author_type', $request->string('author_type')->toString());
        }
        if ($request->filled('market_area')) {
            $query->where('market_area', $request->string('market_area')->toString());
        }
        if ($request->filled('category_term_id')) {
            $tid = (int) $request->input('category_term_id');
            $query->whereHas('terms', fn ($q) => $q->where('terms.id', $tid));
        }
        if ($request->filled('q')) {
            $needle = '%'.str_replace(['%', '_'], ['\\%', '\\_'], trim($request->string('q')->toString())).'%';
            $query->where(function ($q) use ($needle): void {
                $q->where('title', 'like', $needle)->orWhere('excerpt', 'like', $needle);
            });
        }

        return view('admin.market-updates.index', [
            'marketUpdates' => $query->paginate(20)->withQueryString(),
            'categoryParents' => $this->categoryParents(),
            'filters' => $request->only(['status', 'author_type', 'market_area', 'category_term_id', 'q']),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', MarketUpdate::class);

        return view('admin.market-updates.form', [
            'marketUpdate' => new MarketUpdate,
            'mode' => 'create',
            'categoryParents' => $this->categoryParents(),
            'categoryChildren' => $this->categoryChildrenGrouped(),
            'tagTerms' => $this->tagTerms(),
        ]);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $this->authorize('create', MarketUpdate::class);
        $data = $this->validated($request, null);
        $termIds = $this->mergeTermIds($data);
        unset($data['category_term_ids'], $data['tag_term_ids']);

        if (($data['status'] ?? '') === MarketUpdate::STATUS_PUBLISHED) {
            abort_unless($request->user()?->can('market_updates.publish'), 403);
        }

        $data['slug'] = app(UniqueSlugGenerator::class)->unique(
            'market_updates',
            'slug',
            Str::slug($data['slug'] ?? $data['title']) ?: 'market-update',
            null
        );
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        $row = MarketUpdate::query()->create($data);
        $row->terms()->sync($termIds);
        $this->syncMainImage($request, $row);

        return $this->respond($request, 'Market update created.', route('admin.market-updates.index'));
    }

    public function edit(MarketUpdate $marketUpdate): View
    {
        $this->authorize('update', $marketUpdate);
        $marketUpdate->load(['terms.taxonomy']);

        return view('admin.market-updates.form', [
            'marketUpdate' => $marketUpdate,
            'mode' => 'edit',
            'categoryParents' => $this->categoryParents(),
            'categoryChildren' => $this->categoryChildrenGrouped(),
            'tagTerms' => $this->tagTerms(),
        ]);
    }

    public function update(Request $request, MarketUpdate $marketUpdate): JsonResponse|RedirectResponse
    {
        $this->authorize('update', $marketUpdate);
        $data = $this->validated($request, $marketUpdate);
        $termIds = $this->mergeTermIds($data);
        unset($data['category_term_ids'], $data['tag_term_ids']);

        if (($data['status'] ?? '') === MarketUpdate::STATUS_PUBLISHED) {
            abort_unless($request->user()?->can('market_updates.publish'), 403);
        }

        if ($request->boolean('unlock_slug')) {
            $data['slug_locked'] = false;
        }

        if (! ($marketUpdate->slug_locked && ! $request->boolean('unlock_slug'))) {
            $base = ($data['slug'] ?? '') !== '' ? Str::slug($data['slug']) : Str::slug($data['title']);
            $data['slug'] = app(UniqueSlugGenerator::class)->unique('market_updates', 'slug', $base ?: 'market-update', $marketUpdate->id);
        } else {
            unset($data['slug']);
        }

        $data['updated_by'] = Auth::id();
        $marketUpdate->update($data);
        $marketUpdate->terms()->sync($termIds);
        $this->syncMainImage($request, $marketUpdate);

        return $this->respond($request, 'Market update saved.', route('admin.market-updates.index'));
    }

    public function preview(MarketUpdate $marketUpdate): View
    {
        $this->authorize('view', $marketUpdate);
        $s = app(HtmlSanitizer::class);
        $bodyHtml = $s->hardenLinks($s->sanitizeForPublic((string) ($marketUpdate->body ?? '')));

        return view('admin.market-updates.preview', [
            'update' => $marketUpdate->load(['terms.taxonomy', 'terms.parent']),
            'bodyHtml' => $bodyHtml,
            'sectionsHtml' => [
                'trend_summary' => $s->hardenLinks($s->sanitizeForPublic((string) ($marketUpdate->trend_summary ?? ''))),
                'business_impact' => $s->hardenLinks($s->sanitizeForPublic((string) ($marketUpdate->business_impact ?? ''))),
                'technology_impact' => $s->hardenLinks($s->sanitizeForPublic((string) ($marketUpdate->technology_impact ?? ''))),
                'opportunities' => $s->hardenLinks($s->sanitizeForPublic((string) ($marketUpdate->opportunities ?? ''))),
                'risks' => $s->hardenLinks($s->sanitizeForPublic((string) ($marketUpdate->risks ?? ''))),
                'what_next' => $s->hardenLinks($s->sanitizeForPublic((string) ($marketUpdate->what_next ?? ''))),
            ],
            'relatedMarketUpdates' => collect(),
            'videoEmbed' => $marketUpdate->video_embed,
        ]);
    }

    public function destroy(Request $request, MarketUpdate $marketUpdate): JsonResponse|RedirectResponse
    {
        $this->authorize('delete', $marketUpdate);
        $marketUpdate->terms()->detach();
        $marketUpdate->delete();

        return $this->respond($request, 'Market update deleted.', route('admin.market-updates.index'));
    }

    private function syncMainImage(Request $request, MarketUpdate $row): void
    {
        if ($request->hasFile('main_image')) {
            $path = $request->file('main_image')->store('market-updates', 'public');
            $row->update(['main_image_path' => $path]);
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
    private function validated(Request $request, ?MarketUpdate $row): array
    {
        $resolver = app(VideoEmbedResolver::class);

        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('market_updates', 'slug')->ignore($row?->id),
            ],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['nullable', 'string', 'max:200000'],
            'market_area' => ['nullable', 'string', 'max:255'],
            'trend_summary' => ['nullable', 'string', 'max:200000'],
            'business_impact' => ['nullable', 'string', 'max:200000'],
            'technology_impact' => ['nullable', 'string', 'max:200000'],
            'opportunities' => ['nullable', 'string', 'max:200000'],
            'risks' => ['nullable', 'string', 'max:200000'],
            'what_next' => ['nullable', 'string', 'max:200000'],
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
            'status' => ['required', 'string', Rule::in([
                MarketUpdate::STATUS_DRAFT,
                MarketUpdate::STATUS_PENDING_REVIEW,
                MarketUpdate::STATUS_SCHEDULED,
                MarketUpdate::STATUS_PUBLISHED,
                MarketUpdate::STATUS_ARCHIVED,
            ])],
            'author_name' => ['nullable', 'string', 'max:120'],
            'author_type' => ['nullable', 'string', Rule::in([MarketUpdate::AUTHOR_TYPE_AI, MarketUpdate::AUTHOR_TYPE_HUMAN])],
            'featured' => ['sometimes', 'boolean'],
            'published_at' => ['nullable', 'date'],
            'scheduled_for' => ['nullable', 'date'],
            'source_topic' => ['nullable', 'string', 'max:500'],
            'fact_check_notes' => ['nullable', 'string', 'max:5000'],
            'source_requirements' => ['nullable', 'string', 'max:5000'],
            'source_urls_json' => ['nullable', 'string', 'max:10000'],
            'ai_prompt' => ['nullable', 'string', 'max:50000'],
            'review_required' => ['sometimes', 'boolean'],
            'unlock_slug' => ['sometimes', 'boolean'],
            'category_term_ids' => ['sometimes', 'array'],
            'category_term_ids.*' => ['integer', 'exists:terms,id'],
            'tag_term_ids' => ['sometimes', 'array'],
            'tag_term_ids.*' => ['integer', 'exists:terms,id'],
            'main_image' => ['nullable', 'image', 'max:8192'],
            'clear_main_image' => ['sometimes', 'boolean'],
        ];

        $data = $request->validate($rules) + [
            'featured' => $request->boolean('featured'),
            'review_required' => $request->boolean('review_required'),
            'category_term_ids' => $request->input('category_term_ids', []),
            'tag_term_ids' => $request->input('tag_term_ids', []),
        ];

        foreach (['body', 'trend_summary', 'business_impact', 'technology_impact', 'opportunities', 'risks', 'what_next'] as $field) {
            if (isset($data[$field]) && is_string($data[$field])) {
                $data[$field] = app(HtmlSanitizer::class)->sanitize($data[$field]);
            }
        }

        $urlsRaw = trim((string) ($request->input('source_urls_json', '')));
        $data['source_urls'] = null;
        if ($urlsRaw !== '') {
            $decoded = json_decode($urlsRaw, true);
            $data['source_urls'] = is_array($decoded) ? $decoded : null;
        }

        if ($request->boolean('clear_main_image')) {
            $data['main_image_path'] = null;
        }

        unset($data['unlock_slug'], $data['clear_main_image'], $data['main_image']);
        unset($data['source_urls_json']);

        return $data;
    }

    private function respond(Request $request, string $message, string $redirect): JsonResponse|RedirectResponse
    {
        if (AjaxRequestExpectations::prefersJsonResponse($request)) {
            return AjaxFormEnvelope::success($message);
        }

        return redirect()->to($redirect)->with('status', $message);
    }
}
