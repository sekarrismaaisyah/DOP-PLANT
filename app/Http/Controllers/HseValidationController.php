<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\QwenAIService;
use App\Models\HseAiValidation;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Exception;

class HseValidationController extends Controller
{
    protected $qwenService;

    public function __construct(QwenAIService $qwenService)
    {
        $this->qwenService = $qwenService;
    }

    /**
     * Tampilkan halaman validasi (tidak perlu upload, langsung ambil dari ClickHouse)
     */
    public function index()
    {
        // Cek apakah ada data hari ini yang sudah divalidasi
        $today = Carbon::now()->format('Y-m-d');
        $validatedCount = HseAiValidation::where('validation_date', $today)->count();
        
        return view('hse-validation.index', [
            'validated_count' => $validatedCount,
            'validation_date' => $today
        ]);
    }

    /**
     * Proses validasi data dari ClickHouse (hari ini)
     */
    public function process(Request $request)
    {
        try {
            // Ambil data dari ClickHouse untuk hari ini
            $today = Carbon::now()->format('Y-m-d');
            $clickHouseData = $this->getTodayDataFromClickHouse($today);
            
            if (empty($clickHouseData)) {
                return back()->withErrors(['message' => 'Tidak ada data untuk divalidasi hari ini.']);
            }

            // Hitung total baris yang akan diproses
            $totalRows = count($clickHouseData);

            // Generate unique ID untuk proses ini
            $processId = uniqid('hse_validation_', true);
            
            // Simpan data awal ke cache
            Cache::put("hse_process_{$processId}", [
                'total_rows' => $totalRows,
                'processed' => 0,
                'current_row' => 0,
                'status' => 'processing',
                'results' => [],
                'clickhouse_data' => $clickHouseData,
                'validation_date' => $today,
            ], now()->addHours(2));

            // Redirect ke halaman loading dengan process ID
            return redirect()->route('hse-validation.loading', ['processId' => $processId]);

        } catch (Exception $e) {
            Log::error('Error processing ClickHouse data', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['message' => 'Error processing data: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Ambil data hari ini dari ClickHouse
     */
    private function getTodayDataFromClickHouse($today)
    {
        try {
            $host = '10.10.10.38';
            $port = 8123;
            $protocol = 'http';
            $baseUrl = $protocol . '://' . $host . ':' . $port;
            $username = 'default';
            $password = 'Zxcdsaqwe321:;';
            $database = 'hse_automation';
            $timeout = 60;

            $sql = "
                SELECT 
                    toString(id) as task_number,
                    ifNull(toString(jenis_laporan), 'INSPEKSI_HAZARD') as jenis_laporan,
                    ifNull(toString(deskripsi), '') as aktivitas_pekerjaan,
                    ifNull(toString(nama_lokasi), '') as lokasi,
                    ifNull(toString(nama_detail_lokasi), '') as detail_lokasi,
                    ifNull(toString(deskripsi), '') as keterangan,
                    ifNull(toString(tanggal_pembuatan), toString(bedraft_date)) as tanggal_pelaporan,
                    ifNull(toString(perusahaan_pelapor), '') as perusahaan_pelapor,
                    ifNull(toString(nama_pelapor), '') as pelapor,
                    ifNull(toString(sid_pelapor), '') as sid_pelapor,
                    ifNull(toString(jabatan_fungsional_pelapor), '') as jabatan_fungsional_pelapor,
                    ifNull(toString(departemen_pelapor), '') as departemen_pelapor,
                    ifNull(toString(nama_pic), '') as pic,
                    ifNull(toString(sid_pic), '') as sid_pic,
                    ifNull(toString(jabatan_fungsional_pic), '') as jabatan_fungsional_pic,
                    ifNull(toString(perusahaan_pic), '') as perusahaan_pic,
                    ifNull(toString(departemen_pic), '') as departemen_pic,
                    ifNull(toString(url_photo), '') as uri_foto,
                    ifNull(toString(name_tools_observation), '') as tools_pengawasan,
                    ifNull(toString(tindakan), '') as catatan_tindakan,
                    ifNull(toString(id_pelapor), '') as nik_pelapor,
                    ifNull(toString(nama_pelapor), '') as nama_pelapor,
                    ifNull(toString(perusahaan_pelapor), '') as nama_perusahaan_pelapor_karyawan,
                    ifNull(toString(jabatan_fungsional_pelapor), '') as jabatan_fungsional_karyawan_pelapor,
                    ifNull(toString(latitude), '') as latitude,
                    ifNull(toString(longitude), '') as longitude,
                    ifNull(toString(nama_site), '') as site,
                    ifNull(toString(lokasi_detail), '') as keterangan_lokasi,
                    ifNull(toString(jam), '') as jam,
                    ifNull(toString(menit), '') as menit,
                    ifNull(toString(nama_lokasi), '') as nama_lokasi,
                    ifNull(toString(nama_detail_lokasi), '') as nama_detail_lokasi
                FROM aaj_car_all_year_from_dav
                WHERE (
                    (tanggal_pembuatan IS NOT NULL 
                        AND toDate(toTimeZone(tanggal_pembuatan, 'Asia/Makassar')) = toDate('{$today}'))
                    OR (bedraft_date IS NOT NULL 
                        AND toDate(toTimeZone(bedraft_date, 'Asia/Makassar')) = toDate('{$today}'))
                )
                ORDER BY 
                    CASE 
                        WHEN tanggal_pembuatan IS NOT NULL THEN toDateTime(tanggal_pembuatan)
                        WHEN bedraft_date IS NOT NULL THEN toDateTime(bedraft_date)
                        ELSE toDateTime('1970-01-01 00:00:00')
                    END DESC
                LIMIT 12500
            ";

            $url = $baseUrl . '/?database=' . urlencode($database) . '&default_format=JSON';
            
            $httpClient = Http::timeout($timeout)
                ->withBasicAuth($username, $password)
                ->withBody($sql, 'text/plain');
            
            $response = $httpClient->post($url);

            if (!$response->successful()) {
                Log::error('ClickHouse query failed', [
                    'status' => $response->status(),
                    'body' => substr($response->body(), 0, 500)
                ]);
                return [];
            }

            $body = $response->body();
            $result = json_decode($body, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                // Try to parse as JSON lines format
                $lines = explode("\n", trim($body));
                $data = [];
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (!empty($line)) {
                        $decoded = json_decode($line, true);
                        if ($decoded !== null && json_last_error() === JSON_ERROR_NONE) {
                            $data[] = $decoded;
                        }
                    }
                }
                return $data;
            }
            
            if (is_array($result)) {
                if (isset($result['data'])) {
                    return $result['data'];
                } elseif (!empty($result) && isset($result[0])) {
                    return $result;
                }
            }
            
            return [];
        } catch (Exception $e) {
            Log::error('Error getting data from ClickHouse', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Halaman loading dengan progress bar
     */
    public function loading($processId)
    {
        return view('hse-validation.loading', compact('processId'));
    }

    /**
     * Endpoint untuk mendapatkan progress
     */
    public function getProgress($processId)
    {
        $processData = Cache::get("hse_process_{$processId}");
        
        if (!$processData) {
            Log::warning('Progress not found', ['processId' => $processId]);
            return response()->json([
                'status' => 'not_found',
                'progress' => 0,
                'message' => 'Process not found'
            ]);
        }

        $progress = $processData['total_rows'] > 0 
            ? round(($processData['processed'] / $processData['total_rows']) * 100) 
            : 0;

        $response = [
            'status' => $processData['status'],
            'progress' => min(100, $progress),
            'processed' => $processData['processed'],
            'total' => $processData['total_rows'],
            'current_row' => $processData['current_row'],
        ];

        // Jika completed, pastikan progress 100%
        if ($processData['status'] === 'completed') {
            $response['progress'] = 100;
            $response['redirect'] = route('hse-validation.results', ['processId' => $processId]);
        }

        return response()->json($response);
    }

    /**
     * Proses validasi secara async
     */
    public function processAsync($processId)
    {
        $processData = Cache::get("hse_process_{$processId}");
        
        if (!$processData) {
            return response()->json(['error' => 'Process not found'], 404);
        }

        // Jika sudah selesai, return hasil
        if ($processData['status'] === 'completed') {
            return response()->json([
                'status' => 'completed',
                'redirect' => route('hse-validation.results', ['processId' => $processId])
            ]);
        }

        // Proses baris berikutnya
        $clickHouseData = $processData['clickhouse_data'] ?? [];
        $results = $processData['results'] ?? [];
        $processed = $processData['processed'] ?? 0;
        $currentRow = $processData['current_row'] ?? 0;
        $validationDate = $processData['validation_date'] ?? Carbon::now()->format('Y-m-d');

        // Proses beberapa baris sekaligus (batch processing)
        $batchSize = 1; // Proses 1 baris per request untuk update progress lebih smooth
        $batchCount = 0;

        // Cek apakah sudah mencapai akhir baris
        $totalRowsCount = count($clickHouseData);
        $isCompleted = ($currentRow) >= $totalRowsCount;

        // Mulai dari baris berikutnya
        if (!$isCompleted) {
            for ($i = $currentRow; $i < $totalRowsCount && $batchCount < $batchSize; $i++) {
                $row = $clickHouseData[$i];
                
                // Skip baris kosong
                $deskripsi = $row['keterangan'] ?? $row['aktivitas_pekerjaan'] ?? '';
                if (empty($deskripsi)) {
                    $currentRow = $i + 1;
                    if ($i >= $totalRowsCount - 1) {
                        $isCompleted = true;
                    }
                    continue;
                }

                $urlPhoto = $row['uri_foto'] ?? '';

                try {
                    // Validasi menggunakan AI
                    $validationResult = $this->qwenService->validateFinding($deskripsi, $urlPhoto);

                    // Parse tanggal_pelaporan
                    $tanggalPelaporan = null;
                    if (!empty($row['tanggal_pelaporan'])) {
                        try {
                            $tanggalPelaporan = Carbon::parse($row['tanggal_pelaporan'])->format('Y-m-d H:i:s');
                        } catch (\Exception $e) {
                            $tanggalPelaporan = null;
                        }
                    }

                    // Parse latitude dan longitude
                    $latitude = null;
                    $longitude = null;
                    if (!empty($row['latitude']) && is_numeric($row['latitude'])) {
                        $latitude = floatval($row['latitude']);
                    }
                    if (!empty($row['longitude']) && is_numeric($row['longitude'])) {
                        $longitude = floatval($row['longitude']);
                    }

                    // Simpan ke database
                    HseAiValidation::create([
                        // Original data from ClickHouse
                        'task_number' => $row['task_number'] ?? null,
                        'jenis_laporan' => $row['jenis_laporan'] ?? null,
                        'aktivitas_pekerjaan' => $row['aktivitas_pekerjaan'] ?? null,
                        'lokasi' => $row['lokasi'] ?? null,
                        'detail_lokasi' => $row['detail_lokasi'] ?? null,
                        'keterangan' => $deskripsi,
                        'tanggal_pelaporan' => $tanggalPelaporan,
                        'perusahaan_pelapor' => $row['perusahaan_pelapor'] ?? null,
                        'pelapor' => $row['pelapor'] ?? null,
                        'sid_pelapor' => $row['sid_pelapor'] ?? null,
                        'jabatan_fungsional_pelapor' => $row['jabatan_fungsional_pelapor'] ?? null,
                        'departemen_pelapor' => $row['departemen_pelapor'] ?? null,
                        'pic' => $row['pic'] ?? null,
                        'sid_pic' => $row['sid_pic'] ?? null,
                        'jabatan_fungsional_pic' => $row['jabatan_fungsional_pic'] ?? null,
                        'perusahaan_pic' => $row['perusahaan_pic'] ?? null,
                        'departemen_pic' => $row['departemen_pic'] ?? null,
                        'uri_foto' => $urlPhoto,
                        'tools_pengawasan' => $row['tools_pengawasan'] ?? null,
                        'catatan_tindakan' => $row['catatan_tindakan'] ?? null,
                        'nik_pelapor' => $row['nik_pelapor'] ?? null,
                        'nama_pelapor' => $row['nama_pelapor'] ?? null,
                        'nama_perusahaan_pelapor_karyawan' => $row['nama_perusahaan_pelapor_karyawan'] ?? null,
                        'jabatan_fungsional_karyawan_pelapor' => $row['jabatan_fungsional_karyawan_pelapor'] ?? null,
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                        'site' => $row['site'] ?? null,
                        'keterangan_lokasi' => $row['keterangan_lokasi'] ?? null,
                        'jam' => $row['jam'] ?? null,
                        'menit' => $row['menit'] ?? null,
                        'nama_lokasi' => $row['nama_lokasi'] ?? null,
                        'nama_detail_lokasi' => $row['nama_detail_lokasi'] ?? null,
                        
                        // AI Validation Results
                        'ai_match_found' => $validationResult['match_found'] ?? false,
                        'ai_main_category' => $validationResult['main_category'] ?? null,
                        'ai_sub_category' => $validationResult['sub_category'] ?? null,
                        'ai_tbc' => $validationResult['concern_level']['TBC'] ?? false,
                        'ai_pspp' => $validationResult['concern_level']['PSPP'] ?? false,
                        'ai_gr' => $validationResult['concern_level']['GR'] ?? false,
                        'ai_incident' => $validationResult['concern_level']['Incident'] ?? false,
                        'ai_justification' => $validationResult['justification'] ?? null,
                        'ai_confidence_score' => $validationResult['confidence_score'] ?? null,
                        
                        // Metadata
                        'validation_date' => $validationDate,
                        'validated_by' => Auth::id(),
                    ]);

                    // Tambahkan hasil validasi ke results untuk ditampilkan
                    $rowData = [
                        'row_number' => $i + 1,
                        'task_number' => $row['task_number'] ?? null,
                        'deskripsi' => $deskripsi,
                        'url_photo' => $urlPhoto,
                        'validasi_main_category' => $validationResult['main_category'] ?? null,
                        'validasi_sub_category' => $validationResult['sub_category'] ?? null,
                        'validasi_TBC' => $validationResult['concern_level']['TBC'] ?? false,
                        'validasi_PSPP' => $validationResult['concern_level']['PSPP'] ?? false,
                        'validasi_GR' => $validationResult['concern_level']['GR'] ?? false,
                        'validasi_Incident' => $validationResult['concern_level']['Incident'] ?? false,
                        'validasi_justifikasi' => $validationResult['justification'] ?? '',
                        'validasi_confidence' => $validationResult['confidence_score'] ?? 0.0,
                        'match_found' => $validationResult['match_found'] ?? false,
                    ];

                    $results[] = $rowData;
                    $processed++;
                    $currentRow = $i + 1;
                    $batchCount++;

                    // Cek apakah ini baris terakhir
                    if ($i >= $totalRowsCount - 1) {
                        $isCompleted = true;
                    }

                } catch (Exception $e) {
                    // Jika error, skip baris ini dan lanjut
                    Log::error('Error processing row', [
                        'row' => $i,
                        'task_number' => $row['task_number'] ?? null,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    $currentRow = $i + 1;
                    // Cek apakah ini baris terakhir
                    if ($i >= $totalRowsCount - 1) {
                        $isCompleted = true;
                    }
                    continue;
                }
            }
        }

        // Update progress - pastikan completed jika sudah mencapai akhir
        if ($currentRow >= $totalRowsCount) {
            $isCompleted = true;
        }
        
        Cache::put("hse_process_{$processId}", [
            'total_rows' => $processData['total_rows'],
            'processed' => $processed,
            'current_row' => $currentRow,
            'status' => $isCompleted ? 'completed' : 'processing',
            'results' => $results,
            'clickhouse_data' => $clickHouseData,
            'validation_date' => $validationDate,
        ], now()->addHours(2));

        // Jika selesai, simpan ke session
        if ($isCompleted) {
            session(['hse_validation_results' => $results]);
            Log::info('HSE Validation completed', [
                'processId' => $processId,
                'total_results' => count($results),
                'processed' => $processed,
                'validation_date' => $validationDate
            ]);
        }

        $progress = $processData['total_rows'] > 0 
            ? round(($processed / $processData['total_rows']) * 100) 
            : 0;

        return response()->json([
            'status' => $isCompleted ? 'completed' : 'processing',
            'progress' => min(100, $progress),
            'processed' => $processed,
            'total' => $processData['total_rows'],
            'redirect' => $isCompleted ? route('hse-validation.results', ['processId' => $processId]) : null
        ]);
    }

    /**
     * Tampilkan hasil validasi
     */
    public function results(Request $request, $processId = null)
    {
        // Cek apakah ini request AJAX atau request normal
        // Jika AJAX, return JSON untuk menghindari reload loop
        if ($request->ajax() || $request->wantsJson()) {
            $processData = Cache::get("hse_process_{$processId}");
            if ($processData && $processData['status'] === 'completed') {
                return response()->json([
                    'status' => 'completed',
                    'results_count' => count($processData['results'] ?? [])
                ]);
            }
            return response()->json(['status' => 'processing']);
        }

        $results = [];
        $headers = [];

        // Jika ada processId, ambil dari cache
        if ($processId) {
            $processData = Cache::get("hse_process_{$processId}");
            if ($processData) {
                if ($processData['status'] === 'completed' && !empty($processData['results'])) {
                    $results = $processData['results'];
                    $headers = $processData['headers'];
                    Log::info('Results loaded from cache', [
                        'processId' => $processId,
                        'results_count' => count($results)
                    ]);
                } else {
                    // Jika belum completed, coba ambil dari session
                    $results = session('hse_validation_results', []);
                    $headers = session('hse_validation_headers', []);
                    Log::warning('Process not completed, trying session', [
                        'processId' => $processId,
                        'status' => $processData['status'] ?? 'unknown',
                        'session_results_count' => count($results)
                    ]);
                }
            } else {
                // Cache tidak ditemukan, coba dari session
                $results = session('hse_validation_results', []);
                $headers = session('hse_validation_headers', []);
                Log::warning('Process cache not found, trying session', [
                    'processId' => $processId,
                    'session_results_count' => count($results)
                ]);
            }
        } else {
            // Tidak ada processId, ambil dari session
            $results = session('hse_validation_results', []);
            $headers = session('hse_validation_headers', []);
        }

        if (empty($results)) {
            Log::warning('No results found', [
                'processId' => $processId,
                'has_session_results' => !empty(session('hse_validation_results'))
            ]);
            return redirect()->route('hse-validation.index')
                ->withErrors(['message' => 'Tidak ada hasil validasi. Silakan upload file terlebih dahulu.']);
        }

        return view('hse-validation.results', compact('results', 'headers'));
    }

    /**
     * Proxy untuk gambar (mengatasi CORS dan extract gambar dari halaman)
     */
    public function imageProxy(Request $request)
    {
        $url = $request->get('url');
        
        if (!$url) {
            abort(400, 'URL parameter is required');
        }

        try {
            // Jika URL adalah halaman HTML (beraucoal.co.id), coba extract gambar
            if (strpos($url, 'hseautomation.beraucoal.co.id/report/photoCar') !== false) {
                $response = Http::timeout(10)->get($url);
                
                if ($response->successful()) {
                    $html = $response->body();
                    
                    // Cek apakah ada "No Photo" di halaman
                    if (stripos($html, 'No Photo') !== false && stripos($html, 'Foto Temuan') === false) {
                        Log::info('No photo found on page', ['url' => $url]);
                        // Return placeholder image
                        return $this->generatePlaceholderImage('No Photo Available');
                    }
                    
                    // Coba berbagai pattern untuk extract URL gambar dari HTML
                    // Prioritas: cari gambar di section "Foto Temuan" terlebih dahulu
                    $patterns = [
                        // Cari gambar di dalam section Foto Temuan (prioritas tertinggi)
                        '/Foto Temuan[^>]*>.*?<img[^>]+src=["\']([^"\']+)["\']/is',
                        '/Foto Temuan[^>]*>.*?<img[^>]+data-src=["\']([^"\']+)["\']/is',
                        // Cari semua gambar dengan ekstensi gambar
                        '/<img[^>]+src=["\']([^"\']+\.(jpg|jpeg|png|gif|webp|bmp)[^"\']*)["\']/i',
                        '/<img[^>]+src=["\']([^"\']+)["\']/i',
                        // Cari background-image
                        '/background-image:\s*url\(["\']?([^"\']+)["\']?\)/i',
                        // Cari data-src (lazy loading)
                        '/<img[^>]+data-src=["\']([^"\']+)["\']/i',
                        // Cari URL gambar langsung di HTML
                        '/(https?:\/\/[^\s"\'<>]+\.(jpg|jpeg|png|gif|webp|bmp))/i',
                    ];
                    
                    $imageUrl = null;
                    foreach ($patterns as $pattern) {
                        if (preg_match($pattern, $html, $matches)) {
                            $imageUrl = $matches[1];
                            // Skip jika URL adalah placeholder, icon, atau data URI
                            if (stripos($imageUrl, 'placeholder') !== false || 
                                stripos($imageUrl, 'icon') !== false ||
                                stripos($imageUrl, 'logo') !== false ||
                                stripos($imageUrl, 'data:image') === 0) {
                                continue;
                            }
                            break;
                        }
                    }
                    
                    if ($imageUrl) {
                        // Jika relative URL, buat absolute
                        if (strpos($imageUrl, 'http') !== 0) {
                            $baseUrl = parse_url($url, PHP_URL_SCHEME) . '://' . parse_url($url, PHP_URL_HOST);
                            // Jika URL dimulai dengan /, langsung append
                            if (strpos($imageUrl, '/') === 0) {
                                $imageUrl = $baseUrl . $imageUrl;
                            } else {
                                $imageUrl = $baseUrl . '/' . ltrim($imageUrl, '/');
                            }
                        }
                        
                        Log::info('Extracted image URL from HTML', [
                            'original_url' => $url,
                            'image_url' => $imageUrl
                        ]);
                        
                        // Fetch gambar
                        $imageResponse = Http::timeout(10)->get($imageUrl);
                        
                        if ($imageResponse->successful()) {
                            $contentType = $imageResponse->header('Content-Type');
                            // Pastikan ini benar-benar gambar
                            if (strpos($contentType, 'image/') === 0) {
                                return response($imageResponse->body(), 200)
                                    ->header('Content-Type', $contentType)
                                    ->header('Cache-Control', 'public, max-age=3600');
                            }
                        }
                    }
                    
                    // Jika tidak ditemukan gambar, return placeholder
                    Log::warning('Could not extract image from HTML', ['url' => $url]);
                    return $this->generatePlaceholderImage('Image not found on page');
                }
            }
            
            // Untuk URL langsung ke gambar
            $response = Http::timeout(10)->get($url);
            
            if ($response->successful()) {
                $contentType = $response->header('Content-Type');
                
                // Jika response adalah HTML, bukan gambar
                if (strpos($contentType, 'text/html') !== false) {
                    Log::warning('URL returned HTML instead of image', ['url' => $url]);
                    return $this->generatePlaceholderImage('URL is not a direct image link');
                }
                
                return response($response->body(), 200)
                    ->header('Content-Type', $contentType ?? 'image/jpeg')
                    ->header('Cache-Control', 'public, max-age=3600');
            }
            
            return $this->generatePlaceholderImage('Image not found');
        } catch (Exception $e) {
            Log::error('Image proxy error', [
                'url' => $url,
                'error' => $e->getMessage()
            ]);
            return $this->generatePlaceholderImage('Failed to load image');
        }
    }

    /**
     * Generate placeholder image SVG
     */
    private function generatePlaceholderImage($message = 'Image not available')
    {
        $svg = '<?xml version="1.0" encoding="UTF-8"?>
            <svg width="200" height="200" xmlns="http://www.w3.org/2000/svg">
            <rect width="200" height="200" fill="#f0f0f0" stroke="#ddd" stroke-width="2"/>
            <text x="100" y="90" font-family="Arial, sans-serif" font-size="14" fill="#999" text-anchor="middle">' . htmlspecialchars($message) . '</text>
            <text x="100" y="120" font-family="Arial, sans-serif" font-size="12" fill="#ccc" text-anchor="middle">Klik untuk membuka link</text>
            </svg>';
        
        return response($svg, 200)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Cache-Control', 'no-cache');
    }

    /**
     * Download hasil validasi sebagai Excel
     */
    public function download()
    {
        $results = session('hse_validation_results', []);
        $headers = session('hse_validation_headers', []);

        if (empty($results)) {
            return redirect()->route('hse-validation.index')
                ->withErrors(['message' => 'Tidak ada hasil validasi untuk diunduh.']);
        }

        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Header row
            $headerRow = [
                'No',
                'Deskripsi',
                'Url Photo',
                'Validasi Main Category',
                'Validasi Sub Category',
                'Validasi TBC',
                'Validasi PSPP',
                'Validasi GR',
                'Validasi Incident',
                'Validasi Justifikasi',
                'Validasi Confidence',
                'Match Found'
            ];

            $col = 'A';
            foreach ($headerRow as $header) {
                $sheet->setCellValue($col . '1', $header);
                $col++;
            }

            // Data rows
            $rowNum = 2;
            foreach ($results as $result) {
                $sheet->setCellValue('A' . $rowNum, $result['row_number']);
                $sheet->setCellValue('B' . $rowNum, $result['deskripsi']);
                $sheet->setCellValue('C' . $rowNum, $result['url_photo']);
                $sheet->setCellValue('D' . $rowNum, $result['validasi_main_category']);
                $sheet->setCellValue('E' . $rowNum, $result['validasi_sub_category']);
                $sheet->setCellValue('F' . $rowNum, $result['validasi_TBC'] ? 'Ya' : 'Tidak');
                $sheet->setCellValue('G' . $rowNum, $result['validasi_PSPP'] ? 'Ya' : 'Tidak');
                $sheet->setCellValue('H' . $rowNum, $result['validasi_GR'] ? 'Ya' : 'Tidak');
                $sheet->setCellValue('I' . $rowNum, $result['validasi_Incident'] ? 'Ya' : 'Tidak');
                $sheet->setCellValue('J' . $rowNum, $result['validasi_justifikasi']);
                $sheet->setCellValue('K' . $rowNum, $result['validasi_confidence']);
                $sheet->setCellValue('L' . $rowNum, $result['match_found'] ? 'Ya' : 'Tidak');
                $rowNum++;
            }

            // Auto-size columns
            foreach (range('A', 'L') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Download file
            $writer = new Xlsx($spreadsheet);
            $filename = 'hse_validation_results_' . date('Y-m-d_His') . '.xlsx';
            
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
            exit;

        } catch (Exception $e) {
            return redirect()->route('hse-validation.results')
                ->withErrors(['message' => 'Error generating download: ' . $e->getMessage()]);
        }
    }
}

