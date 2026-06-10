<?php

namespace App\Http\Controllers\PembatasanLV;

use App\Http\Controllers\Controller;
use App\Http\Requests\PembatasanLV\PembatasanBatasLvPerLokasiRequest;
use App\Models\PembatasanBatasLvPerLokasi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PembatasanLVBatasLvPerLokasiController extends Controller
{
    public function data(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));
        $perPage = min(max((int) $request->query('per_page', 10), 5), 100);
        $page = max((int) $request->query('page', 1), 1);

        $query = PembatasanBatasLvPerLokasi::query()
            ->orderBy('site')
            ->orderBy('lokasi');

        if ($q !== '') {
            $query->where(function ($builder) use ($q): void {
                $builder->where('site', 'like', '%'.$q.'%')
                    ->orWhere('lokasi', 'like', '%'.$q.'%')
                    ->orWhere('detail_lokasi', 'like', '%'.$q.'%');
            });
        }

        $paginator = $query->paginate(perPage: $perPage, page: $page);

        return response()->json([
            'data' => $paginator->getCollection()->map(fn (PembatasanBatasLvPerLokasi $row) => [
                'id' => $row->id,
                'site' => $row->site,
                'lokasi' => $row->lokasi,
                'detail_lokasi' => $row->detail_lokasi ?? '',
                'batas_lv' => (int) $row->batas_lv,
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

    public function show(PembatasanBatasLvPerLokasi $batasLvPerLokasi): JsonResponse
    {
        return response()->json([
            'data' => [
                'id' => $batasLvPerLokasi->id,
                'site' => $batasLvPerLokasi->site,
                'lokasi' => $batasLvPerLokasi->lokasi,
                'detail_lokasi' => $batasLvPerLokasi->detail_lokasi ?? '',
                'batas_lv' => (int) $batasLvPerLokasi->batas_lv,
            ],
        ]);
    }

    public function store(PembatasanBatasLvPerLokasiRequest $request): JsonResponse
    {
        $row = PembatasanBatasLvPerLokasi::query()->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Data batas LV per lokasi berhasil ditambahkan.',
            'data' => $row,
        ], 201);
    }

    public function update(PembatasanBatasLvPerLokasiRequest $request, PembatasanBatasLvPerLokasi $batasLvPerLokasi): JsonResponse
    {
        $batasLvPerLokasi->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Data batas LV per lokasi berhasil diperbarui.',
            'data' => $batasLvPerLokasi->fresh(),
        ]);
    }

    public function destroy(PembatasanBatasLvPerLokasi $batasLvPerLokasi): JsonResponse
    {
        $batasLvPerLokasi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data batas LV per lokasi berhasil dihapus.',
        ]);
    }
}
