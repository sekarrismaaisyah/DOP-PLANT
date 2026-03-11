<?php

namespace App\Http\Controllers;

use App\Jobs\ImportInsidenTabelJob;
use App\Models\InsidenTabel;
use App\Models\InsidenTabelTag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class InsidenTabelController extends Controller
{
    private array $templateColumns = [
        'no_kecelakaan',
        'kode_be_investigasi',
        'status_lpi',
        'target_penyelesaian_lpi',
        'actual_penyelesaian_lpi',
        'ketepatan_waktu_lpi',
        'tanggal',
        'bulan',
        'tahun',
        'minggu_ke',
        'hari',
        'jam',
        'menit',
        'shift',
        'perusahaan',
        'latitude',
        'longitude',
        'departemen',
        'site',
        'lokasi',
        'sublokasi',
        'lokasi_spesifik',
        'lokasi_validasi_hsecm',
        'pja',
        'insiden_dalam_site_mining',
        'kategori',
        'injury_status',
        'kronologis',
        'high_potential',
        'alat_terlibat',
        'nama',
        'jabatan',
        'shift_kerja_ke',
        'hari_kerja_ke',
        'npk',
        'umur',
        'range_umur',
        'masa_kerja_perusahaan_tahun',
        'masa_kerja_perusahaan_bulan',
        'range_masa_kerja_perusahaan',
        'masa_kerja_bc_tahun',
        'masa_kerja_bc_bulan',
        'range_masa_kerja_bc',
        'bagian_luka',
        'loss_cost',
        'saksi_langsung',
        'atasan_langsung',
        'jabatan_atasan_langsung',
        'kontak',
        'detail_kontak',
        'sumber_kecelakaan',
        'layer',
        'jenis_item_ipls',
        'detail_layer',
        'klasifikasi_layer',
        'keterangan_layer',
        'id_lokasi_insiden',
    ];

    private function setCellValueByColumnAndRowCompat(Worksheet $sheet, int $columnIndex, int $row, mixed $value): void
    {
        $cell = Coordinate::stringFromColumnIndex($columnIndex) . $row;
        $sheet->setCellValue($cell, $value);
    }

    public function index(Request $request): View
    {
        return view('insiden-tabel.index');
    }

    public function downloadTemplate(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template');

        foreach ($this->templateColumns as $i => $col) {
            $this->setCellValueByColumnAndRowCompat($sheet, $i + 1, 1, $col);
        }

        // Example row (optional, can be deleted by user)
        $sheet->setCellValue('A2', 'INC-001');
        $sheet->setCellValue('C2', 'Open');
        $sheet->setCellValue('G2', now()->format('Y-m-d'));
        $sheet->setCellValue('S2', 'BMO 1');
        $sheet->setCellValue('Z2', 'Near Miss');

        $writer = new Xlsx($spreadsheet);
        $filename = 'template-insiden-tabel.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * DataTables server-side: return grouped data by no_kecelakaan.
     */
    public function data(Request $request): JsonResponse
    {
        $search = $request->input('search.value');
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 25);
        $length = in_array($length, [10, 25, 50, 100], true) ? $length : 25;
        $orderColIndex = (int) $request->input('order.0.column', 2);
        $orderDir = $request->input('order.0.dir', 'asc') === 'asc' ? 'asc' : 'desc';

        $orderColumns = [
            0 => 'no_kecelakaan',
            1 => 'no_kecelakaan',
            2 => 'no_kecelakaan',
            3 => 'site',
            4 => 'kategori',
            5 => 'status_lpi',
            6 => 'total_entri',
            7 => 'tag',
            8 => 'no_kecelakaan', // Aksi column, not sortable in UI
        ];
        $orderBy = $orderColumns[$orderColIndex] ?? 'no_kecelakaan';

        $baseQuery = InsidenTabel::query();
        if ($search !== null && $search !== '') {
            $baseQuery->where(function ($q) use ($search) {
                $q->where('no_kecelakaan', 'like', '%' . $search . '%')
                    ->orWhere('kategori', 'like', '%' . $search . '%')
                    ->orWhere('site', 'like', '%' . $search . '%')
                    ->orWhere('status_lpi', 'like', '%' . $search . '%')
                    ->orWhereExists(function ($sub) use ($search) {
                        $sub->selectRaw(1)
                            ->from('insiden_tabel_tags')
                            ->whereColumn('insiden_tabel_tags.no_kecelakaan', 'insiden_tabel.no_kecelakaan')
                            ->where('insiden_tabel_tags.tag', 'like', '%' . $search . '%');
                    });
            });
        }

        $recordsTotal = InsidenTabel::selectRaw('count(distinct no_kecelakaan) as c')->value('c') ?? 0;
        $recordsFiltered = (clone $baseQuery)->selectRaw('count(distinct no_kecelakaan) as c')->value('c') ?? 0;

        $groupsQuery = (clone $baseQuery)
            ->selectRaw('insiden_tabel.no_kecelakaan, max(insiden_tabel.site) as site, max(insiden_tabel.kategori) as kategori, max(insiden_tabel.status_lpi) as status_lpi, count(*) as total_entri')
            ->groupBy('insiden_tabel.no_kecelakaan');

        if ($orderBy === 'tag') {
            $groupsQuery->leftJoinSub(
                InsidenTabelTag::selectRaw('no_kecelakaan, MIN(tag) as tag_sort')->groupBy('no_kecelakaan'),
                'tag_sort_sub',
                'insiden_tabel.no_kecelakaan',
                '=',
                'tag_sort_sub.no_kecelakaan'
            )->orderBy('tag_sort_sub.tag_sort', $orderDir);
        } elseif ($orderBy === 'total_entri') {
            $groupsQuery->orderByRaw('count(*) ' . $orderDir);
        } else {
            $groupsQuery->orderBy($orderBy, $orderDir);
        }

        $groups = $groupsQuery->skip($start)->take($length)->get();
        $noKecelakaanList = $groups->pluck('no_kecelakaan')->toArray();

        $tagsByNo = collect();
        if (!empty($noKecelakaanList)) {
            $tagsByNo = InsidenTabelTag::whereIn('no_kecelakaan', $noKecelakaanList)
                ->orderBy('no_kecelakaan')
                ->orderBy('tag')
                ->get()
                ->groupBy('no_kecelakaan')
                ->map(fn ($rows) => $rows->pluck('tag')->values()->all());
        }

        $details = collect();
        if (!empty($noKecelakaanList)) {
            $details = InsidenTabel::whereIn('no_kecelakaan', $noKecelakaanList)
                ->orderBy('no_kecelakaan')
                ->orderBy('id')
                ->get()
                ->groupBy('no_kecelakaan');
        }

        $data = [];
        $rowIndex = $start + 1;
        foreach ($groups as $g) {
            $detailRows = $details->get($g->no_kecelakaan, collect())->values()->map(function ($row, $idx) {
                return [
                    'id' => $row->id,
                    'row_num' => $idx + 1,
                    'kategori' => $row->kategori ?? '-',
                    'site' => $row->site ?? '-',
                    'layer' => $row->layer ?? '-',
                    'jenis_item_ipls' => $row->jenis_item_ipls ?? '-',
                    'detail_layer' => \Str::limit($row->detail_layer ?? '-', 25),
                    'klasifikasi_layer' => $row->klasifikasi_layer ?? '-',
                    'keterangan_layer' => \Str::limit($row->keterangan_layer ?? '-', 25),
                    'status_lpi' => $row->status_lpi ?? '-',
                    'tanggal' => $row->tanggal ? $row->tanggal->format('d M Y') : '-',
                    'edit_url' => route('insiden-tabel.edit', ['insidenTabel' => $row->id]),
                    'destroy_url' => route('insiden-tabel.destroy', ['insidenTabel' => $row->id]),
                    'csrf' => csrf_token(),
                ];
            })->values();
            $tags = $tagsByNo->get($g->no_kecelakaan, []);

            $data[] = [
                'DT_RowIndex' => $rowIndex++,
                'no_kecelakaan' => $g->no_kecelakaan,
                'site' => $g->site ?? '-',
                'kategori' => $g->kategori ?? '-',
                'status_lpi' => $g->status_lpi ?? '-',
                'total_entri' => (int) $g->total_entri,
                'tags' => $tags,
                'detail' => $detailRows,
            ];
        }

        return response()->json([
            'draw' => (int) $request->input('draw', 1),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    /**
     * Update tags per no_kecelakaan. Auto-save dari input di tabel.
     */
    public function updateGroupMeta(Request $request): JsonResponse
    {
        $request->validate([
            'no_kecelakaan' => ['required', 'string', 'max:255'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:255'],
        ]);

        $noKecelakaan = $request->input('no_kecelakaan');

        if ($request->has('tags')) {
            InsidenTabelTag::where('no_kecelakaan', $noKecelakaan)->delete();
            $tags = array_values(array_filter(array_map('trim', $request->input('tags', []))));
            foreach ($tags as $tag) {
                if ($tag !== '') {
                    InsidenTabelTag::create(['no_kecelakaan' => $noKecelakaan, 'tag' => $tag]);
                }
            }
        }

        $tags = InsidenTabelTag::where('no_kecelakaan', $noKecelakaan)->orderBy('tag')->pluck('tag')->values()->all();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil disimpan',
            'meta' => ['tags' => $tags],
        ]);
    }

    public function create(): View
    {
        $insiden = new InsidenTabel();

        return view('insiden-tabel.create', compact('insiden'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        InsidenTabel::create($data);

        return redirect()->route('insiden-tabel.index')->with('success', 'Data insiden berhasil disimpan.');
    }

    public function edit(InsidenTabel $insidenTabel): View
    {
        return view('insiden-tabel.edit', ['insiden' => $insidenTabel]);
    }

    public function update(Request $request, InsidenTabel $insidenTabel): RedirectResponse
    {
        $data = $this->validatedData($request);
        $insidenTabel->update($data);

        return redirect()->route('insiden-tabel.index')->with('success', 'Data insiden berhasil diperbarui.');
    }

    public function destroy(InsidenTabel $insidenTabel): RedirectResponse
    {
        $insidenTabel->delete();

        return redirect()->route('insiden-tabel.index')->with('success', 'Data insiden berhasil dihapus.');
    }

    /**
     * Hapus semua entri dan tag untuk satu no_kecelakaan (grup).
     */
    public function destroyGroup(Request $request): RedirectResponse
    {
        $request->validate(['no_kecelakaan' => ['required', 'string', 'max:255']]);
        $noKecelakaan = $request->input('no_kecelakaan');

        InsidenTabelTag::where('no_kecelakaan', $noKecelakaan)->delete();
        InsidenTabel::where('no_kecelakaan', $noKecelakaan)->delete();

        return redirect()->route('insiden-tabel.index')->with('success', 'Grup insiden "' . e($noKecelakaan) . '" berhasil dihapus.');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'excel_file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:20480'],
        ]);

        $file = $request->file('excel_file');
        $name = uniqid('insiden_', true) . '.' . $file->getClientOriginalExtension();
        $storedPath = $file->storeAs('insiden-imports', $name);

        ImportInsidenTabelJob::dispatch($storedPath);

        return redirect()->route('insiden-tabel.index')->with('success', 'File berhasil diunggah dan sedang diproses di background.');
    }

    protected function validatedData(Request $request): array
    {
        return $request->validate([
            'no_kecelakaan' => ['required', 'string', 'max:255'],
            'kode_be_investigasi' => ['nullable', 'string', 'max:255'],
            'status_lpi' => ['nullable', 'string', 'max:255'],
            'target_penyelesaian_lpi' => ['nullable', 'date'],
            'actual_penyelesaian_lpi' => ['nullable', 'date'],
            'ketepatan_waktu_lpi' => ['nullable', 'string', 'max:255'],
            'tanggal' => ['nullable', 'date'],
            'bulan' => ['nullable', 'integer'],
            'tahun' => ['nullable', 'integer'],
            'minggu_ke' => ['nullable', 'integer'],
            'hari' => ['nullable', 'string', 'max:255'],
            'jam' => ['nullable', 'integer'],
            'menit' => ['nullable', 'integer'],
            'shift' => ['nullable', 'string', 'max:255'],
            'perusahaan' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'departemen' => ['nullable', 'string', 'max:255'],
            'site' => ['nullable', 'string', 'max:255'],
            'lokasi' => ['nullable', 'string', 'max:255'],
            'sublokasi' => ['nullable', 'string', 'max:255'],
            'lokasi_spesifik' => ['nullable', 'string', 'max:255'],
            'lokasi_validasi_hsecm' => ['nullable', 'string', 'max:255'],
            'pja' => ['nullable', 'string', 'max:255'],
            'insiden_dalam_site_mining' => ['nullable', 'string', 'max:255'],
            'kategori' => ['nullable', 'string', 'max:255'],
            'injury_status' => ['nullable', 'string', 'max:255'],
            'kronologis' => ['nullable', 'string'],
            'high_potential' => ['nullable', 'string', 'max:255'],
            'alat_terlibat' => ['nullable', 'string', 'max:255'],
            'nama' => ['nullable', 'string', 'max:255'],
            'jabatan' => ['nullable', 'string', 'max:255'],
            'shift_kerja_ke' => ['nullable', 'integer'],
            'hari_kerja_ke' => ['nullable', 'integer'],
            'npk' => ['nullable', 'string', 'max:255'],
            'umur' => ['nullable', 'integer'],
            'range_umur' => ['nullable', 'string', 'max:255'],
            'masa_kerja_perusahaan_tahun' => ['nullable', 'integer'],
            'masa_kerja_perusahaan_bulan' => ['nullable', 'integer'],
            'range_masa_kerja_perusahaan' => ['nullable', 'string', 'max:255'],
            'masa_kerja_bc_tahun' => ['nullable', 'integer'],
            'masa_kerja_bc_bulan' => ['nullable', 'integer'],
            'range_masa_kerja_bc' => ['nullable', 'string', 'max:255'],
            'bagian_luka' => ['nullable', 'string', 'max:255'],
            'loss_cost' => ['nullable', 'numeric'],
            'saksi_langsung' => ['nullable', 'string', 'max:255'],
            'atasan_langsung' => ['nullable', 'string', 'max:255'],
            'jabatan_atasan_langsung' => ['nullable', 'string', 'max:255'],
            'kontak' => ['nullable', 'string', 'max:255'],
            'detail_kontak' => ['nullable', 'string'],
            'sumber_kecelakaan' => ['nullable', 'string', 'max:255'],
            'layer' => ['nullable', 'string', 'max:255'],
            'jenis_item_ipls' => ['nullable', 'string', 'max:255'],
            'detail_layer' => ['nullable', 'string', 'max:255'],
            'klasifikasi_layer' => ['nullable', 'string', 'max:255'],
            'keterangan_layer' => ['nullable', 'string'],
            'id_lokasi_insiden' => ['nullable', 'string', 'max:255'],
        ]);
    }
}

