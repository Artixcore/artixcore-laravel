<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CaseStudy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CaseStudyAdminController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', CaseStudy::class);

        return view('admin.case-studies.index', [
            'caseStudies' => CaseStudy::query()->orderByDesc('updated_at')->paginate(20),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', CaseStudy::class);

        return view('admin.case-studies.form', ['caseStudy' => new CaseStudy, 'mode' => 'create']);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $this->authorize('create', CaseStudy::class);
        $data = $this->validated($request);
        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);
        CaseStudy::query()->create($data);

        return $this->respond($request, 'Case study created.', route('admin.case-studies.index'));
    }

    public function edit(CaseStudy $caseStudy): View
    {
        $this->authorize('update', $caseStudy);

        return view('admin.case-studies.form', ['caseStudy' => $caseStudy, 'mode' => 'edit']);
    }

    public function update(Request $request, CaseStudy $caseStudy): JsonResponse|RedirectResponse
    {
        $this->authorize('update', $caseStudy);
        $data = $this->validated($request);
        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);
        $caseStudy->update($data);

        return $this->respond($request, 'Case study updated.', route('admin.case-studies.index'));
    }

    public function destroy(Request $request, CaseStudy $caseStudy): JsonResponse|RedirectResponse
    {
        $this->authorize('delete', $caseStudy);
        $caseStudy->delete();

        return $this->respond($request, 'Case study deleted.', route('admin.case-studies.index'));
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('case_studies', 'slug')->ignore($request->route('case_study')),
            ],
            'client_name' => ['nullable', 'string', 'max:255'],
            'summary' => ['nullable', 'string', 'max:500'],
            'body' => ['nullable', 'string', 'max:200000'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'string', 'in:draft,published'],
            'featured' => ['sometimes', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ]) + ['featured' => $request->boolean('featured')];
    }

    private function respond(Request $request, string $message, string $redirect): JsonResponse|RedirectResponse
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return redirect()->to($redirect)->with('status', $message);
    }
}
