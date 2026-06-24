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
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])
    ->prefix('dop-safety')
    ->name('dop-safety.')
    ->group(function (): void {
        Route::redirect('/', '/dop-safety/dashboard')->name('home');

        Route::get('/dashboard', [DopSafetyDashboardController::class, 'index'])->name('dashboard');
        Route::get('/oji', [DopSafetyOjiController::class, 'index'])->name('oji.index');

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
    });
