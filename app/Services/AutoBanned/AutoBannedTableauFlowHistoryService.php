<?php

declare(strict_types=1);

namespace App\Services\AutoBanned;

use App\Models\AutoBannedTableauFlowHistory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class AutoBannedTableauFlowHistoryService
{
    public function tableAvailable(): bool
    {
        try {
            return Schema::hasTable('tableau_flow_history');
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * @return array{status_code: string, flow_name: string, q: string}
     */
    public function resolveFilters(Request $request): array
    {
        return [
            'status_code' => trim((string) $request->query('status_code', '')),
            'flow_name' => trim((string) $request->query('flow_name', '')),
            'q' => trim((string) $request->query('q', '')),
        ];
    }

    /**
     * @param  array{status_code: string, flow_name: string, q: string}  $filters
     */
    public function paginatedHistory(array $filters, int $perPage = 25): LengthAwarePaginator
    {
        return $this->baseQuery($filters)
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * @param  array{status_code: string, flow_name: string, q: string}  $filters
     * @return array{
     *     totalRecords: int,
     *     latestLoggedAt: ?string,
     *     latestLoggedLabel: ?string,
     *     runningCount: int,
     *     pendingCount: int,
     *     successCount: int,
     *     failedCount: int
     * }
     */
    public function summary(array $filters): array
    {
        if (! $this->tableAvailable()) {
            return [
                'totalRecords' => 0,
                'latestLoggedAt' => null,
                'latestLoggedLabel' => null,
                'runningCount' => 0,
                'pendingCount' => 0,
                'successCount' => 0,
                'failedCount' => 0,
            ];
        }

        $query = $this->baseQuery($filters);
        $latest = (clone $query)
            ->select(['logged_at'])
            ->orderByDesc('logged_at')
            ->orderByDesc('id')
            ->first();

        $statusCounts = (clone $query)
            ->selectRaw('status_code, COUNT(*) as total')
            ->groupBy('status_code')
            ->pluck('total', 'status_code');

        return [
            'totalRecords' => (clone $query)->count(),
            'latestLoggedAt' => $latest?->logged_at?->toDateTimeString(),
            'latestLoggedLabel' => $latest?->logged_at?->timezone(config('auto_banned.hsct.timezone', 'Asia/Makassar'))->format('d M Y H:i'),
            'runningCount' => (int) ($statusCounts['RUNNING'] ?? 0),
            'pendingCount' => (int) ($statusCounts['PENDING'] ?? 0),
            'successCount' => (int) (($statusCounts['SUCCESS'] ?? 0) + ($statusCounts['SUCCEEDED'] ?? 0)),
            'failedCount' => (int) (($statusCounts['FAILED'] ?? 0) + ($statusCounts['ERROR'] ?? 0)),
        ];
    }

    /**
     * @return Collection<int, string>
     */
    public function statusCodeOptions(): Collection
    {
        if (! $this->tableAvailable()) {
            return collect();
        }

        return AutoBannedTableauFlowHistory::query()
            ->select('status_code')
            ->whereNotNull('status_code')
            ->where('status_code', '!=', '')
            ->distinct()
            ->orderBy('status_code')
            ->pluck('status_code');
    }

    /**
     * @return Collection<int, string>
     */
    public function flowNameOptions(): Collection
    {
        if (! $this->tableAvailable()) {
            return collect();
        }

        return AutoBannedTableauFlowHistory::query()
            ->select('flow_name')
            ->whereNotNull('flow_name')
            ->where('flow_name', '!=', '')
            ->distinct()
            ->orderBy('flow_name')
            ->pluck('flow_name');
    }

    /**
     * @param  array{status_code: string, flow_name: string, q: string}  $filters
     */
    private function baseQuery(array $filters)
    {
        $query = AutoBannedTableauFlowHistory::query()
            ->select([
                'id',
                'logged_at',
                'status_code',
                'flow_name',
                'output_name',
                'status_detail',
                'trigger_type',
                'flow_url',
                'created_at',
            ])
            ->orderByDesc('logged_at')
            ->orderByDesc('id');

        if ($filters['status_code'] !== '') {
            $query->where('status_code', $filters['status_code']);
        }

        if ($filters['flow_name'] !== '') {
            $query->where('flow_name', $filters['flow_name']);
        }

        if ($filters['q'] !== '') {
            $term = '%'.$filters['q'].'%';
            $query->where(function ($builder) use ($term): void {
                $builder
                    ->where('flow_name', 'like', $term)
                    ->orWhere('output_name', 'like', $term)
                    ->orWhere('status_detail', 'like', $term)
                    ->orWhere('status_code', 'like', $term);
            });
        }

        return $query;
    }
}
