<?php

namespace App\Http\Controllers\SistemRoster;

use App\Http\Controllers\Controller;
use App\Jobs\GeneratePlanningJob;
use App\Models\RosterPlanning;
use App\Models\RosterPlanningJob;
use App\Models\RosterPlanningKaryawan;
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
            return response()->json([
                'success' => true,
                'status' => $newStatus,
                'count' => $planning->karyawans()->count(),
            ]);
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
