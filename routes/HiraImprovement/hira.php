<?php

use App\Http\Controllers\Hira\HiraImprovementDetailApiController;
use App\Http\Controllers\Hira\HiraImprovementController;
use App\Http\Controllers\Hira\HiraImprovementRekayasaApiController;
use App\Http\Controllers\Hira\HiraImprovementRekayasaReplikasiApiController;
use App\Http\Controllers\Hira\HiraImprovementScurveTaskApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('hira')->name('hira.')->group(function (): void {
    Route::get('/improvement', [HiraImprovementController::class, 'index'])->name('improvement.index');

    Route::prefix('improvement/detail-rows')->name('improvement.detail-rows.')->group(function (): void {
        Route::get('/', [HiraImprovementDetailApiController::class, 'index'])->name('index');
        Route::get('/overview', [HiraImprovementDetailApiController::class, 'overview'])->name('overview');
        Route::post('/sync', [HiraImprovementDetailApiController::class, 'sync'])->name('sync');
        Route::post('/', [HiraImprovementDetailApiController::class, 'store'])->name('store');
        Route::post('/reset', [HiraImprovementDetailApiController::class, 'reset'])->name('reset');
        Route::post('/import', [HiraImprovementDetailApiController::class, 'import'])->name('import');
        Route::get('/export.csv', [HiraImprovementDetailApiController::class, 'exportCsv'])->name('export.csv');
        Route::get('/export.xls', [HiraImprovementDetailApiController::class, 'exportExcel'])->name('export.xls');
        Route::delete('/{id}', [HiraImprovementDetailApiController::class, 'destroy'])->whereNumber('id')->name('destroy');
    });

    Route::prefix('improvement/rekayasa-rows')->name('improvement.rekayasa-rows.')->group(function (): void {
        Route::get('/', [HiraImprovementRekayasaApiController::class, 'index'])->name('index');
        Route::post('/sync', [HiraImprovementRekayasaApiController::class, 'sync'])->name('sync');
        Route::post('/reset', [HiraImprovementRekayasaApiController::class, 'reset'])->name('reset');
        Route::post('/import', [HiraImprovementRekayasaApiController::class, 'import'])->name('import');
        Route::get('/export.csv', [HiraImprovementRekayasaApiController::class, 'exportCsv'])->name('export.csv');
        Route::get('/export.xls', [HiraImprovementRekayasaApiController::class, 'exportExcel'])->name('export.xls');
        Route::get('/template.csv', [HiraImprovementRekayasaApiController::class, 'exportTemplate'])->name('export.template');
        Route::get('/template.xls', [HiraImprovementRekayasaApiController::class, 'exportTemplateExcel'])->name('export.template.xls');
        Route::get('/export-merged.xlsx', [HiraImprovementRekayasaApiController::class, 'exportMergedExcelAll'])->name('export.merged');
        Route::get('/template-selected.xlsx', [HiraImprovementRekayasaApiController::class, 'exportSelectedReplikasiTemplate'])->name('export.template.selected');
        Route::get('/{id}/export-merged.xlsx', [HiraImprovementRekayasaApiController::class, 'exportMergedExcel'])->whereNumber('id')->name('export.merged.row');
        Route::delete('/{id}', [HiraImprovementRekayasaApiController::class, 'destroy'])->whereNumber('id')->name('destroy');
    });

    Route::prefix('improvement/rekayasa-replikasi-rows')->name('improvement.rekayasa-replikasi-rows.')->group(function (): void {
        Route::get('/', [HiraImprovementRekayasaReplikasiApiController::class, 'index'])->name('index');
        Route::post('/sync', [HiraImprovementRekayasaReplikasiApiController::class, 'sync'])->name('sync');
        Route::post('/reset', [HiraImprovementRekayasaReplikasiApiController::class, 'reset'])->name('reset');
        Route::post('/import', [HiraImprovementRekayasaReplikasiApiController::class, 'import'])->name('import');
        Route::get('/export.xlsx', [HiraImprovementRekayasaReplikasiApiController::class, 'exportExcel'])->name('export.xlsx');
        Route::get('/template.xlsx', [HiraImprovementRekayasaReplikasiApiController::class, 'exportTemplate'])->name('export.template');
        Route::delete('/{id}', [HiraImprovementRekayasaReplikasiApiController::class, 'destroy'])->whereNumber('id')->name('destroy');
    });

    Route::prefix('improvement/scurve-tasks')->name('improvement.scurve-tasks.')->group(function (): void {
        Route::get('/', [HiraImprovementScurveTaskApiController::class, 'index'])->name('index');
        Route::post('/sync', [HiraImprovementScurveTaskApiController::class, 'sync'])->name('sync');
        Route::post('/reseed', [HiraImprovementScurveTaskApiController::class, 'reseed'])->name('reseed');
        Route::post('/import', [HiraImprovementScurveTaskApiController::class, 'import'])->name('import');
        Route::get('/export.csv', [HiraImprovementScurveTaskApiController::class, 'exportCsv'])->name('export.csv');
        Route::get('/export.xls', [HiraImprovementScurveTaskApiController::class, 'exportExcel'])->name('export.xls');
        Route::delete('/{id}', [HiraImprovementScurveTaskApiController::class, 'destroy'])->whereNumber('id')->name('destroy');
    });
});
