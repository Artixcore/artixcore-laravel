<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\RespondsWithAdminJson;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAiAgentRequest;
use App\Http\Requests\Admin\UpdateAiAgentRequest;
use App\Models\AiAgent;
use App\Models\AiProvider;
use App\Services\Audit\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AiAgentAdminController extends Controller
{
    use RespondsWithAdminJson;

    public function __construct(private ActivityLogger $activityLog) {}

    public function index(): View
    {
        $this->authorize('viewAny', AiAgent::class);

        return view('admin.ai-agents.index', [
            'agents' => AiAgent::query()->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', AiAgent::class);

        return view('admin.ai-agents.form', [
            'agent' => new AiAgent(['status' => 'active', 'focus' => 'general']),
            'mode' => 'create',
            'providers' => AiProvider::query()->where('is_enabled', true)->orderBy('priority')->get(),
        ]);
    }

    public function store(StoreAiAgentRequest $request): JsonResponse|RedirectResponse
    {
        $data = $this->normalizedAgentPayload($request->validated());
        $data['created_by'] = $request->user()?->id;
        $data['updated_by'] = $request->user()?->id;

        $agent = AiAgent::query()->create($data);

        $this->activityLog->log('ai_agent.created', $agent, ['slug' => $agent->slug], $request);

        return $this->adminRespond($request, 'Agent created.', route('admin.ai-agents.index'));
    }

    public function edit(AiAgent $ai_agent): View
    {
        $this->authorize('update', $ai_agent);

        return view('admin.ai-agents.form', [
            'agent' => $ai_agent,
            'mode' => 'edit',
            'providers' => AiProvider::query()->where('is_enabled', true)->orderBy('priority')->get(),
        ]);
    }

    public function update(UpdateAiAgentRequest $request, AiAgent $ai_agent): JsonResponse|RedirectResponse
    {
        $data = $this->normalizedAgentPayload($request->validated());
        $data['updated_by'] = $request->user()?->id;
        $ai_agent->update($data);

        $this->activityLog->log('ai_agent.updated', $ai_agent, ['slug' => $ai_agent->slug], $request);

        return $this->adminRespond($request, 'Agent updated.', route('admin.ai-agents.index'));
    }

    public function destroy(Request $request, AiAgent $ai_agent): JsonResponse|RedirectResponse
    {
        $this->authorize('delete', $ai_agent);
        $this->activityLog->log('ai_agent.deleted', $ai_agent, ['slug' => $ai_agent->slug], $request);
        $ai_agent->delete();

        return $this->adminRespond($request, 'Agent deleted.', route('admin.ai-agents.index'));
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalizedAgentPayload(array $data): array
    {
        $map = [
            'languages_json' => 'languages',
            'lead_capture_schema_json' => 'lead_capture_schema',
            'escalation_rules_json' => 'escalation_rules',
            'availability_json' => 'availability',
            'tools_allowed_json' => 'tools_allowed',
        ];
        foreach ($map as $from => $to) {
            if (! array_key_exists($from, $data)) {
                continue;
            }
            $raw = $data[$from];
            unset($data[$from]);
            $data[$to] = $raw === null ? null : json_decode((string) $raw, true);
        }

        return $data;
    }
}
