<?php

namespace App\Http\Controllers\DMS;

use App\Http\Controllers\Controller;
use App\Models\SafetyScoreLog;
use App\Models\DmsCalibration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SafetyScoreController extends Controller
{
    /**
     * Store safety score data from DMS frontend
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'driver_id' => 'required|string|max:50',
            'trip_id' => 'nullable|string|max:50',
            'calibration_id' => 'nullable|integer|exists:dms_calibrations,id',
            'timestamp' => 'required|date',
            'ear' => 'nullable|numeric',
            'perclos_60s' => 'nullable|numeric|min:0|max:1',
            'blink_60s' => 'nullable|integer|min:0',
            'microsleep_60s' => 'nullable|integer|min:0',
            'fatigue' => 'nullable|numeric|min:0|max:100',
            'drift' => 'nullable|numeric|min:0|max:100',
            'safety_score' => 'nullable|numeric|min:0|max:100',
            'status' => 'nullable|string|in:Safe,Caution,Attention',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $log = SafetyScoreLog::create($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Safety score logged successfully',
                'data' => $log,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to store safety score log', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to store safety score log',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store calibration data from DMS frontend
     */
    public function storeCalibration(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'driver_id' => 'required|string|max:50',
            'trip_id' => 'nullable|string|max:50',
            'calibration_start_time' => 'required|date',
            'calibration_end_time' => 'required|date',
            't_close' => 'required|numeric',
            'ear_mean' => 'required|numeric',
            'ear_sd' => 'required|numeric',
            'data_points_count' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $calibration = DmsCalibration::create($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Calibration data saved successfully',
                'data' => $calibration,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to store calibration data', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to store calibration data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get list of calibrations
     */
    public function getCalibrations(Request $request): JsonResponse
    {
        try {
            $calibrations = DmsCalibration::orderBy('calibration_start_time', 'desc')
                ->get()
                ->map(function ($cal) {
                    return [
                        'id' => $cal->id,
                        'driver_id' => $cal->driver_id,
                        'trip_id' => $cal->trip_id,
                        'calibration_start_time' => $cal->calibration_start_time->toISOString(),
                        'calibration_end_time' => $cal->calibration_end_time->toISOString(),
                        't_close' => (float) $cal->t_close,
                        'ear_mean' => (float) $cal->ear_mean,
                        'ear_sd' => (float) $cal->ear_sd,
                        'data_points_count' => $cal->data_points_count,
                        'notes' => $cal->notes,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $calibrations,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to get calibrations', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get calibrations',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

