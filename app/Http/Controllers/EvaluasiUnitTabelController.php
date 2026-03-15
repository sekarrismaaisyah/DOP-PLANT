<?php

namespace App\Http\Controllers;

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

            $headers = ['TANGGAL', 'NO UNIT', 'JARAK YANG DITEMPUH', 'DURASI (jam)'];
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
                $rowNum++;
            }

            foreach (range('A', 'D') as $colLetter) {
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
}
