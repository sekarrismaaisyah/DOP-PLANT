<?php

declare(strict_types=1);

use App\Http\Controllers\AutoBanned\AutoBannedController;
use App\Http\Controllers\AutoBanned\AutoBannedHsctEmailController;
use App\Http\Controllers\AutoBanned\AutoBannedInputasiController;
use App\Http\Controllers\AutoBanned\AutoBannedMasterDataController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])
    ->prefix('auto-banned')
    ->name('auto-banned.')
    ->group(function (): void {
        Route::get('/', [AutoBannedController::class, 'index'])->name('index');
        Route::get('/hsct-email', [AutoBannedHsctEmailController::class, 'index'])->name('hsct-email.index');
        Route::post('/hsct-email/initial', [AutoBannedHsctEmailController::class, 'sendInitial'])->name('hsct-email.initial');
        Route::post('/hsct-email/reminder', [AutoBannedHsctEmailController::class, 'sendReminder'])->name('hsct-email.reminder');
        Route::post('/hsct-campaign-items/{item}/confirm', [AutoBannedHsctEmailController::class, 'confirmItem'])->name('hsct-campaign-items.confirm');
        Route::post('/snapshots/{snapshot}/hsct-sent', [AutoBannedController::class, 'markHsctSent'])->name('snapshots.hsct-sent');
        Route::post('/snapshots/{snapshot}/hsct-confirmed', [AutoBannedController::class, 'markHsctConfirmed'])->name('snapshots.hsct-confirmed');
        Route::get('/inputasi', [AutoBannedInputasiController::class, 'index'])->name('inputasi.index');
        Route::get('/master-data', [AutoBannedMasterDataController::class, 'index'])->name('master-data.index');
    });
