<?php

use App\Http\Controllers\PembatasanLV\PembatasanLVController;
use App\Http\Controllers\PembatasanLV\PembatasanLVInputasiController;
use App\Http\Controllers\PembatasanLV\PembatasanLVMasterDataController;
use App\Http\Controllers\PembatasanLV\PembatasanLVBatasLvPerLokasiController;
use App\Http\Controllers\PembatasanLV\PembatasanLVBecomelineUnitController;
use App\Http\Controllers\PembatasanLV\PembatasanLVControlRoomPengawasController;
use App\Http\Controllers\PembatasanLV\PembatasanLVPlanningController;
use App\Http\Controllers\PembatasanLV\PembatasanLVMasterAktivitasController;
use App\Http\Controllers\PembatasanLV\PembatasanLVEvaluasiController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])
    ->prefix('pembatasan-lv')
    ->name('pembatasan-lv.')
    ->group(function (): void {
        Route::get('/', [PembatasanLVController::class, 'index'])->name('index');
        Route::post('/checkout/{inputasi}', [PembatasanLVController::class, 'checkoutLv'])->name('checkout.lv');
        Route::post('/checkout-orang/{inputasi}', [PembatasanLVController::class, 'checkoutOrang'])->name('checkout.orang');
        Route::get('/lv-masuk-aktif/data', [PembatasanLVController::class, 'lvMasukAktifData'])->name('lv-masuk-aktif.data');
        Route::get('/orang-masuk-aktif/data', [PembatasanLVController::class, 'orangMasukAktifData'])->name('orang-masuk-aktif.data');
        Route::get('/inputasi', [PembatasanLVInputasiController::class, 'index'])->name('inputasi.index');
        Route::post('/inputasi/lv', [PembatasanLVInputasiController::class, 'storeLv'])->name('inputasi.lv.store');
        Route::post('/inputasi/orang', [PembatasanLVInputasiController::class, 'storeOrang'])->name('inputasi.orang.store');
        Route::get('/inputasi/options/units', [PembatasanLVInputasiController::class, 'optionsUnits'])->name('inputasi.options.units');
        Route::get('/inputasi/options/drivers', [PembatasanLVInputasiController::class, 'optionsDrivers'])->name('inputasi.options.drivers');
        Route::get('/inputasi/options/sid', [PembatasanLVInputasiController::class, 'optionsSid'])->name('inputasi.options.sid');
        Route::get('/inputasi/options/lokasi', [PembatasanLVInputasiController::class, 'optionsLokasi'])->name('inputasi.options.lokasi');
        Route::get('/inputasi/options/detail-lokasi', [PembatasanLVInputasiController::class, 'optionsDetailLokasi'])->name('inputasi.options.detail-lokasi');
        Route::get('/inputasi/options/aktivitas', [PembatasanLVInputasiController::class, 'optionsAktivitas'])->name('inputasi.options.aktivitas');
        Route::get('/inputasi/kapasitas-lokasi', [PembatasanLVInputasiController::class, 'kapasitasLokasi'])->name('inputasi.kapasitas-lokasi');

        Route::get('/planning', [PembatasanLVPlanningController::class, 'index'])->name('planning.index');
        Route::post('/planning/lv', [PembatasanLVPlanningController::class, 'storeLv'])->name('planning.lv.store');
        Route::post('/planning/orang', [PembatasanLVPlanningController::class, 'storeOrang'])->name('planning.orang.store');
        Route::get('/planning/lv/data', [PembatasanLVPlanningController::class, 'dataLv'])->name('planning.lv.data');
        Route::get('/planning/orang/data', [PembatasanLVPlanningController::class, 'dataOrang'])->name('planning.orang.data');
        Route::get('/planning/pending-overview', [PembatasanLVPlanningController::class, 'pendingOverview'])->name('planning.pending-overview');
        Route::post('/planning/lv/{lvPlanning}/checkin', [PembatasanLVPlanningController::class, 'checkinLv'])->name('planning.lv.checkin');
        Route::post('/planning/orang/{orangPlanning}/checkin', [PembatasanLVPlanningController::class, 'checkinOrang'])->name('planning.orang.checkin');
        Route::delete('/planning/lv/{lvPlanning}', [PembatasanLVPlanningController::class, 'destroyLv'])->name('planning.lv.destroy');
        Route::delete('/planning/orang/{orangPlanning}', [PembatasanLVPlanningController::class, 'destroyOrang'])->name('planning.orang.destroy');
        Route::get('/planning/lv/template', [PembatasanLVPlanningController::class, 'downloadTemplateLv'])->name('planning.lv.template');
        Route::get('/planning/orang/template', [PembatasanLVPlanningController::class, 'downloadTemplateOrang'])->name('planning.orang.template');
        Route::post('/planning/lv/import', [PembatasanLVPlanningController::class, 'importLv'])->name('planning.lv.import');
        Route::post('/planning/orang/import', [PembatasanLVPlanningController::class, 'importOrang'])->name('planning.orang.import');

        Route::get('/evaluasi', [PembatasanLVEvaluasiController::class, 'index'])->name('evaluasi.index');
        Route::get('/evaluasi/data', [PembatasanLVEvaluasiController::class, 'data'])->name('evaluasi.data');

        Route::get('/master-data', [PembatasanLVMasterDataController::class, 'index'])->name('master-data.index');

        Route::prefix('master-data/batas-lv-per-lokasi')
            ->name('master-data.batas-lv-per-lokasi.')
            ->group(function (): void {
                Route::get('/data', [PembatasanLVBatasLvPerLokasiController::class, 'data'])->name('data');
                Route::post('/', [PembatasanLVBatasLvPerLokasiController::class, 'store'])->name('store');
                Route::get('/{batasLvPerLokasi}', [PembatasanLVBatasLvPerLokasiController::class, 'show'])->name('show');
                Route::put('/{batasLvPerLokasi}', [PembatasanLVBatasLvPerLokasiController::class, 'update'])->name('update');
                Route::delete('/{batasLvPerLokasi}', [PembatasanLVBatasLvPerLokasiController::class, 'destroy'])->name('destroy');
            });

        Route::prefix('master-data/control-room-pengawas')
            ->name('master-data.control-room-pengawas.')
            ->group(function (): void {
                Route::get('/data', [PembatasanLVControlRoomPengawasController::class, 'data'])->name('data');
                Route::get('/options', [PembatasanLVControlRoomPengawasController::class, 'options'])->name('options');
                Route::post('/', [PembatasanLVControlRoomPengawasController::class, 'store'])->name('store');
                Route::get('/{controlRoomPengawas}', [PembatasanLVControlRoomPengawasController::class, 'show'])->name('show');
                Route::put('/{controlRoomPengawas}', [PembatasanLVControlRoomPengawasController::class, 'update'])->name('update');
                Route::delete('/{controlRoomPengawas}', [PembatasanLVControlRoomPengawasController::class, 'destroy'])->name('destroy');
            });

        Route::prefix('master-data/becomeline-unit')
            ->name('master-data.becomeline-unit.')
            ->group(function (): void {
                Route::get('/data', [PembatasanLVBecomelineUnitController::class, 'data'])->name('data');
            });

        Route::prefix('master-data/aktivitas')
            ->name('master-data.aktivitas.')
            ->group(function (): void {
                Route::get('/data', [PembatasanLVMasterAktivitasController::class, 'data'])->name('data');
                Route::get('/template', [PembatasanLVMasterAktivitasController::class, 'downloadTemplate'])->name('template');
                Route::post('/import', [PembatasanLVMasterAktivitasController::class, 'import'])->name('import');
                Route::post('/', [PembatasanLVMasterAktivitasController::class, 'store'])->name('store');
                Route::get('/{masterAktivitas}', [PembatasanLVMasterAktivitasController::class, 'show'])->name('show');
                Route::put('/{masterAktivitas}', [PembatasanLVMasterAktivitasController::class, 'update'])->name('update');
                Route::delete('/{masterAktivitas}', [PembatasanLVMasterAktivitasController::class, 'destroy'])->name('destroy');
            });
    });
