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
use App\Services\HtmlSanitizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ServiceAdminController extends Controller
{
    use SyncsAdminContentGraph;

    public function index(): View
    {
        $this->authorize('viewAny', Service::class);

        return view('admin.services.index', [
            'services' => Service::query()->orderBy('sort_order')->orderBy('title')->get(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Service::class);

        return view('admin.services.form', $this->formPayload(new Service, 'create'));
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $this->authorize('create', Service::class);
        $data = $this->validated($request);
        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);

        $service = Service::query()->create($data);
        $this->syncServiceGraph($request, $service);

        return $this->respond($request, 'Service created.', route('admin.services.index'));
    }

    public function edit(Service $service): View
    {
        $this->authorize('update', $service);

        return view('admin.services.form', $this->formPayload($service, 'edit'));
    }

    public function update(Request $request, Service $service): JsonResponse|RedirectResponse
    {
        $this->authorize('update', $service);
        $data = $this->validated($request);
        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);
        $service->update($data);
        $this->syncServiceGraph($request, $service);

        return $this->respond($request, 'Service updated.', route('admin.services.index'));
    }

    public function destroy(Request $request, Service $service): JsonResponse|RedirectResponse
    {
        $this->authorize('delete', $service);
        $service->faqs()->detach();
        $service->testimonials()->detach();
        ContentRelation::query()
            ->where(function ($q) use ($service): void {
                $q->where(fn ($q2) => $q2->where('source_type', Service::class)->where('source_id', $service->id))
                    ->orWhere(fn ($q2) => $q2->where('related_type', Service::class)->where('related_id', $service->id));
            })
            ->delete();
        $service->delete();

        return $this->respond($request, 'Service deleted.', route('admin.services.index'));
    }

    /**
     * @return array<string, mixed>
     */
    private function formPayload(Service $service, string $mode): array
    {
        return [
            'service' => $service,
            'mode' => $mode === 'create' ? 'create' : 'edit',
            'pickArticles' => Article::query()->orderBy('title')->limit(500)->get(['id', 'title']),
            'pickCaseStudies' => CaseStudy::query()->orderBy('title')->limit(500)->get(['id', 'title']),
            'pickPortfolio' => PortfolioItem::query()->orderBy('title')->limit(500)->get(['id', 'title']),
            'pickFaqs' => Faq::query()->orderBy('sort_order')->orderBy('question')->get(['id', 'question']),
            'pickTestimonials' => Testimonial::query()->orderBy('sort_order')->orderBy('author_name')->get(['id', 'author_name', 'company']),
            'relatedArticleIds' => old('related_article_ids', $this->existingOutgoingIds($service, Article::class, ContentRelation::RELATED_ARTICLE)),
            'relatedCaseStudyIds' => old('related_case_study_ids', $this->existingOutgoingIds($service, CaseStudy::class, ContentRelation::RELATED_CASE_STUDY)),
            'relatedPortfolioIds' => old('related_portfolio_ids', $this->existingOutgoingIds($service, PortfolioItem::class, ContentRelation::RELATED_PORTFOLIO)),
            'faqIds' => old('faq_ids', $service->exists ? $service->faqs()->orderByPivot('sort_order')->pluck('faqs.id')->all() : []),
            'testimonialIds' => old('testimonial_ids', $service->exists ? $service->testimonials()->orderByPivot('sort_order')->pluck('testimonials.id')->all() : []),
        ];
    }

    /**
     * @return list<int>
     */
    private function existingOutgoingIds(Service $service, string $relatedClass, string $relationType): array
    {
        if (! $service->exists) {
            return [];
        }

        return ContentRelation::query()
            ->where('source_type', Service::class)
            ->where('source_id', $service->id)
            ->where('related_type', $relatedClass)
            ->where('relation_type', $relationType)
            ->orderBy('sort_order')
            ->pluck('related_id')
            ->map(static fn ($id): int => (int) $id)
            ->all();
    }

    private function syncServiceGraph(Request $request, Service $service): void
    {
        $this->syncOutgoingRelationIds($service, ContentRelation::RELATED_ARTICLE, Article::class, $request->input('related_article_ids', []));
        $this->syncOutgoingRelationIds($service, ContentRelation::RELATED_CASE_STUDY, CaseStudy::class, $request->input('related_case_study_ids', []));
        $this->syncOutgoingRelationIds($service, ContentRelation::RELATED_PORTFOLIO, PortfolioItem::class, $request->input('related_portfolio_ids', []));
        $this->syncMorphPivotOrdered($service, 'faqs', $request->input('faq_ids', []));
        $this->syncMorphPivotOrdered($service, 'testimonials', $request->input('testimonial_ids', []));
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('services', 'slug')->ignore($request->route('service')),
            ],
            'summary' => ['nullable', 'string', 'max:500'],
            'body' => ['nullable', 'string', 'max:100000'],
            'benefits_json' => ['nullable', 'string', 'max:50000'],
            'process_json' => ['nullable', 'string', 'max:50000'],
            'technologies_json' => ['nullable', 'string', 'max:50000'],
            'icon' => ['nullable', 'string', 'max:100'],
            'featured_image_media_id' => ['nullable', 'integer', 'exists:media_assets,id'],
            'featured' => ['sometimes', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'string', 'in:draft,published'],
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
            'related_portfolio_ids' => ['sometimes', 'array'],
            'related_portfolio_ids.*' => ['integer', 'exists:portfolio_items,id'],
            'faq_ids' => ['sometimes', 'array'],
            'faq_ids.*' => ['integer', 'exists:faqs,id'],
            'testimonial_ids' => ['sometimes', 'array'],
            'testimonial_ids.*' => ['integer', 'exists:testimonials,id'],
        ]);

        $data['featured'] = $request->boolean('featured');

        foreach (['benefits_json' => 'benefits', 'process_json' => 'process', 'technologies_json' => 'technologies'] as $jsonKey => $col) {
            $raw = $data[$jsonKey] ?? null;
            unset($data[$jsonKey]);
            if ($raw === null || trim((string) $raw) === '') {
                $data[$col] = null;

                continue;
            }
            $decoded = json_decode((string) $raw, true);
            if (! is_array($decoded)) {
                throw ValidationException::withMessages([$jsonKey => ['Must be valid JSON array.']]);
            }
            $data[$col] = $decoded;
        }

        if (isset($data['body']) && is_string($data['body'])) {
            $data['body'] = app(HtmlSanitizer::class)->sanitize($data['body']);
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
