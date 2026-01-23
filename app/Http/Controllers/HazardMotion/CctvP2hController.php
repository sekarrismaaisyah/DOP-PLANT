<?php

namespace App\Http\Controllers\HazardMotion;

use App\Http\Controllers\Controller;
use App\Models\CctvP2hChecklist;
use App\Models\CctvData;
use App\Models\CctvControlRoomPengawas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Exception;

class CctvP2hController extends Controller
{
    /**
     * Display P2H checklist form for a control room
     */
    public function create($controlRoom)
    {
        $user = Auth::user();
        $userName = $user ? $user->name : null;
        
        // Verify user has access to this control room
        if ($userName) {
            $hasAccess = CctvControlRoomPengawas::where('control_room', $controlRoom)
                ->where('nama_pengawas', $userName)
                ->exists();
            
            if (!$hasAccess) {
                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak memiliki akses ke control room ini.'
                    ], 403);
                }
                abort(403, 'Anda tidak memiliki akses ke control room ini.');
            }
        }
        
        // Get CCTV data for this control room
        $cctvList = CctvData::where('control_room', $controlRoom)
            ->orderBy('nama_cctv')
            ->get();
        
        // Get today's date and current shift
        $today = Carbon::now();
        $currentShift = $this->getCurrentShift();
        
        // Check if P2H already exists for today
        $existingP2h = CctvP2hChecklist::where('control_room', $controlRoom)
            ->whereDate('tanggal_pemeriksaan', $today->toDateString())
            ->where('shift', $currentShift)
            ->first();
        
        // If AJAX request, return partial view for modal
        if (request()->ajax() || request()->wantsJson()) {
            $html = view('HazardMotion.admin.partials.p2h-form', compact(
                'controlRoom',
                'cctvList',
                'today',
                'currentShift',
                'existingP2h',
                'userName'
            ))->render();
            
            return response()->json([
                'success' => true,
                'html' => $html,
                'control_room' => $controlRoom
            ]);
        }
        
        return view('HazardMotion.admin.p2h-checklist', compact(
            'controlRoom',
            'cctvList',
            'today',
            'currentShift',
            'existingP2h',
            'userName'
        ));
    }

    /**
     * Store P2H checklist
     */
    public function store(Request $request)
    {
        // Handle both JSON and form data
        $data = $request->isJson() ? $request->json()->all() : $request->all();
        
        $validated = validator($data, [
            'control_room' => 'required|string|max:255',
            'tanggal_pemeriksaan' => 'required|date',
            'shift' => 'required|string|max:50',
            'jenis_cctv' => 'nullable|array',
            'jenis_cctv.*' => 'string',
            'pemeriksaan_fisik' => 'nullable|array',
            'pemeriksaan_fungsi' => 'nullable|array',
            'detail_cctv' => 'nullable|array',
            'catatan_lain' => 'nullable|string',
        ])->validate();

        try {
            $user = Auth::user();
            $userName = $user ? $user->name : null;
            
            if (!$userName) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak terautentikasi.'
                ], 401);
            }
            
            // Verify user has access to this control room
            $hasAccess = CctvControlRoomPengawas::where('control_room', $validated['control_room'])
                ->where('nama_pengawas', $userName)
                ->exists();
            
            if (!$hasAccess) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke control room ini.'
                ], 403);
            }
            
            // Check if P2H already exists for this date and shift
            $existingP2h = CctvP2hChecklist::where('control_room', $validated['control_room'])
                ->whereDate('tanggal_pemeriksaan', $validated['tanggal_pemeriksaan'])
                ->where('shift', $validated['shift'])
                ->first();
            
            // Process detail_cctv - ensure it's an array
            $detailCctv = [];
            if (isset($validated['detail_cctv']) && is_array($validated['detail_cctv'])) {
                foreach ($validated['detail_cctv'] as $cctvDetail) {
                    if (isset($cctvDetail['cctv_id']) && isset($cctvDetail['status'])) {
                        $detailCctv[] = [
                            'cctv_id' => (int) $cctvDetail['cctv_id'],
                            'nama_cctv' => $cctvDetail['nama_cctv'] ?? '',
                            'status' => $cctvDetail['status'],
                            'catatan' => $cctvDetail['catatan'] ?? '',
                        ];
                    }
                }
            }
            
            if ($existingP2h) {
                // Update existing
                $existingP2h->update([
                    'jenis_cctv' => $validated['jenis_cctv'] ?? [],
                    'pemeriksaan_fisik' => $validated['pemeriksaan_fisik'] ?? [],
                    'pemeriksaan_fungsi' => $validated['pemeriksaan_fungsi'] ?? [],
                    'detail_cctv' => $detailCctv,
                    'catatan_lain' => $validated['catatan_lain'] ?? null,
                    'status' => 'completed',
                ]);
                
                $message = 'Data P2H berhasil diperbarui.';
                $p2hId = $existingP2h->id;
            } else {
                // Create new
                $p2h = CctvP2hChecklist::create([
                    'control_room' => $validated['control_room'],
                    'tanggal_pemeriksaan' => $validated['tanggal_pemeriksaan'],
                    'shift' => $validated['shift'],
                    'jenis_cctv' => $validated['jenis_cctv'] ?? [],
                    'nama_pengawas' => $userName,
                    'pemeriksaan_fisik' => $validated['pemeriksaan_fisik'] ?? [],
                    'pemeriksaan_fungsi' => $validated['pemeriksaan_fungsi'] ?? [],
                    'detail_cctv' => $detailCctv,
                    'catatan_lain' => $validated['catatan_lain'] ?? null,
                    'status' => 'completed',
                ]);
                
                $message = 'Data P2H berhasil disimpan.';
                $p2hId = $p2h->id;
            }
            
            // Check if request wants JSON response (AJAX or API request)
            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => [
                        'id' => $p2hId,
                        'control_room' => $validated['control_room'],
                    ]
                ]);
            }
            
            return redirect()->route('maps.map')
                ->with('success', $message);
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            Log::error('Validation error storing P2H checklist: ' . $e->getMessage());
            
            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal: ' . $e->getMessage(),
                    'errors' => $e->errors()
                ], 422);
            }
            
            return back()->withErrors($e->errors())->withInput();
            
        } catch (Exception $e) {
            Log::error('Error storing P2H checklist: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data.')->withInput();
        }
    }

    /**
     * Get P2H status for control rooms (API)
     */
    public function getStatus(Request $request)
    {
        $user = Auth::user();
        $userName = $user ? $user->name : null;
        
        if (!$userName) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak terautentikasi.'
            ], 401);
        }
        
        // Get control rooms supervised by user
        $pengawasRecords = CctvControlRoomPengawas::where('nama_pengawas', $userName)->get();
        $supervisedControlRooms = $pengawasRecords->pluck('control_room')->filter()->unique();
        
        $date = $request->get('date', Carbon::now()->toDateString());
        $shift = $request->get('shift', $this->getCurrentShift());
        
        $status = [];
        foreach ($supervisedControlRooms as $controlRoom) {
            $hasP2h = CctvP2hChecklist::hasP2hToday($controlRoom, $shift, $date);
            $status[$controlRoom] = [
                'has_p2h' => $hasP2h,
                'control_room' => $controlRoom,
            ];
        }
        
        return response()->json([
            'success' => true,
            'data' => $status,
            'date' => $date,
            'shift' => $shift,
        ]);
    }

    /**
     * Display P2H evaluation dashboard
     */
    public function evaluation(Request $request)
    {
        // Get filter parameters
        $startDate = $request->get('start_date', Carbon::now()->subDays(30)->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());
        $shift = $request->get('shift', 'all');
        $controlRoom = $request->get('control_room', 'all');
        
        // Build query
        $query = CctvP2hChecklist::whereBetween('tanggal_pemeriksaan', [$startDate, $endDate])
            ->where('status', 'completed');
        
        if ($shift !== 'all') {
            $query->where('shift', $shift);
        }
        
        if ($controlRoom !== 'all') {
            $query->where('control_room', $controlRoom);
        }
        
        // Get all P2H records
        $p2hRecords = $query->orderBy('tanggal_pemeriksaan', 'desc')
            ->orderBy('shift', 'desc')
            ->get();
        
        // Statistics
        $totalRecords = $p2hRecords->count();
        $totalControlRooms = CctvData::distinct('control_room')->whereNotNull('control_room')->count('control_room');
        $uniqueControlRooms = $p2hRecords->pluck('control_room')->unique()->count();
        
        // Calculate completion rate
        $expectedRecords = $this->calculateExpectedRecords($startDate, $endDate, $shift, $controlRoom);
        $completionRate = $expectedRecords > 0 ? ($totalRecords / $expectedRecords * 100) : 0;
        
        // Get control rooms with their status
        $controlRooms = CctvData::select('control_room')
            ->whereNotNull('control_room')
            ->distinct()
            ->orderBy('control_room')
            ->get()
            ->pluck('control_room')
            ->filter();
        
        $controlRoomStats = [];
        foreach ($controlRooms as $cr) {
            $crRecords = $p2hRecords->where('control_room', $cr);
            $crCount = $crRecords->count();
            $latestRecord = $crRecords->first();
            
            $controlRoomStats[] = [
                'control_room' => $cr,
                'total_p2h' => $crCount,
                'latest_date' => $latestRecord ? $latestRecord->tanggal_pemeriksaan->format('Y-m-d') : null,
                'latest_shift' => $latestRecord ? $latestRecord->shift : null,
                'latest_pengawas' => $latestRecord ? $latestRecord->nama_pengawas : null,
                'status' => $latestRecord ? 'completed' : 'pending',
            ];
        }
        
        // Daily statistics for chart
        $dailyStats = [];
        $currentDate = Carbon::parse($startDate);
        $endDateCarbon = Carbon::parse($endDate);
        
        while ($currentDate <= $endDateCarbon) {
            $dateStr = $currentDate->toDateString();
            $dayRecords = $p2hRecords->filter(function($record) use ($dateStr) {
                return $record->tanggal_pemeriksaan->toDateString() === $dateStr;
            });
            
            $dailyStats[] = [
                'date' => $dateStr,
                'count' => $dayRecords->count(),
                'shift_1' => $dayRecords->where('shift', '1')->count(),
                'shift_2' => $dayRecords->where('shift', '2')->count(),
                'shift_3' => $dayRecords->where('shift', '3')->count(),
            ];
            
            $currentDate->addDay();
        }
        
        // Shift statistics
        $shiftStats = [
            'shift_1' => $p2hRecords->where('shift', '1')->count(),
            'shift_2' => $p2hRecords->where('shift', '2')->count(),
            'shift_3' => $p2hRecords->where('shift', '3')->count(),
        ];
        
        // Items evaluation (from pemeriksaan_fisik and pemeriksaan_fungsi)
        $itemsEvaluation = $this->evaluateItems($p2hRecords);
        
        return view('HazardMotion.admin.p2h-evaluation', compact(
            'p2hRecords',
            'totalRecords',
            'totalControlRooms',
            'uniqueControlRooms',
            'completionRate',
            'controlRoomStats',
            'dailyStats',
            'shiftStats',
            'itemsEvaluation',
            'startDate',
            'endDate',
            'shift',
            'controlRoom',
            'controlRooms'
        ));
    }
    
    /**
     * Calculate expected number of P2H records
     */
    private function calculateExpectedRecords($startDate, $endDate, $shift, $controlRoom)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $days = $start->diffInDays($end) + 1;
        
        $controlRooms = $controlRoom !== 'all' 
            ? [$controlRoom] 
            : CctvData::distinct('control_room')->whereNotNull('control_room')->pluck('control_room')->toArray();
        
        $shifts = $shift !== 'all' ? [$shift] : ['1', '2', '3'];
        
        return count($controlRooms) * count($shifts) * $days;
    }
    
    /**
     * Evaluate items from pemeriksaan_fisik and pemeriksaan_fungsi
     */
    private function evaluateItems($p2hRecords)
    {
        $items = [
            'fisik' => [
                'Kamera CCTV',
                'Solar Panel/Baterai (sesuai sumber energi yang digunakan)',
                'Unit PC',
                'Unit NVR (Network Video Record)',
                'Additional Monitor',
                'Kondisi Penerangan (khusus shift 2)',
                'Unit UPS (Uninterruptible Power Supply) server',
                'Unit Server',
                'Air conditioner (AC)',
            ],
            'fungsi' => [
                'Unit PC dapat dinyalakan dan berfungsi',
                'Unit NVR dapat dinyalakan dan berfungsi',
                'Gambar dari kamera dapat ditampilkan dengan jelas dalam PC maupun additional monitor',
                'PC dapat tersambung ke internet (khusus Mining Eyes)',
                'Gambar di website Mining Eyes Analytics dapat ditampilkan dengan jelas dalam PC',
                'Unit UPS server dapat dinyalakan dan berfungsi',
                'Unit Server dapat dinyalakan dan berfungsi',
                'Unit AC dapat dinyalakan dan berfungsi',
            ],
        ];
        
        $evaluation = [
            'fisik' => [],
            'fungsi' => [],
        ];
        
        // Evaluate fisik items (indexed array 0-8)
        foreach ($items['fisik'] as $index => $item) {
            $baik = 0;
            $rusak = 0;
            $total = 0;
            
            foreach ($p2hRecords as $record) {
                if (isset($record->pemeriksaan_fisik) && is_array($record->pemeriksaan_fisik)) {
                    if (isset($record->pemeriksaan_fisik[$index])) {
                        $check = $record->pemeriksaan_fisik[$index];
                        if (isset($check['kondisi']) && !empty($check['kondisi'])) {
                            $total++;
                            if (strtolower($check['kondisi']) === 'baik') {
                                $baik++;
                            } elseif (strtolower($check['kondisi']) === 'rusak') {
                                $rusak++;
                            }
                        }
                    }
                }
            }
            
            $evaluation['fisik'][$item] = [
                'total' => $total,
                'baik' => $baik,
                'rusak' => $rusak,
                'baik_percentage' => $total > 0 ? ($baik / $total * 100) : 0,
            ];
        }
        
        // Evaluate fungsi items (indexed array 0-7)
        foreach ($items['fungsi'] as $index => $item) {
            $baik = 0;
            $rusak = 0;
            $total = 0;
            
            foreach ($p2hRecords as $record) {
                if (isset($record->pemeriksaan_fungsi) && is_array($record->pemeriksaan_fungsi)) {
                    if (isset($record->pemeriksaan_fungsi[$index])) {
                        $check = $record->pemeriksaan_fungsi[$index];
                        if (isset($check['status']) && !empty($check['status'])) {
                            $total++;
                            if (strtolower($check['status']) === 'baik') {
                                $baik++;
                            } elseif (strtolower($check['status']) === 'rusak' || strtolower($check['status']) === 'tidak_ada') {
                                $rusak++;
                            }
                        }
                    }
                }
            }
            
            $evaluation['fungsi'][$item] = [
                'total' => $total,
                'baik' => $baik,
                'rusak' => $rusak,
                'baik_percentage' => $total > 0 ? ($baik / $total * 100) : 0,
            ];
        }
        
        return $evaluation;
    }

    /**
     * Get P2H history for a control room
     */
    public function history($controlRoom)
    {
        $user = Auth::user();
        $userName = $user ? $user->name : null;
        
        // Verify user has access
        if ($userName) {
            $hasAccess = CctvControlRoomPengawas::where('control_room', $controlRoom)
                ->where('nama_pengawas', $userName)
                ->exists();
            
            if (!$hasAccess) {
                abort(403, 'Anda tidak memiliki akses ke control room ini.');
            }
        }
        
        $history = CctvP2hChecklist::where('control_room', $controlRoom)
            ->orderBy('tanggal_pemeriksaan', 'desc')
            ->orderBy('shift', 'desc')
            ->paginate(20);
        
        return view('HazardMotion.admin.p2h-history', compact('controlRoom', 'history'));
    }

    /**
     * Determine current shift based on time
     */
    private function getCurrentShift()
    {
        $hour = Carbon::now()->hour;
        
        // Shift 1: 06:00 - 14:00
        // Shift 2: 14:00 - 22:00
        // Shift 3: 22:00 - 06:00
        if ($hour >= 6 && $hour < 14) {
            return '1';
        } elseif ($hour >= 14 && $hour < 22) {
            return '2';
        } else {
            return '3';
        }
    }
}

