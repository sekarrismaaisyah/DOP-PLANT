<?php

declare(strict_types=1);

namespace App\Http\Controllers\AutoBanned;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AutoBanned\Concerns\ProvidesAutoBannedLayout;
use App\Http\Requests\AutoBanned\AutoBannedMasterSodRequest;
use App\Models\AutoBannedMasterSod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AutoBannedMasterSodController extends Controller
{
    use ProvidesAutoBannedLayout;

    public function index(Request $request): View
    {
        return view('AutoBanned.master-sod.index', [
            'navActive' => 'master-sod',
            'navItems' => $this->autoBannedNavItems(),
            'q' => trim((string) $request->query('q', '')),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));
        $perPage = min(max((int) $request->query('per_page', 10), 5), 100);
        $page = max((int) $request->query('page', 1), 1);

        $query = AutoBannedMasterSod::query()
            ->orderBy('site')
            ->orderBy('nama');

        if ($q !== '') {
            $query->where(function ($builder) use ($q): void {
                $builder->where('nama', 'like', '%'.$q.'%')
                    ->orWhere('site', 'like', '%'.$q.'%')
                    ->orWhere('no_hp', 'like', '%'.$q.'%');
            });
        }

        $paginator = $query->paginate(perPage: $perPage, page: $page);

        return response()->json([
            'data' => $paginator->getCollection()->map(fn (AutoBannedMasterSod $row) => [
                'id' => $row->id,
                'nama' => $row->nama,
                'site' => $row->site,
                'no_hp' => $row->no_hp,
                'updated_at' => $row->updated_at?->format('d M Y H:i'),
            ])->values(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem() ?? 0,
                'to' => $paginator->lastItem() ?? 0,
            ],
        ]);
    }

    public function show(AutoBannedMasterSod $masterSod): JsonResponse
    {
        return response()->json([
            'data' => [
                'id' => $masterSod->id,
                'nama' => $masterSod->nama,
                'site' => $masterSod->site,
                'no_hp' => $masterSod->no_hp,
            ],
        ]);
    }

    public function store(AutoBannedMasterSodRequest $request): JsonResponse
    {
        $row = AutoBannedMasterSod::query()->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Data master SOD berhasil ditambahkan.',
            'data' => $row,
        ], 201);
    }

    public function update(AutoBannedMasterSodRequest $request, AutoBannedMasterSod $masterSod): JsonResponse
    {
        $masterSod->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Data master SOD berhasil diperbarui.',
            'data' => $masterSod->fresh(),
        ]);
    }

    public function destroy(AutoBannedMasterSod $masterSod): JsonResponse
    {
        $masterSod->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data master SOD berhasil dihapus.',
        ]);
    }
}
