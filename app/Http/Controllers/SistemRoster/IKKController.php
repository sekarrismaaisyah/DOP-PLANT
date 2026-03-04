<?php

namespace App\Http\Controllers\SistemRoster;

use App\Http\Controllers\Controller;
use App\Services\ClickHouseService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class IKKController extends Controller
{
    public function index(Request $request): View
    {
        $filterStartDate = $request->get('start_date', now()->toDateString());
        $filterEndDate = $request->get('end_date', now()->toDateString());
        
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $filterStartDate)) {
            $filterStartDate = now()->toDateString();
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $filterEndDate)) {
            $filterEndDate = now()->toDateString();
        }
        
        if ($filterStartDate > $filterEndDate) {
            $temp = $filterStartDate;
            $filterStartDate = $filterEndDate;
            $filterEndDate = $temp;
        }

        $filterSite = trim((string) ($request->query('site') ?? ''));
        $perPage = (int) $request->get('per_page', 25);
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 25;
        $page = (int) $request->get('page', 1);

        $ikkList = collect();
        $siteList = [];
        $totalRecords = 0;
        $clickhouseConnected = false;

        try {
            if (class_exists(ClickHouseService::class)) {
                $clickHouse = app(ClickHouseService::class);
                if (method_exists($clickHouse, 'query') && $clickHouse->isConnected()) {
                    $clickhouseConnected = true;
                    $startDateStr = addslashes($filterStartDate);
                    $endDateStr = addslashes($filterEndDate);

                    $siteFilterClause = '';
                    if ($filterSite !== '' && $filterSite !== null) {
                        if ($filterSite === 'Lainnya') {
                            $siteFilterClause = " AND trim(COALESCE(ra_site_name, '')) = ''";
                        } else {
                            $siteFilterClause = " AND trim(COALESCE(ra_site_name, '')) = '" . addslashes($filterSite) . "'";
                        }
                    }

                    $sqlSites = "
                        SELECT DISTINCT trim(COALESCE(ra_site_name, '')) as site_name
                        FROM hse_automation.ikk_work_permit
                        WHERE toDate(start_date) <= toDate('{$endDateStr}')
                          AND toDate(end_date) >= toDate('{$startDateStr}')
                          AND status IN ('APPROVED')
                          AND deleted_at IS NULL
                        ORDER BY site_name
                    ";
                    $siteRows = $clickHouse->query($sqlSites);
                    foreach ($siteRows ?? [] as $row) {
                        $siteName = trim($row['site_name'] ?? '');
                        if ($siteName === '') {
                            $siteList[] = 'Lainnya';
                        } else {
                            $siteList[] = $siteName;
                        }
                    }
                    $siteList = array_unique($siteList);
                    sort($siteList);

                    $sqlCount = "
                        SELECT count(DISTINCT code) as total
                        FROM hse_automation.ikk_work_permit
                        WHERE toDate(start_date) <= toDate('{$endDateStr}')
                          AND toDate(end_date) >= toDate('{$startDateStr}')
                          AND status IN ('APPROVED')
                          AND deleted_at IS NULL
                          {$siteFilterClause}
                    ";
                    $countResult = $clickHouse->query($sqlCount);
                    $totalRecords = isset($countResult[0]['total']) ? (int) $countResult[0]['total'] : 0;

                    $offset = ($page - 1) * $perPage;
                    $sql = "
                        SELECT
                            id,
                            code,
                            status,
                            ra_site_name,
                            company_name,
                            m_job_id,
                            start_date,
                            end_date,
                            location_name,
                            location_detail_name,
                            created_at
                        FROM hse_automation.ikk_work_permit
                        WHERE toDate(start_date) <= toDate('{$endDateStr}')
                          AND toDate(end_date) >= toDate('{$startDateStr}')
                          AND status IN ('APPROVED')
                          AND deleted_at IS NULL
                          {$siteFilterClause}
                        ORDER BY start_date DESC, created_at DESC
                        LIMIT {$perPage} OFFSET {$offset}
                    ";
                    $rows = $clickHouse->query($sql);

                    $jobIds = [];
                    $wpIds = [];
                    foreach ($rows ?? [] as $row) {
                        $jobId = $this->getClickHouseRowValue($row, 'm_job_id');
                        $wpId = $this->getClickHouseRowValue($row, 'id');
                        if ($jobId !== null && $jobId !== '') {
                            $jobIds[] = $jobId;
                        }
                        if ($wpId !== null) {
                            $wpIds[] = $wpId;
                        }
                    }

                    $jobMap = [];
                    if (!empty($jobIds)) {
                        $jobIds = array_unique($jobIds);
                        $inJobs = implode(',', array_map(fn($id) => "'" . addslashes((string) $id) . "'", $jobIds));
                        $sqlJobs = "
                            SELECT id, name
                            FROM hse_automation.ikk_m_job
                            WHERE id IN ({$inJobs})
                        ";
                        $jobRows = $clickHouse->query($sqlJobs);
                        foreach ($jobRows ?? [] as $jr) {
                            $jId = $this->getClickHouseRowValue($jr, 'id');
                            $jName = $this->getClickHouseRowValue($jr, 'name');
                            if ($jId !== null) {
                                $jobMap[$jId] = $jName;
                            }
                        }
                    }

                    $employeeMap = [];
                    if (!empty($wpIds)) {
                        $inWpIds = implode(',', array_map(fn($id) => "'" . addslashes((string) $id) . "'", $wpIds));
                        $sqlEmp = "
                            SELECT work_permit_id, layer, employee_name, employee_sid
                            FROM hse_automation.ikk_work_permit_employee
                            WHERE work_permit_id IN ({$inWpIds})
                              AND (deleted_at IS NULL OR deleted_at = toDateTime(0))
                        ";
                        $empRows = $clickHouse->query($sqlEmp);
                        foreach ($empRows ?? [] as $er) {
                            $empWpId = $this->getClickHouseRowValue($er, 'work_permit_id');
                            $layer = $this->getClickHouseRowValue($er, 'layer');
                            $empName = $this->getClickHouseRowValue($er, 'employee_name');
                            $empSid = $this->getClickHouseRowValue($er, 'employee_sid');
                            if ($empWpId !== null) {
                                if (!isset($employeeMap[$empWpId])) {
                                    $employeeMap[$empWpId] = [];
                                }
                                $employeeMap[$empWpId][] = [
                                    'layer' => $layer,
                                    'name' => $empName,
                                    'sid' => $empSid,
                                ];
                            }
                        }
                    }

                    foreach ($rows ?? [] as $row) {
                        $wpId = $this->getClickHouseRowValue($row, 'id');
                        $jobId = $this->getClickHouseRowValue($row, 'm_job_id');
                        $siteName = trim($this->getClickHouseRowValue($row, 'ra_site_name') ?? '');
                        $companyName = trim($this->getClickHouseRowValue($row, 'company_name') ?? '');

                        $employees = $employeeMap[$wpId] ?? [];
                        $layer1 = collect($employees)->where('layer', '1')->first();
                        $layer2 = collect($employees)->where('layer', '2')->first();
                        $layer3 = collect($employees)->where('layer', '3')->first();
                        $layer4 = collect($employees)->where('layer', '4')->first();

                        $startDate = $this->parseClickHouseDate($this->getClickHouseRowValue($row, 'start_date'));
                        $endDate = $this->parseClickHouseDate($this->getClickHouseRowValue($row, 'end_date'));

                        $ikkList->push((object) [
                            'id' => $wpId,
                            'code' => $this->getClickHouseRowValue($row, 'code'),
                            'status' => $this->getClickHouseRowValue($row, 'status'),
                            'site' => $siteName !== '' ? $siteName : 'Lainnya',
                            'company_name' => $companyName !== '' ? $companyName : '-',
                            'job_name' => $jobMap[$jobId] ?? '-',
                            'start_date' => $startDate,
                            'end_date' => $endDate,
                            'location_name' => $this->getClickHouseRowValue($row, 'location_name'),
                            'location_detail_name' => $this->getClickHouseRowValue($row, 'location_detail_name'),
                            'layer_1_name' => $layer1['name'] ?? null,
                            'layer_1_sid' => $layer1['sid'] ?? null,
                            'layer_2_name' => $layer2['name'] ?? null,
                            'layer_2_sid' => $layer2['sid'] ?? null,
                            'layer_3_name' => $layer3['name'] ?? null,
                            'layer_3_sid' => $layer3['sid'] ?? null,
                            'layer_4_name' => $layer4['name'] ?? null,
                            'layer_4_sid' => $layer4['sid'] ?? null,
                        ]);
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error('IKK ClickHouse Error: ' . $e->getMessage());
        }

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $ikkList,
            $totalRecords,
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return view('SistemRoster.ikk.index', [
            'ikks' => $paginator,
            'filterStartDate' => $filterStartDate,
            'filterEndDate' => $filterEndDate,
            'filterSite' => $filterSite,
            'siteList' => $siteList,
            'perPage' => $perPage,
            'clickhouseConnected' => $clickhouseConnected,
        ]);
    }

    public function show(Request $request, string $id): View
    {
        $ikk = null;

        try {
            if (class_exists(ClickHouseService::class)) {
                $clickHouse = app(ClickHouseService::class);
                if (method_exists($clickHouse, 'query') && $clickHouse->isConnected()) {
                    $idEsc = addslashes($id);

                    $sql = "
                        SELECT
                            id, code, status, ra_site_name, m_job_id,
                            start_date, end_date, location_name, location_detail_name, created_at
                        FROM hse_automation.ikk_work_permit
                        WHERE id = '{$idEsc}'
                        LIMIT 1
                    ";
                    $rows = $clickHouse->query($sql);

                    if (!empty($rows)) {
                        $row = $rows[0];
                        $wpId = $this->getClickHouseRowValue($row, 'id');
                        $jobId = $this->getClickHouseRowValue($row, 'm_job_id');

                        $jobName = '-';
                        if ($jobId) {
                            $sqlJob = "SELECT name FROM hse_automation.ikk_m_job WHERE id = '" . addslashes($jobId) . "' LIMIT 1";
                            $jobRows = $clickHouse->query($sqlJob);
                            $jobName = $jobRows[0]['name'] ?? '-';
                        }

                        $sqlEmp = "
                            SELECT layer, employee_name, employee_sid
                            FROM hse_automation.ikk_work_permit_employee
                            WHERE work_permit_id = '{$idEsc}'
                              AND (deleted_at IS NULL OR deleted_at = toDateTime(0))
                        ";
                        $empRows = $clickHouse->query($sqlEmp);
                        $employees = [];
                        foreach ($empRows ?? [] as $er) {
                            $employees[] = [
                                'layer' => $this->getClickHouseRowValue($er, 'layer'),
                                'name' => $this->getClickHouseRowValue($er, 'employee_name'),
                                'sid' => $this->getClickHouseRowValue($er, 'employee_sid'),
                            ];
                        }

                        $layer1 = collect($employees)->where('layer', '1')->first();
                        $layer2 = collect($employees)->where('layer', '2')->first();
                        $layer3 = collect($employees)->where('layer', '3')->first();
                        $layer4 = collect($employees)->where('layer', '4')->first();

                        $siteName = trim($this->getClickHouseRowValue($row, 'ra_site_name') ?? '');

                        $ikk = (object) [
                            'id' => $wpId,
                            'code' => $this->getClickHouseRowValue($row, 'code'),
                            'status' => $this->getClickHouseRowValue($row, 'status'),
                            'site' => $siteName !== '' ? $siteName : 'Lainnya',
                            'job_name' => $jobName,
                            'start_date' => $this->parseClickHouseDate($this->getClickHouseRowValue($row, 'start_date')),
                            'end_date' => $this->parseClickHouseDate($this->getClickHouseRowValue($row, 'end_date')),
                            'location_name' => $this->getClickHouseRowValue($row, 'location_name'),
                            'location_detail_name' => $this->getClickHouseRowValue($row, 'location_detail_name'),
                            'layer_1_name' => $layer1['name'] ?? null,
                            'layer_1_sid' => $layer1['sid'] ?? null,
                            'layer_2_name' => $layer2['name'] ?? null,
                            'layer_2_sid' => $layer2['sid'] ?? null,
                            'layer_3_name' => $layer3['name'] ?? null,
                            'layer_3_sid' => $layer3['sid'] ?? null,
                            'layer_4_name' => $layer4['name'] ?? null,
                            'layer_4_sid' => $layer4['sid'] ?? null,
                            'employees' => $employees,
                        ];
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error('IKK Show ClickHouse Error: ' . $e->getMessage());
        }

        if (!$ikk) {
            abort(404, 'IKK tidak ditemukan');
        }

        return view('SistemRoster.ikk.show', [
            'ikk' => $ikk,
        ]);
    }

    private function getClickHouseRowValue(array $row, string $key)
    {
        if (isset($row[$key])) {
            $val = $row[$key];
            if (is_array($val) && isset($val[0])) {
                return $val[0];
            }
            return $val;
        }
        return null;
    }

    private function parseClickHouseDate($value): ?Carbon
    {
        if ($value === null || $value === '' || $value === '0000-00-00' || $value === '1970-01-01') {
            return null;
        }
        try {
            if (is_numeric($value)) {
                return Carbon::createFromTimestamp((int) $value);
            }
            return Carbon::parse($value);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
