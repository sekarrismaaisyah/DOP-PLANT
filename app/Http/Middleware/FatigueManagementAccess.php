<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\FatigueManagement\FatigueManagementPartnerAccessService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FatigueManagementAccess
{
    public function __construct(
        private readonly FatigueManagementPartnerAccessService $accessService,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $this->accessService->canAccessModule($user)) {
            abort(403, 'Anda tidak memiliki akses ke modul Fatigue Management GMO.');
        }

        return $next($request);
    }
}
