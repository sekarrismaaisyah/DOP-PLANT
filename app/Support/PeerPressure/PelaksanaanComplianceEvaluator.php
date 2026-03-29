<?php

declare(strict_types=1);

namespace App\Support\PeerPressure;

/**
 * Satu sumber aturan comply KPI "Pelaksanaan Comply" (sama dengan GetPeerPressureDashboardKpiStatsAction).
 */
final class PelaksanaanComplianceEvaluator
{
    public static function isPelaksanaanClosed(mixed $status): bool
    {
        $u = mb_strtoupper(trim((string) $status));

        return str_contains($u, 'CLOSE') || str_contains($u, 'SELESAI');
    }

    public static function hasBerecordId(mixed $id): bool
    {
        return $id !== null && trim((string) $id) !== '';
    }

    /**
     * @return array{comply: bool, alasan: string, reason_code: string}
     */
    public static function evaluate(string $bucket, mixed $status, mixed $idBerecord): array
    {
        $closed = self::isPelaksanaanClosed($status);
        $hasBe = self::hasBerecordId($idBerecord);

        if (KategoriDeviasiBucket::isNonBerecordPolicyBucket($bucket)) {
            if ($closed) {
                return [
                    'comply' => true,
                    'alasan' => 'Pelaksanaan selesai (kategori ini tidak wajib BeRecord).',
                    'reason_code' => 'ok',
                ];
            }

            return [
                'comply' => false,
                'alasan' => 'Pelaksanaan belum selesai (status harus mengandung CLOSED atau SELESAI).',
                'reason_code' => 'fb_belum_selesai',
            ];
        }

        if (KategoriDeviasiBucket::isBerecordPolicyBucket($bucket)) {
            if ($closed && $hasBe) {
                return [
                    'comply' => true,
                    'alasan' => 'Pelaksanaan selesai dan id BeRecord terisi.',
                    'reason_code' => 'ok',
                ];
            }
            if (! $closed && ! $hasBe) {
                return [
                    'comply' => false,
                    'alasan' => 'Belum selesai dan id BeRecord kosong.',
                    'reason_code' => 'be_belum_selesai_dan_tanpa_id',
                ];
            }
            if (! $closed) {
                return [
                    'comply' => false,
                    'alasan' => 'Pelaksanaan belum selesai.',
                    'reason_code' => 'be_belum_selesai',
                ];
            }

            return [
                'comply' => false,
                'alasan' => 'Selesai tetapi id BeRecord kosong (wajib untuk PSPP / Golden Rules / Insiden).',
                'reason_code' => 'be_tanpa_id_berecord',
            ];
        }

        return [
            'comply' => false,
            'alasan' => 'Kategori tidak termasuk pelacakan comply.',
            'reason_code' => 'unknown',
        ];
    }
}
