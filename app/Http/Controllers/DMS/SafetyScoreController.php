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
     * Create initial calibration record (before calibration starts)
     */
    public function createInitialCalibration(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'driver_id' => 'required|string|max:50',
            'trip_id' => 'required|string|max:50',
            'notes' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Create initial calibration record with null values for calibration data
            // These will be updated when calibration completes
            $calibration = DmsCalibration::create([
                'driver_id' => $request->driver_id,
                'trip_id' => $request->trip_id,
                'calibration_start_time' => now(),
                'calibration_end_time' => now(), // Will be updated when calibration completes
                't_close' => 0,
                'ear_mean' => 0,
                'ear_sd' => 0,
                'data_points_count' => 0,
                'notes' => $request->notes,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Initial calibration record created successfully',
                'data' => [
                    'id' => $calibration->id,
                    'driver_id' => $calibration->driver_id,
                    'trip_id' => $calibration->trip_id,
                    'notes' => $calibration->notes,
                    'calibration_start_time' => $calibration->calibration_start_time->toISOString(),
                ],
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create initial calibration', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create initial calibration',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update calibration record (when calibration completes)
     */
    public function updateCalibration(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'driver_id' => 'sometimes|string|max:50',
            'trip_id' => 'sometimes|string|max:50',
            'calibration_start_time' => 'sometimes|date',
            'calibration_end_time' => 'sometimes|date',
            't_close' => 'sometimes|numeric',
            'ear_mean' => 'sometimes|numeric',
            'ear_sd' => 'sometimes|numeric',
            'data_points_count' => 'sometimes|integer|min:0',
            'notes' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $calibration = DmsCalibration::findOrFail($id);
            $calibration->update($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Calibration updated successfully',
                'data' => $calibration,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to update calibration', [
                'error' => $e->getMessage(),
                'id' => $id,
                'request' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update calibration',
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

