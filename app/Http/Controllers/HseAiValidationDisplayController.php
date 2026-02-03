<?php

namespace App\Http\Controllers;

use App\Models\HseAiValidation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class HseAiValidationDisplayController extends Controller
{
    /**
     * Display HSE AI Validation data grouped by site in tabs
     */
    public function index()
    {
        // Define sites
        $sites = [
            'BMO 1' => 'BMO 1',
            'BMO 2' => 'BMO 2',
            'BMO 3' => 'BMO 3',
            'SMO' => 'SMO',
            'LMO' => 'LMO',
            'GMO' => 'GMO',
            'Marine' => 'Marine',
        ];

        // Get data count for each site
        $siteCounts = [];
        foreach ($sites as $siteKey => $siteName) {
            $siteCounts[$siteKey] = HseAiValidation::where('site', $siteName)->count();
        }

        // Statistik validasi (seluruh data)
        $total = HseAiValidation::count();
        $stats = [
            'total' => $total,
            // Validasi BY AI
            'ai_match_found' => HseAiValidation::where('ai_match_found', true)->count(),
            'ai_no_match' => HseAiValidation::where('ai_match_found', false)->count(),
            // Klasifikasi AI (TBC, GR, dll)
            'ai_tbc_yes' => HseAiValidation::where('ai_tbc', true)->count(),
            'ai_gr_yes' => HseAiValidation::where('ai_gr', true)->count(),
            'ai_incident_yes' => HseAiValidation::where('ai_incident', true)->count(),
            // Validasi Evaluator - TBC
            'evaluator_tbc_valid' => HseAiValidation::where('tbc', 'Valid')->count(),
            'evaluator_tbc_invalid' => HseAiValidation::where('tbc', 'Invalid')->count(),
            'evaluator_tbc_done' => HseAiValidation::whereNotNull('tbc')->where('tbc', '!=', '')->count(),
            'evaluator_tbc_pending' => HseAiValidation::where(function ($q) {
                $q->whereNull('tbc')->orWhere('tbc', '');
            })->count(),
            // Validasi Evaluator - GR
            'evaluator_gr_valid' => HseAiValidation::where('gr', 'Valid')->count(),
            'evaluator_gr_potential' => HseAiValidation::where('gr', 'Potential')->count(),
            'evaluator_gr_invalid' => HseAiValidation::where('gr', 'Invalid')->count(),
            'evaluator_gr_non_gr' => HseAiValidation::where('gr', 'NonGrRelated')->count(),
            'evaluator_gr_done' => HseAiValidation::whereNotNull('gr')->where('gr', '!=', '')->count(),
            'evaluator_gr_pending' => HseAiValidation::where(function ($q) {
                $q->whereNull('gr')->orWhere('gr', '');
            })->count(),
        ];

        return view('hse-ai-validation.index', [
            'sites' => $sites,
            'siteCounts' => $siteCounts,
            'stats' => $stats,
        ]);
    }

    /**
     * Get validation data for a specific site (AJAX endpoint)
     */
    public function getSiteData(Request $request, $site)
    {
        // Normalize site name (handle variations)
        $siteMap = [
            'bmo-1' => 'BMO 1',
            'bmo-2' => 'BMO 2',
            'bmo-3' => 'BMO 3',
            'smo' => 'SMO',
            'lmo' => 'LMO',
            'gmo' => 'GMO',
            'marine' => 'Marine',
        ];

        $normalizedSite = $siteMap[strtolower($site)] ?? $site;

        // Get data for the site
        $validations = HseAiValidation::where('site', $normalizedSite)
            ->orderBy('tanggal_pelaporan', 'desc')
            ->get();

        // Format data for DataTable - Include ALL columns
        $data = [];
        $tooltipData = [];
        
        foreach ($validations as $validation) {
            // Extract both foto temuan and foto penyelesaian from photoCar URL
            $fotos = $this->extractFotos($validation->uri_foto ?? '');
            
            // Table data with ALL columns
            $data[] = [
                'id' => $validation->id,
                'task_number' => $validation->task_number ?? '',
                'jenis_laporan' => $validation->jenis_laporan ?? '',
                'aktivitas_pekerjaan' => $validation->aktivitas_pekerjaan ?? '',
                'lokasi' => $validation->lokasi ?? '',
                'detail_lokasi' => $validation->detail_lokasi ?? '',
                'keterangan' => $validation->keterangan ?? '',
                'tanggal_pelaporan' => $validation->tanggal_pelaporan ? $validation->tanggal_pelaporan->format('Y-m-d H:i') : '',
                'perusahaan_pelapor' => $validation->perusahaan_pelapor ?? '',
                'pelapor' => $validation->pelapor ?? '',
                'sid_pelapor' => $validation->sid_pelapor ?? '',
                'jabatan_fungsional_pelapor' => $validation->jabatan_fungsional_pelapor ?? '',
                'departemen_pelapor' => $validation->departemen_pelapor ?? '',
                'pic' => $validation->pic ?? '',
                'sid_pic' => $validation->sid_pic ?? '',
                'jabatan_fungsional_pic' => $validation->jabatan_fungsional_pic ?? '',
                'perusahaan_pic' => $validation->perusahaan_pic ?? '',
                'departemen_pic' => $validation->departemen_pic ?? '',
                'foto_temuan' => $fotos['foto_temuan'],
                'foto_penyelesaian' => $fotos['foto_penyelesaian'],
                'tools_pengawasan' => $validation->tools_pengawasan ?? '',
                'catatan_tindakan' => $validation->catatan_tindakan ?? '',
                'nik_pelapor' => $validation->nik_pelapor ?? '',
                'nama_pelapor' => $validation->nama_pelapor ?? '',
                'nama_perusahaan_pelapor_karyawan' => $validation->nama_perusahaan_pelapor_karyawan ?? '',
                'jabatan_fungsional_karyawan_pelapor' => $validation->jabatan_fungsional_karyawan_pelapor ?? '',
                'latitude' => $validation->latitude ?? '',
                'longitude' => $validation->longitude ?? '',
                'site' => $validation->site ?? '',
                'keterangan_lokasi' => $validation->keterangan_lokasi ?? '',
                'jam' => $validation->jam ?? '',
                'menit' => $validation->menit ?? '',
                'nama_lokasi' => $validation->nama_lokasi ?? '',
                'nama_detail_lokasi' => $validation->nama_detail_lokasi ?? '',
                'ai_match_found' => $validation->ai_match_found,
                'ai_main_category' => $validation->ai_main_category ?? '',
                'ai_sub_category' => $validation->ai_sub_category ?? '',
                'ai_tbc' => $validation->ai_tbc,
                'ai_pspp' => $validation->ai_pspp,
                'ai_gr' => $validation->ai_gr,
                'ai_incident' => $validation->ai_incident,
                'ai_justification' => $validation->ai_justification ?? '',
                'ai_confidence_score' => $validation->ai_confidence_score ?? '',
                'validation_date' => $validation->validation_date ? $validation->validation_date->format('Y-m-d') : '',
                'validated_by' => $validation->validated_by ?? '',
                'tbc' => $validation->tbc ?? '',
                'gr' => $validation->gr ?? '',
                'catatan' => $validation->catatan ?? '',
            ];
            
            // Full inspection data for tooltip (same data structure)
            $tooltipData[] = [
                'task_number' => $validation->task_number ?? '',
                'jenis_laporan' => $validation->jenis_laporan ?? '',
                'aktivitas_pekerjaan' => $validation->aktivitas_pekerjaan ?? '',
                'lokasi' => $validation->lokasi ?? '',
                'detail_lokasi' => $validation->detail_lokasi ?? '',
                'keterangan' => $validation->keterangan ?? '',
                'tanggal_pelaporan' => $validation->tanggal_pelaporan ? $validation->tanggal_pelaporan->format('Y-m-d H:i') : '',
                'perusahaan_pelapor' => $validation->perusahaan_pelapor ?? '',
                'pelapor' => $validation->pelapor ?? '',
                'sid_pelapor' => $validation->sid_pelapor ?? '',
                'jabatan_fungsional_pelapor' => $validation->jabatan_fungsional_pelapor ?? '',
                'departemen_pelapor' => $validation->departemen_pelapor ?? '',
                'pic' => $validation->pic ?? '',
                'sid_pic' => $validation->sid_pic ?? '',
                'jabatan_fungsional_pic' => $validation->jabatan_fungsional_pic ?? '',
                'perusahaan_pic' => $validation->perusahaan_pic ?? '',
                'departemen_pic' => $validation->departemen_pic ?? '',
                'tools_pengawasan' => $validation->tools_pengawasan ?? '',
                'catatan_tindakan' => $validation->catatan_tindakan ?? '',
                'nik_pelapor' => $validation->nik_pelapor ?? '',
                'nama_pelapor' => $validation->nama_pelapor ?? '',
                'nama_perusahaan_pelapor_karyawan' => $validation->nama_perusahaan_pelapor_karyawan ?? '',
                'jabatan_fungsional_karyawan_pelapor' => $validation->jabatan_fungsional_karyawan_pelapor ?? '',
                'latitude' => $validation->latitude ?? '',
                'longitude' => $validation->longitude ?? '',
                'site' => $validation->site ?? '',
                'keterangan_lokasi' => $validation->keterangan_lokasi ?? '',
                'jam' => $validation->jam ?? '',
                'menit' => $validation->menit ?? '',
                'nama_lokasi' => $validation->nama_lokasi ?? '',
                'nama_detail_lokasi' => $validation->nama_detail_lokasi ?? '',
                'ai_sub_category' => $validation->ai_sub_category ?? '',
                'ai_tbc' => $validation->ai_tbc ? 'Yes' : 'No',
                'ai_pspp' => $validation->ai_pspp ? 'Yes' : 'No',
                'ai_gr' => $validation->ai_gr ? 'Yes' : 'No',
                'ai_incident' => $validation->ai_incident ? 'Yes' : 'No',
                'ai_confidence_score' => $validation->ai_confidence_score ?? '',
                'validation_date' => $validation->validation_date ? $validation->validation_date->format('Y-m-d') : '',
                'validated_by' => $validation->validated_by ?? '',
            ];
        }

        return response()->json([
            'data' => $data,
            'tooltipData' => $tooltipData,
            'count' => count($data),
        ]);
    }

    /**
     * Update validation data (TBC, GR, Catatan)
     */
    public function update(Request $request, $id)
    {
        try {
            $validation = HseAiValidation::findOrFail($id);
            
            $request->validate([
                'tbc' => 'nullable|in:,Valid,Invalid',
                'gr' => 'nullable|in:,Valid,Potential,Invalid,NonGrRelated',
                'catatan' => 'nullable|string|max:500',
            ]);
            
            if ($request->has('tbc')) {
                $validation->tbc = $request->tbc ?: null;
            }
            
            if ($request->has('gr')) {
                $validation->gr = $request->gr ?: null;
            }
            
            if ($request->has('catatan')) {
                $validation->catatan = $request->catatan ?: null;
            }
            
            $validation->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diperbarui',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Extract both Foto Temuan and Foto Penyelesaian URLs from photoCar page
     * Returns array with keys 'foto_temuan' and 'foto_penyelesaian'
     */
    private function extractFotos($uriFoto)
    {
        $result = [
            'foto_temuan' => '',
            'foto_penyelesaian' => ''
        ];
        
        if (empty($uriFoto)) {
            return $result;
        }

        // If URL is already a direct image URL (beats2/file), return it as foto_temuan
        if (strpos($uriFoto, 'beats2/file') !== false) {
            // Make sure it's absolute URL
            if (strpos($uriFoto, 'http') !== 0) {
                if (strpos($uriFoto, '/') === 0) {
                    $result['foto_temuan'] = 'https://hseautomation.beraucoal.co.id' . $uriFoto;
                } else {
                    $result['foto_temuan'] = 'https://hseautomation.beraucoal.co.id/' . ltrim($uriFoto, '/');
                }
            } else {
                $result['foto_temuan'] = $uriFoto;
            }
            return $result;
        }

        // If URL is photoCar page, extract both photos
        if (strpos($uriFoto, 'hseautomation.beraucoal.co.id/report/photoCar') !== false) {
            try {
                $response = Http::timeout(10)->get($uriFoto);
                
                if (!$response->successful()) {
                    Log::warning('Failed to fetch photoCar page', ['url' => $uriFoto]);
                    return $result;
                }

                $html = $response->body();

                // Check if page has "No Photo"
                if (stripos($html, 'No Photo') !== false && stripos($html, 'Foto Temuan') === false) {
                    return $result; // No photo available
                }

                // Extract Foto Temuan URL
                $fotoTemuanUrl = $this->extractPhotoFromSection($html, 'Foto Temuan');
                
                // Extract Foto Penyelesaian URL
                $fotoPenyelesaianUrl = $this->extractPhotoFromSection($html, 'Foto Penyelesaian');
                
                // Fallback: if no specific sections found, get all beats2/file URLs
                if (!$fotoTemuanUrl && !$fotoPenyelesaianUrl) {
                    // Try to get all beats2/file URLs from the page
                    $allUrls = [];
                    
                    // Match all href or src with beats2/file
                    if (preg_match_all('/(?:href|src|data-src)=["\']([^"\']*beats2\/file[^"\']*)["\']/i', $html, $allMatches)) {
                        foreach ($allMatches[1] as $url) {
                            $allUrls[] = $this->makeAbsoluteUrl($url);
                        }
                        // Remove duplicates
                        $allUrls = array_unique($allUrls);
                        $allUrls = array_values($allUrls);
                    }
                    
                    // Assign first URL to foto_temuan, second to foto_penyelesaian
                    if (isset($allUrls[0])) {
                        $fotoTemuanUrl = $allUrls[0];
                    }
                    if (isset($allUrls[1])) {
                        $fotoPenyelesaianUrl = $allUrls[1];
                    }
                }

                $result['foto_temuan'] = $fotoTemuanUrl ? $this->makeAbsoluteUrl($fotoTemuanUrl) : '';
                $result['foto_penyelesaian'] = $fotoPenyelesaianUrl ? $this->makeAbsoluteUrl($fotoPenyelesaianUrl) : '';

                return $result;

            } catch (Exception $e) {
                Log::error('Error extracting photos', [
                    'url' => $uriFoto,
                    'error' => $e->getMessage()
                ]);
                return $result;
            }
        }

        // For other URLs, return as foto_temuan
        $result['foto_temuan'] = $uriFoto;
        return $result;
    }
    
    /**
     * Extract photo URL from a specific section in HTML
     */
    private function extractPhotoFromSection($html, $sectionName)
    {
        $photoUrl = null;
        
        // Pattern to find the section and extract photo URL
        // The section structure is typically: <div>Section Name</div>...<img src="...">...<a href="...">Unduh</a>
        
        // Method 1: Look for section label followed by any beats2/file URL
        $escapedSection = preg_quote($sectionName, '/');
        
        // Pattern untuk mencari section dengan format seperti di halaman photoCar
        $patterns = [
            // Cari section header diikuti dengan URL beats2/file dalam anchor tag (prioritas tinggi - link Unduh)
            '/' . $escapedSection . '.*?<a[^>]+href=["\']([^"\']*beats2\/file[^"\']*)["\']/is',
            // Cari section header diikuti dengan img src
            '/' . $escapedSection . '.*?<img[^>]+src=["\']([^"\']*beats2\/file[^"\']*)["\']/is',
            // Cari section header diikuti dengan img data-src
            '/' . $escapedSection . '.*?<img[^>]+data-src=["\']([^"\']*beats2\/file[^"\']*)["\']/is',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $html, $matches)) {
                $photoUrl = $matches[1];
                break;
            }
        }
        
        return $photoUrl;
    }
    
    /**
     * Make URL absolute if it's relative
     */
    private function makeAbsoluteUrl($url)
    {
        if (empty($url)) {
            return '';
        }
        
        if (strpos($url, 'http') === 0) {
            return $url;
        }
        
        if (strpos($url, '/') === 0) {
            return 'https://hseautomation.beraucoal.co.id' . $url;
        }
        
        return 'https://hseautomation.beraucoal.co.id/' . ltrim($url, '/');
    }
}

