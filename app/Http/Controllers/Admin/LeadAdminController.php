<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\RespondsWithAdminJson;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LeadStatusUpdateRequest;
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

        $base = Lead::query()->with(['assignee', 'conversation', 'reviewedBy']);

        $statsQuery = Lead::query();

        $stats = [
            'total' => (clone $statsQuery)->count(),
            'new' => (clone $statsQuery)->where('status', Lead::STATUS_NEW)->count(),
            'contacted' => (clone $statsQuery)->where('status', Lead::STATUS_CONTACTED)->count(),
            'converted' => (clone $statsQuery)->where('status', Lead::STATUS_CONVERTED)->count(),
        ];

        if ($request->filled('status')) {
            $base->where('status', $request->string('status'));
        }

        if ($request->filled('service_type')) {
            $base->where('service_type', $request->string('service_type'));
        }

        if ($request->filled('q')) {
            $q = $request->string('q')->toString();
            $base->where(function ($query) use ($q): void {
                $query
                    ->where('name', 'like', '%'.$q.'%')
                    ->orWhere('email', 'like', '%'.$q.'%')
                    ->orWhere('service_type', 'like', '%'.$q.'%');
            });
        }

        $allowedSorts = ['created_at', 'updated_at', 'submitted_at'];
        $sort = $request->query('sort');
        $sort = is_string($sort) && in_array($sort, $allowedSorts, true) ? $sort : null;

        $direction = strtolower((string) $request->query('direction', 'desc')) === 'asc' ? 'asc' : 'desc';

        if ($sort === 'created_at' || $sort === 'updated_at') {
            $base->orderBy($sort, $direction);
        } elseif ($sort === 'submitted_at') {
            $base->orderByRaw('COALESCE(submitted_at, created_at) '.$direction);
        } else {
            $base->orderByRaw('COALESCE(submitted_at, created_at) DESC');
        }

        $leads = $base
            ->paginate(30)
            ->withQueryString();

        return view('admin.leads.index', [
            'leads' => $leads,
            'statuses' => Lead::statuses(),
            'currentStatus' => $request->string('status')->toString(),
            'currentServiceType' => $request->string('service_type')->toString(),
            'searchQuery' => $request->string('q')->toString(),
            'serviceTypes' => Lead::SERVICE_TYPES,
            'stats' => $stats,
        ]);
    }

    public function show(Lead $lead): View
    {
        $this->authorize('view', $lead);

        $lead->load(['assignee', 'reviewedBy', 'conversation.messages']);

        return view('admin.leads.show', [
            'lead' => $lead,
            'staff' => User::query()->orderBy('name')->get(),
        ]);
    }

    public function update(LeadStatusUpdateRequest $request, Lead $lead): JsonResponse|RedirectResponse
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
