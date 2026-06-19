<?php

declare(strict_types=1);

namespace App\Services\AutoBanned;

use App\Enums\AutoBannedUnbanStatus;
use App\Models\AutoBannedStatusSnapshot;
use App\Models\AutoBannedUnbanRequest;
use App\Models\ScrAutoBannedTbcSap;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AutoBannedTreatmentService
{
    public function __construct(
        private readonly AutoBannedStatusNormalizer $normalizer,
        private readonly AutoBannedStatusResolver $statusResolver,
    ) {}

    /**
     * @return array{sid: string, karyawan: string, perusahaan: string, site_dedicated: string, banned_reason: string, status_banned_ref: string, week: string, iso_year: string}|null
     */
    public function resolveSidContext(string $sid, string $week, string $year): ?array
    {
        $sid = strtoupper(trim($sid));
        $week = $this->normalizer->normalizeWeek($week);
        $year = trim($year);

        if ($sid === '' || $week === '' || $year === '') {
            return null;
        }

        if (Schema::hasTable('auto_banned_status_snapshots')) {
            $snapshot = AutoBannedStatusSnapshot::query()
                ->where('sid', $sid)
                ->where('week', $week)
                ->where('iso_year', $year)
                ->first();

            if ($snapshot !== null) {
                return [
                    'sid' => $snapshot->sid,
                    'karyawan' => trim((string) ($snapshot->karyawan ?? '')),
                    'perusahaan' => trim((string) ($snapshot->perusahaan ?? '')),
                    'site_dedicated' => trim((string) ($snapshot->site_dedicated ?? '')),
                    'banned_reason' => trim((string) ($snapshot->banned_reason ?? '')),
                    'status_banned_ref' => trim((string) ($snapshot->scrap_status_raw ?? '')),
                    'week' => $week,
                    'iso_year' => $year,
                ];
            }
        }

        if (! Schema::hasTable('scr_auto_banned_tbc_sap')) {
            return null;
        }

        $scrapRow = ScrAutoBannedTbcSap::query()
            ->whereRaw('UPPER(TRIM(CAST(SID AS CHAR))) = ?', [$sid])
            ->whereRaw('UPPER(TRIM(CAST(Week AS CHAR))) = ?', [$week])
            ->whereRaw('TRIM(CAST(ISO_Year AS CHAR)) = ?', [$year])
            ->orderByDesc('id')
            ->first();

        if ($scrapRow === null) {
            return null;
        }

        return [
            'sid' => $sid,
            'karyawan' => trim((string) ($scrapRow->Karyawan ?? '')),
            'perusahaan' => trim((string) ($scrapRow->Perusahaan ?? '')),
            'site_dedicated' => trim((string) ($scrapRow->Site_Dedicated ?? '')),
            'banned_reason' => trim((string) ($scrapRow->Banned_SID_Reason ?? '')),
            'status_banned_ref' => trim((string) ($scrapRow->Status_Banned_SID_SAP ?? '')),
            'week' => $week,
            'iso_year' => $year,
        ];
    }

    public function storeTreatmentEvidence(
        string $sid,
        string $week,
        string $year,
        string $alasanPengajuan,
        UploadedFile $file,
        ?User $user = null,
        string $submitterName = '',
    ): AutoBannedUnbanRequest {
        if (! Schema::hasTable('auto_banned_unban_requests')) {
            throw ValidationException::withMessages([
                'sid' => ['Tabel pengajuan treatment belum tersedia. Jalankan migration.'],
            ]);
        }

        $sid = strtoupper(trim($sid));
        $week = $this->normalizer->normalizeWeek($week);
        $year = trim($year);

        $context = $this->resolveSidContext($sid, $week, $year);
        if ($context === null) {
            throw ValidationException::withMessages([
                'sid' => ['SID tidak ditemukan pada periode yang dipilih.'],
            ]);
        }

        $hasPending = AutoBannedUnbanRequest::query()
            ->where('sid', $sid)
            ->where('week', $week)
            ->where('iso_year', $year)
            ->where('status', AutoBannedUnbanStatus::Pending)
            ->exists();

        if ($hasPending) {
            throw ValidationException::withMessages([
                'sid' => ['SID ini masih memiliki pengajuan treatment yang menunggu review.'],
            ]);
        }

        $storedPath = $file->storeAs(
            'auto-banned/treatment-evidence/'.$year.'/'.$week,
            $sid.'_'.time().'.'.$file->getClientOriginalExtension(),
        );

        $submitterDisplayName = $user !== null
            ? trim((string) ($user->name ?? 'User'))
            : trim($submitterName);

        if ($submitterDisplayName === '') {
            throw ValidationException::withMessages([
                'nama_pengirim' => ['Nama pengirim wajib diisi.'],
            ]);
        }

        $request = AutoBannedUnbanRequest::query()->create([
            'sid' => $context['sid'],
            'karyawan' => $context['karyawan'] !== '' ? $context['karyawan'] : $sid,
            'perusahaan' => $context['perusahaan'] ?: null,
            'site_dedicated' => $context['site_dedicated'] ?: null,
            'banned_reason' => $context['banned_reason'] ?: null,
            'status_banned_ref' => $context['status_banned_ref'] ?: null,
            'alasan_pengajuan' => trim($alasanPengajuan),
            'evidence_file_path' => $storedPath,
            'evidence_original_name' => $file->getClientOriginalName(),
            'evidence_mime' => $file->getMimeType(),
            'evidence_uploaded_at' => now(),
            'status' => AutoBannedUnbanStatus::Pending,
            'week' => $week,
            'iso_year' => $year,
            'submitted_by_id' => $user?->id,
            'submitted_by_name' => $submitterDisplayName,
        ]);

        $this->syncSnapshotWorkflow($sid, $week, $year);

        return $request;
    }

    public function downloadEvidence(AutoBannedUnbanRequest $unbanRequest): StreamedResponse
    {
        $path = trim((string) ($unbanRequest->evidence_file_path ?? ''));

        if ($path === '' || ! Storage::exists($path)) {
            abort(404, 'File evidence tidak ditemukan.');
        }

        $filename = $unbanRequest->evidence_original_name ?: basename($path);

        return Storage::download($path, $filename);
    }

    private function syncSnapshotWorkflow(string $sid, string $week, string $year): void
    {
        if (! Schema::hasTable('auto_banned_status_snapshots')) {
            return;
        }

        $snapshot = AutoBannedStatusSnapshot::query()
            ->where('sid', $sid)
            ->where('week', $week)
            ->where('iso_year', $year)
            ->first();

        if ($snapshot === null) {
            return;
        }

        $this->statusResolver->syncWorkflowFromUnbanRequests($snapshot);

        if ($snapshot->isDirty()) {
            $snapshot->save();
        }
    }
}
