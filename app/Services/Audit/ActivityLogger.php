<?php

namespace App\Services\Audit;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class ActivityLogger
{
    public function log(string $action, ?Model $subject = null, array $properties = [], ?Request $request = null): ?ActivityLog
    {
        $req = $request ?? request();

        try {
            return ActivityLog::query()->create([
                'actor_id' => $req->user()?->getAuthIdentifier(),
                'action' => $action,
                'subject_type' => $subject ? $subject::class : null,
                'subject_id' => $subject?->getKey(),
                'properties' => $properties === [] ? null : $properties,
                'ip_address' => $req->ip(),
                'created_at' => now(),
            ]);
        } catch (Throwable $e) {
            Log::error('activity_log.write_failed', [
                'action' => $action,
                'exception' => $e::class,
                'message' => $e->getMessage(),
                'route' => $req->route()?->getName(),
                'path' => $req->path(),
            ]);

            return null;
        }
    }
}

