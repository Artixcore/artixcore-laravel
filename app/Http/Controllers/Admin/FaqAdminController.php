<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Responses\AjaxFormEnvelope;
use App\Http\Support\AjaxRequestExpectations;
use App\Models\Faq;
use App\Services\HtmlSanitizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FaqAdminController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Faq::class);

        return view('admin.faqs.index', [
            'faqs' => Faq::query()->orderBy('sort_order')->get(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Faq::class);

        return view('admin.faqs.form', ['faq' => new Faq, 'mode' => 'create']);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $this->authorize('create', Faq::class);
        Faq::query()->create($this->validated($request));

        return $this->respond($request, 'FAQ created.', route('admin.faqs.index'));
    }

    public function edit(Faq $faq): View
    {
        $this->authorize('update', $faq);

        return view('admin.faqs.form', ['faq' => $faq, 'mode' => 'edit']);
    }

    public function update(Request $request, Faq $faq): JsonResponse|RedirectResponse
    {
        $this->authorize('update', $faq);
        $faq->update($this->validated($request));

        return $this->respond($request, 'FAQ updated.', route('admin.faqs.index'));
    }

    public function destroy(Request $request, Faq $faq): JsonResponse|RedirectResponse
    {
        $this->authorize('delete', $faq);
        $faq->delete();

        return $this->respond($request, 'FAQ deleted.', route('admin.faqs.index'));
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'question' => ['required', 'string', 'max:500'],
            'answer' => ['required', 'string', 'max:20000'],
            'category' => ['nullable', 'string', 'max:120'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'status' => ['nullable', 'string', Rule::in(['draft', 'published', 'archived'])],
            'is_featured' => ['sometimes', 'boolean'],
            'is_published' => ['sometimes', 'boolean'],
            'show_on_general_faq' => ['sometimes', 'boolean'],
            'show_on_saas_page' => ['sometimes', 'boolean'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
        ]) + [
            'is_published' => $request->boolean('is_published'),
            'is_featured' => $request->boolean('is_featured'),
            'show_on_general_faq' => $request->boolean('show_on_general_faq'),
            'show_on_saas_page' => $request->boolean('show_on_saas_page'),
        ];

        if (empty($data['status'])) {
            $data['status'] = $data['is_published'] ? 'published' : 'draft';
        }
        $data['is_published'] = ($data['status'] ?? 'draft') === 'published';

        if (isset($data['answer']) && is_string($data['answer'])) {
            $data['answer'] = app(HtmlSanitizer::class)->sanitize($data['answer']);
        }

        $data['updated_by'] = $request->user()?->id;
        if (! $request->route('faq')) {
            $data['created_by'] = $request->user()?->id;
        }

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
