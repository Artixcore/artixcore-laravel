<?php

namespace App\Http\Controllers\Admin\Crm;

use App\Http\Controllers\Admin\Concerns\RespondsWithAdminJson;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CrmSourceRequest;
use App\Models\CrmSource;
use App\Services\Audit\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CrmSourceController extends Controller
{
    use RespondsWithAdminJson;

    public function __construct(
        private ActivityLogger $activityLogger,
    ) {}

    public function index(): View
    {
        $this->authorize('viewAny', CrmSource::class);

        return view('admin.crm.sources.index', [
            'sources' => CrmSource::query()->withTrashed()->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function store(CrmSourceRequest $request): JsonResponse|RedirectResponse
    {
        $this->authorize('create', CrmSource::class);
        $data = $request->validated();
        $data['slug'] = isset($data['slug']) && $data['slug'] !== ''
            ? $data['slug']
            : Str::slug($data['name']);
        $data['is_active'] = $request->boolean('is_active', true);
        $source = CrmSource::query()->create($data);
        $this->activityLogger->log('crm.source.created', $source, ['id' => $source->id], $request);

        return $this->adminRespond($request, 'Source created.', route('admin.crm.sources.index'));
    }

    public function update(CrmSourceRequest $request, CrmSource $crmSource): JsonResponse|RedirectResponse
    {
        $this->authorize('update', $crmSource);
        $data = $request->validated();
        if (isset($data['slug']) && $data['slug'] === '') {
            unset($data['slug']);
        }
        if (array_key_exists('slug', $data) && $data['slug'] === null) {
            $data['slug'] = Str::slug($data['name'] ?? $crmSource->name);
        }
        $data['is_active'] = $request->boolean('is_active', $crmSource->is_active);
        $crmSource->update($data);
        $this->activityLogger->log('crm.source.updated', $crmSource, ['id' => $crmSource->id], $request);

        return $this->adminRespond($request, 'Source updated.', route('admin.crm.sources.index'));
    }

    public function destroy(Request $request, CrmSource $crmSource): JsonResponse|RedirectResponse
    {
        $this->authorize('delete', $crmSource);
        $crmSource->delete();
        $this->activityLogger->log('crm.source.deleted', $crmSource, ['id' => $crmSource->id], $request);

        return $this->adminRespond($request, 'Source archived.', route('admin.crm.sources.index'));
    }
}
