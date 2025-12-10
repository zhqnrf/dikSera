<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminPerawatController;
use App\Http\Controllers\PerawatDrhController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES (Bisa diakses tanpa login)
|--------------------------------------------------------------------------
*/
Route::get('/', [LandingController::class, 'index'])->name('landing');

// Authentication
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::get('/register/perawat', [AuthController::class, 'showPerawatRegisterForm'])->name('register.perawat');
Route::post('/register/perawat', [AuthController::class, 'registerPerawat'])->name('register.perawat.process');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES (Harus Login)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // Dashboard Umum
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('dashboard/admin', [DashboardController::class, 'adminIndex'])->name('dashboard.admin');

    // === GROUP ADMIN ===
    Route::prefix('admin')->name('admin.')->group(function(){
        Route::get('/perawat', [AdminPerawatController::class, 'index'])->name('perawat.index');
        Route::get('/perawat/{id}', [AdminPerawatController::class, 'show'])->name('perawat.show');
        Route::get('/perawat/{id}/edit', [AdminPerawatController::class, 'edit'])->name('perawat.edit');
        Route::put('/perawat/{id}', [AdminPerawatController::class, 'update'])->name('perawat.update');
        Route::delete('/perawat/{id}', [AdminPerawatController::class, 'destroy'])->name('perawat.destroy');
        Route::get('/perawat/{id}/sertifikat', [AdminPerawatController::class, 'sertifikat'])
        ->name('perawat.sertifikat');
    });

    // === GROUP PERAWAT ===
    Route::prefix('perawat')->name('perawat.')->group(function () {

        // DRH Summary
        Route::get('/drh', [PerawatDrhController::class, 'index'])->name('drh');

        // Identitas
        Route::get('/drh/identitas', [PerawatDrhController::class, 'editIdentitas'])->name('identitas.edit');
        Route::post('/drh/identitas', [PerawatDrhController::class, 'updateIdentitas'])->name('identitas.update');

        // Data Lengkap
        Route::get('/drh/data-lengkap', [PerawatDrhController::class, 'showDataLengkap'])->name('data.lengkap');

        // === PENDIDIKAN ===
        Route::get('/pendidikan', [PerawatDrhController::class, 'pendidikanIndex'])->name('pendidikan.index');
        Route::get('/pendidikan/create', [PerawatDrhController::class, 'pendidikanCreate'])->name('pendidikan.create');
        Route::post('/pendidikan', [PerawatDrhController::class, 'pendidikanStore'])->name('pendidikan.store');
        Route::get('/pendidikan/{id}/edit', [PerawatDrhController::class, 'pendidikanEdit'])->name('pendidikan.edit');
        Route::put('/pendidikan/{id}', [PerawatDrhController::class, 'pendidikanUpdate'])->name('pendidikan.update');
        Route::delete('/pendidikan/{id}', [PerawatDrhController::class, 'pendidikanDestroy'])->name('pendidikan.destroy');

        // === PELATIHAN ===
        Route::get('/pelatihan', [PerawatDrhController::class, 'pelatihanIndex'])->name('pelatihan.index');
        Route::get('/pelatihan/create', [PerawatDrhController::class, 'pelatihanCreate'])->name('pelatihan.create');
        Route::post('/pelatihan', [PerawatDrhController::class, 'pelatihanStore'])->name('pelatihan.store');
        Route::get('/pelatihan/{id}/edit', [PerawatDrhController::class, 'pelatihanEdit'])->name('pelatihan.edit');
        Route::put('/pelatihan/{id}', [PerawatDrhController::class, 'pelatihanUpdate'])->name('pelatihan.update');
        Route::delete('/pelatihan/{id}', [PerawatDrhController::class, 'pelatihanDestroy'])->name('pelatihan.destroy');

        // === PEKERJAAN ===
        Route::get('/pekerjaan', [PerawatDrhController::class, 'pekerjaanIndex'])->name('pekerjaan.index');
        Route::get('/pekerjaan/create', [PerawatDrhController::class, 'pekerjaanCreate'])->name('pekerjaan.create');
        Route::post('/pekerjaan', [PerawatDrhController::class, 'pekerjaanStore'])->name('pekerjaan.store');
        Route::get('/pekerjaan/{id}/edit', [PerawatDrhController::class, 'pekerjaanEdit'])->name('pekerjaan.edit');
        Route::put('/pekerjaan/{id}', [PerawatDrhController::class, 'pekerjaanUpdate'])->name('pekerjaan.update');
        Route::delete('/pekerjaan/{id}', [PerawatDrhController::class, 'pekerjaanDestroy'])->name('pekerjaan.destroy');

        // === KELUARGA ===
        Route::get('/keluarga', [PerawatDrhController::class, 'keluargaIndex'])->name('keluarga.index');
        Route::get('/keluarga/create', [PerawatDrhController::class, 'keluargaCreate'])->name('keluarga.create');
        Route::post('/keluarga', [PerawatDrhController::class, 'keluargaStore'])->name('keluarga.store');
        Route::get('/keluarga/{id}/edit', [PerawatDrhController::class, 'keluargaEdit'])->name('keluarga.edit');
        Route::put('/keluarga/{id}', [PerawatDrhController::class, 'keluargaUpdate'])->name('keluarga.update');
        Route::delete('/keluarga/{id}', [PerawatDrhController::class, 'keluargaDestroy'])->name('keluarga.destroy');

        // === ORGANISASI ===
        Route::get('/organisasi', [PerawatDrhController::class, 'organisasiIndex'])->name('organisasi.index');
        Route::get('/organisasi/create', [PerawatDrhController::class, 'organisasiCreate'])->name('organisasi.create');
        Route::post('/organisasi', [PerawatDrhController::class, 'organisasiStore'])->name('organisasi.store');
        Route::get('/organisasi/{id}/edit', [PerawatDrhController::class, 'organisasiEdit'])->name('organisasi.edit');
        Route::put('/organisasi/{id}', [PerawatDrhController::class, 'organisasiUpdate'])->name('organisasi.update');
        Route::delete('/organisasi/{id}', [PerawatDrhController::class, 'organisasiDestroy'])->name('organisasi.destroy');

        // === TANDA JASA ===
        Route::get('/tanda-jasa', [PerawatDrhController::class, 'tandajasaIndex'])->name('tandajasa.index');
        Route::get('/tanda-jasa/create', [PerawatDrhController::class, 'tandajasaCreate'])->name('tandajasa.create');
        Route::post('/tanda-jasa', [PerawatDrhController::class, 'tandajasaStore'])->name('tandajasa.store');
        Route::get('/tanda-jasa/{id}/edit', [PerawatDrhController::class, 'tandajasaEdit'])->name('tandajasa.edit');
        Route::put('/tanda-jasa/{id}', [PerawatDrhController::class, 'tandajasaUpdate'])->name('tandajasa.update');
        Route::delete('/tanda-jasa/{id}', [PerawatDrhController::class, 'tandajasaDestroy'])->name('tandajasa.destroy');

        // === DOKUMEN: LISENSI ===
        Route::get('/dokumen/lisensi', [PerawatDrhController::class, 'lisensiIndex'])->name('lisensi.index');
        Route::get('/dokumen/lisensi/create', [PerawatDrhController::class, 'lisensiCreate'])->name('lisensi.create');
        Route::post('/dokumen/lisensi', [PerawatDrhController::class, 'lisensiStore'])->name('lisensi.store');
        Route::get('/dokumen/lisensi/{id}/edit', [PerawatDrhController::class, 'lisensiEdit'])->name('lisensi.edit');
        Route::put('/dokumen/lisensi/{id}', [PerawatDrhController::class, 'lisensiUpdate'])->name('lisensi.update');
        Route::delete('/dokumen/lisensi/{id}', [PerawatDrhController::class, 'lisensiDestroy'])->name('lisensi.destroy');

        // === DOKUMEN: STR ===
        Route::get('/dokumen/str', [PerawatDrhController::class, 'strIndex'])->name('str.index');
        Route::get('/dokumen/str/create', [PerawatDrhController::class, 'strCreate'])->name('str.create');
        Route::post('/dokumen/str', [PerawatDrhController::class, 'strStore'])->name('str.store');
        Route::get('/dokumen/str/{id}/edit', [PerawatDrhController::class, 'strEdit'])->name('str.edit');
        Route::put('/dokumen/str/{id}', [PerawatDrhController::class, 'strUpdate'])->name('str.update');
        Route::delete('/dokumen/str/{id}', [PerawatDrhController::class, 'strDestroy'])->name('str.destroy');

        // === DOKUMEN: SIP ===
        Route::get('/dokumen/sip', [PerawatDrhController::class, 'sipIndex'])->name('sip.index');
        Route::get('/dokumen/sip/create', [PerawatDrhController::class, 'sipCreate'])->name('sip.create');
        Route::post('/dokumen/sip', [PerawatDrhController::class, 'sipStore'])->name('sip.store');
        Route::get('/dokumen/sip/{id}/edit', [PerawatDrhController::class, 'sipEdit'])->name('sip.edit');
        Route::put('/dokumen/sip/{id}', [PerawatDrhController::class, 'sipUpdate'])->name('sip.update');
        Route::delete('/dokumen/sip/{id}', [PerawatDrhController::class, 'sipDestroy'])->name('sip.destroy');

        // === DOKUMEN: TAMBAHAN (DATA TAMBAHAN) ===
        Route::get('/dokumen/tambahan', [PerawatDrhController::class, 'tambahanIndex'])->name('tambahan.index');
        Route::get('/dokumen/tambahan/create', [PerawatDrhController::class, 'tambahanCreate'])->name('tambahan.create');
        Route::post('/dokumen/tambahan', [PerawatDrhController::class, 'tambahanStore'])->name('tambahan.store');
        Route::get('/dokumen/tambahan/{id}/edit', [PerawatDrhController::class, 'tambahanEdit'])->name('tambahan.edit');
        Route::put('/dokumen/tambahan/{id}', [PerawatDrhController::class, 'tambahanUpdate'])->name('tambahan.update');
        Route::delete('/dokumen/tambahan/{id}', [PerawatDrhController::class, 'tambahanDestroy'])->name('tambahan.destroy');
    });

});
