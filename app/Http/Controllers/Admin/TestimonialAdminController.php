<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        return $request->validate([
            'author_name' => ['required', 'string', 'max:255'],
            'role' => ['nullable', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:5000'],
            'avatar_media_id' => ['nullable', 'integer', 'exists:media_assets,id'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_published' => ['sometimes', 'boolean'],
        ]) + ['is_published' => $request->boolean('is_published')];
    }

    private function respond(Request $request, string $message, string $redirect): JsonResponse|RedirectResponse
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return redirect()->to($redirect)->with('status', $message);
    }
}
