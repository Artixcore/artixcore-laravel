<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobPosting;
use App\Services\HtmlSanitizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JobPostingAdminController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', JobPosting::class);

        return view('admin.job-postings.index', [
            'jobs' => JobPosting::query()->orderBy('sort_order')->orderBy('title')->get(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', JobPosting::class);

        return view('admin.job-postings.form', ['job' => new JobPosting, 'mode' => 'create']);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $this->authorize('create', JobPosting::class);
        JobPosting::query()->create($this->validated($request));

        return $this->respond($request, 'Job posting created.', route('admin.job-postings.index'));
    }

    public function edit(JobPosting $jobPosting): View
    {
        $this->authorize('update', $jobPosting);

        return view('admin.job-postings.form', ['job' => $jobPosting, 'mode' => 'edit']);
    }

    public function update(Request $request, JobPosting $jobPosting): JsonResponse|RedirectResponse
    {
        $this->authorize('update', $jobPosting);
        $jobPosting->update($this->validated($request));

        return $this->respond($request, 'Job posting updated.', route('admin.job-postings.index'));
    }

    public function destroy(Request $request, JobPosting $jobPosting): JsonResponse|RedirectResponse
    {
        $this->authorize('delete', $jobPosting);
        $jobPosting->delete();

        return $this->respond($request, 'Job posting deleted.', route('admin.job-postings.index'));
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'employment_type' => ['nullable', 'string', 'max:100'],
            'body' => ['nullable', 'string', 'max:100000'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_published' => ['sometimes', 'boolean'],
        ]) + ['is_published' => $request->boolean('is_published')];

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
