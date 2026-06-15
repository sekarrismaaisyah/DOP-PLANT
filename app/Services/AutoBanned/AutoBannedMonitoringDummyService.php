<?php

declare(strict_types=1);

namespace App\Services\AutoBanned;

use App\Enums\AutoBannedBanStatus;
use App\Enums\AutoBannedSystemStatus;
use App\Enums\AutoBannedTreatmentStatus;
use App\Enums\AutoBannedVerificationStatus;
use Illuminate\Support\Collection;

class AutoBannedMonitoringDummyService
{
    /**
     * Data dummy: satu baris per SID — status lifecycle terakhir saja.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function lifecycleRows(): Collection
    {
        return collect([
            $this->row(
                karyawan: 'Indra',
                sid: '556PK',
                systemStatus: AutoBannedSystemStatus::NotPassed,
                followUpLabel: 'Banned 8 April 2026, 09:00 am',
                slaBannedLabel: 'Counting overdue…',
                slaBannedDetail: 'delta waktu banned - waktu list (8 - 7 = 1 hari)',
                slaBannedTone: 'danger',
                banStatus: AutoBannedBanStatus::OnTreatmentBanned,
                remainingBannedLabel: 'On treatment',
                remainingBannedDetail: 'menunggu verifikasi SOD',
                treatmentStatus: AutoBannedTreatmentStatus::Submitted,
                verificationStatus: AutoBannedVerificationStatus::NeedVerifikasi,
                slaUnbannedLabel: '—',
                slaUnbannedDetail: '—',
                slaUnbannedTone: 'muted',
            ),
            $this->row(
                karyawan: 'Budi Santoso',
                sid: '882LM',
                systemStatus: AutoBannedSystemStatus::NotPassed,
                followUpLabel: 'Banned 10 April 2026, 07:30 am',
                slaBannedLabel: 'Counting open…',
                slaBannedDetail: 'delta waktu banned - waktu list (10 - 9 = 1 hari)',
                slaBannedTone: 'wait',
                banStatus: AutoBannedBanStatus::OpenUnbanned,
                remainingBannedLabel: '1 hari',
                remainingBannedDetail: 'hari dia harus unbanned - waktu sekarang',
                treatmentStatus: AutoBannedTreatmentStatus::Submitted,
                verificationStatus: AutoBannedVerificationStatus::Done,
                slaUnbannedLabel: 'Counting open…',
                slaUnbannedDetail: 'delta waktu Done Verifikasi - waktu unbanned (11 - 10 = 1 hari)',
                slaUnbannedTone: 'wait',
            ),
            $this->row(
                karyawan: 'Rina Wulandari',
                sid: '441TR',
                systemStatus: AutoBannedSystemStatus::NotPassed,
                followUpLabel: 'Banned 5 April 2026, 02:15 pm',
                slaBannedLabel: 'Counting overdue…',
                slaBannedDetail: 'delta waktu banned - waktu list (5 - 4 = 1 hari)',
                slaBannedTone: 'danger',
                banStatus: AutoBannedBanStatus::OverdueUnbanned,
                remainingBannedLabel: 'Overdue',
                remainingBannedDetail: 'hari dia harus unbanned - waktu sekarang',
                treatmentStatus: AutoBannedTreatmentStatus::Submitted,
                verificationStatus: AutoBannedVerificationStatus::DoneOverdue,
                slaUnbannedLabel: 'Counting overdue…',
                slaUnbannedDetail: 'delta waktu Done Verifikasi - waktu unbanned (8 - 7 = 1 hari)',
                slaUnbannedTone: 'danger',
            ),
            $this->row(
                karyawan: 'Agus Pratama',
                sid: '993XZ',
                systemStatus: AutoBannedSystemStatus::Passed,
                followUpLabel: 'Registered 12 April 2026, 10:00 am',
                slaBannedLabel: '—',
                slaBannedDetail: '—',
                slaBannedTone: 'muted',
                banStatus: AutoBannedBanStatus::ClosedUnbanned,
                remainingBannedLabel: '—',
                remainingBannedDetail: '—',
                treatmentStatus: AutoBannedTreatmentStatus::Submitted,
                verificationStatus: AutoBannedVerificationStatus::Done,
                slaUnbannedLabel: '—',
                slaUnbannedDetail: '—',
                slaUnbannedTone: 'muted',
            ),
            $this->row(
                karyawan: 'Dewi Lestari',
                sid: '215QW',
                systemStatus: AutoBannedSystemStatus::NotPassed,
                followUpLabel: 'Banned 11 April 2026, 04:45 pm',
                slaBannedLabel: 'Counting open…',
                slaBannedDetail: 'delta waktu banned - waktu list (11 - 10 = 1 hari)',
                slaBannedTone: 'wait',
                banStatus: AutoBannedBanStatus::OpenBanned,
                remainingBannedLabel: '3 hari',
                remainingBannedDetail: 'hari dia harus unbanned - waktu sekarang',
                treatmentStatus: AutoBannedTreatmentStatus::Overdue,
                verificationStatus: AutoBannedVerificationStatus::None,
                slaUnbannedLabel: '—',
                slaUnbannedDetail: '—',
                slaUnbannedTone: 'muted',
            ),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function row(
        string $karyawan,
        string $sid,
        AutoBannedSystemStatus $systemStatus,
        string $followUpLabel,
        string $slaBannedLabel,
        string $slaBannedDetail,
        string $slaBannedTone,
        AutoBannedBanStatus $banStatus,
        string $remainingBannedLabel,
        string $remainingBannedDetail,
        AutoBannedTreatmentStatus $treatmentStatus,
        AutoBannedVerificationStatus $verificationStatus,
        string $slaUnbannedLabel,
        string $slaUnbannedDetail,
        string $slaUnbannedTone,
    ): array {
        return [
            'karyawan' => $karyawan,
            'sid' => $sid,
            'systemStatus' => $systemStatus,
            'followUpLabel' => $followUpLabel,
            'slaBannedLabel' => $slaBannedLabel,
            'slaBannedDetail' => $slaBannedDetail,
            'slaBannedTone' => $slaBannedTone,
            'banStatus' => $banStatus,
            'remainingBannedLabel' => $remainingBannedLabel,
            'remainingBannedDetail' => $remainingBannedDetail,
            'treatmentStatus' => $treatmentStatus,
            'verificationStatus' => $verificationStatus,
            'slaUnbannedLabel' => $slaUnbannedLabel,
            'slaUnbannedDetail' => $slaUnbannedDetail,
            'slaUnbannedTone' => $slaUnbannedTone,
        ];
    }
}
