<?php

declare(strict_types=1);

namespace App\Services\AutoBanned;

use App\Enums\AutoBannedUnbanStatus;
use App\Models\AutoBannedMasterSod;
use App\Models\AutoBannedStatusSnapshot;
use App\Models\AutoBannedUnbanRequest;
use App\Models\ScrAutoBannedTbcSap;
use App\Models\ScrDailyBanned;
use App\Models\User;
use App\Support\AutoBanned\AutoBannedSchema;
use App\Support\AutoBanned\ScrDailyBannedColumns;
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

    /**
     * Resolve SID context dari tabel scr_daily_banned (Tableau Daily Banned).
     *
     * @return array{sid: string, karyawan: string, perusahaan: string, site_dedicated: string, banned_reason: string, status_banned_ref: string, week: string, iso_year: string}|null
     */
    public function resolveSidContextFromScrDailyBanned(string $sid, ?int $scrDailyBannedId = null): ?array
    {
        if (! AutoBannedSchema::hasScrDailyBannedTable()) {
            return null;
        }

        $sid = strtoupper(trim($sid));
        if ($sid === '') {
            return null;
        }

        $query = ScrDailyBanned::query()
            ->whereRaw('UPPER(TRIM('.ScrDailyBannedColumns::SID.')) = ?', [$sid]);

        if ($scrDailyBannedId !== null) {
            $query->where('id', $scrDailyBannedId);
        } else {
            $query->orderByDesc('filter_date')->orderByDesc('scraped_at');
        }

        $row = $query->first([
            'id',
            ScrDailyBannedColumns::SID,
            ScrDailyBannedColumns::NAMA,
            ScrDailyBannedColumns::PERUSAHAAN,
            ScrDailyBannedColumns::SITE,
            ScrDailyBannedColumns::BANNED_REASON,
            ScrDailyBannedColumns::BANNED_STATUS,
        ]);

        if ($row === null) {
            return null;
        }

        $rowSid = strtoupper(trim((string) ($row->{ScrDailyBannedColumns::SID} ?? '')));

        return [
            'sid' => $rowSid !== '' ? $rowSid : $sid,
            'karyawan' => trim((string) ($row->{ScrDailyBannedColumns::NAMA} ?? '')),
            'perusahaan' => trim((string) ($row->{ScrDailyBannedColumns::PERUSAHAAN} ?? '')),
            'site_dedicated' => trim((string) ($row->{ScrDailyBannedColumns::SITE} ?? '')),
            'banned_reason' => trim((string) ($row->{ScrDailyBannedColumns::BANNED_REASON} ?? '')),
            'status_banned_ref' => trim((string) ($row->{ScrDailyBannedColumns::BANNED_STATUS} ?? '')),
            'week' => '',
            'iso_year' => '',
        ];
    }

    /**
     * @return array<int, array{id: int, label: string, filter_date: ?string, banned_reason: string, site: string, nama: string}>
     */
    public function scrDailyBannedOptionsForSid(string $sid): array
    {
        if (! AutoBannedSchema::hasScrDailyBannedTable()) {
            return [];
        }

        $sid = strtoupper(trim($sid));
        if ($sid === '') {
            return [];
        }

        return ScrDailyBanned::query()
            ->whereRaw('UPPER(TRIM('.ScrDailyBannedColumns::SID.')) = ?', [$sid])
            ->orderByDesc('filter_date')
            ->orderByDesc('scraped_at')
            ->limit(50)
            ->get([
                'id',
                'filter_date',
                ScrDailyBannedColumns::BANNED_REASON,
                ScrDailyBannedColumns::BANNED_STATUS,
                ScrDailyBannedColumns::SITE,
                ScrDailyBannedColumns::NAMA,
            ])
            ->map(function (ScrDailyBanned $row): array {
                $filterDate = $row->filter_date?->format('d M Y') ?? '—';
                $reason = trim((string) ($row->{ScrDailyBannedColumns::BANNED_REASON} ?? '')) ?: '—';
                $site = trim((string) ($row->{ScrDailyBannedColumns::SITE} ?? '')) ?: '—';

                return [
                    'id' => (int) $row->id,
                    'label' => sprintf('%s — %s (%s)', $filterDate, $reason, $site),
                    'filter_date' => $row->filter_date?->toDateString(),
                    'banned_reason' => $reason,
                    'site' => $site,
                    'nama' => trim((string) ($row->{ScrDailyBannedColumns::NAMA} ?? '')),
                ];
            })
            ->values()
            ->all();
    }

    public function storeTreatmentEvidence(
        string $sid,
        string $week,
        string $year,
        string $alasanPengajuan,
        UploadedFile $file,
        ?User $user = null,
        string $submitterName = '',
        ?int $scrDailyBannedId = null,
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
            $context = $this->resolveSidContextFromScrDailyBanned($sid, $scrDailyBannedId);
        }
        if ($context === null) {
            throw ValidationException::withMessages([
                'sid' => ['SID tidak ditemukan di data banned.'],
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

        $scrRow = $this->resolveScrDailyBannedForSid($sid, $scrDailyBannedId);

        $directory = 'auto-banned/treatment-evidence/'.$year.'/'.$week;
        Storage::disk('local')->makeDirectory($directory);

        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'bin');
        $extension = preg_replace('/[^a-z0-9]+/', '', $extension) ?: 'bin';
        $filename = $sid.'_'.time().'.'.$extension;

        $storedPath = $file->storeAs($directory, $filename, 'local');

        if ($storedPath === false || ! Storage::disk('local')->exists($storedPath)) {
            throw ValidationException::withMessages([
                'evidence_file' => ['Gagal menyimpan file ke server. Pastikan folder storage/app dapat ditulis (chmod) dan jalankan php artisan storage:link jika diperlukan.'],
            ]);
        }

        $submitterDisplayName = $user !== null
            ? trim((string) ($user->name ?? 'User'))
            : ($submitterName !== '' ? $submitterName : ($context['karyawan'] !== '' ? $context['karyawan'] : 'Form Publik'));

        if ($submitterDisplayName === '') {
            $submitterDisplayName = 'Form Publik';
        }

        $request = AutoBannedUnbanRequest::query()->create([
            'scr_daily_banned_id' => $scrRow?->id,
            'sid' => $context['sid'],
            'karyawan' => $scrRow?->{ScrDailyBannedColumns::NAMA}
                ? trim((string) $scrRow->{ScrDailyBannedColumns::NAMA})
                : ($context['karyawan'] !== '' ? $context['karyawan'] : $sid),
            'perusahaan' => $context['perusahaan'] ?: null,
            'site_dedicated' => $scrRow?->{ScrDailyBannedColumns::SITE}
                ? trim((string) $scrRow->{ScrDailyBannedColumns::SITE})
                : ($context['site_dedicated'] ?: null),
            'banned_reason' => $scrRow?->{ScrDailyBannedColumns::BANNED_REASON}
                ? trim((string) $scrRow->{ScrDailyBannedColumns::BANNED_REASON})
                : ($context['banned_reason'] ?: null),
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

    private function resolveScrDailyBannedForSid(string $sid, ?int $scrDailyBannedId): ?ScrDailyBanned
    {
        if (! AutoBannedSchema::hasScrDailyBannedTable()) {
            return null;
        }

        if ($scrDailyBannedId === null) {
            throw ValidationException::withMessages([
                'scr_daily_banned_id' => ['Pilih record Daily Banned yang terkait.'],
            ]);
        }

        $scrRow = ScrDailyBanned::query()->find($scrDailyBannedId);
        if ($scrRow === null) {
            throw ValidationException::withMessages([
                'scr_daily_banned_id' => ['Record Daily Banned tidak ditemukan.'],
            ]);
        }

        $scrSid = strtoupper(trim((string) ($scrRow->{ScrDailyBannedColumns::SID} ?? '')));
        if ($scrSid !== $sid) {
            throw ValidationException::withMessages([
                'scr_daily_banned_id' => ['Record Daily Banned tidak cocok dengan SID yang dipilih.'],
            ]);
        }

        return $scrRow;
    }

    public function resolveMasterSodWhatsappRedirectUrl(AutoBannedUnbanRequest $unbanRequest): ?string
    {
        if (! Schema::hasTable('auto_banned_master_sods')) {
            return null;
        }

        $site = trim((string) ($unbanRequest->site_dedicated ?? ''));
        if ($site === '') {
            return null;
        }

        $masterSod = AutoBannedMasterSod::query()
            ->whereRaw('UPPER(TRIM(site)) = ?', [mb_strtoupper($site)])
            ->orderBy('id')
            ->first();

        if ($masterSod === null) {
            return null;
        }

        $phone = $this->normalizeWhatsappPhone((string) $masterSod->no_hp);
        if ($phone === '') {
            return null;
        }

        $reviewUrl = route('auto-banned.sod-verification.index', [], true);

        $message = sprintf(
            "Halo %s,\n\nSaya %s (SID: %s) telah mengajukan treatment banned untuk periode %s %s.\n\nMohon bantuannya untuk review.\nLink review: %s\n\nTerima kasih.",
            trim((string) $masterSod->nama),
            trim((string) ($unbanRequest->karyawan ?? $unbanRequest->sid)),
            trim((string) $unbanRequest->sid),
            trim((string) ($unbanRequest->week ?? '')),
            trim((string) ($unbanRequest->iso_year ?? '')),
            $reviewUrl,
        );

        return 'https://wa.me/'.$phone.'?text='.rawurlencode($message);
    }

    private function normalizeWhatsappPhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';
        if ($digits === '') {
            return '';
        }

        if (str_starts_with($digits, '0')) {
            return '62'.substr($digits, 1);
        }

        if (str_starts_with($digits, '62')) {
            return $digits;
        }

        return $digits;
    }

    public function downloadEvidence(AutoBannedUnbanRequest $unbanRequest): StreamedResponse
    {
        $path = trim((string) ($unbanRequest->evidence_file_path ?? ''));

        if ($path === '' || ! Storage::disk('local')->exists($path)) {
            abort(404, 'File evidence tidak ditemukan.');
        }

        $filename = $unbanRequest->evidence_original_name ?: basename($path);

        return Storage::disk('local')->download($path, $filename);
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
