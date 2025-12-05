<?php

use App\Http\Controllers\AdminPerawatController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LandingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PerawatDrhController;
// Landing page
Route::get('/', [LandingController::class, 'index'])->name('landing');

// Login page
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::get('/register/perawat', [AuthController::class, 'showPerawatRegisterForm'])->name('register.perawat');
Route::post('/register/perawat', [AuthController::class, 'registerPerawat'])->name('register.perawat.process');

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
// ADMIN – daftar perawat
Route::get('/admin/perawat', [AdminPerawatController::class, 'index'])
    ->name('admin.perawat.index');

// ADMIN – detail per perawat
Route::get('/admin/perawat/{id}', [AdminPerawatController::class, 'show'])
    ->name('admin.perawat.show');

Route::prefix('perawat')->name('perawat.')->group(function () {

    // DRH summary
    Route::get('/drh', [PerawatDrhController::class, 'index'])->name('drh');

    // IDENTITAS
    Route::get('/drh/identitas', [PerawatDrhController::class, 'editIdentitas'])->name('identitas.edit');
    Route::post('/drh/identitas', [PerawatDrhController::class, 'updateIdentitas'])->name('identitas.update');

    // PENDIDIKAN
    Route::get('/pendidikan', [PerawatDrhController::class, 'pendidikanIndex'])->name('pendidikan.index');
    Route::post('/pendidikan', [PerawatDrhController::class, 'pendidikanStore'])->name('pendidikan.store');
    Route::post('/pendidikan/{id}', [PerawatDrhController::class, 'pendidikanUpdate'])->name('pendidikan.update');
    Route::delete('/pendidikan/{id}', [PerawatDrhController::class, 'pendidikanDestroy'])->name('pendidikan.destroy');

   // === PELATIHAN ===
    Route::get('/pelatihan', [PerawatDrhController::class, 'pelatihanIndex'])->name('pelatihan.index');
    Route::get('/pelatihan/create', [PerawatDrhController::class, 'pelatihanCreate'])->name('pelatihan.create'); // Create
    Route::post('/pelatihan', [PerawatDrhController::class, 'pelatihanStore'])->name('pelatihan.store');
    Route::get('/pelatihan/{id}/edit', [PerawatDrhController::class, 'pelatihanEdit'])->name('pelatihan.edit'); // Edit
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

    // TANDA JASA
    Route::get('/tanda-jasa', [PerawatDrhController::class, 'tandajasaIndex'])->name('tandajasa.index');
    Route::post('/tanda-jasa', [PerawatDrhController::class, 'tandajasaStore'])->name('tandajasa.store');
    Route::post('/tanda-jasa/{id}', [PerawatDrhController::class, 'tandajasaUpdate'])->name('tandajasa.update');
    Route::delete('/tanda-jasa/{id}', [PerawatDrhController::class, 'tandajasaDestroy'])->name('tandajasa.destroy');
});
