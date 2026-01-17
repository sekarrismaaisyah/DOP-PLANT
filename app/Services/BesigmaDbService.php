<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class BesigmaDbService
{
    private $clickhouse;
    private $database = 'nitip';

    public function __construct()
    {
        $this->clickhouse = new ClickHouseService();
    }

    /**
     * Check if ClickHouse is connected
     */
    public function isConnected()
    {
        return $this->clickhouse->isConnected();
    }

    /**
     * Get unit GPS logs data from ClickHouse
     */
    public function getUnitGpsLogs()
    {
        try {
            // Check if ClickHouse is connected
            if (!$this->isConnected()) {
                Log::info('ClickHouse is not connected. Unit vehicle data will not be available.');
                return [];
            }
            
            // Try to get from unit_gps_latests first (latest data per unit)
            // Menggunakan kolom-kolom yang sesuai dengan struktur tabel nitip.unit_gps_latests
            try {
                $sql = "SELECT 
                    id,
                    unit_id,
                    integration_id,
                    latitude,
                    longitude,
                    course,
                    speed,
                    heading,
                    battery,
                    vehicle_type,
                    vehicle_number,
                    vehicle_name,
                    vendor_name,
                    vendor_type,
                    user_id,
                    is_unit,
                    timezone,
                    created_at,
                    updated_at
                FROM {$this->database}.unit_gps_latests
                WHERE latitude IS NOT NULL 
                    AND longitude IS NOT NULL 
                    AND latitude != 0 
                    AND longitude != 0
                    AND is_unit = true";
                
                $results = $this->queryWithDatabase($sql, $this->database);
                
                return $this->formatUnitData($results);
            } catch (Exception $e) {
                Log::info('unit_gps_latests table not found, trying unit_gps_logs: ' . $e->getMessage());
                
                // Fallback to unit_gps_logs with latest record per unit
                // Menggunakan kolom-kolom yang sesuai dengan struktur tabel nitip.unit_gps_logs
                $sql = "SELECT 
                    id,
                    unit_id,
                    integration_id,
                    latitude,
                    longitude,
                    course,
                    speed,
                    heading,
                    battery,
                    vehicle_type,
                    vehicle_number,
                    vehicle_name,
                    vendor_name,
                    vendor_type,
                    user_id,
                    is_unit,
                    timezone,
                    created_at,
                    updated_at
                FROM {$this->database}.unit_gps_logs
                WHERE latitude IS NOT NULL 
                    AND longitude IS NOT NULL 
                    AND latitude != 0 
                    AND longitude != 0
                    AND is_unit = true
                ORDER BY updated_at DESC
                LIMIT 1000";
                
                $allLogs = $this->queryWithDatabase($sql, $this->database);
                
                // Group by unit_id or integration_id and get latest for each
                $grouped = [];
                foreach ($allLogs as $log) {
                    $key = $log['unit_id'] ?? $log['integration_id'] ?? $log['id'] ?? null;
                    if ($key && !isset($grouped[$key])) {
                        $grouped[$key] = $log;
                    }
                }
                
                return $this->formatUnitData(array_values($grouped));
            }

        } catch (Exception $e) {
            Log::error('Error fetching unit GPS logs from ClickHouse: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get units data from ClickHouse (all units, no filter)
     */
    public function getUnits()
    {
        try {
            // Check if ClickHouse is connected
            if (!$this->isConnected()) {
                Log::info('ClickHouse is not connected. Unit vehicle data will not be available.');
                return [];
            }
            
            // Get ALL units from units table (no coordinate filter)
            // Menggunakan kolom-kolom yang sesuai dengan struktur tabel nitip.units
            $sql = "SELECT 
                id,
                integration_id,
                vendor_type,
                vendor_name,
                vehicle_type,
                vehicle_number,
                vehicle_name,
                last_latitude as latitude,
                last_longitude as longitude,
                last_course as course,
                last_battery as battery,
                timezone,
                created_at,
                updated_at
            FROM {$this->database}.units
            ORDER BY vehicle_name, vehicle_number";
            
            $results = $this->queryWithDatabase($sql, $this->database);
            
            return $this->formatUnitData($results);

        } catch (Exception $e) {
            Log::error('Error fetching units from ClickHouse: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Query ClickHouse with specific database (nitip)
     */
    private function queryWithDatabase($sql, $database)
    {
        // ClickHouse supports database.table syntax, so we can use it directly
        // But we need to ensure the database is set correctly in the URL
        // Since ClickHouseService uses database from config, we'll use database.table in SQL
        // which should work regardless of the default database setting
        return $this->clickhouse->query($sql);
    }

    /**
     * Format unit data to consistent structure
     */
    private function formatUnitData($results)
    {
        $formatted = [];
        foreach ($results as $item) {
            $formatted[] = [
                'id' => $item['id'] ?? null,
                'unit_id' => $item['unit_id'] ?? null,
                'integration_id' => $item['integration_id'] ?? null,
                'latitude' => isset($item['latitude']) && $item['latitude'] !== '' ? (float) $item['latitude'] : 0,
                'longitude' => isset($item['longitude']) && $item['longitude'] !== '' ? (float) $item['longitude'] : 0,
                'course' => isset($item['course']) && $item['course'] !== '' ? (float) $item['course'] : 0,
                'speed' => isset($item['speed']) && $item['speed'] !== '' && $item['speed'] !== null ? (float) $item['speed'] : null,
                'heading' => isset($item['heading']) && $item['heading'] !== '' && $item['heading'] !== null ? (float) $item['heading'] : null,
                'battery' => isset($item['battery']) && $item['battery'] !== '' ? (float) $item['battery'] : 0,
                'vehicle_type' => $item['vehicle_type'] ?? 'Unknown',
                'vehicle_number' => $item['vehicle_number'] ?? 'N/A',
                'vehicle_name' => $item['vehicle_name'] ?? 'N/A',
                'vendor_name' => $item['vendor_name'] ?? 'N/A',
                'vendor_type' => $item['vendor_type'] ?? null,
                'user_id' => $item['user_id'] ?? null,
                'is_unit' => isset($item['is_unit']) ? (bool) $item['is_unit'] : true,
                'timezone' => $item['timezone'] ?? null,
                'created_at' => $item['created_at'] ?? null,
                'updated_at' => $item['updated_at'] ?? null,
            ];
        }
        return $formatted;
    }

    /**
     * Get latest GPS data per unit from unit_gps_logs table
     * Returns the most recent GPS log for each unit
     */
    public function getLatestUnitGpsLogs()
    {
        try {
            // Check if ClickHouse is connected
            if (!$this->isConnected()) {
                Log::info('ClickHouse is not connected. Unit GPS logs will not be available.');
                return [];
            }
            
            // Gunakan today() dari ClickHouse untuk menghindari masalah timezone
            // Ini lebih akurat karena menggunakan timezone server ClickHouse
            // Gunakan range tanggal untuk memastikan data hari ini terambil (termasuk kemungkinan timezone berbeda)
            // Perhatikan: latitude dan longitude adalah String di database, jadi perlu casting ke Float64 untuk perbandingan
            // Gunakan subquery dengan ROW_NUMBER untuk mendapatkan data terbaru per id (menghindari duplikasi)
            // Ini lebih reliable daripada argMax dengan GROUP BY
            $sql = "SELECT 
                toString(id) as id,
                toString(unit_id) as unit_id,
                toString(integration_id) as integration_id,
                toString(latitude) as latitude,
                toString(longitude) as longitude,
                toString(course) as course,
                toString(speed) as speed,
                toString(heading) as heading,
                battery,
                toString(vehicle_type) as vehicle_type,
                toString(vehicle_number) as vehicle_number,
                toString(vehicle_name) as vehicle_name,
                toString(vendor_name) as vendor_name,
                toString(vendor_type) as vendor_type,
                toString(user_id) as user_id,
                is_unit,
                toString(timezone) as timezone,
                toString(created_at) as created_at,
                toString(updated_at) as updated_at
            FROM (
                SELECT 
                    id,
                    unit_id,
                    integration_id,
                    latitude,
                    longitude,
                    course,
                    speed,
                    heading,
                    battery,
                    vehicle_type,
                    vehicle_number,
                    vehicle_name,
                    vendor_name,
                    vendor_type,
                    user_id,
                    is_unit,
                    timezone,
                    created_at,
                    updated_at,
                    ROW_NUMBER() OVER (PARTITION BY id ORDER BY updated_at DESC) as rn
                FROM {$this->database}.unit_gps_latests
                WHERE latitude IS NOT NULL 
                    AND longitude IS NOT NULL 
                    AND latitude != ''
                    AND longitude != ''
                    AND toFloat64OrNull(latitude) IS NOT NULL
                    AND toFloat64OrNull(longitude) IS NOT NULL
                    AND toFloat64OrNull(latitude) != 0 
                    AND toFloat64OrNull(longitude) != 0
                    AND is_unit = true
                    AND toDate(updated_at) >= today() - INTERVAL 1 DAY
                    AND toDate(updated_at) <= today() + INTERVAL 1 DAY
            ) ranked
            WHERE rn = 1";
            
            $results = $this->queryWithDatabase($sql, $this->database);
            
            // Log raw results untuk debugging
            Log::info('Unit GPS latests raw query results', [
                'raw_count' => count($results),
                'sample_row' => !empty($results) ? [
                    'id' => $results[0]['id'] ?? 'N/A',
                    'updated_at' => $results[0]['updated_at'] ?? 'N/A',
                    'latitude' => $results[0]['latitude'] ?? 'N/A',
                    'longitude' => $results[0]['longitude'] ?? 'N/A',
                    'vehicle_number' => $results[0]['vehicle_number'] ?? 'N/A',
                ] : 'No data'
            ]);
            
            // Filter di PHP untuk memastikan hanya data hari ini (untuk menghindari masalah timezone)
            // Dan deduplikasi berdasarkan id (ambil yang terbaru berdasarkan updated_at)
            $today = date('Y-m-d');
            $filteredResults = [];
            $idMap = []; // Map untuk menyimpan data terbaru per id
            $skippedCount = 0;
            $skippedReasons = [];
            
            foreach ($results as $row) {
                $id = $row['id'] ?? null;
                $updatedAt = $row['updated_at'] ?? null;
                
                // Parse tanggal dari updated_at - handle berbagai format
                $updatedDate = null;
                if (!empty($updatedAt)) {
                    try {
                        // Coba parse berbagai format tanggal
                        if (is_numeric($updatedAt)) {
                            // Jika timestamp
                            $updatedDate = date('Y-m-d', $updatedAt);
                        } else {
                            // Jika string datetime
                            $timestamp = strtotime($updatedAt);
                            if ($timestamp !== false) {
                                $updatedDate = date('Y-m-d', $timestamp);
                            } else {
                                // Coba parse format DateTime64(3) dari ClickHouse
                                $updatedDate = substr($updatedAt, 0, 10); // Ambil YYYY-MM-DD
                            }
                        }
                    } catch (\Exception $e) {
                        Log::warning('Error parsing updated_at', [
                            'updated_at' => $updatedAt,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
                
                // Jika tanggal sesuai dengan hari ini
                if ($updatedDate === $today) {
                    if ($id) {
                        // Jika id belum ada atau updated_at lebih baru, simpan/update
                        if (!isset($idMap[$id])) {
                            $idMap[$id] = $row;
                        } else {
                            // Bandingkan updated_at untuk ambil yang terbaru
                            $existingUpdatedAt = $idMap[$id]['updated_at'] ?? null;
                            try {
                                $currentTimestamp = is_numeric($updatedAt) ? $updatedAt : strtotime($updatedAt);
                                $existingTimestamp = is_numeric($existingUpdatedAt) ? $existingUpdatedAt : strtotime($existingUpdatedAt);
                                
                                if ($currentTimestamp !== false && $existingTimestamp !== false && 
                                    $currentTimestamp > $existingTimestamp) {
                                    $idMap[$id] = $row;
                                }
                            } catch (\Exception $e) {
                                // Jika error, tetap gunakan yang baru
                                $idMap[$id] = $row;
                            }
                        }
                    } else {
                        // Jika tidak ada id, tambahkan langsung
                        $filteredResults[] = $row;
                    }
                } else {
                    $skippedCount++;
                    if ($skippedCount <= 5) {
                        $skippedReasons[] = [
                            'id' => $id,
                            'updated_at' => $updatedAt,
                            'parsed_date' => $updatedDate,
                            'today' => $today
                        ];
                    }
                }
            }
            
            // Convert map ke array
            $filteredResults = array_merge($filteredResults, array_values($idMap));
            
            // Log untuk debugging
            Log::info('Unit GPS latests query executed', [
                'raw_count' => count($results),
                'filtered_count' => count($filteredResults),
                'skipped_count' => $skippedCount,
                'today' => $today,
                'skipped_samples' => $skippedReasons,
                'sample_updated_at' => !empty($filteredResults) ? ($filteredResults[0]['updated_at'] ?? 'N/A') : 'No data',
                'sample_vehicle_number' => !empty($filteredResults) ? ($filteredResults[0]['vehicle_number'] ?? 'N/A') : 'No data',
                'sample_latitude' => !empty($filteredResults) ? ($filteredResults[0]['latitude'] ?? 'N/A') : 'No data',
                'sample_longitude' => !empty($filteredResults) ? ($filteredResults[0]['longitude'] ?? 'N/A') : 'No data'
            ]);
            
            return $this->formatUnitData($filteredResults);
            
        } catch (Exception $e) {
            Log::error('Error fetching unit GPS from unit_gps_latests: ' . $e->getMessage(), [
                'exception' => $e->getTraceAsString()
            ]);
            // Return empty array jika error, tidak ada fallback
            return [];
        }
    }

    /**
     * Get unit GPS logs for movement tracking (from unit_gps_logs table)
     * Can filter by unit_id and limit results for history tracking
     */
    public function getUnitGpsLogsForTracking($unitId = null, $limit = 1000)
    {
        try {
            // Check if ClickHouse is connected
            if (!$this->isConnected()) {
                Log::info('ClickHouse is not connected. Unit GPS logs will not be available.');
                return [];
            }
            
            $whereClause = "WHERE latitude IS NOT NULL 
                AND longitude IS NOT NULL 
                AND latitude != 0 
                AND longitude != 0
                AND is_unit = true";
            
            if ($unitId) {
                $whereClause .= " AND (unit_id = '{$unitId}' OR integration_id = '{$unitId}')";
            }
            
            $sql = "SELECT 
                id,
                unit_id,
                integration_id,
                latitude,
                longitude,
                course,
                speed,
                heading,
                battery,
                vehicle_type,
                vehicle_number,
                vehicle_name,
                vendor_name,
                vendor_type,
                user_id,
                is_unit,
                timezone,
                updated_at,
                created_at
            FROM {$this->database}.unit_gps_logs
            {$whereClause}
            ORDER BY updated_at DESC
            LIMIT {$limit}";
            
            $results = $this->queryWithDatabase($sql, $this->database);
            
            return $this->formatUnitData($results);

        } catch (Exception $e) {
            Log::error('Error fetching unit GPS logs from ClickHouse: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get combined unit data - dari MySQL tabel unit_gps_latests
     * Mengambil 100 unit terbaru
     */
    public function getCombinedUnitData()
    {
        try {
            // Ambil data dari MySQL tabel unit_gps_latests
            // Filter: is_unit = true, latitude dan longitude tidak null, ambil 100 terbaru
            $gpsLogs = DB::table('unit_gps_latests')
                ->where('is_unit', true)
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->where('latitude', '!=', '')
                ->where('longitude', '!=', '')
                ->whereRaw('CAST(latitude AS DECIMAL(10,8)) IS NOT NULL')
                ->whereRaw('CAST(longitude AS DECIMAL(10,8)) IS NOT NULL')
                ->whereRaw('CAST(latitude AS DECIMAL(10,8)) != 0')
                ->whereRaw('CAST(longitude AS DECIMAL(10,8)) != 0')
                ->orderBy('updated_at', 'desc')
                ->limit(100)
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id ?? null,
                        'unit_id' => $item->unit_id ?? null,
                        'integration_id' => $item->integration_id ?? null,
                        'latitude' => $item->latitude ?? null,
                        'longitude' => $item->longitude ?? null,
                        'course' => $item->course ?? 0,
                        'speed' => $item->speed ?? null,
                        'heading' => $item->heading ?? null,
                        'battery' => $item->battery ?? 0,
                        'vehicle_type' => $item->vehicle_type ?? 'Unknown',
                        'vehicle_number' => $item->vehicle_number ?? 'N/A',
                        'vehicle_name' => $item->vehicle_name ?? 'N/A',
                        'vendor_name' => $item->vendor_name ?? 'N/A',
                        'vendor_type' => $item->vendor_type ?? null,
                        'user_id' => $item->user_id ?? null,
                        'is_unit' => $item->is_unit ?? true,
                        'timezone' => $item->timezone ?? null,
                        'created_at' => $item->created_at ?? null,
                        'updated_at' => $item->updated_at ?? null,
                    ];
                })
                ->toArray();
            
            // Logging detail untuk debugging
            Log::info('GPS logs fetched from MySQL unit_gps_latests', [
                'count' => count($gpsLogs),
                'sample_data' => !empty($gpsLogs) ? [
                    'id' => $gpsLogs[0]['id'] ?? null,
                    'vehicle_number' => $gpsLogs[0]['vehicle_number'] ?? null,
                    'latitude' => $gpsLogs[0]['latitude'] ?? null,
                    'longitude' => $gpsLogs[0]['longitude'] ?? null,
                    'updated_at' => $gpsLogs[0]['updated_at'] ?? null,
                    'is_unit' => $gpsLogs[0]['is_unit'] ?? null,
                ] : 'No data'
            ]);
            
            // Jika tidak ada data GPS, return empty
            if (empty($gpsLogs)) {
                Log::warning('No GPS logs found in MySQL unit_gps_latests');
                return [];
            }
            
            // Get users data for integration - only fetch users that exist in GPS logs to avoid timeout
            $userIds = [];
            foreach ($gpsLogs as $gpsLog) {
                if (!empty($gpsLog['user_id'])) {
                    $userIds[] = $gpsLog['user_id'];
                }
            }
            $users = !empty($userIds) ? $this->getUsersByIds($userIds) : [];
            
            // Create a map of users by id
            $usersMap = [];
            foreach ($users as $user) {
                if (!empty($user['id'])) {
                    $usersMap[$user['id']] = $user;
                }
            }
            
            // Langsung gunakan data dari GPS latests sebagai hasil akhir
            $combined = [];
            foreach ($gpsLogs as $gpsLog) {
                // Get user data if user_id is available
                $userData = null;
                if (!empty($gpsLog['user_id']) && isset($usersMap[$gpsLog['user_id']])) {
                    $userData = $usersMap[$gpsLog['user_id']];
                }
                
                // Convert latitude and longitude to float
                $latitude = (!empty($gpsLog['latitude']) && is_numeric($gpsLog['latitude'])) 
                    ? (float) $gpsLog['latitude'] 
                    : 0;
                $longitude = (!empty($gpsLog['longitude']) && is_numeric($gpsLog['longitude'])) 
                    ? (float) $gpsLog['longitude'] 
                    : 0;
                
                // Build combined data langsung dari GPS latests
                $combined[] = [
                    'id' => $gpsLog['id'] ?? null,
                    'unit_id' => $gpsLog['unit_id'] ?? null,
                    'integration_id' => $gpsLog['integration_id'] ?? null,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'course' => !empty($gpsLog['course']) && is_numeric($gpsLog['course']) 
                        ? (float) $gpsLog['course'] 
                        : 0,
                    'speed' => !empty($gpsLog['speed']) && is_numeric($gpsLog['speed']) 
                        ? (float) $gpsLog['speed'] 
                        : null,
                    'heading' => !empty($gpsLog['heading']) && is_numeric($gpsLog['heading']) 
                        ? (float) $gpsLog['heading'] 
                        : null,
                    'battery' => !empty($gpsLog['battery']) && is_numeric($gpsLog['battery']) 
                        ? (float) $gpsLog['battery'] 
                        : 0,
                    'vehicle_type' => $gpsLog['vehicle_type'] ?? 'Unknown',
                    'vehicle_number' => $gpsLog['vehicle_number'] ?? 'N/A',
                    'vehicle_name' => $gpsLog['vehicle_name'] ?? 'N/A',
                    'vendor_name' => $gpsLog['vendor_name'] ?? 'N/A',
                    'vendor_type' => $gpsLog['vendor_type'] ?? null,
                    'timezone' => $gpsLog['timezone'] ?? null,
                    'updated_at' => $gpsLog['updated_at'] ?? null,
                    'created_at' => $gpsLog['created_at'] ?? null,
                    // User data from users table
                    'user_id' => $gpsLog['user_id'] ?? null,
                    'user' => $userData ? [
                        'id' => $userData['id'],
                        'npk' => $userData['npk'],
                        'fullname' => $userData['fullname'],
                        'sid_code' => $userData['sid_code'],
                        'email' => $userData['email'],
                        'phone' => $userData['phone'],
                        'employee_id' => $userData['employee_id'],
                        'functional_position' => $userData['functional_position'],
                        'structural_position' => $userData['structural_position'],
                        'department_name' => $userData['department_name'],
                        'division_name' => $userData['division_name'],
                        'site_assignment' => $userData['site_assignment'],
                        'dedicated_site' => $userData['dedicated_site'],
                    ] : null,
                ];
            }

            // Log summary for debugging
            Log::info('Combined unit data from MySQL unit_gps_latests', [
                'total_units' => count($combined),
                'units_with_users' => count(array_filter($combined, function($unit) {
                    return !empty($unit['user']);
                })),
                'sample_units' => !empty($combined) ? array_slice(array_map(function($unit) {
                    return [
                        'id' => $unit['id'] ?? null,
                        'vehicle_number' => $unit['vehicle_number'] ?? null,
                        'latitude' => $unit['latitude'] ?? null,
                        'longitude' => $unit['longitude'] ?? null,
                        'updated_at' => $unit['updated_at'] ?? null,
                    ];
                }, $combined), 0, 3) : []
            ]);

            return $combined;
            
        } catch (Exception $e) {
            Log::error('Error fetching unit data from MySQL: ' . $e->getMessage(), [
                'exception' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Get users data from ClickHouse by IDs (optimized to avoid timeout)
     */
    public function getUsersByIds(array $userIds)
    {
        try {
            // Check if ClickHouse is connected
            if (!$this->isConnected()) {
                Log::info('ClickHouse is not connected. Users data will not be available.');
                return [];
            }
            
            if (empty($userIds)) {
                return [];
            }
            
            // Remove duplicates and prepare IDs for query
            $uniqueIds = array_unique($userIds);
            $idsList = implode(',', array_map(function($id) {
                return "'" . addslashes($id) . "'";
            }, $uniqueIds));
            
            $sql = "SELECT 
                id,
                npk,
                fullname,
                sid_code,
                email,
                phone,
                employee_id,
                functional_position,
                structural_position,
                department_name,
                division_name,
                site_assignment,
                dedicated_site,
                company_id
            FROM {$this->database}.users
            WHERE id IN ({$idsList})
                AND is_active = true
                AND is_deleted = false";
            
            $results = $this->queryWithDatabase($sql, $this->database);
            
            $formatted = [];
            foreach ($results as $item) {
                $formatted[] = [
                    'id' => $item['id'] ?? null,
                    'npk' => $item['npk'] ?? null,
                    'fullname' => $item['fullname'] ?? 'N/A',
                    'sid_code' => $item['sid_code'] ?? null,
                    'email' => $item['email'] ?? null,
                    'phone' => $item['phone'] ?? null,
                    'employee_id' => $item['employee_id'] ?? null,
                    'functional_position' => $item['functional_position'] ?? null,
                    'structural_position' => $item['structural_position'] ?? null,
                    'department_name' => $item['department_name'] ?? null,
                    'division_name' => $item['division_name'] ?? null,
                    'site_assignment' => $item['site_assignment'] ?? null,
                    'dedicated_site' => $item['dedicated_site'] ?? null,
                    'company_id' => $item['company_id'] ?? null,
                ];
            }
            
            return $formatted;
        } catch (Exception $e) {
            Log::error('Error fetching users from ClickHouse: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get all users data from ClickHouse (use with caution - may timeout)
     */
    public function getUsers()
    {
        try {
            // Check if ClickHouse is connected
            if (!$this->isConnected()) {
                Log::info('ClickHouse is not connected. Users data will not be available.');
                return [];
            }
            
            $sql = "SELECT 
                id,
                npk,
                fullname,
                sid_code,
                email,
                phone,
                employee_id,
                functional_position,
                structural_position,
                department_name,
                division_name,
                site_assignment,
                dedicated_site,
                company_id,
                is_active
            FROM {$this->database}.users
            WHERE is_active = true
                AND is_deleted = false
            LIMIT 10000";
            
            $results = $this->queryWithDatabase($sql, $this->database);
            
            $formatted = [];
            foreach ($results as $item) {
                $formatted[] = [
                    'id' => $item['id'] ?? null,
                    'npk' => $item['npk'] ?? null,
                    'fullname' => $item['fullname'] ?? 'N/A',
                    'sid_code' => $item['sid_code'] ?? null,
                    'email' => $item['email'] ?? null,
                    'phone' => $item['phone'] ?? null,
                    'employee_id' => $item['employee_id'] ?? null,
                    'functional_position' => $item['functional_position'] ?? null,
                    'structural_position' => $item['structural_position'] ?? null,
                    'department_name' => $item['department_name'] ?? null,
                    'division_name' => $item['division_name'] ?? null,
                    'site_assignment' => $item['site_assignment'] ?? null,
                    'dedicated_site' => $item['dedicated_site'] ?? null,
                    'company_id' => $item['company_id'] ?? null,
                ];
            }
            
            return $formatted;
        } catch (Exception $e) {
            Log::error('Error fetching users from ClickHouse: ' . $e->getMessage());
            return [];
        }
    }
}

