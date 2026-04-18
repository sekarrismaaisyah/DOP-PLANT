<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\PeerPressureSbsKelompokRequest;
use App\Models\SbsAnggota;
use App\Models\SbsKelompok;
use App\Services\PeerPressure\PeerPressureKaryawanNitipService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Throwable;

class PeerPressureSbsKelompokController extends Controller
{
    /** Judul sheet pada template unduhan (sheet pertama saat impor). */
    private const SHEET_IMPORT = 'SBS';

    /**
     * Satu sheet: tiap baris = satu anggota; kolom kelompok diulang per baris.
     * Baris dengan Nama Kelompok sama digabung ke satu grup (data kelompok harus identik).
     *
     * @var list<string>
     */
    private const HEADER_IMPORT = [
        'Site',
        'Perusahaan',
        'Level Grup',
        'Nama Kelompok',
        'Nama Bapak Asuh',
        'SID Bapak Asuh',
        'SID Anggota',
        'Nama Anggota',
    ];

    public function index(Request $request, PeerPressureKaryawanNitipService $karyawanNitip): View
    {
        $query = SbsKelompok::query()
            ->with('anggota')
            ->orderBy('nama_kelompok')
            ->orderByDesc('id');

        $q = trim((string) $request->get('q', ''));
        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('site', 'like', '%' . $q . '%')
                    ->orWhere('perusahaan', 'like', '%' . $q . '%')
                    ->orWhere('level_grup', 'like', '%' . $q . '%')
                    ->orWhere('nama_kelompok', 'like', '%' . $q . '%')
                    ->orWhere('nama_bapak_asuh', 'like', '%' . $q . '%')
                    ->orWhere('sid_bapak_asuh', 'like', '%' . $q . '%')
                    ->orWhereHas('anggota', function ($a) use ($q) {
                        $a->where('sid', 'like', '%' . $q . '%')
                            ->orWhere('nama', 'like', '%' . $q . '%');
                    });
            });
        }

        $kelompok = $query->paginate(12)->withQueryString();

        $sids = [];
        foreach ($kelompok as $k) {
            $sids[] = $k->sid_bapak_asuh;
            foreach ($k->anggota as $a) {
                $sids[] = $a->sid;
            }
        }
        $peerFotoUrls = $karyawanNitip->fotoUrlsByKodeSids($sids);

        return view('peer-pressure-edukasi.sbs.index', [
            'kelompok' => $kelompok,
            'q' => $q,
            'navActive' => 'sbs',
            'peerFotoUrls' => $peerFotoUrls,
        ]);
    }

    public function create(): View
    {
        return view('peer-pressure-edukasi.sbs.form', [
            'mode' => 'create',
            'row' => new SbsKelompok(),
            'anggotaRows' => [['sid' => '', 'nama' => '']],
            'navActive' => 'sbs',
        ]);
    }

    public function store(PeerPressureSbsKelompokRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request): void {
            $k = SbsKelompok::query()->create($request->kelompokAttributes());
            foreach ($request->anggotaPayload() as $row) {
                SbsAnggota::query()->create([
                    'kelompok_id' => $k->id,
                    'sid' => $row['sid'],
                    'nama' => $row['nama'],
                    'urutan' => $row['urutan'],
                ]);
            }
        });

        return redirect()
            ->route('peer-pressure-edukasi.sbs.index')
            ->with('success', 'Kelompok SBS berhasil ditambahkan.');
    }

    public function edit(int $id): View
    {
        $row = SbsKelompok::query()
            ->with('anggota')
            ->findOrFail($id);

        $anggota = $row->anggota->values();
        $anggotaRows = $anggota->isNotEmpty()
            ? $anggota->map(fn ($a) => ['sid' => $a->sid, 'nama' => $a->nama])->all()
            : [['sid' => '', 'nama' => '']];

        return view('peer-pressure-edukasi.sbs.form', [
            'mode' => 'edit',
            'row' => $row,
            'anggotaRows' => $anggotaRows,
            'navActive' => 'sbs',
        ]);
    }

    public function update(PeerPressureSbsKelompokRequest $request, int $id): RedirectResponse
    {
        $kelompok = SbsKelompok::query()->findOrFail($id);

        DB::transaction(function () use ($request, $kelompok): void {
            $kelompok->update($request->kelompokAttributes());
            $kelompok->anggota()->delete();
            foreach ($request->anggotaPayload() as $anggotaRow) {
                SbsAnggota::query()->create([
                    'kelompok_id' => $kelompok->id,
                    'sid' => $anggotaRow['sid'],
                    'nama' => $anggotaRow['nama'],
                    'urutan' => $anggotaRow['urutan'],
                ]);
            }
        });

        return redirect()
            ->route('peer-pressure-edukasi.sbs.index')
            ->with('success', 'Kelompok SBS berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $kelompok = SbsKelompok::query()->findOrFail($id);
        $kelompok->delete();

        return redirect()
            ->route('peer-pressure-edukasi.sbs.index')
            ->with('success', 'Kelompok SBS berhasil dihapus.');
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

        // Dua baris = satu kelompok "Topaz", dua anggota (kolom kelompok diulang).
        $sheet->fromArray([
            ['BMO 1', 'MTL', 'Non Pengawas', 'Topaz', 'Ambas', '4567Y', '1111A', 'Contoh Anggota 1'],
            ['BMO 1', 'MTL', 'Non Pengawas', 'Topaz', 'Ambas', '4567Y', '2222B', 'Contoh Anggota 2'],
        ], null, 'A2');

        foreach (range(1, count(self::HEADER_IMPORT)) as $i) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setAutoSize(true);
        }

        $filename = 'template_import_sbs_' . date('Y-m-d') . '.xlsx';
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
                ->route('peer-pressure-edukasi.sbs.index', ['modal' => 'import'])
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $file = $request->file('excel_file');
            if ($file === null) {
                return redirect()
                    ->route('peer-pressure-edukasi.sbs.index', ['modal' => 'import'])
                    ->with('notify_error', 'File tidak valid.');
            }

            $path = $file->getRealPath();
            if ($path === false) {
                return redirect()
                    ->route('peer-pressure-edukasi.sbs.index', ['modal' => 'import'])
                    ->with('notify_error', 'Tidak dapat membaca file.');
            }

            $spreadsheet = IOFactory::load($path);

            $sheet = $spreadsheet->getSheet(0);
            $rows = $sheet->toArray();

            if ($rows === []) {
                return redirect()
                    ->route('peer-pressure-edukasi.sbs.index', ['modal' => 'import'])
                    ->with('notify_error', 'File kosong atau tidak terbaca. Gunakan sheet pertama berisi data SBS.');
            }

            $this->assertHeaderMatches($rows[0] ?? [], self::HEADER_IMPORT, 'sheet pertama');

            $dataRows = array_slice($rows, 1);

            /**
             * Kunci: Nama Kelompok (trim). Tiap nama punya satu set metadata kelompok + daftar anggota.
             *
             * @var array<string, array{site: string, perusahaan: string, level_grup: string, nama_kelompok: string, nama_bapak_asuh: string, sid_bapak_asuh: string, members: list<array{sid: string, nama: string}>}>
             */
            $groups = [];

            $rowNum = 2;
            foreach ($dataRows as $row) {
                if ($this->isRowEmpty($row)) {
                    $rowNum++;

                    continue;
                }

                $site = $this->cellStr($row, 0);
                $perusahaan = $this->cellStr($row, 1);
                $levelGrup = $this->cellStr($row, 2);
                $namaKelompok = $this->cellStr($row, 3);
                $namaBapak = $this->cellStr($row, 4);
                $sidBapak = $this->cellStr($row, 5);
                $sidAnggota = $this->cellStr($row, 6);
                $namaAnggota = $this->cellStr($row, 7);

                if ($levelGrup === '' || $namaKelompok === '' || $namaBapak === '' || $sidBapak === '') {
                    throw new \InvalidArgumentException(
                        'Baris ' . $rowNum . ': Level Grup, Nama Kelompok, Nama Bapak Asuh, dan SID Bapak Asuh wajib diisi.'
                    );
                }

                if ($sidAnggota === '' || $namaAnggota === '') {
                    throw new \InvalidArgumentException(
                        'Baris ' . $rowNum . ': SID Anggota dan Nama Anggota wajib diisi (satu baris = satu anggota).'
                    );
                }

                if (! isset($groups[$namaKelompok])) {
                    $groups[$namaKelompok] = [
                        'site' => $site,
                        'perusahaan' => $perusahaan,
                        'level_grup' => $levelGrup,
                        'nama_kelompok' => $namaKelompok,
                        'nama_bapak_asuh' => $namaBapak,
                        'sid_bapak_asuh' => $sidBapak,
                        'members' => [],
                    ];
                } else {
                    if (! $this->kelompokBarisSama(
                        $groups[$namaKelompok],
                        $site,
                        $perusahaan,
                        $levelGrup,
                        $namaKelompok,
                        $namaBapak,
                        $sidBapak
                    )) {
                        throw new \InvalidArgumentException(
                            'Baris ' . $rowNum . ': untuk Nama Kelompok "' . $namaKelompok . '", data Site / Perusahaan / Level / Bapak Asuh harus sama persis pada setiap baris.'
                        );
                    }
                }

                $groups[$namaKelompok]['members'][] = [
                    'sid' => $sidAnggota,
                    'nama' => $namaAnggota,
                ];

                $rowNum++;
            }

            if ($groups === []) {
                throw new \InvalidArgumentException(
                    'Tidak ada baris data. Isi minimal satu baris anggota (selain header).'
                );
            }

            $kelompokImported = 0;
            $anggotaImported = 0;

            DB::transaction(function () use ($groups, &$kelompokImported, &$anggotaImported): void {
                foreach ($groups as $payload) {
                    $k = SbsKelompok::query()->create([
                        'site' => $payload['site'] !== '' ? $payload['site'] : null,
                        'perusahaan' => $payload['perusahaan'] !== '' ? $payload['perusahaan'] : null,
                        'level_grup' => $payload['level_grup'],
                        'nama_kelompok' => $payload['nama_kelompok'],
                        'nama_bapak_asuh' => $payload['nama_bapak_asuh'],
                        'sid_bapak_asuh' => $payload['sid_bapak_asuh'],
                    ]);
                    $kelompokImported++;

                    $urutan = 0;
                    foreach ($payload['members'] as $m) {
                        SbsAnggota::query()->create([
                            'kelompok_id' => $k->id,
                            'sid' => $m['sid'],
                            'nama' => $m['nama'],
                            'urutan' => $urutan,
                        ]);
                        $urutan++;
                        $anggotaImported++;
                    }
                }
            });

            $msg = 'Import berhasil: ' . $kelompokImported . ' kelompok, ' . $anggotaImported . ' anggota.';

            return redirect()
                ->route('peer-pressure-edukasi.sbs.index')
                ->with('notify_success', $msg);
        } catch (\InvalidArgumentException $e) {
            return redirect()
                ->route('peer-pressure-edukasi.sbs.index', ['modal' => 'import'])
                ->with('notify_error', $e->getMessage());
        } catch (Throwable $e) {
            Log::error('SBS Excel import: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return redirect()
                ->route('peer-pressure-edukasi.sbs.index', ['modal' => 'import'])
                ->with('notify_error', 'Gagal memproses file. Pastikan format mengikuti template (.xlsx) dan header baris pertama tidak diubah.');
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
                    'Template tidak sesuai (' . $context . '): kolom ' . ($i + 1) . ' harus "' . $label . '" (terbaca: "' . ($actual === '' ? '(kosong)' : $actual) . '"). Unduh template resmi dan jangan mengubah urutan atau teks header.'
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

    /**
     * @param  array{site: string, perusahaan: string, level_grup: string, nama_kelompok: string, nama_bapak_asuh: string, sid_bapak_asuh: string, members: list<array{sid: string, nama: string}>}  $stored
     */
    private function kelompokBarisSama(
        array $stored,
        string $site,
        string $perusahaan,
        string $levelGrup,
        string $namaKelompok,
        string $namaBapak,
        string $sidBapak,
    ): bool {
        return $this->normCell($stored['site']) === $this->normCell($site)
            && $this->normCell($stored['perusahaan']) === $this->normCell($perusahaan)
            && $stored['level_grup'] === $levelGrup
            && $stored['nama_kelompok'] === $namaKelompok
            && $stored['nama_bapak_asuh'] === $namaBapak
            && $stored['sid_bapak_asuh'] === $sidBapak;
    }

    private function normCell(string $value): string
    {
        return trim($value);
    }
}
