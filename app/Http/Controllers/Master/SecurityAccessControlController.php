<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Master\AdminAccessRuleRequest;
use App\Models\AdminAccessRule;
use App\Services\Audit\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SecurityAccessControlController extends Controller
{
    public function __construct(private ActivityLogger $activityLogger) {}

    public function index(): View
    {
        $rules = AdminAccessRule::query()
            ->orderByDesc('id')
            ->get();

        return view('master.security.access-control', [
            'rules' => $rules,
        ]);
    }

    public function store(AdminAccessRuleRequest $request): RedirectResponse|JsonResponse
    {
        $data = array_merge($request->validated(), [
            'is_active' => $request->boolean('is_active', true),
        ]);
        $data['created_by'] = $request->user()?->id;
        $data['updated_by'] = $request->user()?->id;
        $rule = AdminAccessRule::query()->create($data);
        $this->activityLogger->log('security.ip_rule.created', $rule, ['guard_area' => $rule->guard_area], $request);

        if ($this->wantsJson($request)) {
            return response()->json(['ok' => true, 'message' => 'Rule created.', 'rule' => $rule]);
        }

        return redirect()
            ->route('master.security.access-control')
            ->with('status', 'Rule created.');
    }

    public function update(AdminAccessRuleRequest $request, AdminAccessRule $rule): RedirectResponse|JsonResponse
    {
        $data = $request->validated();
        $data['updated_by'] = $request->user()?->id;
        $rule->update($data);
        $this->activityLogger->log('security.ip_rule.updated', $rule, ['guard_area' => $rule->guard_area], $request);

        if ($this->wantsJson($request)) {
            return response()->json(['ok' => true, 'message' => 'Rule updated.', 'rule' => $rule->fresh()]);
        }

        return redirect()
            ->route('master.security.access-control')
            ->with('status', 'Rule updated.');
    }

    public function destroy(Request $request, AdminAccessRule $rule): RedirectResponse|JsonResponse
    {
        $rule->delete();
        $this->activityLogger->log('security.ip_rule.deleted', $rule, [], $request);

        if ($this->wantsJson($request)) {
            return response()->json(['ok' => true, 'message' => 'Rule deleted.']);
        }

        return redirect()
            ->route('master.security.access-control')
            ->with('status', 'Rule deleted.');
    }

    private function wantsJson(Request $request): bool
    {
        return $request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest';
    }
}
