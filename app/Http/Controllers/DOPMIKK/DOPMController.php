<?php

namespace App\Http\Controllers\DOPMIKK;

use App\Http\Controllers\Controller;
use App\Jobs\ImportDopmJob;
use App\Models\Dopm;
use App\Models\IpkIkk;
use App\Models\Okk;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DOPMController extends Controller
{
    /**
     * Dashboard statistik harian DOPM, IKK, OKK, OAK. Semua tampilan by tanggal terpilih.
     */
    public function dashboard(Request $request): View
    {
        $filterDate = $request->get('date', now()->toDateString());
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $filterDate)) {
            $filterDate = now()->toDateString();
        }
        // Normalisasi: null / kosong / spasi = Semua Site (jangan filter by site)
        $filterSite = trim((string) ($request->query('site') ?? ''));

        // Scope tanggal & status (exclude Cancel)
        $scopeDate = function ($q) use ($filterDate) {
            $q->whereDate('tanggal_dop', $filterDate)->orWhereDate('timestamp', $filterDate);
        };
        $scopeNotCancel = function ($q) {
            $q->whereNull('status')->orWhereNotIn('status', ['Cancel', 'CANCEL']);
        };
        $scopeSite = function ($q) use ($filterSite) {
            if ($filterSite === '' || $filterSite === null) {
                return;
            }
            if ($filterSite === 'Lainnya') {
                $q->where(function ($q2) {
                    $q2->whereNull('site_ijin_kerja_khusus')
                        ->orWhereRaw("TRIM(COALESCE(site_ijin_kerja_khusus, '')) = ''");
                });
            } else {
                $q->whereRaw("TRIM(COALESCE(site_ijin_kerja_khusus, '')) = ?", [$filterSite]);
            }
        };

        // Daftar site untuk dropdown (tanggal terpilih, bukan Cancel)
        $siteList = Dopm::where($scopeDate)
            ->where($scopeNotCancel)
            ->get()
            ->map(fn ($d) => trim($d->site_ijin_kerja_khusus ?? '') ?: 'Lainnya')
            ->unique()
            ->sort()
            ->values()
            ->all();

        // Data harian: hitung per tanggal (+ optional site)
        $totalDopmHarian = Dopm::where($scopeDate)->where($scopeNotCancel)->when($filterSite !== '', $scopeSite)->count();

        // DOPM dengan status Cancel di hari ini
        $totalDopmCancelHarian = Dopm::where($scopeDate)
            ->whereIn('status', ['Cancel', 'CANCEL'])
            ->when($filterSite !== '', $scopeSite)
            ->count();

        // Total DOPM minggu ini (Senin–Minggu), tanpa status Cancel
        $mingguStart = Carbon::parse($filterDate)->startOfWeek(Carbon::MONDAY);
        $mingguEnd = $mingguStart->copy()->addDays(6)->endOfDay();
        $scopeWeek = function ($q) use ($mingguStart, $mingguEnd) {
            $q->whereBetween('tanggal_dop', [$mingguStart, $mingguEnd])
                ->orWhereBetween('timestamp', [$mingguStart, $mingguEnd]);
        };
        $totalDopmMingguIni = Dopm::where($scopeWeek)->where($scopeNotCancel)->when($filterSite !== '', $scopeSite)->count();

        // IPK-IKK dan OKK harian: setelah dapat DOPM list, hitung dari kode_ikk yang ada di list (agar konsisten dengan filter site)
        $totalIkkHarian = IpkIkk::whereDate('ts', $filterDate)->count();
        $totalOkkHarian = Okk::whereDate('ts', $filterDate)->count();

        // IPK-IKK dengan status_pekerjaan Batal di hari ini
        $totalPekerjaanBatalHarian = IpkIkk::whereDate('ts', $filterDate)
            ->whereIn('status_pekerjaan', ['Batal', 'BATAL'])
            ->count();

        // Daftar DOPM untuk tanggal terpilih (+ optional site)
        $dopmListHarian = Dopm::where($scopeDate)
            ->where($scopeNotCancel)
            ->when($filterSite !== '', $scopeSite)
            ->orderBy('tanggal_dop')
            ->orderBy('id')
            ->get();

        // Hitung total IKK/OKK hanya dari kode_ikk yang ada di DOPM terfilter (konsisten dengan filter site)
        $kodeIkksAll = $dopmListHarian->pluck('kode_ikk')->filter()->unique()->values()->all();
        if ($filterSite !== '' && !empty($kodeIkksAll)) {
            $totalIkkHarian = IpkIkk::whereDate('ts', $filterDate)->whereIn('kode_ikk', $kodeIkksAll)->count();
            $totalOkkHarian = Okk::whereDate('ts', $filterDate)->whereIn('kode_ikk', $kodeIkksAll)->count();
        }

        // Status matriks per DOPM: Is IKK ada IPK, Is IKK ada OKK (by kode_ikk + filterDate)
        $kodeIkks = $dopmListHarian->pluck('kode_ikk')->filter()->unique()->values()->all();
        $hasIpkByKode = [];
        $hasOkkByKode = [];
        if (!empty($kodeIkks)) {
            $ipkKodes = IpkIkk::whereIn('kode_ikk', $kodeIkks)
                ->whereDate('ts', $filterDate)
                ->select('kode_ikk')
                ->distinct()
                ->pluck('kode_ikk')
                ->flip()
                ->all();
            $okkKodes = Okk::whereIn('kode_ikk', $kodeIkks)
                ->whereDate('ts', $filterDate)
                ->select('kode_ikk')
                ->distinct()
                ->pluck('kode_ikk')
                ->flip()
                ->all();
            foreach ($kodeIkks as $k) {
                $hasIpkByKode[$k] = isset($ipkKodes[$k]);
                $hasOkkByKode[$k] = isset($okkKodes[$k]);
            }
        }

        foreach ($dopmListHarian as $dopm) {
            $k = $dopm->kode_ikk;
            $hasIpk = ($k === null || $k === '') ? null : ($hasIpkByKode[$k] ?? false);
            $hasOkk = ($k === null || $k === '') ? null : ($hasOkkByKode[$k] ?? false);
            $dopm->status_matriks = self::hitungStatusMatriks($hasIpk, $hasOkk);
            $dopm->is_ikk_ada_ipk = $hasIpk;
            $dopm->is_ikk_ada_okk = $hasOkk;
        }

        // OAK dari ClickHouse: tipe OBSERVE/OBSERVEE, laporan dari layer 2/3/4 yang ada di DOPM hari ini
        $totalOakHarian = 0;
        $layerSidsForOak = [];
        foreach ($dopmListHarian as $dopm) {
            foreach (['sid_layer_2', 'sid_layer_3', 'sid_layer_4'] as $key) {
                $v = trim((string) ($dopm->{$key} ?? ''));
                if ($v !== '') {
                    $layerSidsForOak[$v] = true;
                }
            }
        }
        $layerSidsForOak = array_keys($layerSidsForOak);
        if (!empty($layerSidsForOak)) {
            try {
                if (class_exists(\App\Services\ClickHouseService::class)) {
                    $clickHouse = app(\App\Services\ClickHouseService::class);
                    if (method_exists($clickHouse, 'query') && $clickHouse->isConnected()) {
                        $sidsIn = implode(',', array_map(function ($s) {
                            return "'" . addslashes($s) . "'";
                        }, $layerSidsForOak));
                        $sql = "SELECT count() as cnt FROM hse_automation.aaj_vw_car_oak_register_ytd_only"
                            . " WHERE toDate(submit_date) = '" . addslashes($filterDate) . "'"
                            . " AND (trim(lower(toString(tipe))) = 'observe' OR trim(lower(toString(tipe))) = 'observee')"
                            . " AND ((toString(kode_sid) IN ({$sidsIn})) OR (toString(kode_sid_pelapor) IN ({$sidsIn})) OR (toString(kode_sid_team) IN ({$sidsIn})))";
                        $result = $clickHouse->query($sql);
                        $totalOakHarian = isset($result[0]['cnt']) ? (int) $result[0]['cnt'] : 0;
                    }
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::debug('Dashboard OAK harian skip: ' . $e->getMessage());
            }
        }

        // Persentase IKK yang ada IPK / ada OKK (dasar: jumlah IKK unik dari DOPM tanggal terpilih)
        $totalIkkUnikHarian = count($kodeIkks);
        $ikkAdaIpkCount = $totalIkkUnikHarian > 0 ? count(array_filter($hasIpkByKode)) : 0;
        $ikkAdaOkkCount = $totalIkkUnikHarian > 0 ? count(array_filter($hasOkkByKode)) : 0;
        $pctIkkAdaIpk = $totalIkkUnikHarian > 0 ? round($ikkAdaIpkCount / $totalIkkUnikHarian * 100, 1) : 0;
        $pctIkkAdaOkk = $totalIkkUnikHarian > 0 ? round($ikkAdaOkkCount / $totalIkkUnikHarian * 100, 1) : 0;

        // Presentase dari total DOPM: berapa banyak DOPM yang IKK-nya punya IPK / OKK (basis = total DOPM, bukan IKK unik)
        $dopmAdaIpkCount = $dopmListHarian->where('is_ikk_ada_ipk', true)->count();
        $dopmAdaOkkCount = $dopmListHarian->where('is_ikk_ada_okk', true)->count();
        $pctDopmAdaIpk = $totalDopmHarian > 0 ? round($dopmAdaIpkCount / $totalDopmHarian * 100, 1) : 0;
        $pctDopmAdaOkk = $totalDopmHarian > 0 ? round($dopmAdaOkkCount / $totalDopmHarian * 100, 1) : 0;
        // OAK: rasio laporan OAK vs DOPM (min 100%), lalu dijadikan persen untuk dirata-ratakan dengan IPK & OKK
        $pctDopmOak = $totalDopmHarian > 0 ? min(100.0, round($totalOakHarian / $totalDopmHarian * 100, 1)) : 0;
        // Satu presentase gabungan: rata-rata IPK, OKK & OAK
        $pctPengisianRataRata = round(($pctDopmAdaIpk + $pctDopmAdaOkk + $pctDopmOak) / 3, 1);

        // Summary harian per site: jumlah per jenis IJK + status Hijau/Kuning/Merah
        $summaryBySite = [];
        $allJenis = [];
        foreach ($dopmListHarian as $dopm) {
            $site = trim($dopm->site_ijin_kerja_khusus ?? '') ?: 'Lainnya';
            $jenis = trim($dopm->jenis_ijin_kerja_khusus ?? '') ?: '-';
            $matriks = $dopm->status_matriks ?? 'Merah';

            if (!isset($summaryBySite[$site])) {
                $summaryBySite[$site] = [
                    'jenis' => [],
                    'hijau' => 0,
                    'kuning' => 0,
                    'merah' => 0,
                ];
            }
            $summaryBySite[$site]['hijau'] += ($matriks === 'Hijau' ? 1 : 0);
            $summaryBySite[$site]['kuning'] += ($matriks === 'Kuning' ? 1 : 0);
            $summaryBySite[$site]['merah'] += ($matriks === 'Merah' ? 1 : 0);
            $summaryBySite[$site]['jenis'][$jenis] = ($summaryBySite[$site]['jenis'][$jenis] ?? 0) + 1;
            $allJenis[$jenis] = true;
        }
        foreach ($summaryBySite as $site => &$row) {
            $row['total'] = $row['hijau'] + $row['kuning'] + $row['merah'];
        }
        unset($row);
        ksort($summaryBySite, SORT_NATURAL);
        $summaryJenisKeys = array_keys($allJenis);
        usort($summaryJenisKeys, function ($a, $b) {
            return strnatcasecmp($a, $b);
        });

        // Chart DOPM vs IPK vs OKK per jenis_ijin_kerja_khusus (3 bar per jenis)
        $chartJenisLabels = [];
        $chartDopmPerJenis = [];
        $chartIpkPerJenis = [];
        $chartOkkPerJenis = [];
        foreach ($summaryJenisKeys as $jenis) {
            $chartJenisLabels[] = self::singkatJenisIjin($jenis);
            $dopmPerJenis = $dopmListHarian->filter(function ($d) use ($jenis) {
                $j = trim($d->jenis_ijin_kerja_khusus ?? '') ?: '-';
                return $j === $jenis;
            });
            $chartDopmPerJenis[] = $dopmPerJenis->count();
            $kodeIkksJenis = $dopmPerJenis->pluck('kode_ikk')->filter()->unique()->values()->all();
            $chartIpkPerJenis[] = empty($kodeIkksJenis)
                ? 0
                : IpkIkk::whereDate('ts', $filterDate)->whereIn('kode_ikk', $kodeIkksJenis)->count();
            $chartOkkPerJenis[] = empty($kodeIkksJenis)
                ? 0
                : Okk::whereDate('ts', $filterDate)->whereIn('kode_ikk', $kodeIkksJenis)->count();
        }

        // Data IKK (work permit) dari ClickHouse untuk tampilan harian
        $ikkClickhouseListHarian = [];
        try {
            if (class_exists(\App\Services\ClickHouseService::class)) {
                /** @var \App\Services\ClickHouseService $clickHouse */
                $clickHouse = app(\App\Services\ClickHouseService::class);
                if (method_exists($clickHouse, 'query') && $clickHouse->isConnected()) {
                    // Ambil work permit harian berdasarkan start_date
                    $dateStr = addslashes($filterDate);
                    $siteFilterClause = '';
                    if ($filterSite !== '' && $filterSite !== null) {
                        if ($filterSite === 'Lainnya') {
                            $siteFilterClause = " AND trim(COALESCE(ra_site_name, '')) = ''";
                        } else {
                            $siteFilterClause = " AND trim(COALESCE(ra_site_name, '')) = '" . addslashes($filterSite) . "'";
                        }
                    }

                    $sqlWorkPermits = "
                        SELECT
                            id,
                            code,
                            name,
                            ra_site_name,
                            company_name,
                            status,
                            m_job_id
                        FROM hse_automation.ikk_work_permit
                        WHERE toDate(start_date) = '{$dateStr}'
                        {$siteFilterClause}
                    ";
                    $wpRows = $clickHouse->query($sqlWorkPermits);

                    if (!empty($wpRows)) {
                        // Map job_id -> job_name
                        $jobIds = array_values(array_unique(array_filter(array_column($wpRows, 'm_job_id'))));
                        $jobNamesById = [];
                        if (!empty($jobIds)) {
                            $inJobs = implode(',', array_map(function ($id) {
                                return "'" . addslashes($id) . "'";
                            }, $jobIds));
                            $sqlJobs = "
                                SELECT id, name
                                FROM hse_automation.ikk_m_job
                                WHERE id IN ({$inJobs})
                            ";
                            $jobRows = $clickHouse->query($sqlJobs);
                            foreach ($jobRows as $jr) {
                                if (!isset($jr['id'])) {
                                    continue;
                                }
                                $jobId = $jr['id'];
                                $jobNamesById[$jobId] = $jr['name'] ?? null;
                            }
                        }

                        // Ambil employee per work permit untuk layer 1/2/3/4
                        $wpIds = array_values(array_unique(array_column($wpRows, 'id')));
                        $layersByWp = [];
                        if (!empty($wpIds)) {
                            $inWpIds = implode(',', array_map(function ($id) {
                                return "'" . addslashes($id) . "'";
                            }, $wpIds));
                            $sqlEmp = "
                                SELECT work_permit_id, layer, employee_name
                                FROM hse_automation.ikk_work_permit_employee
                                WHERE work_permit_id IN ({$inWpIds})
                            ";
                            $empRows = $clickHouse->query($sqlEmp);

                            foreach ($empRows as $er) {
                                $wpId = $er['work_permit_id'] ?? null;
                                if ($wpId === null || $wpId === '') {
                                    continue;
                                }
                                $layerRaw = $er['layer'] ?? null;
                                if ($layerRaw === null || $layerRaw === '') {
                                    continue;
                                }
                                $layerNum = (int) $layerRaw;
                                if (!in_array($layerNum, [1, 2, 3, 4], true)) {
                                    continue;
                                }
                                if (!isset($layersByWp[$wpId])) {
                                    $layersByWp[$wpId] = [];
                                }
                                // Ambil nama pertama per layer
                                if (!isset($layersByWp[$wpId][$layerNum])) {
                                    $layersByWp[$wpId][$layerNum] = trim((string) ($er['employee_name'] ?? ''));
                                }
                            }
                        }

                        foreach ($wpRows as $row) {
                            $wpId = $row['id'] ?? null;
                            if ($wpId === null || $wpId === '') {
                                continue;
                            }
                            $layers = $layersByWp[$wpId] ?? [];
                            $namaLayer1 = $layers[1] ?? null;
                            $namaLayer2 = $layers[2] ?? null;
                            $namaLayer3 = $layers[3] ?? null;
                            $namaLayer4 = $layers[4] ?? null;

                            $status = $row['status'] ?? null;
                            $matriks = self::hitungStatusMatriksIkkClickhouse($status);

                            $ikkClickhouseListHarian[] = (object) [
                                'id' => $wpId,
                                'code' => $row['code'] ?? null,
                                'site' => $row['ra_site_name'] ?? null,
                                'jenis_ijin_kerja_khusus' => isset($row['m_job_id']) && $row['m_job_id']
                                    ? ($jobNamesById[$row['m_job_id']] ?? null)
                                    : null,
                                'nama_pekerjaan' => $row['name'] ?? null,
                                'perusahaan' => $row['company_name'] ?? null,
                                'status' => $status,
                                'status_matriks' => $matriks,
                                'nama_layer_1' => $namaLayer1,
                                'nama_layer_2' => $namaLayer2,
                                'nama_layer_3' => $namaLayer3,
                                'nama_layer_4' => $namaLayer4,
                            ];
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::debug('Dashboard IKK ClickHouse skip: ' . $e->getMessage());
        }

        return view('dopmikk.dopm.dashboard', [
            'filterDate' => $filterDate,
            'filterSite' => $filterSite,
            'siteList' => $siteList,
            'totalDopmHarian' => $totalDopmHarian,
            'totalDopmCancelHarian' => $totalDopmCancelHarian,
            'totalDopmMingguIni' => $totalDopmMingguIni,
            'totalPekerjaanBatalHarian' => $totalPekerjaanBatalHarian,
            'totalIkkHarian' => $totalIkkHarian,
            'totalOkkHarian' => $totalOkkHarian,
            'totalOakHarian' => $totalOakHarian,
            'dopmListHarian' => $dopmListHarian,
            'totalIkkUnikHarian' => $totalIkkUnikHarian,
            'pctIkkAdaIpk' => $pctIkkAdaIpk,
            'pctIkkAdaOkk' => $pctIkkAdaOkk,
            'pctDopmAdaIpk' => $pctDopmAdaIpk,
            'pctDopmAdaOkk' => $pctDopmAdaOkk,
            'pctDopmOak' => $pctDopmOak,
            'pctPengisianRataRata' => $pctPengisianRataRata,
            'ikkAdaIpkCount' => $ikkAdaIpkCount,
            'ikkAdaOkkCount' => $ikkAdaOkkCount,
            'summaryBySite' => $summaryBySite,
            'summaryJenisKeys' => $summaryJenisKeys,
            'chartJenisLabels' => $chartJenisLabels,
            'chartDopmPerJenis' => $chartDopmPerJenis,
            'chartIpkPerJenis' => $chartIpkPerJenis,
            'chartOkkPerJenis' => $chartOkkPerJenis,
            'ikkClickhouseListHarian' => $ikkClickhouseListHarian,
        ]);
    }

    /**
     * API data untuk modal detail DOPM: IPK-IKK, OKK, OAK (by kode_ikk dan konteks).
     */
    public function getDetailModalData(Request $request): JsonResponse
    {
        $kodeIkk = $request->input('kode_ikk', '');
        $jenisIjin = $request->input('jenis_ijin_kerja_khusus', '');
        $namaLayer2 = $request->input('nama_layer_2', '');
        $namaLayer3 = $request->input('nama_layer_3', '');
        $namaLayer4 = $request->input('nama_layer_4', '');
        $sidLayer2 = $request->input('sid_layer_2', '');
        $sidLayer3 = $request->input('sid_layer_3', '');
        $sidLayer4 = $request->input('sid_layer_4', '');

        $ipkIkk = [];
        $okk = [];
        $oak = [];

        if ($kodeIkk !== '' && $kodeIkk !== null) {
            // IPK-IKK dan OKK di modal: tampilkan semua data yang berelasi dengan kode_ikk
            $ipkIkk = IpkIkk::where('kode_ikk', $kodeIkk)
                ->orderByDesc('ts')
                ->get()
                ->map(function ($row) {
                    return $row->toArray();
                })
                ->values()
                ->toArray();

            $okk = Okk::where('kode_ikk', $kodeIkk)
                ->orderByDesc('ts')
                ->get()
                ->map(function ($row) {
                    return $row->toArray();
                })
                ->values()
                ->toArray();
        }

        try {
            $fullMapsUrl = route('full-maps.api.ikk-modal-data') . '?' . http_build_query([
                'kode_ikk' => $kodeIkk,
                'jenis_ijin_kerja_khusus' => $jenisIjin,
                'nama_layer_2' => $namaLayer2,
                'nama_layer_3' => $namaLayer3,
                'nama_layer_4' => $namaLayer4,
                'sid_layer_2' => $sidLayer2,
                'sid_layer_3' => $sidLayer3,
                'sid_layer_4' => $sidLayer4,
            ]);
            $response = Http::timeout(15)->get($fullMapsUrl);
            if ($response->successful()) {
                $body = $response->json();
                $oak = $body['oak'] ?? [];
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::debug('Dashboard modal OAK fetch: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'ipk_ikk' => $ipkIkk,
            'okk' => $okk,
            'oak' => $oak,
            'dopm_context' => [
                'nama_layer_2' => $namaLayer2,
                'nama_layer_3' => $namaLayer3,
                'nama_layer_4' => $namaLayer4,
            ],
        ]);
    }

    /**
     * API untuk modal Intervensi: ambil user Layer 1 dari vw_user dengan join by SID (sid_layer_1).
     * Kriteria sama seperti CctvDataController getUsersFromClickHouse: is_active=1, username/nama tidak kosong.
     * Return id, username, nama, email, selular, nik untuk tampilan dan kirim WA (IPK/OKK).
     */
    public function getLayer1Users(Request $request): JsonResponse
    {
        $sidLayer1 = trim((string) $request->input('sid_layer_1', ''));
        $namaLayer1 = trim((string) $request->input('nama_layer_1', ''));

        if ($sidLayer1 === '' && $namaLayer1 === '') {
            return response()->json(['success' => true, 'users' => [], 'nama_layer_1' => '']);
        }

        try {
            $query = \Illuminate\Support\Facades\DB::table('vw_user')
                ->where('is_active', 1)
                ->whereNotNull('username')
                ->where('username', '!=', '')
                ->whereNotNull('nama')
                ->where('nama', '!=', '');

            // Resolve by SID: username = sid_layer_1, atau banyak SID dipisah koma
            if ($sidLayer1 !== '') {
                $sids = array_map('trim', preg_split('/[\s,;]+/', $sidLayer1, -1, PREG_SPLIT_NO_EMPTY));
                $sids = array_unique(array_filter($sids));
                if (!empty($sids)) {
                    $query->whereIn('username', $sids);
                } else {
                    $query->where('username', '=', $sidLayer1);
                }
            } else {
                // Fallback: cari by nama (nama_layer_1) seperti sebelumnya
                $query->where(function ($q) use ($namaLayer1) {
                    $q->where('nama', 'LIKE', '%' . $namaLayer1 . '%')
                        ->orWhere('username', 'LIKE', '%' . $namaLayer1 . '%');
                });
            }

            $users = $query->select('id', 'username', 'nama', 'email', 'selular', 'nik')
                ->orderBy('username', 'ASC')
                ->limit(50)
                ->get()
                ->map(function ($row) {
                    return [
                        'id' => $row->id,
                        'username' => trim($row->username ?? ''),
                        'nama' => trim($row->nama ?? ''),
                        'email' => trim($row->email ?? ''),
                        'selular' => trim($row->selular ?? ''),
                        'nik' => trim($row->nik ?? ''),
                    ];
                })
                ->values()
                ->toArray();

            return response()->json([
                'success' => true,
                'users' => $users,
                'nama_layer_1' => $namaLayer1,
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('DOPMController getLayer1Users: ' . $e->getMessage());
            return response()->json(['success' => false, 'users' => [], 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * API untuk modal Intervensi OAK: ambil user Layer 2, 3, 4 dari vw_user (by SID/nama).
     * Return layer_2, layer_3, layer_4 masing-masing berisi users + nama_layer untuk WA.
     */
    public function getLayers234Users(Request $request): JsonResponse
    {
        $layers = [
            'layer_2' => ['sid' => trim((string) $request->input('sid_layer_2', '')), 'nama' => trim((string) $request->input('nama_layer_2', ''))],
            'layer_3' => ['sid' => trim((string) $request->input('sid_layer_3', '')), 'nama' => trim((string) $request->input('nama_layer_3', ''))],
            'layer_4' => ['sid' => trim((string) $request->input('sid_layer_4', '')), 'nama' => trim((string) $request->input('nama_layer_4', ''))],
        ];

        $result = ['success' => true, 'layer_2' => ['users' => [], 'nama_layer' => $layers['layer_2']['nama']], 'layer_3' => ['users' => [], 'nama_layer' => $layers['layer_3']['nama']], 'layer_4' => ['users' => [], 'nama_layer' => $layers['layer_4']['nama']]];

        try {
            foreach (['layer_2', 'layer_3', 'layer_4'] as $key) {
                $sid = $layers[$key]['sid'];
                $nama = $layers[$key]['nama'];
                if ($sid === '' && $nama === '') {
                    continue;
                }

                $query = \Illuminate\Support\Facades\DB::table('vw_user')
                    ->where('is_active', 1)
                    ->whereNotNull('username')
                    ->where('username', '!=', '')
                    ->whereNotNull('nama')
                    ->where('nama', '!=', '');

                if ($sid !== '') {
                    $sids = array_map('trim', preg_split('/[\s,;]+/', $sid, -1, PREG_SPLIT_NO_EMPTY));
                    $sids = array_unique(array_filter($sids));
                    if (!empty($sids)) {
                        $query->whereIn('username', $sids);
                    } else {
                        $query->where('username', '=', $sid);
                    }
                } else {
                    $query->where(function ($q) use ($nama) {
                        $q->where('nama', 'LIKE', '%' . $nama . '%')
                            ->orWhere('username', 'LIKE', '%' . $nama . '%');
                    });
                }

                $users = $query->select('id', 'username', 'nama', 'email', 'selular', 'nik')
                    ->orderBy('username', 'ASC')
                    ->limit(50)
                    ->get()
                    ->map(function ($row) {
                        return [
                            'id' => $row->id,
                            'username' => trim($row->username ?? ''),
                            'nama' => trim($row->nama ?? ''),
                            'email' => trim($row->email ?? ''),
                            'selular' => trim($row->selular ?? ''),
                            'nik' => trim($row->nik ?? ''),
                        ];
                    })
                    ->values()
                    ->toArray();

                $result[$key] = ['users' => $users, 'nama_layer' => $nama];
            }

            return response()->json($result);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('DOPMController getLayers234Users: ' . $e->getMessage());
            return response()->json(array_merge($result, ['success' => false, 'message' => $e->getMessage()]), 500);
        }
    }

    /**
     * Display DOPM entries with search and pagination.
     */
    public function index(Request $request): View
    {
        $perPage = (int) $request->get('per_page', 25);
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 25;

        $query = Dopm::query();

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('id_dop', 'like', "%{$search}%")
                    ->orWhere('site_ijin_kerja_khusus', 'like', "%{$search}%")
                    ->orWhere('perusahaan_ijin_kerja_khusus', 'like', "%{$search}%")
                    ->orWhere('kode_ikk', 'like', "%{$search}%")
                    ->orWhere('nama_pekerjaan', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhere('nama_layer_2', 'like', "%{$search}%")
                    ->orWhere('nama_layer_3', 'like', "%{$search}%")
                    ->orWhere('nama_layer_4', 'like', "%{$search}%");
            });
        }

        $entries = $query->orderByDesc('id')->paginate($perPage)->withQueryString();

        return view('dopmikk.dopm.index', compact('entries', 'perPage'));
    }

    /**
     * Show the form for creating a new DOPM (optional; can use import only).
     */
    public function create(): View
    {
        return view('dopmikk.dopm.create');
    }

    /**
     * Store a new DOPM entry.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_dop' => ['nullable', 'string', 'max:100'],
            'timestamp' => ['nullable', 'date'],
            'site_ijin_kerja_khusus' => ['nullable', 'string', 'max:255'],
            'perusahaan_ijin_kerja_khusus' => ['nullable', 'string', 'max:255'],
            'jenis_ijin_kerja_khusus' => ['nullable', 'string', 'max:255'],
            'kode_ikk' => ['nullable', 'string', 'max:255'],
            'tanggal_selesai_ijin' => ['nullable', 'date'],
            'nama_pekerjaan' => ['nullable', 'string', 'max:255'],
            'tanggal_dop' => ['nullable', 'date'],
            'status_pengiriman_notif' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:255'],
            'deskripsi_atau_alasan_cancel' => ['nullable', 'string'],
            'sid_layer_1' => ['nullable', 'string', 'max:255'],
            'nama_layer_1' => ['nullable', 'string', 'max:255'],
            'shift' => ['nullable', 'string', 'max:255'],
            'jam_mulai' => ['nullable', 'string', 'max:255'],
            'jam_akhir' => ['nullable', 'string', 'max:255'],
            'sid_layer_2' => ['nullable', 'string', 'max:255'],
            'nama_layer_2' => ['nullable', 'string', 'max:255'],
            'sid_layer_3' => ['nullable', 'string', 'max:255'],
            'nama_layer_3' => ['nullable', 'string', 'max:255'],
            'sid_layer_4' => ['nullable', 'string', 'max:255'],
            'nama_layer_4' => ['nullable', 'string', 'max:255'],
            'jenis_pengawasan_layer' => ['nullable', 'string', 'max:255'],
            'detail_lokasi' => ['nullable', 'string'],
        ]);

        if (!empty($validated['timestamp'])) {
            $validated['timestamp'] = Carbon::parse($validated['timestamp']);
        }

        Dopm::create($validated);

        return redirect()
            ->route('dopmikk.dopm.index')
            ->with('success', 'Data DOPM berhasil disimpan.');
    }

    /**
     * Show the form for editing the specified DOPM.
     */
    public function edit(Dopm $dopm): View
    {
        return view('dopmikk.dopm.edit', compact('dopm'));
    }

    /**
     * Update the specified DOPM.
     */
    public function update(Request $request, Dopm $dopm): RedirectResponse
    {
        $validated = $request->validate([
            'id_dop' => ['nullable', 'string', 'max:100'],
            'timestamp' => ['nullable', 'date'],
            'site_ijin_kerja_khusus' => ['nullable', 'string', 'max:255'],
            'perusahaan_ijin_kerja_khusus' => ['nullable', 'string', 'max:255'],
            'jenis_ijin_kerja_khusus' => ['nullable', 'string', 'max:255'],
            'kode_ikk' => ['nullable', 'string', 'max:255'],
            'tanggal_selesai_ijin' => ['nullable', 'date'],
            'nama_pekerjaan' => ['nullable', 'string', 'max:255'],
            'tanggal_dop' => ['nullable', 'date'],
            'status_pengiriman_notif' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:255'],
            'deskripsi_atau_alasan_cancel' => ['nullable', 'string'],
            'sid_layer_1' => ['nullable', 'string', 'max:255'],
            'nama_layer_1' => ['nullable', 'string', 'max:255'],
            'shift' => ['nullable', 'string', 'max:255'],
            'jam_mulai' => ['nullable', 'string', 'max:255'],
            'jam_akhir' => ['nullable', 'string', 'max:255'],
            'sid_layer_2' => ['nullable', 'string', 'max:255'],
            'nama_layer_2' => ['nullable', 'string', 'max:255'],
            'sid_layer_3' => ['nullable', 'string', 'max:255'],
            'nama_layer_3' => ['nullable', 'string', 'max:255'],
            'sid_layer_4' => ['nullable', 'string', 'max:255'],
            'nama_layer_4' => ['nullable', 'string', 'max:255'],
            'jenis_pengawasan_layer' => ['nullable', 'string', 'max:255'],
            'detail_lokasi' => ['nullable', 'string'],
        ]);

        if (!empty($validated['timestamp'])) {
            $validated['timestamp'] = Carbon::parse($validated['timestamp']);
        }

        $dopm->update($validated);

        return redirect()
            ->route('dopmikk.dopm.index')
            ->with('success', 'Data DOPM berhasil diperbarui.');
    }

    /**
     * Remove the specified DOPM.
     */
    public function destroy(Dopm $dopm): RedirectResponse
    {
        $dopm->delete();

        return redirect()
            ->route('dopmikk.dopm.index')
            ->with('success', 'Data DOPM berhasil dihapus.');
    }

    /**
     * Import DOPM data from Excel.
     */
    public function import(Request $request): RedirectResponse
    {
        try {
            $request->validate([
                'excel_file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
            ]);

            $file = $request->file('excel_file');

            if (!$file || !$file->isValid()) {
                return redirect()
                    ->route('dopmikk.dopm.index')
                    ->with('error', 'File tidak valid atau gagal diunggah.');
            }

            $uniqueName = uniqid('dopm_', true) . '.' . $file->getClientOriginalExtension();
            $storedPath = $file->storeAs('dopm-imports', $uniqueName);

            if (!$storedPath) {
                return redirect()
                    ->route('dopmikk.dopm.index')
                    ->with('error', 'Gagal menyimpan file.');
            }

            ImportDopmJob::dispatch($storedPath)->onQueue('default');

            return redirect()
                ->route('dopmikk.dopm.index')
                ->with('success', 'File berhasil diunggah dan sedang diproses. Silakan cek beberapa saat lagi.');
        } catch (\Exception $e) {
            \Log::error('DOPMController import error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()
                ->route('dopmikk.dopm.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Download template Excel untuk import DOPM.
     */
    public function downloadTemplate()
    {
        // Urutan harus sama dengan ImportDopmJob
        $headers = [
            'ID',
            'Timestamp',
            'Site Ijin Kerja Khusus',
            'Perusahaan Ijin Kerja Khusus',
            'Jenis Ijin Kerja Khusus',
            'Kode IKK (Ijin Kerja Khusus)',
            'Tanggal Selesai Ijin',
            'Nama Pekerjaan',
            'Tanggal DOP',
            'SID Layer 1',
            'Nama Layer 1',
            'Shift',
            'Jam Mulai',
            'Jam Akhir',
            'Status Pengiriman Notif',
            'Status',
            'Deskripsi / Alasan cancel',
            'SID Layer 2',
            'Nama Layer 2',
            'SID Layer 3',
            'Nama Layer 3',
            'SID Layer 4',
            'Nama Layer 4',
            'Jenis Pengawasan Layer 2,3,4',
            'Detail Lokasi',
        ];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('DOPM');
        $col = 1;
        foreach ($headers as $h) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($col) . '1', $h);
            $col++;
        }
        $lastCol = Coordinate::stringFromColumnIndex(count($headers));
        $sheet->getStyle('A1:' . $lastCol . '1')->getFont()->setBold(true);

        $writer = new Xlsx($spreadsheet);
        $filename = 'template_import_dopm.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    /**
     * Singkatan label jenis ijin kerja khusus untuk chart (maks 12 karakter).
     */
    public static function singkatJenisIjin(string $jenis): string
    {
        $s = trim($jenis);
        if ($s === '' || $s === '-') {
            return $s ?: '-';
        }
        $s = preg_replace('/\s*ijin\s+kerja\s+khusus\s*/iu', 'IKK ', $s);
        $s = preg_replace('/\s*-\s*/u', ' ', trim($s));
        $s = trim($s);
        if (mb_strlen($s) > 12) {
            $s = mb_substr($s, 0, 11) . '…';
        }
        return $s ?: '-';
    }

    /**
     * Hitung status matriks berdasarkan Is IKK ada IPK dan Is IKK ada OKK.
     * Returns: 'Hijau' | 'Kuning' | 'Merah'
     *
     * @param  bool|null  $isIkkAdaIpk
     * @param  bool|null  $isIkkAdaOkk
     */
    public static function hitungStatusMatriks(?bool $isIkkAdaIpk, ?bool $isIkkAdaOkk): string
    {
        $ipk = $isIkkAdaIpk;
        $okk = $isIkkAdaOkk;

        // IF [Is IKK ada Full ada IPK OKK]=TRUE then 'Hijau'
        if ($ipk === true && $okk === true) {
            return 'Hijau';
        }
        // ELSEIF [Is IKK ada IPK]=TRUE and [Is IKK ada OKK]=TRUE then 'Hijau' (same)
        // ELSEIF [Is IKK ada IPK]=FALSE and [Is IKK ada OKK]=FALSE then 'Merah'
        if ($ipk === false && $okk === false) {
            return 'Merah';
        }
        // ELSEIF [Is IKK ada IPK]=NULL and [Is IKK ada OKK]=FALSE then 'Merah'
        if ($ipk === null && $okk === false) {
            return 'Merah';
        }
        // ELSEIF [Is IKK ada IPK]=FALSE and [Is IKK ada OKK]=NULL then 'Merah'
        if ($ipk === false && $okk === null) {
            return 'Merah';
        }
        // ELSEIF ISNULL([Is IKK ada IPK]) and ISNULL([Is IKK ada OKK]) then 'Merah'
        if ($ipk === null && $okk === null) {
            return 'Merah';
        }
        // ELSEIF [Is IKK ada IPK]=TRUE and [Is IKK ada OKK]=FALSE then 'Kuning'
        if ($ipk === true && $okk === false) {
            return 'Kuning';
        }
        // ELSEIF [Is IKK ada IPK]=TRUE and ISNULL([Is IKK ada OKK]) then 'Kuning'
        if ($ipk === true && $okk === null) {
            return 'Kuning';
        }
        // ELSEIF [Is IKK ada IPK]=FALSE and [Is IKK ada OKK]=TRUE then 'Kuning'
        if ($ipk === false && $okk === true) {
            return 'Kuning';
        }
        // ELSEIF ISNULL([Is IKK ada IPK]) and [Is IKK ada OKK]=TRUE then 'Kuning'
        if ($ipk === null && $okk === true) {
            return 'Kuning';
        }
        // ELSEIF [Is IKK ada OKK]=FALSE then 'Kuning'
        if ($okk === false) {
            return 'Kuning';
        }
        // ELSEIF [Is IKK ada IPK]=FALSE then 'Kuning'
        if ($ipk === false) {
            return 'Kuning';
        }
        // ELSEIF ISNULL([Is IKK ada OKK]) then 'Kuning'
        if ($okk === null) {
            return 'Kuning';
        }
        // ELSEIF ISNULL([Is IKK ada IPK]) then 'Kuning'
        if ($ipk === null) {
            return 'Kuning';
        }
        // ELSE 'Merah'
        return 'Merah';
    }

    /**
     * Hitung status matriks untuk data IKK (work permit) dari ClickHouse
     * berdasarkan kolom status di tabel ikk_work_permit.
     * Returns: 'Hijau' | 'Kuning' | 'Merah'
     */
    public static function hitungStatusMatriksIkkClickhouse(?string $status): string
    {
        if ($status === null) {
            return 'Merah';
        }
        $s = strtoupper(trim($status));
        if ($s === '') {
            return 'Merah';
        }

        // Status baik / hijau
        if (in_array($s, ['ACTIVE', 'APPROVED', 'ONGOING', 'IN PROGRESS'], true)) {
            return 'Hijau';
        }

        // Status kuning (masih berjalan / menunggu)
        if (in_array($s, ['PENDING', 'SUBMITTED', 'EXTEND', 'EXTENDED', 'WAITING APPROVAL'], true)) {
            return 'Kuning';
        }

        // Status merah (selesai / kadaluarsa / batal)
        if (in_array($s, ['EXPIRED', 'REJECTED', 'CANCELLED', 'CANCELED', 'CLOSED'], true)) {
            return 'Merah';
        }

        // Default: kuning sebagai tengah
        return 'Kuning';
    }
}
