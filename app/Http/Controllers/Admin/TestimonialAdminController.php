<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Responses\AjaxFormEnvelope;
use App\Http\Support\AjaxRequestExpectations;
use App\Models\Testimonial;
use App\Services\HtmlSanitizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TestimonialAdminController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Testimonial::class);

        return view('admin.testimonials.index', [
            'testimonials' => Testimonial::query()->orderBy('sort_order')->get(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Testimonial::class);

        return view('admin.testimonials.form', ['testimonial' => new Testimonial, 'mode' => 'create']);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $this->authorize('create', Testimonial::class);
        $data = $this->validated($request);
        Testimonial::query()->create($data);

        return $this->respond($request, 'Testimonial created.', route('admin.testimonials.index'));
    }

    public function edit(Testimonial $testimonial): View
    {
        $this->authorize('update', $testimonial);

        return view('admin.testimonials.form', ['testimonial' => $testimonial, 'mode' => 'edit']);
    }

    public function update(Request $request, Testimonial $testimonial): JsonResponse|RedirectResponse
    {
        $this->authorize('update', $testimonial);
        $testimonial->update($this->validated($request));

        return $this->respond($request, 'Testimonial updated.', route('admin.testimonials.index'));
    }

    public function destroy(Request $request, Testimonial $testimonial): JsonResponse|RedirectResponse
    {
        $this->authorize('delete', $testimonial);
        $testimonial->delete();

        return $this->respond($request, 'Testimonial deleted.', route('admin.testimonials.index'));
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'author_name' => ['required', 'string', 'max:255'],
            'role' => ['nullable', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'submitter_email' => ['nullable', 'string', 'lowercase', 'email:rfc,dns', 'max:255'],
            'body' => ['required', 'string', 'max:5000'],
            'rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'avatar_media_id' => ['nullable', 'integer', 'exists:media_assets,id'],
            'company_logo_media_id' => ['nullable', 'integer', 'exists:media_assets,id'],
            'service_id' => ['nullable', 'integer', 'exists:services,id'],
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
            'portfolio_item_id' => ['nullable', 'integer', 'exists:portfolio_items,id'],
            'case_study_id' => ['nullable', 'integer', 'exists:case_studies,id'],
            'crm_contact_id' => ['nullable', 'integer', 'exists:crm_contacts,id'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_published' => ['sometimes', 'boolean'],
            'featured' => ['sometimes', 'boolean'],
            'moderation_status' => ['nullable', 'string', Rule::in(['pending', 'approved', 'rejected', 'archived'])],
            'published_at' => ['nullable', 'date'],
        ]) + [
            'is_published' => $request->boolean('is_published'),
            'featured' => $request->boolean('featured'),
        ];

        if (empty($data['moderation_status'])) {
            $data['moderation_status'] = $data['is_published'] ? 'approved' : 'pending';
        }
        $data['is_published'] = ($data['moderation_status'] ?? 'pending') === 'approved';

        if (isset($data['body']) && is_string($data['body'])) {
            $data['body'] = app(HtmlSanitizer::class)->sanitize($data['body']);
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
