<?php

namespace App\Http\Controllers\Admin\Crm;

use App\Http\Controllers\Admin\Concerns\RespondsWithAdminJson;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CrmProjectRequest;
use App\Models\CrmContact;
use App\Models\CrmProject;
use App\Models\User;
use App\Services\Audit\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CrmProjectController extends Controller
{
    use RespondsWithAdminJson;

    public function __construct(
        private ActivityLogger $activityLogger,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', CrmProject::class);

        $q = CrmProject::query()->with(['contact', 'assignee']);

        if ($request->filled('status')) {
            $q->where('status', $request->string('status')->toString());
        }

        return view('admin.crm.projects.index', [
            'projects' => $q->orderByDesc('id')->paginate(25)->withQueryString(),
            'filters' => $request->only(['status']),
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', CrmProject::class);

        $contactId = $request->integer('contact_id');

        return view('admin.crm.projects.form', [
            'project' => new CrmProject(['contact_id' => $contactId ?: null, 'currency' => 'USD']),
            'mode' => 'create',
            'contacts' => CrmContact::query()->orderBy('name')->limit(500)->get(),
            'admins' => User::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(CrmProjectRequest $request): JsonResponse|RedirectResponse
    {
        $this->authorize('create', CrmProject::class);
        $data = $request->validated();
        $slug = $data['slug'] ?? (isset($data['title']) ? Str::slug((string) $data['title']) : null);
        $data['slug'] = $slug !== '' ? $slug : null;
        $data['created_by'] = $request->user()?->id;
        $data['updated_by'] = $request->user()?->id;

        $project = CrmProject::query()->create($data);
        $this->activityLogger->log('crm.project.created', $project, ['id' => $project->id], $request);

        return $this->adminRespond($request, 'Project created.', route('admin.crm.projects.index'));
    }

    public function edit(CrmProject $crmProject): View
    {
        $this->authorize('update', $crmProject);

        return view('admin.crm.projects.form', [
            'project' => $crmProject,
            'mode' => 'edit',
            'contacts' => CrmContact::query()->orderBy('name')->limit(500)->get(),
            'admins' => User::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(CrmProjectRequest $request, CrmProject $crmProject): JsonResponse|RedirectResponse
    {
        $this->authorize('update', $crmProject);
        $data = $request->validated();
        $data['updated_by'] = $request->user()?->id;
        $crmProject->update($data);
        $this->activityLogger->log('crm.project.updated', $crmProject, ['id' => $crmProject->id], $request);

        return $this->adminRespond($request, 'Project updated.', route('admin.crm.projects.index'));
    }

    public function destroy(Request $request, CrmProject $crmProject): JsonResponse|RedirectResponse
    {
        $this->authorize('delete', $crmProject);
        $crmProject->delete();
        $this->activityLogger->log('crm.project.deleted', $crmProject, ['id' => $crmProject->id], $request);

        return $this->adminRespond($request, 'Project deleted.', route('admin.crm.projects.index'));
    }
}
