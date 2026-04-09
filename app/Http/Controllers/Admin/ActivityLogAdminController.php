<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogAdminController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', ActivityLog::class);

        $q = ActivityLog::query()->with('actor')->orderByDesc('id');

        if ($request->filled('action')) {
            $q->where('action', 'like', '%'.$request->string('action').'%');
        }
        if ($request->filled('actor_id')) {
            $q->where('actor_id', $request->integer('actor_id'));
        }

        return view('admin.activity-logs.index', [
            'logs' => $q->paginate(50)->withQueryString(),
            'actors' => User::query()->orderBy('name')->get(),
            'currentAction' => $request->string('action')->toString(),
            'currentActorId' => $request->filled('actor_id') ? $request->integer('actor_id') : null,
        ]);
    }
}
