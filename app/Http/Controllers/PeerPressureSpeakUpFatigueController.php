<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\PeerPressureSpeakUpFatigueRequest;
use App\Models\SpeakUpFatigue;
use App\Services\PeerPressure\PeerPressureKaryawanNitipService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Throwable;

class PeerPressureSpeakUpFatigueController extends Controller
{
    private const SHEET_IMPORT = 'SpeakUpFatigue';

    /** @var list<string> */
    private const HEADER_IMPORT = [
        'Site',
        'Perusahaan',
        'SID',
        'Nama',
        'Tanggal',
        'Waktu',
    ];

    public function index(Request $request, PeerPressureKaryawanNitipService $karyawanNitip): View
    {
        $query = SpeakUpFatigue::query()
            ->orderByDesc('tanggal')
            ->orderByDesc('waktu')
            ->orderByDesc('id');

        $q = trim((string) $request->get('q', ''));
        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('site', 'like', '%' . $q . '%')
                    ->orWhere('perusahaan', 'like', '%' . $q . '%')
                    ->orWhere('sid', 'like', '%' . $q . '%')
                    ->orWhere('nama', 'like', '%' . $q . '%');
            });
        }

        $rows = $query->paginate(12)->withQueryString();

        $sids = [];
        foreach ($rows as $r) {
            $sids[] = $r->sid;
        }
        $peerFotoUrls = $karyawanNitip->fotoUrlsByKodeSids($sids);

        return view('peer-pressure-edukasi.speak-up-fatigue.index', [
            'rows' => $rows,
            'q' => $q,
            'navActive' => 'speak-up-fatigue',
            'peerFotoUrls' => $peerFotoUrls,
        ]);
    }

    public function create(): View
    {
        return view('peer-pressure-edukasi.speak-up-fatigue.form', [
            'mode' => 'create',
            'row' => new SpeakUpFatigue(),
            'navActive' => 'speak-up-fatigue',
        ]);
    }

    public function store(PeerPressureSpeakUpFatigueRequest $request): RedirectResponse
    {
        SpeakUpFatigue::query()->create($request->attributesPayload());

        return redirect()
            ->route('peer-pressure-edukasi.speak-up-fatigue.index')
            ->with('success', 'Data Speak Up Fatigue berhasil ditambahkan.');
    }

    public function edit(int $id): View
    {
        $row = SpeakUpFatigue::query()->findOrFail($id);

        return view('peer-pressure-edukasi.speak-up-fatigue.form', [
            'mode' => 'edit',
            'row' => $row,
            'navActive' => 'speak-up-fatigue',
        ]);
    }

    public function update(PeerPressureSpeakUpFatigueRequest $request, int $id): RedirectResponse
    {
        $row = SpeakUpFatigue::query()->findOrFail($id);
        $row->update($request->attributesPayload());

        return redirect()
            ->route('peer-pressure-edukasi.speak-up-fatigue.index')
            ->with('success', 'Data Speak Up Fatigue berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $row = SpeakUpFatigue::query()->findOrFail($id);
        $row->delete();

        return redirect()
            ->route('peer-pressure-edukasi.speak-up-fatigue.index')
            ->with('success', 'Data Speak Up Fatigue berhasil dihapus.');
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(self::SHEET_IMPORT);

        $col = 1;
        foreach (self::HEADER_IMPORT as $h) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($col) . '1', $h);
            $col++;
        }
        $lastCol = Coordinate::stringFromColumnIndex(count(self::HEADER_IMPORT));
        $sheet->getStyle('A1:' . $lastCol . '1')->getFont()->setBold(true);

        $sheet->fromArray([
            ['BMO 1', 'MTL', '4567Y', 'Contoh Nama', date('Y-m-d'), '08:30'],
        ], null, 'A2');

        foreach (range(1, count(self::HEADER_IMPORT)) as $i) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setAutoSize(true);
        }

        $filename = 'template_speak_up_fatigue_' . date('Y-m-d') . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer): void {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function import(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'excel_file' => ['required', 'file', 'mimes:xlsx,xls', 'max:10240'],
        ], [
            'excel_file.required' => 'File Excel wajib diunggah.',
            'excel_file.mimes' => 'File harus berformat .xlsx atau .xls.',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('peer-pressure-edukasi.speak-up-fatigue.index', ['modal' => 'import'])
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $file = $request->file('excel_file');
            if ($file === null) {
                return redirect()
                    ->route('peer-pressure-edukasi.speak-up-fatigue.index', ['modal' => 'import'])
                    ->with('notify_error', 'File tidak valid.');
            }

            $path = $file->getRealPath();
            if ($path === false) {
                return redirect()
                    ->route('peer-pressure-edukasi.speak-up-fatigue.index', ['modal' => 'import'])
                    ->with('notify_error', 'Tidak dapat membaca file.');
            }

            $spreadsheet = IOFactory::load($path);
            $sheet = $spreadsheet->getSheet(0);
            $rows = $sheet->toArray();

            if ($rows === []) {
                return redirect()
                    ->route('peer-pressure-edukasi.speak-up-fatigue.index', ['modal' => 'import'])
                    ->with('notify_error', 'File kosong atau tidak terbaca.');
            }

            $this->assertHeaderMatches($rows[0] ?? [], self::HEADER_IMPORT, 'sheet pertama');

            $dataRows = array_slice($rows, 1);
            $imported = 0;

            DB::transaction(function () use ($dataRows, &$imported): void {
                $rowNum = 2;
                foreach ($dataRows as $row) {
                    if ($this->isRowEmpty($row)) {
                        $rowNum++;

                        continue;
                    }

                    $site = $this->cellStr($row, 0);
                    $perusahaan = $this->cellStr($row, 1);
                    $sid = $this->cellStr($row, 2);
                    $nama = $this->cellStr($row, 3);
                    $tanggalRaw = $row[4] ?? null;
                    $waktuRaw = $row[5] ?? null;

                    if ($sid === '' || $nama === '') {
                        throw new \InvalidArgumentException(
                            'Baris ' . $rowNum . ': SID dan Nama wajib diisi.'
                        );
                    }

                    $tanggal = $this->parseTanggalCell($tanggalRaw, $rowNum);
                    $waktu = $this->parseWaktuCell($waktuRaw, $rowNum);

                    SpeakUpFatigue::query()->create([
                        'site' => $site !== '' ? $site : null,
                        'perusahaan' => $perusahaan !== '' ? $perusahaan : null,
                        'sid' => $sid,
                        'nama' => $nama,
                        'tanggal' => $tanggal,
                        'waktu' => $waktu,
                    ]);
                    $imported++;
                    $rowNum++;
                }

                if ($imported === 0) {
                    throw new \InvalidArgumentException(
                        'Tidak ada baris data. Isi minimal satu baris (selain header).'
                    );
                }
            });

            return redirect()
                ->route('peer-pressure-edukasi.speak-up-fatigue.index')
                ->with('notify_success', 'Import berhasil: ' . $imported . ' baris.');
        } catch (\InvalidArgumentException $e) {
            return redirect()
                ->route('peer-pressure-edukasi.speak-up-fatigue.index', ['modal' => 'import'])
                ->with('notify_error', $e->getMessage());
        } catch (Throwable $e) {
            Log::error('Speak Up Fatigue import: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return redirect()
                ->route('peer-pressure-edukasi.speak-up-fatigue.index', ['modal' => 'import'])
                ->with('notify_error', 'Gagal memproses file. Pastikan format mengikuti template dan header baris pertama tidak diubah.');
        }
    }

    /**
     * @param  list<mixed>|array<int|string, mixed>  $row
     * @param  list<string>  $expected
     */
    private function assertHeaderMatches(array $row, array $expected, string $context): void
    {
        foreach ($expected as $i => $label) {
            $actual = isset($row[$i]) ? trim((string) $row[$i]) : '';
            if ($actual !== $label) {
                throw new \InvalidArgumentException(
                    'Template tidak sesuai (' . $context . '): kolom ' . ($i + 1) . ' harus "' . $label . '" (terbaca: "' . ($actual === '' ? '(kosong)' : $actual) . '"). Unduh template resmi.'
                );
            }
        }
    }

    /**
     * @param  list<mixed>|array<int|string, mixed>  $row
     */
    private function isRowEmpty(array $row): bool
    {
        foreach ($row as $cell) {
            if ($cell !== null && trim((string) $cell) !== '') {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  list<mixed>|array<int|string, mixed>  $row
     */
    private function cellStr(array $row, int $index): string
    {
        if (! array_key_exists($index, $row)) {
            return '';
        }

        return trim((string) $row[$index]);
    }

    private function parseTanggalCell(mixed $value, int $rowNum): string
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        if (is_numeric($value)) {
            try {
                return ExcelDate::excelToDateTimeObject((float) $value)->format('Y-m-d');
            } catch (Throwable) {
                throw new \InvalidArgumentException('Baris ' . $rowNum . ': format Tanggal tidak valid.');
            }
        }

        $s = trim((string) $value);
        if ($s === '') {
            throw new \InvalidArgumentException('Baris ' . $rowNum . ': Tanggal wajib diisi.');
        }

        $ts = strtotime($s);
        if ($ts === false) {
            throw new \InvalidArgumentException('Baris ' . $rowNum . ': Tanggal tidak dikenali (gunakan YYYY-MM-DD atau format tanggal standar).');
        }

        return date('Y-m-d', $ts);
    }

    private function parseWaktuCell(mixed $value, int $rowNum): string
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format('H:i:s');
        }

        if (is_numeric($value)) {
            try {
                return ExcelDate::excelToDateTimeObject((float) $value)->format('H:i:s');
            } catch (Throwable) {
                throw new \InvalidArgumentException('Baris ' . $rowNum . ': format Waktu tidak valid.');
            }
        }

        $s = trim((string) $value);
        if ($s === '') {
            throw new \InvalidArgumentException('Baris ' . $rowNum . ': Waktu wajib diisi.');
        }

        if (preg_match('/^\d{1,2}:\d{2}(:\d{2})?$/', $s)) {
            return strlen($s) === 5 ? $s . ':00' : $s;
        }

        $ts = strtotime($s);
        if ($ts !== false) {
            return date('H:i:s', $ts);
        }

        throw new \InvalidArgumentException('Baris ' . $rowNum . ': Waktu tidak dikenali (gunakan HH:MM atau HH:MM:SS).');
    }
}
