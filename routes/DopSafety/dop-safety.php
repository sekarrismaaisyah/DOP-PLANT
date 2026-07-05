<?php

declare(strict_types=1);

use App\Http\Controllers\DopSafety\DopSafetyCoverageController;
use App\Http\Controllers\DopSafety\DopSafetyDashboardController;
use App\Http\Controllers\DopSafety\DopSafetyFgdController;
use App\Http\Controllers\DopSafety\DopSafetyInspectionController;
use App\Http\Controllers\DopSafety\DopSafetyObservationController;
use App\Http\Controllers\DopSafety\DopSafetyOjiController;
use App\Http\Controllers\DopSafety\DopSafetyPlanController;
use App\Http\Controllers\DopSafety\DopSafetyReviewController;
use App\Http\Controllers\DopSafety\DopOjiPlanController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])
    ->prefix('dop-safety')
    ->name('dop-safety.')
    ->group(function (): void {
        Route::redirect('/', '/dop-safety/dashboard')->name('home');

        Route::get('/dashboard', [DopSafetyDashboardController::class, 'index'])->name('dashboard');
        // Route::get('/oji', [DopSafetyOjiController::class, 'index'])->name('oji.index');

        Route::get('/plan', [DopSafetyPlanController::class, 'index'])->name('plan.index');
        Route::get('/plan/create', [DopSafetyPlanController::class, 'create'])->name('plan.create');
        Route::post('/plan', [DopSafetyPlanController::class, 'store'])->name('plan.store');
        Route::get('/plan/template', [DopSafetyPlanController::class, 'downloadTemplate'])->name('plan.template');
        Route::post('/plan/import-items', [DopSafetyPlanController::class, 'importItems'])->name('plan.import-items');
        Route::post('/plan/import', [DopSafetyPlanController::class, 'import'])->name('plan.import');
        Route::get('/plan/{plan}', [DopSafetyPlanController::class, 'show'])->whereNumber('plan')->name('plan.show');
        Route::get('/plan/{plan}/edit', [DopSafetyPlanController::class, 'edit'])->whereNumber('plan')->name('plan.edit');
        Route::put('/plan/{plan}', [DopSafetyPlanController::class, 'update'])->whereNumber('plan')->name('plan.update');
        Route::delete('/plan/{plan}', [DopSafetyPlanController::class, 'destroy'])->whereNumber('plan')->name('plan.destroy');
        Route::get('/inspection', [DopSafetyInspectionController::class, 'index'])->name('inspection.index');
        Route::get('/observation', [DopSafetyObservationController::class, 'index'])->name('observation.index');
        Route::get('/review', [DopSafetyReviewController::class, 'index'])->name('review.index');
        Route::get('/fgd', [DopSafetyFgdController::class, 'index'])->name('fgd.index');
        Route::get('/coverage', [DopSafetyCoverageController::class, 'index'])->name('coverage.index');
        Route::post('/plan/bulk-approval', [DopSafetyPlanController::class, 'bulkApproval'])->name('plan.bulk-approval');
        Route::get('plan/{plan}/export-pdf', [DopSafetyPlanController::class, 'exportPdf'])->name('plan.export-pdf');

        Route::get('/oji', [DopOjiPlanController::class, 'index'])->name('oji.index');
        Route::get('/oji/create', [DopOjiPlanController::class, 'create'])->name('oji.create');
        Route::post('/oji', [DopOjiPlanController::class, 'store'])->name('oji.store');
        Route::get('/oji/template', [DopOjiPlanController::class, 'downloadTemplate'])->name('oji.template');
        Route::post('/oji/import-items', [DopOjiPlanController::class, 'importItems'])->name('oji.import-items');
        Route::post('/oji/import', [DopOjiPlanController::class, 'import'])->name('oji.import');
        Route::get('/oji/{plan}', [DopOjiPlanController::class, 'show'])->whereNumber('plan')->name('oji.show');
        Route::get('/oji/{plan}/edit', [DopOjiPlanController::class, 'edit'])->whereNumber('plan')->name('oji.edit');
        Route::put('/oji/{plan}', [DopOjiPlanController::class, 'update'])->whereNumber('plan')->name('oji.update');
        Route::patch('/oji/items/{item}/approve',[DopOjiPlanController::class, 'approve'])->name('oji.items.approve');
        Route::post('/oji/item/{item}/reject',[DopOjiPlanController::class, 'rejectItem'])->name('oji.item.reject');
        Route::delete('/oji/{plan}', [DopOjiPlanController::class, 'destroy'])->whereNumber('plan')->name('oji.destroy');
        Route::get('oji/download-worker-template', [DopOjiPlanController::class, 'downloadWorkerTemplate'])->name('download-worker-template');
        Route::post('oji/upload-item-workers', [DopOjiPlanController::class, 'uploadItemWorkers'])->name('upload-item-workers');

    });
