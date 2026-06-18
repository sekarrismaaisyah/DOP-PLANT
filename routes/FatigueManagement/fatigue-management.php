<?php

use App\Http\Controllers\FatigueManagement\FatigueManagementDashboardController;
use App\Http\Controllers\FatigueManagement\FatigueManagementMonitoringController;
use App\Http\Controllers\FatigueManagement\FatigueManagementUploadController;
use Illuminate\Support\Facades\Route;

Route::prefix('fatigue-management')->name('fatigue-management.')->middleware('fatigue.management')->group(function (): void {
    Route::get('/dashboard', [FatigueManagementDashboardController::class, 'index'])->name('dashboard');
    Route::get('/upload', [FatigueManagementUploadController::class, 'index'])->name('upload');

    Route::prefix('monitoring')->name('monitoring.')->group(function (): void {
        Route::post('/evidence', [FatigueManagementMonitoringController::class, 'storeEvidence'])->name('evidence.store');
        Route::post('/{id}/evaluation', [FatigueManagementMonitoringController::class, 'storeEvaluation'])->whereNumber('id')->name('evaluation.store');
        Route::get('/{id}/evidence/download', [FatigueManagementMonitoringController::class, 'downloadEvidence'])->whereNumber('id')->name('evidence.download');
    });
});
