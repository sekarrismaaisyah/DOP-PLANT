<?php

declare(strict_types=1);

namespace App\Support\FatigueManagement;

use App\Enums\FatigueManagementEvidenceStatus;

/**
 * Menilai apakah evidence sudah memenuhi frekuensi pada periode ISO week.
 */
final class FatigueManagementFrequencyChecker
{
    /**
     * @param  list<array{done: bool, status?: string}>  $slotStates
     * @return array{status: string, label: string, color: string, met: bool}
     */
    public static function evaluateFromSlots(string $frequencyRaw, array $slotStates): array
    {
        $total = count($slotStates);
        $done = count(array_filter($slotStates, static fn (array $s): bool => (bool) ($s['done'] ?? false)));

        if ($total === 0) {
            return self::belum();
        }

        if ($done === 0) {
            return self::belum();
        }

        if ($done < $total) {
            $plan = FatigueManagementFrequencyPlan::resolve($frequencyRaw);

            return [
                'status' => 'uploaded',
                'label' => sprintf('Sebagian (%d/%d)', $done, $total),
                'color' => 'amber',
                'met' => false,
            ];
        }

        $hasRevision = count(array_filter(
            $slotStates,
            static fn (array $s): bool => ($s['status'] ?? '') === FatigueManagementEvidenceStatus::PerluLengkap->value,
        )) > 0;

        if ($hasRevision) {
            return [
                'status' => 'perlu_perbaikan',
                'label' => 'Perlu Dilengkapi',
                'color' => 'amber',
                'met' => false,
            ];
        }

        $allVerified = count(array_filter(
            $slotStates,
            static fn (array $s): bool => ($s['status'] ?? '') === FatigueManagementEvidenceStatus::Terverifikasi->value,
        )) === $total;

        $categoryLabel = $plan['category_label'] ?? 'Checklist';

        if ($allVerified) {
            return [
                'status' => 'sesuai',
                'label' => 'Lengkap · ' . $categoryLabel,
                'color' => 'green',
                'met' => true,
            ];
        }

        return [
            'status' => 'uploaded',
            'label' => 'Lengkap · Menunggu Verifikasi',
            'color' => 'blue',
            'met' => true,
        ];
    }

    /**
     * @param  list<array{done: bool, status?: string, label?: string}>  $slotStates
     * @return list<array{label: string, done: bool, key?: string}>
     */
    public static function frequencySlotsFromStates(array $slotStates): array
    {
        return array_map(static fn (array $slot): array => [
            'key' => $slot['key'] ?? '',
            'label' => $slot['label'] ?? '—',
            'done' => (bool) ($slot['done'] ?? false),
        ], $slotStates);
    }

    /**
     * @return array{status: string, label: string, color: string, met: bool}
     */
    private static function belum(): array
    {
        return [
            'status' => 'belum',
            'label' => 'Belum Checklist',
            'color' => 'red',
            'met' => false,
        ];
    }
}
