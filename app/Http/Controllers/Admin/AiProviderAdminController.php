<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\RespondsWithAdminJson;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAiProviderRequest;
use App\Http\Requests\Admin\UpdateAiProviderRequest;
use App\Models\AiProvider;
use App\Services\Ai\AiProviderService;
use App\Services\Audit\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AiProviderAdminController extends Controller
{
    use RespondsWithAdminJson;

    public function __construct(
        private AiProviderService $providers,
        private ActivityLogger $activityLog,
    ) {}

    public function index(): View
    {
        $this->authorize('viewAny', AiProvider::class);

        return view('admin.ai-providers.index', [
            'providers' => AiProvider::query()->orderBy('priority')->orderBy('id')->get(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', AiProvider::class);

        return view('admin.ai-providers.form', ['provider' => new AiProvider, 'mode' => 'create']);
    }

    public function store(StoreAiProviderRequest $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();
        $validated['is_enabled'] = $request->boolean('is_enabled');
        $data = $this->normalizedProviderPayload($validated);
        $this->providers->store($data, $request);

        return $this->adminRespond($request, 'Provider saved.', route('admin.ai-providers.index'));
    }

    public function edit(AiProvider $ai_provider): View
    {
        $this->authorize('update', $ai_provider);

        return view('admin.ai-providers.form', ['provider' => $ai_provider, 'mode' => 'edit']);
    }

    public function update(UpdateAiProviderRequest $request, AiProvider $ai_provider): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();
        $validated['is_enabled'] = $request->boolean('is_enabled');
        $data = $this->normalizedProviderPayload($validated);
        $this->providers->update($ai_provider, $data, $request);

        return $this->adminRespond($request, 'Provider updated.', route('admin.ai-providers.index'));
    }

    public function destroy(Request $request, AiProvider $ai_provider): JsonResponse|RedirectResponse
    {
        $this->authorize('delete', $ai_provider);
        $this->activityLog->log('ai_provider.deleted', $ai_provider, [
            'driver' => $ai_provider->driver,
        ], $request);
        $ai_provider->delete();

        return $this->adminRespond($request, 'Provider deleted.', route('admin.ai-providers.index'));
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalizedProviderPayload(array $data): array
    {
        if (array_key_exists('rate_limit_json', $data)) {
            $raw = $data['rate_limit_json'];
            $data['rate_limit_json'] = $raw === null ? null : json_decode((string) $raw, true);
        }
        if (array_key_exists('metadata_json', $data)) {
            $raw = $data['metadata_json'];
            $data['metadata'] = $raw === null ? null : json_decode((string) $raw, true);
            unset($data['metadata_json']);
        }

        $data['is_enabled'] = (bool) ($data['is_enabled'] ?? true);

        if (array_key_exists('api_key', $data) && ($data['api_key'] === null || $data['api_key'] === '')) {
            unset($data['api_key']);
        }

        return $data;
    }
}
