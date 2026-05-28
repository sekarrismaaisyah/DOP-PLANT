<?php

use App\Http\Controllers\Hira\HiraImprovementController;
use Illuminate\Support\Facades\Route;

Route::prefix('hira')->name('hira.')->group(function (): void {
    Route::get('/improvement', [HiraImprovementController::class, 'index'])->name('improvement.index');
});
