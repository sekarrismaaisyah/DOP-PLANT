<?php

namespace App\Http\Controllers\SistemRoster;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateLokasiNonKritisJob;
use App\Models\LokasiNonKritis;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LokasiNonKritisController extends Controller
{
    public function index(Request $request): View
    {
        $filterTanggal = $request->get('tanggal', now()->toDateString());
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $filterTanggal)) {
            $filterTanggal = now()->toDateString();
        }

        $filterKategori = $request->get('kategori_area', '');

        return view('SistemRoster.lokasiNonKritis.index', [
            'filterTanggal' => $filterTanggal,
            'filterKategori' => $filterKategori,
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $tanggal = $request->get('tanggal', now()->toDateString());
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
            $tanggal = now()->toDateString();
        }

        $kategori = $request->get('kategori_area', '');
        $search = $request->get('search', []);
        $searchValue = isset($search['value']) ? trim((string) $search['value']) : '';
        $start = (int) $request->get('start', 0);
        $length = (int) $request->get('length', 25);
        $length = min(max($length, 10), 100);
        $orderCol = (int) $request->get('order.0.column', 1);
        $orderDir = $request->get('order.0.dir', 'asc') === 'desc' ? 'desc' : 'asc';

        $query = LokasiNonKritis::whereDate('tanggal', $tanggal);

        if ($kategori !== '' && in_array($kategori, [LokasiNonKritis::KATEGORI_KRITIS, LokasiNonKritis::KATEGORI_NON_KRITIS], true)) {
            $query->where('kategori_area', $kategori);
        }

        if ($searchValue !== '') {
            $term = '%' . $searchValue . '%';
            $query->where(function ($q) use ($term) {
                $q->where('site', 'like', $term)
                    ->orWhere('lokasi', 'like', $term)
                    ->orWhere('detil_lokasi', 'like', $term)
                    ->orWhere('kategori_area', 'like', $term);
            });
        }

        $baseQuery = LokasiNonKritis::whereDate('tanggal', $tanggal);
        if ($kategori !== '' && in_array($kategori, [LokasiNonKritis::KATEGORI_KRITIS, LokasiNonKritis::KATEGORI_NON_KRITIS], true)) {
            $baseQuery->where('kategori_area', $kategori);
        }
        $recordsTotal = $baseQuery->count();

        $recordsFiltered = $query->count();

        $orderColumns = ['id', 'tanggal', 'site', 'lokasi', 'detil_lokasi', 'kategori_area'];
        $orderBy = $orderColumns[$orderCol] ?? 'site';
        $query->orderBy($orderBy, $orderDir);

        $items = $query->offset($start)->limit($length)->get();

        $data = [];
        foreach ($items as $index => $item) {
            $data[] = [
                $start + $index + 1,
                $item->tanggal->format('d M Y'),
                $item->site ?? '-',
                $item->lokasi ?? '-',
                $item->detil_lokasi ?? '-',
                $item->kategori_area ?? 'non_kritis',
            ];
        }

        return response()->json([
            'draw' => (int) $request->get('draw', 1),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    public function generate(Request $request): RedirectResponse
    {
        $tanggal = $request->get('tanggal', now()->toDateString());
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
            $tanggal = now()->toDateString();
        }

        GenerateLokasiNonKritisJob::dispatch($tanggal);

        $queueNote = config('queue.default') !== 'sync'
            ? ' Pastikan queue worker berjalan: php artisan queue:work'
            : '';

        return redirect()
            ->route('sistem-roster.lokasi-non-kritis.index', ['tanggal' => $tanggal])
            ->with('info', 'Proses generate lokasi non kritis sedang berjalan di background. Refresh halaman setelah beberapa saat untuk melihat hasil.' . $queueNote);
    }
}
