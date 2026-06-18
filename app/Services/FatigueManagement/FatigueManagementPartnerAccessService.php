<?php

declare(strict_types=1);

namespace App\Services\FatigueManagement;

use App\Models\User;
use App\Support\FatigueManagement\FatigueManagementCompanyResolver;
use App\Support\FatigueManagement\FatigueManagementPartnerAccessContext;

final class FatigueManagementPartnerAccessService
{
    public function contextForUser(?User $user): FatigueManagementPartnerAccessContext
    {
        if ($user === null) {
            return new FatigueManagementPartnerAccessContext(
                isGmoViewer: false,
                isMitraUser: false,
                partnerKey: null,
                partnerName: null,
            );
        }

        if ($this->isGmoViewer($user)) {
            return new FatigueManagementPartnerAccessContext(
                isGmoViewer: true,
                isMitraUser: false,
                partnerKey: null,
                partnerName: null,
            );
        }

        $partnerKey = $this->resolvePartnerKeyForUser($user);
        if ($partnerKey !== null) {
            return new FatigueManagementPartnerAccessContext(
                isGmoViewer: false,
                isMitraUser: true,
                partnerKey: $partnerKey,
                partnerName: FatigueManagementCompanyResolver::partnerToCompany($partnerKey),
            );
        }

        return new FatigueManagementPartnerAccessContext(
            isGmoViewer: true,
            isMitraUser: false,
            partnerKey: null,
            partnerName: null,
        );
    }

    public function canAccessModule(?User $user): bool
    {
        return $user !== null;
    }

    public function isGmoViewer(User $user): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        foreach (config('fatigue_management.gmo_roles', []) as $role) {
            if ($user->hasRole($role)) {
                return true;
            }
        }

        $legacyRole = strtolower((string) ($user->role ?? ''));
        if (in_array($legacyRole, ['admin', 'administrator'], true)) {
            return true;
        }

        return false;
    }

    public function resolvePartnerKeyForUser(User $user): ?string
    {
        return FatigueManagementCompanyResolver::partnerKeyFromUser(
            (string) ($user->email ?? ''),
            (string) ($user->name ?? ''),
        );
    }
}
