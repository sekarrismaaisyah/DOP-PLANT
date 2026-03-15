<?php

namespace App\Http\Controllers;

use App\Models\Becomline;
use App\Models\UnitMtd;
use App\Services\EvaluasiUnitDataService;
use Carbon\Carbon;
use Exception;
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
            'dailyPerUnit' => $dailyPerUnit,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'error' => $error,
        ]);
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
     * Enrich daily per-unit rows with Becomline (no_registrasi = no_unit) and konsumsi_bbm_unit (no_unit).
     *
     * @param array<int, array{tanggal: string, no_unit: string, jarak: string, total_jam: float}> $rows
     * @return array<int, array{tanggal: string, no_unit: string, jarak: string, total_jam: float, perusahaan_pemilik?: string, site_operasional?: string, jenis_unit_spip?: string, expired?: string, status_permit_spip?: string, mtd?: string|float, avg_per_day?: string|float}>
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

        $becomlineByNoReg = [];
        if (!empty($noUnits)) {
            $becomlines = Becomline::whereIn('no_registrasi', $noUnits)->get();
            foreach ($becomlines as $b) {
                $nr = trim((string) $b->no_registrasi);
                if ($nr !== '' && !isset($becomlineByNoReg[$nr])) {
                    $becomlineByNoReg[$nr] = $b;
                }
            }
        }

        $konsumsiByNoUnit = [];
        if (!empty($noUnits)) {
            $konsumesis = UnitMtd::whereIn('no_unit', $noUnits)->get();
            foreach ($konsumesis as $k) {
                $nu = trim((string) $k->no_unit);
                if ($nu !== '' && !isset($konsumsiByNoUnit[$nu])) {
                    $konsumsiByNoUnit[$nu] = $k;
                }
            }
        }

        foreach ($rows as $i => $row) {
            $noUnit = trim((string) ($row['no_unit'] ?? ''));
            $b = $becomlineByNoReg[$noUnit] ?? null;
            $k = $konsumsiByNoUnit[$noUnit] ?? null;

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
