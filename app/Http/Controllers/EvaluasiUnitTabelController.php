<?php

namespace App\Http\Controllers;

use App\Models\Becomline;
use App\Models\UnitMtd;
use App\Services\EvaluasiUnitDataService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class EvaluasiUnitTabelController extends Controller
{
    /**
     * Tampilkan halaman tabel Evaluasi Unit (NO UNIT | JARAK | WAKTU AKTIF | TANGGAL).
     * Parameter: date_from, date_to (YYYY-MM-DD). Default: 30 hari terakhir.
     * Data diambil via EvaluasiUnitDataService (satu query agregat ClickHouse).
     */
    public function index(Request $request): View
    {
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        if ($dateFrom && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFrom)) {
            $dateFrom = null;
        }
        if ($dateTo && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateTo)) {
            $dateTo = null;
        }

        if (!$dateFrom || !$dateTo) {
            $dateTo = $dateTo ?: Carbon::now()->format('Y-m-d');
            $dateFrom = $dateFrom ?: Carbon::now()->subDays(30)->format('Y-m-d');
        }

        $evaluasiUnits = [];
        $error = null;
        try {
            $service = new EvaluasiUnitDataService();
            $evaluasiUnits = $service->getAggregatedData($dateFrom, $dateTo);
        } catch (Exception $e) {
            Log::error('EvaluasiUnitTabelController::index: ' . $e->getMessage());
            $error = $e->getMessage();
        }

        return view('fuelingEvaluasi.index', [
            'evaluasiUnits' => $evaluasiUnits,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'error' => $error,
        ]);
    }

    /**
     * Tampilkan ringkasan per hari: total jarak dan total durasi (jam) per tanggal.
     * Parameter: date_from, date_to. Default: 30 hari terakhir.
     */
    public function perHari(Request $request): View
    {
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        if ($dateFrom && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFrom)) {
            $dateFrom = null;
        }
        if ($dateTo && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateTo)) {
            $dateTo = null;
        }

        if (!$dateFrom || !$dateTo) {
            $dateTo = $dateTo ?: Carbon::now()->format('Y-m-d');
            $dateFrom = $dateFrom ?: Carbon::now()->subDays(30)->format('Y-m-d');
        }

        $dailyPerUnit = [];
        $error = null;
        try {
            $service = new EvaluasiUnitDataService();
            $dailyPerUnit = $service->getDailyPerUnitPerDay($dateFrom, $dateTo);
            $dailyPerUnit = $this->enrichDailyPerUnitWithBecomlineAndKonsumsi($dailyPerUnit);
        } catch (Exception $e) {
            Log::error('EvaluasiUnitTabelController::perHari: ' . $e->getMessage());
            $error = $e->getMessage();
        }

        return view('fuelingEvaluasi.per-hari', [
            'dailyPerUnit' => [],
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'error' => $error,
        ]);
    }

    /**
     * DataTables server-side: data Per Hari per Unit (JSON).
     * Query: date_from, date_to required; plus draw, start, length, search[value], order.
     */
    public function perHariData(Request $request): JsonResponse
    {
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        if (!$dateFrom || !$dateTo || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFrom) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateTo)) {
            return response()->json([
                'draw' => (int) $request->input('draw', 0),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
            ]);
        }

        $draw = (int) $request->input('draw', 0);
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        if ($length < 1 || $length > 500) {
            $length = 25;
        }
        $search = trim((string) ($request->input('search.value') ?? ''));
        $orderColIndex = (int) $request->input('order.0.column', 0);
        $orderDir = strtolower($request->input('order.0.dir', 'asc')) === 'desc' ? 'desc' : 'asc';

        $orderKeys = [
            0 => 'tanggal',
            1 => 'no_unit',
            2 => 'jarak',
            3 => 'total_jam',
            4 => 'perusahaan_pemilik',
            5 => 'site_operasional',
            6 => 'jenis_unit_spip',
            7 => 'expired',
            8 => 'status_permit_spip',
            9 => 'mtd',
            10 => 'avg_per_day',
        ];
        $orderKey = $orderKeys[$orderColIndex] ?? 'tanggal';

        try {
            $service = new EvaluasiUnitDataService();
            $rows = $service->getDailyPerUnitPerDay($dateFrom, $dateTo);
            $rows = $this->enrichDailyPerUnitWithBecomlineAndKonsumsi($rows);
        } catch (Exception $e) {
            Log::error('EvaluasiUnitTabelController::perHariData: ' . $e->getMessage());
            return response()->json([
                'draw' => $draw,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
            ]);
        }

        if ($search !== '') {
            $searchLower = mb_strtolower($search);
            $rows = array_values(array_filter($rows, function ($r) use ($searchLower) {
                $concat = mb_strtolower(implode(' ', array_map(function ($v) {
                    return $v === null ? '' : (string) $v;
                }, $r)));
                return str_contains($concat, $searchLower);
            }));
        }

        $recordsTotal = count($rows);
        $recordsFiltered = $recordsTotal;

        usort($rows, function ($a, $b) use ($orderKey, $orderDir) {
            $va = $a[$orderKey] ?? '';
            $vb = $b[$orderKey] ?? '';
            if (is_numeric($va) && is_numeric($vb)) {
                $cmp = $va <=> $vb;
            } else {
                $cmp = strcmp((string) $va, (string) $vb);
            }
            return $orderDir === 'desc' ? -$cmp : $cmp;
        });

        $page = array_slice($rows, $start, $length);
        $data = [];
        foreach ($page as $r) {
            $mtdStr = isset($r['mtd']) && $r['mtd'] !== null ? number_format((float) $r['mtd'], 2, ',', '.') : '-';
            $avgStr = isset($r['avg_per_day']) && $r['avg_per_day'] !== null ? number_format((float) $r['avg_per_day'], 2, ',', '.') : '-';
            $data[] = [
                $r['tanggal'] ?? '-',
                $r['no_unit'] ?? '-',
                $r['jarak'] ?? '-',
                ($r['total_jam'] ?? 0) . ' jam',
                $r['perusahaan_pemilik'] ?? '-',
                $r['site_operasional'] ?? '-',
                $r['jenis_unit_spip'] ?? '-',
                !empty($r['expired']) ? $r['expired'] : '-',
                $r['status_permit_spip'] ?? '-',
                $mtdStr,
                $avgStr,
            ];
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    /**
     * Semua data Per Hari per Unit (tanpa pagination) untuk dashboard. JSON: { data: [{ ... }] }.
     */
    public function perHariAllData(Request $request): JsonResponse
    {
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        if (!$dateFrom || !$dateTo || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFrom) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateTo)) {
            return response()->json(['data' => []]);
        }
        try {
            $service = new EvaluasiUnitDataService();
            $rows = $service->getDailyPerUnitPerDay($dateFrom, $dateTo);
            $rows = $this->enrichDailyPerUnitWithBecomlineAndKonsumsi($rows);
        } catch (Exception $e) {
            Log::error('EvaluasiUnitTabelController::perHariAllData: ' . $e->getMessage());
            return response()->json(['data' => []]);
        }
        usort($rows, function ($a, $b) {
            $c = strcmp($a['tanggal'] ?? '', $b['tanggal'] ?? '');
            return $c !== 0 ? $c : strcmp($a['no_unit'] ?? '', $b['no_unit'] ?? '');
        });
        $list = [];
        foreach ($rows as $r) {
            $mtdStr = isset($r['mtd']) && $r['mtd'] !== null ? number_format((float) $r['mtd'], 2, ',', '.') : '-';
            $avgStr = isset($r['avg_per_day']) && $r['avg_per_day'] !== null ? number_format((float) $r['avg_per_day'], 2, ',', '.') : '-';
            $list[] = [
                'tanggal' => $r['tanggal'] ?? '-',
                'no_unit' => $r['no_unit'] ?? '-',
                'jarak' => $r['jarak'] ?? '-',
                'total_jam' => $r['total_jam'] ?? 0,
                'perusahaan_pemilik' => $r['perusahaan_pemilik'] ?? '-',
                'site_operasional' => $r['site_operasional'] ?? '-',
                'jenis_unit_spip' => $r['jenis_unit_spip'] ?? '-',
                'expired' => !empty($r['expired']) ? $r['expired'] : '-',
                'status_permit_spip' => $r['status_permit_spip'] ?? '-',
                'avg_per_day' => $avgStr,
                'mtd' => $mtdStr,
            ];
        }
        return response()->json(['data' => $list]);
    }

    /**
     * Export data Per Hari per Unit ke Excel (TANGGAL | NO UNIT | JARAK | DURASI jam).
     */
    public function exportPerHariExcel(Request $request)
    {
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        if ($dateFrom && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFrom)) {
            $dateFrom = null;
        }
        if ($dateTo && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateTo)) {
            $dateTo = null;
        }
        if (!$dateFrom || !$dateTo) {
            $dateTo = $dateTo ?: Carbon::now()->format('Y-m-d');
            $dateFrom = $dateFrom ?: Carbon::now()->subDays(30)->format('Y-m-d');
        }

        try {
            $service = new EvaluasiUnitDataService();
            $rows = $service->getDailyPerUnitPerDay($dateFrom, $dateTo);
            $rows = $this->enrichDailyPerUnitWithBecomlineAndKonsumsi($rows);

            $headers = [
                'TANGGAL', 'NO UNIT', 'JARAK YANG DITEMPUH', 'DURASI (jam)',
                'Perusahaan Pemilik', 'Site Operasional', 'Jenis Unit SPIP', 'Expired', 'Status Permit SPIP',
                'MTD', 'AVG per Day',
            ];
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Per Hari per Unit');

            $col = 1;
            foreach ($headers as $h) {
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($col) . '1', $h);
                $col++;
            }
            $lastCol = Coordinate::stringFromColumnIndex(count($headers));
            $sheet->getStyle('A1:' . $lastCol . '1')->getFont()->setBold(true);
            $sheet->getStyle('A1:' . $lastCol . '1')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFD9EAD3');

            $rowNum = 2;
            foreach ($rows as $row) {
                $sheet->setCellValue('A' . $rowNum, $row['tanggal']);
                $sheet->setCellValue('B' . $rowNum, $row['no_unit']);
                $sheet->setCellValue('C' . $rowNum, $row['jarak']);
                $sheet->setCellValue('D' . $rowNum, $row['total_jam']);
                $sheet->setCellValue('E' . $rowNum, $row['perusahaan_pemilik'] ?? '');
                $sheet->setCellValue('F' . $rowNum, $row['site_operasional'] ?? '');
                $sheet->setCellValue('G' . $rowNum, $row['jenis_unit_spip'] ?? '');
                $sheet->setCellValue('H' . $rowNum, isset($row['expired']) && $row['expired'] ? $row['expired'] : '');
                $sheet->setCellValue('I' . $rowNum, $row['status_permit_spip'] ?? '');
                $sheet->setCellValue('J' . $rowNum, $row['mtd'] ?? '');
                $sheet->setCellValue('K' . $rowNum, $row['avg_per_day'] ?? '');
                $rowNum++;
            }

            foreach (range('A', 'K') as $colLetter) {
                $sheet->getColumnDimension($colLetter)->setAutoSize(true);
            }

            $filename = 'Evaluasi_Unit_Per_Hari_' . date('Y-m-d_His') . '.xlsx';
            $writer = new Xlsx($spreadsheet);
            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        } catch (Exception $e) {
            Log::error('EvaluasiUnitTabelController::exportPerHariExcel: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Normalize unit key for matching: trim, uppercase, then remove spaces, hyphens, underscores, dots.
     * So "BM 265", "BM-265", "BM.265" all become "BM265" and match.
     */
    private function normalizeUnitKey(?string $value): string
    {
        $s = strtoupper(trim((string) $value));
        $s = str_replace([' ', '-', '_', '.'], '', $s);
        return $s;
    }

    /**
     * Return possible normalized keys to try when matching a log no_unit to Becomline/konsumsi.
     * If no_unit contains " - " (e.g. "BMO - BM 365"), also add the part after last " - " so "BM-365" in DB matches.
     */
    private function getPossibleMatchKeys(?string $noUnit): array
    {
        $noUnit = trim((string) $noUnit);
        $keys = [];
        $full = $this->normalizeUnitKey($noUnit);
        if ($full !== '') {
            $keys[] = $full;
        }
        if (str_contains($noUnit, ' - ')) {
            $afterLast = trim((string) substr($noUnit, strrpos($noUnit, ' - ') + 3));
            $suffix = $this->normalizeUnitKey($afterLast);
            if ($suffix !== '' && !in_array($suffix, $keys, true)) {
                $keys[] = $suffix;
            }
        }
        return $keys;
    }

    /**
     * SQL expression to normalize no_registrasi / no_unit the same way as normalizeUnitKey (MySQL).
     */
    private function sqlNormalizedUnitColumn(string $column): string
    {
        return "REPLACE(REPLACE(REPLACE(REPLACE(UPPER(TRIM({$column})), ' ', ''), '-', ''), '_', ''), '.', '')";
    }

    /**
     * Enrich daily per-unit rows with Becomline (no_registrasi = no_unit) and konsumsi_bbm_unit (no_unit).
     * Matching is loose: case-insensitive, trim, ignore spaces/hyphens; and if log has "X - Y" we also try matching by Y (e.g. "BMO - BM 365" matches "BM-365").
     */
    private function enrichDailyPerUnitWithBecomlineAndKonsumsi(array $rows): array
    {
        if (empty($rows)) {
            return $rows;
        }

        $noUnits = array_unique(array_map(function ($r) {
            return trim((string) ($r['no_unit'] ?? ''));
        }, $rows));
        $noUnits = array_filter($noUnits);
        $normalizedKeys = [];
        foreach ($noUnits as $u) {
            foreach ($this->getPossibleMatchKeys($u) as $k) {
                $normalizedKeys[$k] = true;
            }
        }
        $normalizedKeys = array_keys($normalizedKeys);

        $becomlineByNoReg = [];
        if (!empty($normalizedKeys)) {
            $placeholders = implode(',', array_fill(0, count($normalizedKeys), '?'));
            $expr = $this->sqlNormalizedUnitColumn('no_registrasi');
            $becomlines = Becomline::whereRaw("{$expr} IN ({$placeholders})", array_values($normalizedKeys))->get();
            foreach ($becomlines as $b) {
                $nr = $this->normalizeUnitKey($b->no_registrasi);
                if ($nr !== '' && !isset($becomlineByNoReg[$nr])) {
                    $becomlineByNoReg[$nr] = $b;
                }
            }
        }

        $konsumsiByNoUnit = [];
        if (!empty($normalizedKeys)) {
            $placeholders = implode(',', array_fill(0, count($normalizedKeys), '?'));
            $expr = $this->sqlNormalizedUnitColumn('no_unit');
            $konsumesis = UnitMtd::whereRaw("{$expr} IN ({$placeholders})", array_values($normalizedKeys))->get();
            foreach ($konsumesis as $k) {
                $nu = $this->normalizeUnitKey($k->no_unit);
                if ($nu !== '' && !isset($konsumsiByNoUnit[$nu])) {
                    $konsumsiByNoUnit[$nu] = $k;
                }
            }
        }

        foreach ($rows as $i => $row) {
            $keys = $this->getPossibleMatchKeys($row['no_unit'] ?? '');
            $b = null;
            $k = null;
            foreach ($keys as $key) {
                if ($key !== '' && isset($becomlineByNoReg[$key])) {
                    $b = $becomlineByNoReg[$key];
                    break;
                }
            }
            foreach ($keys as $key) {
                if ($key !== '' && isset($konsumsiByNoUnit[$key])) {
                    $k = $konsumsiByNoUnit[$key];
                    break;
                }
            }

            $rows[$i]['perusahaan_pemilik'] = $b ? $b->perusahaan_pemilik : null;
            $rows[$i]['site_operasional'] = $b ? $b->site_operasional : null;
            $rows[$i]['jenis_unit_spip'] = $b ? $b->jenis_unit_spip : null;
            $rows[$i]['expired'] = $b && $b->expired ? $b->expired->format('Y-m-d') : null;
            $rows[$i]['status_permit_spip'] = $b ? $b->status_permit_spip : null;
            $rows[$i]['mtd'] = $k !== null && $k->mtd !== null ? (float) $k->mtd : null;
            $rows[$i]['avg_per_day'] = $k !== null && $k->avg_per_day !== null ? (float) $k->avg_per_day : null;
        }

        return $rows;
    }
}
