<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\RespondsWithAdminJson;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateLeadRequest;
use App\Models\Lead;
use App\Models\User;
use App\Services\Audit\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeadAdminController extends Controller
{
    use RespondsWithAdminJson;

    public function __construct(private ActivityLogger $activityLog) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Lead::class);

        $q = Lead::query()->with(['assignee', 'conversation']);

        if ($request->filled('status')) {
            $q->where('status', $request->string('status'));
        }

        return view('admin.leads.index', [
            'leads' => $q->orderByDesc('created_at')->paginate(30)->withQueryString(),
            'statuses' => Lead::statuses(),
            'currentStatus' => $request->string('status')->toString(),
        ]);
    }

    public function show(Lead $lead): View
    {
        $this->authorize('view', $lead);

        $lead->load(['assignee', 'conversation.messages']);

        return view('admin.leads.show', [
            'lead' => $lead,
            'staff' => User::query()->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateLeadRequest $request, Lead $lead): JsonResponse|RedirectResponse
    {
        $data = $request->validated();
        $lead->update($data);

        $this->activityLog->log('lead.updated', $lead, ['status' => $lead->status], $request);

        return $this->adminRespond($request, 'Lead updated.', route('admin.leads.show', $lead));
    }

    public function destroy(Request $request, Lead $lead): JsonResponse|RedirectResponse
    {
        $this->authorize('delete', $lead);
        $this->activityLog->log('lead.deleted', $lead, [], $request);
        $lead->delete();

        return $this->adminRespond($request, 'Lead deleted.', route('admin.leads.index'));
    }
}
