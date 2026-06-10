<?php

namespace App\Http\Controllers\PembatasanLV;

use App\Http\Controllers\Controller;
use App\Models\BecomelineUnit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PembatasanLVBecomelineUnitController extends Controller
{
    public function data(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));
        $perPage = min(max((int) $request->query('per_page', 10), 5), 100);
        $page = max((int) $request->query('page', 1), 1);

        $query = BecomelineUnit::query()
            ->select([
                'id_unit',
                'perusahaan',
                'sid_unit',
                'no_lambung',
                'kategori_unit',
                'jenis_unit',
                'merk_unit',
                'tipe_detail_unit',
            ])
            ->orderBy('perusahaan')
            ->orderBy('no_lambung');

        if ($q !== '') {
            $query->where(function ($builder) use ($q): void {
                $builder->where('perusahaan', 'like', '%'.$q.'%')
                    ->orWhere('sid_unit', 'like', '%'.$q.'%')
                    ->orWhere('no_lambung', 'like', '%'.$q.'%')
                    ->orWhere('kategori_unit', 'like', '%'.$q.'%')
                    ->orWhere('jenis_unit', 'like', '%'.$q.'%')
                    ->orWhere('merk_unit', 'like', '%'.$q.'%')
                    ->orWhere('tipe_detail_unit', 'like', '%'.$q.'%');
            });
        }

        $paginator = $query->paginate(perPage: $perPage, page: $page);

        return response()->json([
            'data' => $paginator->getCollection()->map(fn (BecomelineUnit $row) => [
                'id_unit' => $row->id_unit,
                'perusahaan' => $row->perusahaan ?? '',
                'sid_unit' => $row->sid_unit ?? '',
                'no_lambung' => $row->no_lambung ?? '',
                'kategori_unit' => $row->kategori_unit ?? '',
                'jenis_unit' => $row->jenis_unit ?? '',
                'merk_unit' => $row->merk_unit ?? '',
                'tipe_detail_unit' => $row->tipe_detail_unit ?? '',
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
}
