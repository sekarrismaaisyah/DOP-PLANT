<?php

use App\Http\Controllers\API\TelegramWebhookController;
use App\Http\Controllers\DMS\SafetyScoreController;
use App\Http\Controllers\DMS\DMSDashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/telegram/webhook', TelegramWebhookController::class);

// DMS (Driver Monitoring System) Routes
Route::post('/dms/safety-score', [SafetyScoreController::class, 'store']);
Route::post('/dms/calibration', [SafetyScoreController::class, 'storeCalibration']);
Route::post('/dms/calibration/create-initial', [SafetyScoreController::class, 'createInitialCalibration']);
Route::put('/dms/calibration/{id}', [SafetyScoreController::class, 'updateCalibration']);
Route::get('/dms/calibrations', [SafetyScoreController::class, 'getCalibrations']);

// DMS Dashboard Routes
Route::get('/dms/dashboard/realtime', [DMSDashboardController::class, 'getRealtimeData']);
Route::get('/dms/dashboard/statistics', [DMSDashboardController::class, 'getStatistics']);
Route::get('/dms/dashboard/driver-logs', [DMSDashboardController::class, 'getDriverLogs']);
