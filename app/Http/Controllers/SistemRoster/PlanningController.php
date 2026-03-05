<?php

namespace App\Http\Controllers\SistemRoster;

use App\Http\Controllers\Controller;
use App\Jobs\GeneratePlanningJob;
use App\Models\CctvCoverage;
use App\Models\CctvData;
use App\Models\InsidenTabel;
use App\Models\RosterPlanning;
use App\Models\RosterPlanningJob;
use App\Models\RosterPlanningKaryawan;
use App\Services\ClickHouseService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PlanningController extends Controller
{
    public function index(Request $request): View
    {
        $filterStartDate = $request->get('start_date', now()->toDateString());
        $filterEndDate = $request->get('end_date', now()->toDateString());
        
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $filterStartDate)) {
            $filterStartDate = now()->toDateString();
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $filterEndDate)) {
            $filterEndDate = now()->toDateString();
        }
        
        if ($filterStartDate > $filterEndDate) {
            $temp = $filterStartDate;
            $filterStartDate = $filterEndDate;
            $filterEndDate = $temp;
        }

        $perPage = (int) $request->get('per_page', 25);
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 25;

        $search = $request->get('search', '');
        $filterSite = $request->get('filter_site', '');
        $filterPerusahaan = $request->get('filter_perusahaan', '');

        $query = RosterPlanning::with(['karyawans' => function ($q) {
                $q->select('id', 'roster_planning_id', 'nama_karyawan');
            }])
            ->select([
                'id', 'tanggal', 'source_type', 'source_id', 'site', 'no_ikk', 
                'aktivitas', 'lokasi', 'detail_lokasi', 'shift', 
                'perusahaan_pic', 'status', 'created_at'
            ])
            ->whereBetween('tanggal', [$filterStartDate, $filterEndDate]);

        if ($search !== '') {
            $term = '%' . trim($search) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('no_ikk', 'like', $term)
                    ->orWhere('aktivitas', 'like', $term)
                    ->orWhere('lokasi', 'like', $term)
                    ->orWhere('detail_lokasi', 'like', $term)
                    ->orWhere('site', 'like', $term)
                    ->orWhere('perusahaan_pic', 'like', $term)
                    ->orWhere('pengawas_langsung', 'like', $term);
            });
        }
        if ($filterSite !== '') {
            $query->where('site', 'like', '%' . trim($filterSite) . '%');
        }
        if ($filterPerusahaan !== '') {
            $query->where('perusahaan_pic', 'like', '%' . trim($filterPerusahaan) . '%');
        }

        $plannings = $query->orderByDesc('tanggal')
            ->orderBy('source_type')
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();

        $sites = RosterPlanning::whereBetween('tanggal', [$filterStartDate, $filterEndDate])
            ->whereNotNull('site')
            ->where('site', '!=', '')
            ->distinct()
            ->orderBy('site')
            ->pluck('site');

        $perusahaanList = RosterPlanning::whereBetween('tanggal', [$filterStartDate, $filterEndDate])
            ->whereNotNull('perusahaan_pic')
            ->where('perusahaan_pic', '!=', '')
            ->distinct()
            ->orderBy('perusahaan_pic')
            ->pluck('perusahaan_pic');

        $latestJob = RosterPlanningJob::whereIn('status', ['pending', 'processing'])
            ->select(['id', 'job_id', 'status', 'start_date', 'end_date', 'created_at'])
            ->orderByDesc('created_at')
            ->first();

        $queueConnection = config('queue.default');

        return view('SistemRoster.planning.index', [
            'plannings' => $plannings,
            'filterStartDate' => $filterStartDate,
            'filterEndDate' => $filterEndDate,
            'perPage' => $perPage,
            'search' => $search,
            'filterSite' => $filterSite,
            'filterPerusahaan' => $filterPerusahaan,
            'sites' => $sites,
            'perusahaanList' => $perusahaanList,
            'users' => [],
            'latestJob' => $latestJob,
            'queueConnection' => $queueConnection,
        ]);
    }

    public function generate(Request $request): RedirectResponse
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        if ($startDate > $endDate) {
            $temp = $startDate;
            $startDate = $endDate;
            $endDate = $temp;
        }

        $existingJob = RosterPlanningJob::whereIn('status', ['pending', 'processing'])
            ->where('start_date', $startDate)
            ->where('end_date', $endDate)
            ->first();

        if ($existingJob) {
            return redirect()
                ->route('sistem-roster.planning.index', [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ])
                ->with('warning', 'Proses generate untuk periode ini sedang berjalan. Silakan tunggu hingga selesai.');
        }

        $jobId = Str::uuid()->toString();

        $planningJob = RosterPlanningJob::create([
            'job_id' => $jobId,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'pending',
            'user_id' => Auth::id(),
        ]);

        GeneratePlanningJob::dispatch($jobId, $startDate, $endDate);

        $queueConnection = config('queue.default');
        $queueNote = $queueConnection !== 'sync'
            ? ' Pastikan queue worker berjalan: php artisan queue:work'
            : '';

        return redirect()
            ->route('sistem-roster.planning.index', [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ])
            ->with('info', 'Proses generate planning sedang berjalan di background. Setelah selesai, data DOP & IKK akan muncul di tabel.' . $queueNote);
    }

    public function jobStatus(Request $request): JsonResponse
    {
        $jobId = $request->get('job_id');
        
        if ($jobId) {
            $job = RosterPlanningJob::where('job_id', $jobId)->first();
        } else {
            $job = RosterPlanningJob::whereIn('status', ['pending', 'processing'])
                ->orderByDesc('created_at')
                ->first();
        }

        if (!$job) {
            return response()->json([
                'status' => 'not_found',
                'message' => 'Tidak ada job yang sedang berjalan',
            ]);
        }

        return response()->json([
            'status' => $job->status,
            'job_id' => $job->job_id,
            'start_date' => $job->start_date->format('Y-m-d'),
            'end_date' => $job->end_date->format('Y-m-d'),
            'dop_created' => $job->dop_created,
            'dop_updated' => $job->dop_updated,
            'ikk_created' => $job->ikk_created,
            'ikk_updated' => $job->ikk_updated,
            'error_message' => $job->error_message,
            'started_at' => $job->started_at?->format('H:i:s'),
            'completed_at' => $job->completed_at?->format('H:i:s'),
        ]);
    }

    public function getUsers(Request $request): JsonResponse
    {
        $search = $request->get('search', '');
        
        $query = DB::table('vw_user')
            ->select('id', 'nik', 'nama')
            ->where('is_active', '1');
        
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }
        
        $users = $query->orderBy('nama')->limit(50)->get();
        
        return response()->json($users);
    }

    public function getKaryawans($id): JsonResponse
    {
        $planning = RosterPlanning::with(['karyawans' => function ($q) {
            $q->select('id', 'roster_planning_id', 'user_id', 'nama_karyawan', 'sid_karyawan', 'task', 'reason', 'detail');
        }])->select('id', 'aktivitas', 'tanggal', 'source_type', 'no_ikk', 'lokasi', 'detail_lokasi')
          ->findOrFail($id);

        return response()->json([
            'planning' => $planning,
            'karyawans' => $planning->karyawans,
        ]);
    }

    public function assignKaryawan(Request $request, $id): JsonResponse|RedirectResponse
    {
        $planning = RosterPlanning::findOrFail($id);

        $karyawans = $request->input('karyawans', []);

        $planning->karyawans()->delete();

        if (!empty($karyawans)) {
            foreach ($karyawans as $karyawanData) {
                $nama = $karyawanData['nama_karyawan'] ?? null;
                if (!empty($nama)) {
                    RosterPlanningKaryawan::create([
                        'roster_planning_id' => $planning->id,
                        'user_id' => $karyawanData['user_id'] ?? null,
                        'nama_karyawan' => $nama,
                        'sid_karyawan' => $karyawanData['sid_karyawan'] ?? null,
                        'task' => $karyawanData['task'] ?? null,
                        'reason' => $karyawanData['reason'] ?? null,
                        'detail' => $karyawanData['detail'] ?? null,
                    ]);
                }
            }
        }

        $newStatus = $planning->karyawans()->count() > 0 ? 'assigned' : 'draft';
        $planning->update(['status' => $newStatus]);

        if ($request->expectsJson()) {
            $payload = [
                'success' => true,
                'status' => $newStatus,
                'count' => $planning->karyawans()->count(),
            ];
            if ($newStatus === 'assigned') {
                $payload['planning'] = [
                    'id' => $planning->id,
                    'tanggal' => $planning->tanggal?->format('Y-m-d'),
                    'tanggal_formatted' => $planning->tanggal?->format('d M Y'),
                    'source_type' => $planning->source_type ?? '',
                    'site' => $planning->site ?? '',
                    'no_ikk' => $planning->no_ikk ?? '',
                    'aktivitas' => $planning->aktivitas ?? '',
                    'lokasi' => $planning->lokasi ?? '',
                    'detail_lokasi' => $planning->detail_lokasi ?? '',
                    'perusahaan_pic' => $planning->perusahaan_pic ?? '',
                ];
            }
            return response()->json($payload);
        }

        return redirect()
            ->back()
            ->with('success', 'Karyawan berhasil di-assign.');
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $planning = RosterPlanning::findOrFail($id);

        $validated = $request->validate([
            'shift' => 'nullable|string|max:50',
            'kategori_area' => 'nullable|string|max:255',
            'jenis_sap' => 'nullable|string|max:100',
            'status' => 'nullable|in:draft,assigned,completed',
        ]);

        $planning->update($validated);

        return redirect()
            ->back()
            ->with('success', 'Planning berhasil diupdate.');
    }

    /**
     * Konten pesan WA untuk planning yang di-assign: "Kamu harus mengunjungi" + summary lokasi (insiden, aktivitas kritis, statistik).
     */
    public function waMessageContent($id): JsonResponse
    {
        $planning = RosterPlanning::findOrFail($id);
        $tanggal = $planning->tanggal ? Carbon::parse($planning->tanggal) : today();
        $filterLokasi = $planning->lokasi !== null && $planning->lokasi !== '' ? preg_replace('/\s+/', ' ', trim($planning->lokasi)) : null;
        $filterDetailLokasi = $planning->detail_lokasi !== null && $planning->detail_lokasi !== '' ? preg_replace('/\s+/', ' ', trim($planning->detail_lokasi)) : null;

        // Aktivitas kritis di lokasi ini (termasuk baris planning ini)
        $aktivitasQuery = RosterPlanning::whereDate('tanggal', $tanggal);
        if ($filterLokasi !== null && $filterLokasi !== '') {
            $aktivitasQuery->whereRaw("REGEXP_REPLACE(TRIM(lokasi), '[[:space:]]+', ' ') = ?", [$filterLokasi]);
        }
        if ($filterDetailLokasi !== null && $filterDetailLokasi !== '') {
            $aktivitasQuery->whereRaw("REGEXP_REPLACE(TRIM(detail_lokasi), '[[:space:]]+', ' ') = ?", [$filterDetailLokasi]);
        }
        $aktivitasKritis = $aktivitasQuery->orderBy('lokasi')->orderBy('detail_lokasi')->orderBy('no_ikk')->get();

        // Insiden
        $insidenQuery = InsidenTabel::whereNotNull('lokasi')->where('lokasi', '!=', '');
        if ($filterLokasi !== null && $filterLokasi !== '') {
            $insidenQuery->whereRaw("REGEXP_REPLACE(TRIM(lokasi), '[[:space:]]+', ' ') = ?", [$filterLokasi]);
        }
        $totalInsiden = (int) $insidenQuery->selectRaw('COUNT(DISTINCT no_kecelakaan) as total')->value('total');

        $recentInsidenQuery = InsidenTabel::whereNotNull('lokasi')->where('lokasi', '!=', '');
        if ($filterLokasi !== null && $filterLokasi !== '') {
            $recentInsidenQuery->whereRaw("REGEXP_REPLACE(TRIM(lokasi), '[[:space:]]+', ' ') = ?", [$filterLokasi]);
        }
        $recentInsiden = $recentInsidenQuery->orderByRaw('tahun DESC, bulan DESC, tanggal DESC')->get()->unique('no_kecelakaan')->take(10)->values();

        // CCTV
        $normLokasi = $filterLokasi !== null && $filterLokasi !== '' ? preg_replace('/\s+/', ' ', strtolower(trim($filterLokasi))) : null;
        $normDetail = $filterDetailLokasi !== null && $filterDetailLokasi !== '' ? preg_replace('/\s+/', ' ', strtolower(trim($filterDetailLokasi))) : null;
        $coverageQuery = CctvCoverage::query()->select('id_cctv');
        if ($normLokasi !== null) {
            $coverageQuery->whereRaw('LOWER(REGEXP_REPLACE(TRIM(coverage_lokasi), \'[[:space:]]+\', \' \')) = ?', [$normLokasi]);
        }
        if ($normDetail !== null) {
            $coverageQuery->whereRaw('LOWER(REGEXP_REPLACE(TRIM(coverage_detail_lokasi), \'[[:space:]]+\', \' \')) = ?', [$normDetail]);
        }
        $cctvIds = $coverageQuery->pluck('id_cctv')->unique()->values()->map(fn ($id) => (int) str_replace(',', '', (string) $id))->filter(fn ($id) => $id > 0)->unique()->values()->all();
        $cctvActiveCount = 0;
        if (! empty($cctvIds)) {
            $cctvList = CctvData::query()->select('id', 'kondisi')->whereIn('id', $cctvIds)->get();
            $cctvActiveCount = $cctvList->filter(fn ($c) => strtolower(trim((string) ($c->kondisi ?? ''))) === 'baik')->count();
        }

        // Hazard & Inspeksi Open minggu ini
        $totalHazardWeekly = 0;
        $weekStartStr = $tanggal->copy()->startOfWeek()->format('Y-m-d');
        $weekEndStr = $tanggal->copy()->startOfWeek()->addWeek()->format('Y-m-d');
        $conditions = [
            "jenis_laporan IN ('HAZARD', 'INSPEKSI')",
            "trim(ifNull(status, '')) = 'SUBMITTED'",
            "((tanggal_pembuatan IS NOT NULL AND toDate(tanggal_pembuatan, 'Asia/Makassar') >= toDate('" . addslashes($weekStartStr) . "') AND toDate(tanggal_pembuatan, 'Asia/Makassar') < toDate('" . addslashes($weekEndStr) . "')) "
            . "or (bedraft_date IS NOT NULL AND toDate(bedraft_date, 'Asia/Makassar') >= toDate('" . addslashes($weekStartStr) . "') AND toDate(bedraft_date, 'Asia/Makassar') < toDate('" . addslashes($weekEndStr) . "')))",
        ];
        if ($filterLokasi !== null && $filterLokasi !== '') {
            $conditions[] = "replaceRegexpAll(trim(nama_lokasi), '\\\\s+', ' ') = '" . addslashes($filterLokasi) . "'";
        }
        if ($filterDetailLokasi !== null && $filterDetailLokasi !== '') {
            $conditions[] = "replaceRegexpAll(trim(nama_detail_lokasi), '\\\\s+', ' ') = '" . addslashes($filterDetailLokasi) . "'";
        }
        $whereClause = implode(' AND ', $conditions);
        $sqlCount = "SELECT count() AS total FROM hse_automation.aaj_car_all_year_from_dav WHERE {$whereClause}";
        try {
            if (class_exists(ClickHouseService::class)) {
                $ch = app(ClickHouseService::class);
                if (method_exists($ch, 'query') && $ch->isConnected()) {
                    $results = $ch->query($sqlCount);
                    if (! empty($results)) {
                        $totalHazardWeekly = (int) ($this->getClickHouseRowValue($results[0], 'total') ?? 0);
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::warning('PlanningController waMessageContent: hazard weekly count failed: ' . $e->getMessage());
        }

        $lokasiLabel = $filterLokasi ?: '—';
        $detailLabel = $filterDetailLokasi ?: '—';

        // Link Tasklist: tanggal = tanggal planning, lokasi & detail_lokasi dari planning yang di-assign
        $tasklistUrl = url(route('sistem-roster.tasklist.index', [
            'tanggal' => $planning->tanggal?->format('Y-m-d') ?? now()->format('Y-m-d'),
            'lokasi' => $planning->lokasi ?? '',
            'detail_lokasi' => $planning->detail_lokasi ?? '',
        ]));

        // Build message text — format per baris: Label : nilai (bukan tabel)
        $lines = [];
        $lines[] = 'Kamu harus mengunjungi:';
        $lines[] = '';
        $lines[] = 'Tanggal : ' . ($planning->tanggal?->format('d M Y') ?? '—');
        $lines[] = 'Sumber : ' . ($planning->source_type ?? '—');
        $lines[] = 'Site : ' . ($planning->site ?? '—');
        $lines[] = 'No IKK : ' . ($planning->no_ikk ?? '—');
        $lines[] = 'Aktivitas : ' . ($planning->aktivitas ?? '—');
        $lines[] = 'Lokasi : ' . ($planning->lokasi ?? '—');
        $lines[] = 'Detail Lokasi : ' . ($planning->detail_lokasi ?? '—');
        $lines[] = 'Perusahaan : ' . ($planning->perusahaan_pic ?? '—');
        $lines[] = '';
        $lines[] = 'Link Tasklist (Summary Detail Lokasi):';
        $lines[] = $tasklistUrl;
        $lines[] = '';
        $lines[] = '--- Summary Lokasi: ' . $lokasiLabel . ' / ' . $detailLabel . ' ---';
        $lines[] = '';
        $lines[] = 'Statistik:';
        $lines[] = '• Total Insiden YTD: ' . $totalInsiden;
        $lines[] = '• Total Area/Aktivitas Kritis: ' . $aktivitasKritis->count();
        $lines[] = '• Total CCTV: ' . $cctvActiveCount;
        $lines[] = '• Total Hazard Weekly: ' . $totalHazardWeekly;
        $lines[] = '• Hazard & Inspeksi Open (SUBMITTED) Minggu Ini: ' . $totalHazardWeekly;
        $lines[] = '';

        if ($recentInsiden->isNotEmpty()) {
            $lines[] = 'Detail Insiden:';
            foreach ($recentInsiden as $ins) {
                $lines[] = '  - ' . ($ins->no_kecelakaan ?? '—') . ' | ' . ($ins->lokasi ?? '—') . ($ins->sublokasi ? ' • ' . $ins->sublokasi : '') . ' | ' . ($ins->kategori ?? $ins->status_lpi ?? '—');
            }
            $lines[] = '';
        }

        if ($aktivitasKritis->isNotEmpty()) {
            $lines[] = 'Aktivitas Kritis:';
            foreach ($aktivitasKritis as $row) {
                $lines[] = '  - ' . ($row->aktivitas ?? '—') . ' | IKK: ' . ($row->no_ikk ?? '—') . ' | ' . ($row->lokasi ?? '—') . ' / ' . ($row->detail_lokasi ?? '—') . ' | ' . ($row->perusahaan_pic ?? '—');
            }
        }

        $message = implode("\n", $lines);

        // Karyawan yang di-assign + selular dari vw_user (untuk WA otomatis ke nomor tujuan)
        $karyawansWithSelular = [];
        $assignedKaryawans = $planning->karyawans()->get();
        $userIds = $assignedKaryawans->pluck('user_id')->filter()->unique()->values()->all();
        if (! empty($userIds)) {
            $users = DB::table('vw_user')
                ->whereIn('id', $userIds)
                ->select('id', 'nama', 'selular')
                ->get()
                ->keyBy(function ($u) {
                    return (int) $u->id;
                });
            foreach ($assignedKaryawans as $k) {
                $nama = $k->nama_karyawan ?? '';
                $selular = null;
                $uid = $k->user_id !== null ? (int) $k->user_id : null;
                if ($uid !== null && $users->has($uid)) {
                    $selular = trim($users->get($uid)->selular ?? '');
                }
                $karyawansWithSelular[] = [
                    'nama_karyawan' => $nama,
                    'selular' => $selular !== '' ? $selular : null,
                ];
            }
        }

        return response()->json([
            'message' => $message,
            'planning' => [
                'id' => $planning->id,
                'tanggal_formatted' => $planning->tanggal?->format('d M Y'),
                'lokasi' => $planning->lokasi,
                'detail_lokasi' => $planning->detail_lokasi,
            ],
            'karyawans' => $karyawansWithSelular,
        ]);
    }

    private function getClickHouseRowValue(array $row, string $key): mixed
    {
        $keyLower = strtolower($key);
        foreach ($row as $k => $v) {
            if (strtolower((string) $k) === $keyLower) {
                return $v;
            }
        }
        return $row[$key] ?? null;
    }

    public function destroy($id): RedirectResponse
    {
        $planning = RosterPlanning::findOrFail($id);
        $planning->karyawans()->delete();
        $planning->delete();

        return redirect()
            ->back()
            ->with('success', 'Planning berhasil dihapus.');
    }
}
