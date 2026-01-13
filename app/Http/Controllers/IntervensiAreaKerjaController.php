<?php

namespace App\Http\Controllers;

use App\Models\IntervensiAreaKerja;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class IntervensiAreaKerjaController extends Controller
{
    /**
     * Display list of intervensi area kerja
     */
    public function index()
    {
        return view('intervensi-area-kerja.index');
    }

    /**
     * Get intervensi area kerja data for DataTable
     */
    public function getData(Request $request)
    {
        try {
            $draw = $request->get('draw');
            $start = $request->get('start', 0);
            $length = $request->get('length', 10);
            $search = $request->get('search');
            $searchValue = isset($search['value']) ? $search['value'] : '';
            $order = $request->get('order');
            $orderColumn = (isset($order[0]['column'])) ? $order[0]['column'] : 0;
            $orderDir = (isset($order[0]['dir'])) ? $order[0]['dir'] : 'desc';

            // Column mapping
            $columns = ['id', 'lokasi', 'area_kerja', 'pic_username', 'pic_nama', 'issue', 'status', 'created_at'];
            $orderColumnName = isset($columns[$orderColumn]) ? $columns[$orderColumn] : 'created_at';
            
            // Ensure order direction is valid
            $orderDir = in_array(strtolower($orderDir), ['asc', 'desc']) ? strtolower($orderDir) : 'desc';

            // Base query - only open issues
            $query = IntervensiAreaKerja::where('status', 'open');

            // Search functionality
            if (!empty($searchValue)) {
                $query->where(function($q) use ($searchValue) {
                    $q->where('lokasi', 'like', '%' . $searchValue . '%')
                      ->orWhere('area_kerja', 'like', '%' . $searchValue . '%')
                      ->orWhere('pic_username', 'like', '%' . $searchValue . '%')
                      ->orWhere('pic_nama', 'like', '%' . $searchValue . '%')
                      ->orWhere('issue', 'like', '%' . $searchValue . '%')
                      ->orWhere('status', 'like', '%' . $searchValue . '%');
                });
            }

            // Get total records
            try {
                $recordsTotal = IntervensiAreaKerja::where('status', 'open')->count();
                $recordsFiltered = $query->count();
            } catch (\Exception $e) {
                Log::error('Error counting records: ' . $e->getMessage());
                $recordsTotal = 0;
                $recordsFiltered = 0;
            }

            // Order and paginate
            $intervensiList = $query->orderBy($orderColumnName, $orderDir)
                ->skip($start)
                ->take($length)
                ->get();

            // Format data for DataTable
            $formattedData = $intervensiList->map(function($intervensi) {
                try {
                    // Status badge
                    $statusBadge = '';
                    if ($intervensi->status === 'closed') {
                        $statusBadge = '<span class="badge bg-success">Closed</span>';
                    } else {
                        $statusBadge = '<span class="badge bg-warning">Open</span>';
                    }

                    // Action buttons
                    $actions = '';
                    if ($intervensi->status === 'open') {
                        $actions = '<button class="btn btn-sm btn-success close-intervensi-btn" data-id="' . $intervensi->id . '" title="Close Issue">
                            <i class="material-icons-outlined" style="font-size: 16px;">check_circle</i> Close
                        </button>';
                    } else {
                        $actions = '<span class="text-muted">Closed</span>';
                    }

                    // Format tanggal pelaporan: "9 Jan 2026"
                    $tanggalPelaporan = '-';
                    if ($intervensi->created_at) {
                        $date = is_string($intervensi->created_at) ? \Carbon\Carbon::parse($intervensi->created_at) : $intervensi->created_at;
                        $tanggalPelaporan = $date->format('j M Y');
                    }

                    return [
                        'id' => $intervensi->id,
                        'lokasi' => $intervensi->lokasi ?? '-',
                        'area_kerja' => $intervensi->area_kerja ?? '-',
                        'created_by' => $intervensi->created_by ?? '-',
                        'pic_username' => $intervensi->pic_username ?? '-',
                        'pic_nama' => $intervensi->pic_nama ?? '-',
                        'issue' => '<div style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="' . htmlspecialchars($intervensi->issue ?? '') . '">' . htmlspecialchars($intervensi->issue ?? '') . '</div>',
                        'status' => $statusBadge,
                        'tanggal_pelaporan' => $tanggalPelaporan,
                        'actions' => $actions
                    ];
                } catch (\Exception $e) {
                    Log::error('Error formatting intervensi data for ID ' . ($intervensi->id ?? 'unknown') . ': ' . $e->getMessage());
                    return [
                        'id' => $intervensi->id ?? '-',
                        'lokasi' => '-',
                        'area_kerja' => '-',
                        'created_by' => '-',
                        'pic_username' => '-',
                        'pic_nama' => '-',
                        'issue' => 'Error loading data',
                        'status' => '-',
                        'tanggal_pelaporan' => '-',
                        'actions' => '-'
                    ];
                }
            });

            return response()->json([
                'draw' => intval($draw),
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $formattedData
            ]);
        } catch (Exception $e) {
            Log::error('Error in getData: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request' => $request->all()
            ]);
            
            $errorMessage = 'Terjadi kesalahan saat memuat data.';
            if (config('app.debug')) {
                $errorMessage .= ' ' . $e->getMessage() . ' (File: ' . basename($e->getFile()) . ', Line: ' . $e->getLine() . ')';
            }
            
            return response()->json([
                'draw' => intval($request->get('draw', 0)),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $errorMessage
            ], 500);
        }
    }

    /**
     * Get done intervensi area kerja data for DataTable
     */
    public function getDoneData(Request $request)
    {
        try {
            $draw = $request->get('draw');
            $start = $request->get('start', 0);
            $length = $request->get('length', 10);
            $search = $request->get('search');
            $searchValue = isset($search['value']) ? $search['value'] : '';
            $order = $request->get('order');
            $orderColumn = (isset($order[0]['column'])) ? $order[0]['column'] : 0;
            $orderDir = (isset($order[0]['dir'])) ? $order[0]['dir'] : 'desc';

            // Column mapping
            $columns = ['id', 'lokasi', 'area_kerja', 'created_by', 'pic_username', 'pic_nama', 'issue', 'created_at', 'closed_at'];
            $orderColumnName = isset($columns[$orderColumn]) ? $columns[$orderColumn] : 'closed_at';
            
            // Ensure order direction is valid
            $orderDir = in_array(strtolower($orderDir), ['asc', 'desc']) ? strtolower($orderDir) : 'desc';

            // Base query - only closed issues
            $query = IntervensiAreaKerja::where('status', 'closed');

            // Search functionality
            if (!empty($searchValue)) {
                $query->where(function($q) use ($searchValue) {
                    $q->where('lokasi', 'like', '%' . $searchValue . '%')
                      ->orWhere('area_kerja', 'like', '%' . $searchValue . '%')
                      ->orWhere('pic_username', 'like', '%' . $searchValue . '%')
                      ->orWhere('pic_nama', 'like', '%' . $searchValue . '%')
                      ->orWhere('issue', 'like', '%' . $searchValue . '%');
                });
            }

            // Get total records
            try {
                $recordsTotal = IntervensiAreaKerja::where('status', 'closed')->count();
                $recordsFiltered = $query->count();
            } catch (\Exception $e) {
                Log::error('Error counting done records: ' . $e->getMessage());
                $recordsTotal = 0;
                $recordsFiltered = 0;
            }

            // Order and paginate
            $intervensiList = $query->orderBy($orderColumnName, $orderDir)
                ->skip($start)
                ->take($length)
                ->get();

            // Format data for DataTable
            $formattedData = $intervensiList->map(function($intervensi) {
                try {
                    // Format tanggal pelaporan: "9 Jan 2026"
                    $tanggalPelaporan = '-';
                    if ($intervensi->created_at) {
                        $date = is_string($intervensi->created_at) ? \Carbon\Carbon::parse($intervensi->created_at) : $intervensi->created_at;
                        $tanggalPelaporan = $date->format('j M Y');
                    }

                    // Format tanggal selesai: "9 Jan 2026"
                    $tanggalSelesai = '-';
                    if ($intervensi->closed_at) {
                        $date = is_string($intervensi->closed_at) ? \Carbon\Carbon::parse($intervensi->closed_at) : $intervensi->closed_at;
                        $tanggalSelesai = $date->format('j M Y');
                    }

                    return [
                        'id' => $intervensi->id,
                        'lokasi' => $intervensi->lokasi ?? '-',
                        'area_kerja' => $intervensi->area_kerja ?? '-',
                        'created_by' => $intervensi->created_by ?? '-',
                        'pic_username' => $intervensi->pic_username ?? '-',
                        'pic_nama' => $intervensi->pic_nama ?? '-',
                        'issue' => '<div style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="' . htmlspecialchars($intervensi->issue ?? '') . '">' . htmlspecialchars($intervensi->issue ?? '') . '</div>',
                        'tanggal_pelaporan' => $tanggalPelaporan,
                        'tanggal_selesai' => $tanggalSelesai,
                        'actions' => '<button class="btn btn-sm btn-light border view-done-detail-btn" data-id="' . $intervensi->id . '" title="Lihat Detail">
                            <i class="material-icons-outlined" style="font-size: 16px;">visibility</i> Detail
                        </button>'
                    ];
                } catch (\Exception $e) {
                    Log::error('Error formatting done intervensi data for ID ' . ($intervensi->id ?? 'unknown') . ': ' . $e->getMessage());
                    return [
                        'id' => $intervensi->id ?? '-',
                        'lokasi' => '-',
                        'area_kerja' => '-',
                        'created_by' => '-',
                        'pic_username' => '-',
                        'pic_nama' => '-',
                        'issue' => 'Error loading data',
                        'tanggal_pelaporan' => '-',
                        'tanggal_selesai' => '-',
                        'actions' => '-'
                    ];
                }
            });

            return response()->json([
                'draw' => intval($draw),
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $formattedData
            ]);
        } catch (Exception $e) {
            Log::error('Error in getDoneData: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request' => $request->all()
            ]);
            
            $errorMessage = 'Terjadi kesalahan saat memuat data.';
            if (config('app.debug')) {
                $errorMessage .= ' ' . $e->getMessage() . ' (File: ' . basename($e->getFile()) . ', Line: ' . $e->getLine() . ')';
            }
            
            return response()->json([
                'draw' => intval($request->get('draw', 0)),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $errorMessage
            ], 500);
        }
    }

    /**
     * Get detail done intervensi for card display
     */
    public function getDoneDetail($id)
    {
        try {
            $intervensi = IntervensiAreaKerja::where('status', 'closed')->findOrFail($id);
            
            // Format dates
            $tanggalPelaporan = $intervensi->created_at ? (is_string($intervensi->created_at) ? \Carbon\Carbon::parse($intervensi->created_at) : $intervensi->created_at)->format('j M Y') : '-';
            $tanggalSelesai = $intervensi->closed_at ? (is_string($intervensi->closed_at) ? \Carbon\Carbon::parse($intervensi->closed_at) : $intervensi->closed_at)->format('j M Y, H:i') : '-';
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $intervensi->id,
                    'lokasi' => $intervensi->lokasi,
                    'area_kerja' => $intervensi->area_kerja,
                    'issue' => $intervensi->issue,
                    'resolution' => $intervensi->resolution,
                    'evidence_path' => $intervensi->evidence_path,
                    'pic_username' => $intervensi->pic_username,
                    'pic_nama' => $intervensi->pic_nama,
                    'pic_telepon' => $intervensi->pic_telepon,
                    'created_by' => $intervensi->created_by,
                    'created_by_email' => $intervensi->created_by_email,
                    'closed_by' => $intervensi->closed_by,
                    'tanggal_pelaporan' => $tanggalPelaporan,
                    'tanggal_selesai' => $tanggalSelesai,
                ]
            ]);
        } catch (Exception $e) {
            Log::error('Error getting done intervensi detail: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data intervensi.'
            ], 500);
        }
    }

    /**
     * Get detail intervensi for close form
     */
    public function getDetail($id)
    {
        try {
            $intervensi = IntervensiAreaKerja::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $intervensi->id,
                    'lokasi' => $intervensi->lokasi,
                    'area_kerja' => $intervensi->area_kerja,
                    'issue' => $intervensi->issue,
                    'resolution' => $intervensi->resolution,
                    'evidence_path' => $intervensi->evidence_path,
                    'status' => $intervensi->status,
                ]
            ]);
        } catch (Exception $e) {
            Log::error('Error getting intervensi detail: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data intervensi.'
            ], 500);
        }
    }

    /**
     * Update status of intervensi area kerja
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            // Validate basic fields first
            $validated = $request->validate([
                'status' => 'required|in:open,closed',
                'resolution' => 'nullable|string',
                'evidence' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:10240', // Max 10MB
            ]);

            $intervensi = IntervensiAreaKerja::findOrFail($id);
            
            // Get authenticated user
            $user = Auth::user();
            $closedBy = $user ? $user->name : 'Unknown';

            $intervensi->status = $validated['status'];
            
            if ($validated['status'] === 'closed') {
                $intervensi->closed_at = now();
                $intervensi->closed_by = $closedBy;
                
                // Handle resolution
                if (isset($validated['resolution'])) {
                    $intervensi->resolution = $validated['resolution'];
                }
                
                // Handle main evidence file
                if ($request->hasFile('evidence')) {
                    $evidenceFile = $request->file('evidence');
                    $evidencePath = $evidenceFile->store('intervensi-area-kerja/evidence', 'public');
                    $intervensi->evidence_path = $evidencePath;
                }
            } else {
                $intervensi->closed_at = null;
                $intervensi->closed_by = null;
                $intervensi->resolution = null;
                // Don't delete evidence files when reopening, just clear the path
                $intervensi->evidence_path = null;
            }
            
            $intervensi->save();

            return response()->json([
                'success' => true,
                'message' => 'Status intervensi berhasil diupdate.'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', $e->errors())
            ], 422);
        } catch (Exception $e) {
            Log::error('Error updating intervensi status: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengupdate status: ' . $e->getMessage()
            ], 500);
        }
    }
}
