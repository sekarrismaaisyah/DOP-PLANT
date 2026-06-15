<?php

declare(strict_types=1);

namespace App\Services\AutoBanned;

use App\Enums\AutoBannedBanStatus;
use App\Enums\AutoBannedSystemStatus;
use App\Enums\AutoBannedTreatmentStatus;
use App\Enums\AutoBannedVerificationStatus;
use App\Models\AutoBannedStatusSnapshot;
use Carbon\CarbonInterface;

class AutoBannedSlaCalculator
{
    public const BANNED_SLA_DAYS = 3;

    public const UNBAN_SLA_DAYS = 3;

    public const TREATMENT_DEADLINE_DAYS = 1;

    public const VERIFICATION_DEADLINE_DAYS = 1;

    /**
     * @return array{
     *     followUpLabel: string,
     *     slaBannedLabel: string,
     *     slaBannedDetail: string,
     *     slaBannedTone: string,
     *     slaUnbannedLabel: string,
     *     slaUnbannedDetail: string,
     *     slaUnbannedTone: string,
     *     remainingBannedLabel: string,
     *     remainingBannedDetail: string,
     *     banStatus: AutoBannedBanStatus
     * }
     */
    public function resolve(AutoBannedStatusSnapshot $snapshot, CarbonInterface $now): array
    {
        $followUpLabel = $this->buildFollowUpLabel($snapshot);

        if ($snapshot->system_status !== AutoBannedSystemStatus::NotPassed) {
            return [
                'followUpLabel' => $followUpLabel,
                'slaBannedLabel' => '—',
                'slaBannedDetail' => '—',
                'slaBannedTone' => 'muted',
                'slaUnbannedLabel' => '—',
                'slaUnbannedDetail' => '—',
                'slaUnbannedTone' => 'muted',
                'remainingBannedLabel' => '—',
                'remainingBannedDetail' => '—',
                'banStatus' => $snapshot->ban_status,
            ];
        }

        $listAt = $snapshot->first_seen_at;
        $bannedAt = $snapshot->banned_detected_at ?? $snapshot->first_seen_at;
        $bannedDeadline = $bannedAt->copy()->addDays(self::BANNED_SLA_DAYS);
        $bannedOverdue = $now->greaterThan($bannedDeadline);

        $listDay = (int) $listAt->format('j');
        $bannedDay = (int) $bannedAt->format('j');
        $listBannedDelta = max(0, (int) $listAt->copy()->startOfDay()->diffInDays($bannedAt->copy()->startOfDay()));

        $slaBannedLabel = $bannedOverdue ? 'Counting overdue…' : 'Counting open…';
        $slaBannedDetail = "delta waktu banned - waktu list ({$bannedDay} - {$listDay} = {$listBannedDelta} hari)";

        $banStatus = $snapshot->ban_status;
        $slaUnbannedLabel = '—';
        $slaUnbannedDetail = '—';
        $slaUnbannedTone = 'muted';
        $remainingBannedLabel = '—';
        $remainingBannedDetail = '—';

        if ($snapshot->verification_status === AutoBannedVerificationStatus::Done
            || $snapshot->verification_status === AutoBannedVerificationStatus::DoneOverdue) {
            $unbanStart = $snapshot->unban_opened_at ?? $snapshot->verification_done_at ?? $now;
            $unbanDeadline = $unbanStart->copy()->addDays(self::UNBAN_SLA_DAYS);
            $unbanOverdue = $now->greaterThan($unbanDeadline);

            $banStatus = $unbanOverdue
                ? AutoBannedBanStatus::OverdueUnbanned
                : ($snapshot->unban_closed_at
                    ? AutoBannedBanStatus::ClosedUnbanned
                    : AutoBannedBanStatus::OpenUnbanned);

            $slaUnbannedLabel = $unbanOverdue ? 'Counting overdue…' : 'Counting open…';

            $doneDay = (int) ($snapshot->verification_done_at ?? $unbanStart)->format('j');
            $unbanDay = (int) $unbanStart->format('j');
            $unbanDelta = max(0, (int) ($snapshot->verification_done_at ?? $unbanStart)->copy()->startOfDay()->diffInDays($unbanStart->copy()->startOfDay()));
            $slaUnbannedDetail = "delta waktu Done Verifikasi - waktu unbanned ({$doneDay} - {$unbanDay} = {$unbanDelta} hari)";
            $slaUnbannedTone = $unbanOverdue ? 'danger' : 'wait';

            $remainingDays = $unbanDeadline->isFuture() ? (int) $now->diffInDays($unbanDeadline) : 0;
            $remainingBannedLabel = $unbanOverdue ? 'Overdue' : "{$remainingDays} hari";
            $remainingBannedDetail = 'hari dia harus unbanned - waktu sekarang';
        } elseif ($snapshot->treatment_status === AutoBannedTreatmentStatus::Submitted) {
            $banStatus = AutoBannedBanStatus::OnTreatmentBanned;
            $remainingBannedLabel = 'On treatment';
            $remainingBannedDetail = 'menunggu verifikasi SOD';
        } else {
            $banStatus = $bannedOverdue
                ? AutoBannedBanStatus::OverdueBanned
                : AutoBannedBanStatus::OpenBanned;

            $remainingDays = $bannedDeadline->isFuture() ? (int) $now->diffInDays($bannedDeadline) : 0;
            $remainingBannedLabel = $bannedOverdue ? 'Overdue' : "{$remainingDays} hari";
            $remainingBannedDetail = 'hari dia harus unbanned - waktu sekarang';
        }

        return [
            'followUpLabel' => $followUpLabel,
            'slaBannedLabel' => $slaBannedLabel,
            'slaBannedDetail' => $slaBannedDetail,
            'slaBannedTone' => $bannedOverdue ? 'danger' : 'wait',
            'slaUnbannedLabel' => $slaUnbannedLabel,
            'slaUnbannedDetail' => $slaUnbannedDetail,
            'slaUnbannedTone' => $slaUnbannedTone,
            'remainingBannedLabel' => $remainingBannedLabel,
            'remainingBannedDetail' => $remainingBannedDetail,
            'banStatus' => $banStatus,
        ];
    }

    private function buildFollowUpLabel(AutoBannedStatusSnapshot $snapshot): string
    {
        if ($snapshot->system_status === AutoBannedSystemStatus::NotPassed && $snapshot->banned_detected_at !== null) {
            return 'Banned '.$snapshot->banned_detected_at->format('j F Y, h:i a');
        }

        if ($snapshot->first_seen_at !== null) {
            return 'Registered '.$snapshot->first_seen_at->format('j F Y, h:i a');
        }

        return '—';
    }
}
