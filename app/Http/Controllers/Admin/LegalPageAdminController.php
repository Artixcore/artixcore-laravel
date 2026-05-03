<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LegalPage;
use App\Services\HtmlSanitizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LegalPageAdminController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', LegalPage::class);

        return view('admin.legal-pages.index', [
            'pages' => LegalPage::query()->orderBy('slug')->get(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', LegalPage::class);

        return view('admin.legal-pages.form', ['legalPage' => new LegalPage, 'mode' => 'create']);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $this->authorize('create', LegalPage::class);
        $data = $this->validated($request, null);
        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);
        LegalPage::query()->create($data);

        return $this->respond($request, 'Legal page created.', route('admin.legal-pages.index'));
    }

    public function edit(LegalPage $legalPage): View
    {
        $this->authorize('update', $legalPage);

        return view('admin.legal-pages.form', ['legalPage' => $legalPage, 'mode' => 'edit']);
    }

    public function update(Request $request, LegalPage $legalPage): JsonResponse|RedirectResponse
    {
        $this->authorize('update', $legalPage);
        $data = $this->validated($request, $legalPage->id);
        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);
        $legalPage->update($data);

        return $this->respond($request, 'Legal page updated.', route('admin.legal-pages.index'));
    }

    public function destroy(Request $request, LegalPage $legalPage): JsonResponse|RedirectResponse
    {
        $this->authorize('delete', $legalPage);
        $legalPage->delete();

        return $this->respond($request, 'Legal page deleted.', route('admin.legal-pages.index'));
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?int $ignoreId): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('legal_pages', 'slug')->ignore($ignoreId),
            ],
            'body' => ['required', 'string', 'max:500000'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
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
