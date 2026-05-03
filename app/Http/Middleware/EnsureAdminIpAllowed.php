<?php

namespace App\Http\Middleware;

use App\Models\AdminAccessRule;
use App\Services\Security\AdminAccessControlService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminIpAllowed
{
    public function __construct(private AdminAccessControlService $accessControl) {}

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->accessControl->isAllowed($request, AdminAccessRule::AREA_ADMIN)) {
            return $next($request);
        }

        $this->accessControl->logIpDenied($request, 'admin');

        abort(403, 'Access from your network is not permitted for this area.');
    }
}
