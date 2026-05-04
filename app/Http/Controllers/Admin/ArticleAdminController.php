<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\SyncsAdminContentGraph;
use App\Http\Controllers\Controller;
use App\Http\Responses\AjaxFormEnvelope;
use App\Http\Support\AjaxRequestExpectations;
use App\Models\Article;
use App\Models\CaseStudy;
use App\Models\ContentRelation;
use App\Models\Taxonomy;
use App\Models\Term;
use App\Services\Content\VideoEmbedResolver;
use App\Services\HtmlSanitizer;
use App\Support\Content\ArticleTocExtractor;
use App\Support\Slug\UniqueSlugGenerator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ArticleAdminController extends Controller
{
    use SyncsAdminContentGraph;

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Article::class);

        $query = Article::query()->with(['terms.taxonomy', 'media'])->orderByDesc('updated_at');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }
        if ($request->filled('article_type')) {
            $query->where('article_type', $request->string('article_type')->toString());
        }
        if ($request->filled('author_type')) {
            $query->where('author_type', $request->string('author_type')->toString());
        }
        if ($request->filled('q')) {
            $query->search($request->string('q')->toString());
        }

        return view('admin.articles.index', [
            'articles' => $query->paginate(20)->withQueryString(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Article::class);

        return view('admin.articles.form', [
            'article' => new Article,
            'mode' => 'create',
            'categoryParents' => $this->categoryParents(),
            'categoryChildren' => $this->categoryChildrenGrouped(),
            'tagTerms' => $this->tagTerms(),
            'pickArticles' => Article::query()->orderBy('title')->limit(500)->get(['id', 'title', 'slug']),
            'pickCaseStudies' => CaseStudy::query()->orderBy('title')->limit(500)->get(['id', 'title', 'slug']),
            'relatedArticleIds' => old('related_article_ids', []),
            'relatedCaseStudyIds' => old('related_case_study_ids', []),
        ]);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $this->authorize('create', Article::class);
        $data = $this->validated($request);
        $termIds = $this->mergeTermIds($data);
        unset($data['category_term_ids'], $data['tag_term_ids']);
        $data['slug'] = $this->finalizeSlug($data['slug'] ?? null, $data['title'] ?? '', null);
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        if (($data['status'] ?? '') === Article::STATUS_PUBLISHED) {
            abort_unless($request->user()?->can('articles.publish'), 403);
        }

        $article = Article::query()->create($data);
        $article->terms()->sync($termIds);
        $this->syncMainImageUpload($request, $article);
        $this->syncGalleryUploads($request, $article);
        $this->syncArticleGraph($request, $article);

        return $this->respond($request, 'Article created.', route('admin.articles.index'));
    }

    public function edit(Article $article): View
    {
        $this->authorize('update', $article);
        $article->load(['terms.taxonomy', 'media']);

        return view('admin.articles.form', [
            'article' => $article,
            'mode' => 'edit',
            'categoryParents' => $this->categoryParents(),
            'categoryChildren' => $this->categoryChildrenGrouped(),
            'tagTerms' => $this->tagTerms(),
            'pickArticles' => Article::query()->orderBy('title')->limit(500)->get(['id', 'title', 'slug']),
            'pickCaseStudies' => CaseStudy::query()->orderBy('title')->limit(500)->get(['id', 'title', 'slug']),
            'relatedArticleIds' => old(
                'related_article_ids',
                ContentRelation::query()
                    ->where('source_type', Article::class)
                    ->where('source_id', $article->id)
                    ->where('related_type', Article::class)
                    ->where('relation_type', ContentRelation::RELATED_ARTICLE)
                    ->orderBy('sort_order')
                    ->pluck('related_id')
                    ->all()
            ),
            'relatedCaseStudyIds' => old(
                'related_case_study_ids',
                ContentRelation::query()
                    ->where('source_type', Article::class)
                    ->where('source_id', $article->id)
                    ->where('related_type', CaseStudy::class)
                    ->where('relation_type', ContentRelation::RELATED_CASE_STUDY)
                    ->orderBy('sort_order')
                    ->pluck('related_id')
                    ->all()
            ),
        ]);
    }

    public function update(Request $request, Article $article): JsonResponse|RedirectResponse
    {
        $this->authorize('update', $article);
        $data = $this->validated($request, $article);
        $termIds = $this->mergeTermIds($data);
        unset($data['category_term_ids'], $data['tag_term_ids']);

        if (($data['status'] ?? '') === Article::STATUS_PUBLISHED) {
            abort_unless($request->user()?->can('articles.publish'), 403);
        }

        if (! ($article->slug_locked && ! $request->boolean('unlock_slug'))) {
            $data['slug'] = $this->finalizeSlug($data['slug'] ?? null, $data['title'] ?? '', $article->id);
        } else {
            unset($data['slug']);
        }

        $data['updated_by'] = Auth::id();
        $article->update($data);
        $article->terms()->sync($termIds);
        $this->syncMainImageUpload($request, $article);
        $this->syncGalleryUploads($request, $article);
        $this->syncArticleGraph($request, $article);

        return $this->respond($request, 'Article updated.', route('admin.articles.index'));
    }

    public function preview(Article $article): View
    {
        $this->authorize('view', $article);

        $sanitizer = app(HtmlSanitizer::class);
        $sanitized = $sanitizer->sanitizeForPublic($article->body);
        $sanitized = $sanitizer->hardenLinks($sanitized);
        $tocData = ArticleTocExtractor::injectAnchorIds($sanitized);

        return view('admin.articles.preview', [
            'article' => $article->load(['terms.taxonomy', 'media']),
            'articleBodyHtml' => $tocData['html'],
            'toc' => $tocData['toc'],
        ]);
    }

    public function destroy(Request $request, Article $article): JsonResponse|RedirectResponse
    {
        $this->authorize('delete', $article);
        $article->terms()->detach();
        ContentRelation::query()
            ->where(function ($q) use ($article): void {
                $q->where(fn ($q2) => $q2->where('source_type', Article::class)->where('source_id', $article->id))
                    ->orWhere(fn ($q2) => $q2->where('related_type', Article::class)->where('related_id', $article->id));
            })
            ->delete();
        $article->delete();

        return $this->respond($request, 'Article deleted.', route('admin.articles.index'));
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
            ->groupBy(fn (Term $t) => (string) $t->parent_id);
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

    private function finalizeSlug(?string $slug, string $title, ?int $ignoreId): string
    {
        $base = $slug !== null && $slug !== '' ? Str::slug($slug) : Str::slug($title);

        return app(UniqueSlugGenerator::class)->unique('articles', 'slug', $base ?: 'article', $ignoreId);
    }

    private function syncMainImageUpload(Request $request, Article $article): void
    {
        if ($request->hasFile('main_image')) {
            $article->clearMediaCollection('article_main');
            $article->addMediaFromRequest('main_image')->toMediaCollection('article_main');
            $article->update(['main_image_path' => null]);
        }
    }

    private function syncGalleryUploads(Request $request, Article $article): void
    {
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images', []) as $file) {
                if ($file !== null && $file->isValid()) {
                    $article->addMedia($file)->toMediaCollection('article_gallery');
                }
            }
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Article $article = null): array
    {
        $resolver = app(VideoEmbedResolver::class);

        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('articles', 'slug')->ignore($article?->id),
            ],
            'summary' => ['nullable', 'string', 'max:500'],
            'body' => ['nullable', 'string', 'max:200000'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'canonical_url' => ['nullable', 'string', 'max:500'],
            'robots' => ['nullable', 'string', 'max:120'],
            'status' => ['required', 'string', Rule::in([
                Article::STATUS_DRAFT,
                Article::STATUS_PENDING_REVIEW,
                Article::STATUS_SCHEDULED,
                Article::STATUS_PUBLISHED,
                Article::STATUS_ARCHIVED,
            ])],
            'article_type' => ['nullable', 'string', 'max:64'],
            'featured' => ['sometimes', 'boolean'],
            'published_at' => ['nullable', 'date'],
            'scheduled_for' => ['nullable', 'date'],
            'author_name' => ['nullable', 'string', 'max:120'],
            'author_type' => ['nullable', 'string', Rule::in([Article::AUTHOR_TYPE_AI, Article::AUTHOR_TYPE_HUMAN])],
            'video_url' => ['nullable', 'string', 'max:2048', function (string $attribute, mixed $value, \Closure $fail) use ($resolver): void {
                if ($value === null || $value === '') {
                    return;
                }
                if ($resolver->resolve((string) $value) === null) {
                    $fail('Video URL must be a supported YouTube or Vimeo link.');
                }
            }],
            'originality_notes' => ['nullable', 'string', 'max:5000'],
            'plagiarism_score' => ['nullable', 'numeric', 'between:0,100'],
            'review_required' => ['sometimes', 'boolean'],
            'unlock_slug' => ['sometimes', 'boolean'],
            'category_term_ids' => ['sometimes', 'array'],
            'category_term_ids.*' => ['integer', 'exists:terms,id'],
            'tag_term_ids' => ['sometimes', 'array'],
            'tag_term_ids.*' => ['integer', 'exists:terms,id'],
            'main_image' => ['nullable', 'image', 'max:8192'],
            'gallery_images' => ['nullable', 'array'],
            'gallery_images.*' => ['nullable', 'image', 'max:8192'],
            'related_article_ids' => ['sometimes', 'array'],
            'related_article_ids.*' => ['integer', 'exists:articles,id'],
            'related_case_study_ids' => ['sometimes', 'array'],
            'related_case_study_ids.*' => ['integer', 'exists:case_studies,id'],
        ];

        $data = $request->validate($rules) + [
            'featured' => $request->boolean('featured'),
            'review_required' => $request->boolean('review_required'),
            'category_term_ids' => $request->input('category_term_ids', []),
            'tag_term_ids' => $request->input('tag_term_ids', []),
        ];

        if (isset($data['body']) && is_string($data['body'])) {
            $data['body'] = app(HtmlSanitizer::class)->sanitize($data['body']);
        }

        return $data;
    }

    private function syncArticleGraph(Request $request, Article $article): void
    {
        $this->syncOutgoingRelationIds(
            $article,
            ContentRelation::RELATED_ARTICLE,
            Article::class,
            array_values(array_filter(
                $request->input('related_article_ids', []),
                static fn ($id): bool => (int) $id !== (int) $article->id
            ))
        );
        $this->syncOutgoingRelationIds(
            $article,
            ContentRelation::RELATED_CASE_STUDY,
            CaseStudy::class,
            $request->input('related_case_study_ids', [])
        );
    }

    private function respond(Request $request, string $message, string $redirect): JsonResponse|RedirectResponse
    {
        if (AjaxRequestExpectations::prefersJsonResponse($request)) {
            return AjaxFormEnvelope::success($message);
        }

        return redirect()->to($redirect)->with('status', $message);
    }
}
