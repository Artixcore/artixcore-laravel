<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Services\HtmlSanitizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ServiceAdminController extends Controller
{
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

        return view('admin.services.form', ['service' => new Service, 'mode' => 'create']);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $this->authorize('create', Service::class);
        $data = $this->validated($request);
        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);
        Service::query()->create($data);

        return $this->respond($request, 'Service created.', route('admin.services.index'));
    }

    public function edit(Service $service): View
    {
        $this->authorize('update', $service);

        return view('admin.services.form', ['service' => $service, 'mode' => 'edit']);
    }

    public function update(Request $request, Service $service): JsonResponse|RedirectResponse
    {
        $this->authorize('update', $service);
        $data = $this->validated($request);
        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);
        $service->update($data);

        return $this->respond($request, 'Service updated.', route('admin.services.index'));
    }

    public function destroy(Request $request, Service $service): JsonResponse|RedirectResponse
    {
        $this->authorize('delete', $service);
        $service->delete();

        return $this->respond($request, 'Service deleted.', route('admin.services.index'));
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
            'icon' => ['nullable', 'string', 'max:100'],
            'featured_image_media_id' => ['nullable', 'integer', 'exists:media_assets,id'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'string', 'in:draft,published'],
            'published_at' => ['nullable', 'date'],
        ]);

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
