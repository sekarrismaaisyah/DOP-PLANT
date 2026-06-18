<?php

declare(strict_types=1);

namespace App\Http\Controllers\FatigueManagement\Concerns;

use App\Services\FatigueManagement\FatigueManagementPartnerAccessService;
use App\Support\FatigueManagement\FatigueManagementPartnerAccessContext;
use Illuminate\Http\Request;

trait ResolvesFatigueManagementPartnerAccess
{
    protected function fatiguePartnerAccess(Request $request): FatigueManagementPartnerAccessContext
    {
        return app(FatigueManagementPartnerAccessService::class)
            ->contextForUser($request->user());
    }

    /**
     * @return array<string, mixed>
     */
    protected function fatiguePartnerAccessViewData(FatigueManagementPartnerAccessContext $access): array
    {
        return [
            'locked' => $access->isLocked(),
            'is_gmo_viewer' => $access->isGmoViewer,
            'is_mitra_user' => $access->isMitraUser,
            'partner_key' => $access->partnerKey,
            'partner_name' => $access->partnerName,
        ];
    }
}
