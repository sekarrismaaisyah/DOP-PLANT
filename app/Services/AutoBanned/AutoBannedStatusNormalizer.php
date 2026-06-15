<?php

declare(strict_types=1);

namespace App\Services\AutoBanned;

use App\Enums\AutoBannedSystemStatus;
use App\Models\ScrAutoBannedTbcSap;

class AutoBannedStatusNormalizer
{
    public function normalizeWeek(string $week): string
    {
        $trimmed = strtoupper(trim($week));
        if ($trimmed === '') {
            return '';
        }

        if (str_starts_with($trimmed, 'W')) {
            return $trimmed;
        }

        if (ctype_digit($trimmed)) {
            return 'W'.ltrim($trimmed, '0');
        }

        return $trimmed;
    }

    public function resolveSystemStatus(?string $rawStatus): AutoBannedSystemStatus
    {
        $normalized = strtoupper(trim((string) $rawStatus));

        if ($normalized === '') {
            return AutoBannedSystemStatus::Passed;
        }

        if (
            str_contains($normalized, 'NOT PASS')
            || str_contains($normalized, 'NOT_PASS')
            || str_contains($normalized, 'BANNED')
            || str_contains($normalized, 'FAIL')
        ) {
            if (str_contains($normalized, 'UNBAN') || str_contains($normalized, 'CLEAR')) {
                return AutoBannedSystemStatus::Passed;
            }

            return AutoBannedSystemStatus::NotPassed;
        }

        if (str_contains($normalized, 'PASS') || str_contains($normalized, 'CLEAR') || str_contains($normalized, 'OK')) {
            return AutoBannedSystemStatus::Passed;
        }

        return AutoBannedSystemStatus::NotPassed;
    }

    /**
     * @return array{sid: string, week: string, iso_year: string}
     */
    public function resolveIdentity(ScrAutoBannedTbcSap $row): array
    {
        return [
            'sid' => trim((string) ($row->SID ?? '')),
            'week' => $this->normalizeWeek((string) ($row->Week ?? '')),
            'iso_year' => trim((string) ($row->ISO_Year ?? '')),
        ];
    }
}
