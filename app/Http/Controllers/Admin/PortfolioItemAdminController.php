<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\SyncsAdminContentGraph;
use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\CaseStudy;
use App\Models\ContentRelation;
use App\Models\Faq;
use App\Models\PortfolioItem;
use App\Models\Service;
use App\Models\Testimonial;
use App\Services\Content\VideoEmbedResolver;
use App\Services\HtmlSanitizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PortfolioItemAdminController extends Controller
{
    use SyncsAdminContentGraph;

    public function index(): View
    {
        $this->authorize('viewAny', PortfolioItem::class);

        return view('admin.portfolio-items.index', [
            'items' => PortfolioItem::query()->orderByDesc('updated_at')->paginate(25),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', PortfolioItem::class);

        return view('admin.portfolio-items.form', $this->formPayload(new PortfolioItem, 'create'));
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $this->authorize('create', PortfolioItem::class);
        $data = $this->validated($request);
        $item = PortfolioItem::query()->create($data);
        $this->syncPortfolioGraph($request, $item);

        return $this->respond($request, 'Portfolio item created.', route('admin.portfolio-items.index'));
    }

    public function edit(PortfolioItem $portfolio_item): View
    {
        $this->authorize('update', $portfolio_item);

        return view('admin.portfolio-items.form', $this->formPayload($portfolio_item, 'edit'));
    }

    public function update(Request $request, PortfolioItem $portfolio_item): JsonResponse|RedirectResponse
    {
        $this->authorize('update', $portfolio_item);
        $portfolio_item->update($this->validated($request, $portfolio_item));
        $this->syncPortfolioGraph($request, $portfolio_item);

        return $this->respond($request, 'Portfolio item updated.', route('admin.portfolio-items.index'));
    }

    public function destroy(Request $request, PortfolioItem $portfolio_item): JsonResponse|RedirectResponse
    {
        $this->authorize('delete', $portfolio_item);
        $portfolio_item->faqs()->detach();
        $portfolio_item->testimonials()->detach();
        ContentRelation::query()
            ->where(function ($q) use ($portfolio_item): void {
                $q->where(fn ($q2) => $q2->where('source_type', PortfolioItem::class)->where('source_id', $portfolio_item->id))
                    ->orWhere(fn ($q2) => $q2->where('related_type', PortfolioItem::class)->where('related_id', $portfolio_item->id));
            })
            ->delete();
        $portfolio_item->delete();

        return $this->respond($request, 'Deleted.', route('admin.portfolio-items.index'));
    }

    /**
     * @return array<string, mixed>
     */
    private function formPayload(PortfolioItem $item, string $mode): array
    {
        return [
            'item' => $item,
            'mode' => $mode === 'create' ? 'create' : 'edit',
            'pickArticles' => Article::query()->orderBy('title')->limit(500)->get(['id', 'title']),
            'pickCaseStudies' => CaseStudy::query()->orderBy('title')->limit(500)->get(['id', 'title']),
            'pickServices' => Service::query()->orderBy('title')->limit(500)->get(['id', 'title']),
            'pickFaqs' => Faq::query()->orderBy('sort_order')->orderBy('question')->get(['id', 'question']),
            'pickTestimonials' => Testimonial::query()->orderBy('sort_order')->orderBy('author_name')->get(['id', 'author_name', 'company']),
            'relatedArticleIds' => old('related_article_ids', $this->existingOutgoing($item, Article::class, ContentRelation::RELATED_ARTICLE)),
            'relatedCaseStudyIds' => old('related_case_study_ids', $this->existingOutgoing($item, CaseStudy::class, ContentRelation::RELATED_CASE_STUDY)),
            'relatedServiceIds' => old('related_service_ids', $this->existingOutgoing($item, Service::class, ContentRelation::RELATED_SERVICE)),
            'faqIds' => old('faq_ids', $item->exists ? $item->faqs()->orderByPivot('sort_order')->pluck('faqs.id')->all() : []),
            'testimonialIds' => old('testimonial_ids', $item->exists ? $item->testimonials()->orderByPivot('sort_order')->pluck('testimonials.id')->all() : []),
        ];
    }

    /**
     * @return list<int>
     */
    private function existingOutgoing(PortfolioItem $item, string $relatedClass, string $relationType): array
    {
        if (! $item->exists) {
            return [];
        }

        return ContentRelation::query()
            ->where('source_type', PortfolioItem::class)
            ->where('source_id', $item->id)
            ->where('related_type', $relatedClass)
            ->where('relation_type', $relationType)
            ->orderBy('sort_order')
            ->pluck('related_id')
            ->map(static fn ($id): int => (int) $id)
            ->all();
    }

    private function syncPortfolioGraph(Request $request, PortfolioItem $item): void
    {
        $this->syncOutgoingRelationIds($item, ContentRelation::RELATED_ARTICLE, Article::class, $request->input('related_article_ids', []));
        $this->syncOutgoingRelationIds($item, ContentRelation::RELATED_CASE_STUDY, CaseStudy::class, $request->input('related_case_study_ids', []));
        $this->syncOutgoingRelationIds($item, ContentRelation::RELATED_SERVICE, Service::class, $request->input('related_service_ids', []));
        $this->syncMorphPivotOrdered($item, 'faqs', $request->input('faq_ids', []));
        $this->syncMorphPivotOrdered($item, 'testimonials', $request->input('testimonial_ids', []));
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?PortfolioItem $item = null): array
    {
        $resolver = app(VideoEmbedResolver::class);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('portfolio_items', 'slug')->ignore($item),
            ],
            'client_name' => ['nullable', 'string', 'max:255'],
            'project_type' => ['nullable', 'string', 'max:255'],
            'industry' => ['nullable', 'string', 'max:255'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'body' => ['nullable', 'string', 'max:200000'],
            'challenge' => ['nullable', 'string', 'max:200000'],
            'solution' => ['nullable', 'string', 'max:200000'],
            'outcome' => ['nullable', 'string', 'max:200000'],
            'technology_stack_json' => ['nullable', 'string', 'max:50000'],
            'main_image_media_id' => ['nullable', 'integer', 'exists:media_assets,id'],
            'video_url' => ['nullable', 'string', 'max:2048', function (string $attribute, mixed $value, \Closure $fail) use ($resolver): void {
                if ($value === null || $value === '') {
                    return;
                }
                if ($resolver->resolve((string) $value) === null) {
                    $fail('Video URL must be YouTube or Vimeo.');
                }
            }],
            'status' => ['required', 'string', Rule::in([PortfolioItem::STATUS_DRAFT, PortfolioItem::STATUS_PUBLISHED])],
            'featured' => ['sometimes', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'published_at' => ['nullable', 'date'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'canonical_url' => ['nullable', 'string', 'max:500'],
            'robots' => ['nullable', 'string', 'max:120'],
            'related_article_ids' => ['sometimes', 'array'],
            'related_article_ids.*' => ['integer', 'exists:articles,id'],
            'related_case_study_ids' => ['sometimes', 'array'],
            'related_case_study_ids.*' => ['integer', 'exists:case_studies,id'],
            'related_service_ids' => ['sometimes', 'array'],
            'related_service_ids.*' => ['integer', 'exists:services,id'],
            'faq_ids' => ['sometimes', 'array'],
            'faq_ids.*' => ['integer', 'exists:faqs,id'],
            'testimonial_ids' => ['sometimes', 'array'],
            'testimonial_ids.*' => ['integer', 'exists:testimonials,id'],
        ]);

        $data['featured'] = $request->boolean('featured');

        $rawTech = $data['technology_stack_json'] ?? null;
        unset($data['technology_stack_json']);
        if ($rawTech === null || trim((string) $rawTech) === '') {
            $data['technology_stack'] = null;
        } else {
            $decoded = json_decode((string) $rawTech, true);
            if (! is_array($decoded)) {
                throw ValidationException::withMessages(['technology_stack_json' => ['Must be valid JSON array of strings.']]);
            }
            $data['technology_stack'] = $decoded;
        }

        foreach (['body', 'challenge', 'solution'] as $htmlField) {
            if (isset($data[$htmlField]) && is_string($data[$htmlField])) {
                $data[$htmlField] = app(HtmlSanitizer::class)->sanitize($data[$htmlField]);
            }
        }

        return $data;
    }

    private function respond(Request $request, string $message, string $redirect): JsonResponse|RedirectResponse
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return redirect()->to($redirect)->with('status', $message);
    }
}
