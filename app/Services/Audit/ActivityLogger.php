<?php

namespace App\Services\Audit;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ActivityLogger
{
    public function log(string $action, ?Model $subject = null, array $properties = [], ?Request $request = null): ActivityLog
    {
        $req = $request ?? request();

        return ActivityLog::query()->create([
            'actor_id' => $req->user()?->getAuthIdentifier(),
            'action' => $action,
            'subject_type' => $subject ? $subject::class : null,
            'subject_id' => $subject?->getKey(),
            'properties' => $properties === [] ? null : $properties,
            'ip_address' => $req->ip(),
            'created_at' => now(),
        ]);
    }
}
