<?php

declare(strict_types=1);

namespace App\Support\FatigueManagement;

/**
 * Konteks akses mitra untuk modul Fatigue Management GMO.
 */
final readonly class FatigueManagementPartnerAccessContext
{
    public function __construct(
        public bool $isGmoViewer,
        public bool $isMitraUser,
        public ?string $partnerKey,
        public ?string $partnerName,
    ) {}

    public function isLocked(): bool
    {
        return $this->isMitraUser && $this->partnerKey !== null && $this->partnerKey !== '';
    }

    public function resolvePartnerFilter(?string $requested): ?string
    {
        if ($this->isLocked()) {
            return $this->partnerKey;
        }

        $requested = trim((string) $requested);

        return $requested !== '' ? strtoupper($requested) : null;
    }

    public function assertCanAccessPartner(string $partnerKey): void
    {
        if (! $this->isLocked()) {
            return;
        }

        if (strtoupper($partnerKey) !== strtoupper((string) $this->partnerKey)) {
            abort(403, 'Anda tidak berwenang mengakses data mitra ini.');
        }
    }
}
