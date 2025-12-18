<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminPerawatController;
use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\BankSoalController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\PerawatDrhController;
use App\Http\Controllers\TelegramController;
use App\Http\Controllers\ManajemenAkunController;
use App\Http\Controllers\PenanggungJawabUjianController;
use App\Http\Controllers\UserFormController;
use App\Http\Controllers\AdminPengajuanController;
use App\Http\Controllers\PengajuanSertifikatController;
use App\Http\Controllers\AdminPengajuanWawancaraController;
use App\Http\Controllers\AdminLisensiController;

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
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/perawat', [AdminPerawatController::class, 'index'])->name('perawat.index');
        Route::get('/perawat/{id}', [AdminPerawatController::class, 'show'])->name('perawat.show');
        Route::get('/perawat/{id}/edit', [AdminPerawatController::class, 'edit'])->name('perawat.edit');
        Route::put('/perawat/{id}', [AdminPerawatController::class, 'update'])->name('perawat.update');
        Route::delete('/perawat/{id}', [AdminPerawatController::class, 'destroy'])->name('perawat.destroy');
        Route::get('/perawat/{id}/sertifikat', [AdminPerawatController::class, 'sertifikat'])
            ->name('perawat.sertifikat');

        // === DOKUMEN: LISENSI ===
        Route::get('/admin/lisensi', [AdminLisensiController::class, 'lisensiIndex'])->name('lisensi.index');
        Route::get('/admin/lisensi/create', [AdminLisensiController::class, 'lisensiCreate'])->name('lisensi.create');
        Route::post('/admin/lisensi', [AdminLisensiController::class, 'lisensiStore'])->name('lisensi.store');
        Route::get('/admin/lisensi/{id}/edit', [AdminLisensiController::class, 'lisensiEdit'])->name('lisensi.edit');
        Route::put('/admin/lisensi/{id}', [AdminLisensiController::class, 'lisensiUpdate'])->name('lisensi.update');
        Route::delete('/admin/lisensi/{id}', [AdminLisensiController::class, 'lisensiDestroy'])->name('lisensi.destroy');

        // APPROVAL PENGAJUAN
        Route::get('/pengajuan', [AdminPengajuanController::class, 'index'])->name('pengajuan.index');

        // HAPUS SALAH SATU ROUTE APPROVE YANG DUPLIKAT (Hapus GET, Pertahankan POST)
        // Route::get('/pengajuan/{id}/approve', [AdminPengajuanController::class, 'approve'])->name('pengajuan.approve'); <--- INI BIANG KEROK ERRORNYA

        Route::get('/pengajuan/{id}/reject', [AdminPengajuanController::class, 'reject'])->name('pengajuan.reject');
        Route::get('/pengajuan/{id}/approve-score', [AdminPengajuanController::class, 'approveExamScore'])->name('pengajuan.approve_score');

        // POST method untuk approve (Form submit)
        Route::post('/pengajuan/{id}/approve', [AdminPengajuanController::class, 'approve'])->name('pengajuan.approve');

        Route::get('/pengajuan/{id}/complete', [AdminPengajuanController::class, 'completeProcess'])->name('pengajuan.complete');
        Route::get('/pengajuan/{id}', [AdminPengajuanController::class, 'show'])->name('pengajuan.show');
        Route::post('/pengajuan/bulk-approve', [AdminPengajuanController::class, 'bulkApprove'])->name('pengajuan.bulk_approve');
        Route::post('/pengajuan/bulk-approve-score', [AdminPengajuanController::class, 'bulkApproveScore'])->name('pengajuan.bulk_approve_score');
        Route::post('/pengajuan/bulk-approve-interview', [AdminPengajuanController::class, 'bulkApproveInterview'])->name('pengajuan.bulk_approve_interview');

        // PENGAJUAN WAWANCARA (Updated Name)
        Route::prefix('pengajuan-wawancara')->name('pengajuan_wawancara.')->group(function() {
            Route::get('/{id}/approve', [AdminPengajuanWawancaraController::class, 'approveJadwal']) ->name('approve');
            Route::post('/{id}/reject', [AdminPengajuanWawancaraController::class, 'rejectJadwal'])->name('reject');
            Route::get('/{id}/penilaian', [AdminPengajuanWawancaraController::class, 'showPenilaian'])->name('penilaian');
            Route::post('/{id}/penilaian', [AdminPengajuanWawancaraController::class, 'storePenilaian'])->name('store_penilaian');
        });

        // Verifikasi kelayakan dokumen
        Route::post('/perawat/verifikasi-kelayakan', [AdminPerawatController::class, 'verifikasiKelayakan'])->name('perawat.verifikasi.kelayakan');

        Route::get('/profile', [AdminProfileController::class, 'index'])->name('profile.index');
        Route::post('/telegram/generate', [AdminProfileController::class, 'generateCode'])->name('telegram.generate');
        Route::post('/telegram/unlink', [AdminProfileController::class, 'unlink'])->name('telegram.unlink');
        Route::post('/telegram/test', [AdminProfileController::class, 'testMessage'])->name('telegram.test');

        // === MANAJEMEN AKUN ===
        Route::get('/manajemen-akun', [ManajemenAkunController::class, 'index'])->name('manajemen_akun.index');
        Route::put('/manajemen-akun/{id}/update', [ManajemenAkunController::class, 'updateStatus'])->name('manajemen_akun.update');
        Route::delete('/manajemen-akun/{id}', [ManajemenAkunController::class, 'destroy'])->name('manajemen_akun.destroy');

        // FORM MANAGEMENT
        Route::get('/forms', [FormController::class, 'index'])->name('form.index');
        Route::get('/forms/create', [FormController::class, 'create'])->name('form.create');
        Route::post('/forms', [FormController::class, 'store'])->name('form.store');
        Route::patch('form/{form}/update-status', [FormController::class, 'updateStatus'])->name('form.update-status');

        // === PENANGGUNG JAWAB UJIAN ===
        Route::resource('penanggung-jawab', PenanggungJawabUjianController::class);
        Route::get('/forms/{form}/edit', [FormController::class, 'edit'])->name('form.edit');
        Route::put('/forms/{form}', [FormController::class, 'update'])->name('form.update');
        Route::delete('/forms/{form}', [FormController::class, 'destroy'])->name('form.destroy');

        // === KELOLA SOAL ===
        Route::get('bank-soal', [BankSoalController::class, 'index'])->name('bank-soal.index');
        Route::get('bank-soal/create', [BankSoalController::class, 'create'])->name('bank-soal.create');
        Route::post('bank-soal/store', [BankSoalController::class, 'store'])->name('bank-soal.store');
        Route::get('bank-soal/{id}/edit', [BankSoalController::class, 'edit'])->name('bank-soal.edit');
        Route::post('bank-soal/{id}/update', [BankSoalController::class, 'update'])->name('bank-soal.update');
        Route::post('bank-soal/{id}/delete', [BankSoalController::class, 'destroy'])->name('bank-soal.delete');

        // === REKAP HASIL UJIAN ===
        Route::get('/forms/{form}/hasil', [FormController::class, 'hasil'])->name('form.hasil');
        Route::delete('/hasil-ujian/{result}', [FormController::class, 'resetHasil'])->name('form.reset-hasil');

        // Menampilkan halaman pilih soal untuk form tertentu
        Route::get('/forms/{form}/kelola-soal', [FormController::class, 'kelolaSoal'])->name('form.kelola-soal');
        // Menyimpan pilihan soal ke database
        Route::post('/forms/{form}/kelola-soal', [FormController::class, 'simpanSoal'])->name('form.simpan-soal');
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

        // === DOKUMEN: LISENSI (READ ONLY) ===
        Route::get('/dokumen/lisensi', [PerawatDrhController::class, 'lisensiIndex'])->name('lisensi.index');

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

        // Link Telegram Account
        Route::get('/telegram/link', [TelegramController::class, 'linkTelegram'])->name('telegram.link');
        Route::post('/telegram/generate-code', [TelegramController::class, 'generateCode'])->name('telegram.generate-code');
        Route::post('/telegram/unlink', [TelegramController::class, 'unlinkTelegram'])->name('telegram.unlink');

        // === MENU UJIAN / FORM ===
        Route::get('/ujian-aktif', [UserFormController::class, 'index'])->name('ujian.index');
        Route::get('/ujian-aktif/{form:slug}', [UserFormController::class, 'show'])->name('ujian.show');
        Route::get('/ujian/{form:slug}/kerjakan', [UserFormController::class, 'kerjakan'])->name('ujian.kerjakan');
        Route::post('/ujian/{form:slug}/submit', [UserFormController::class, 'submit'])->name('ujian.submit');
        Route::get('/ujian/{form:slug}/selesai', [UserFormController::class, 'selesai'])->name('ujian.selesai');

        // CETAK SERTIFIKAT
        Route::get('/pengajuan/{id}/sertifikat', [PengajuanSertifikatController::class, 'printSertifikat'])->name('pengajuan.sertifikat');

        // PENGAJUAN PERPANJANGAN
        Route::get('/pengajuan', [PengajuanSertifikatController::class, 'index'])->name('pengajuan.index');
        Route::post('/pengajuan/store', [PengajuanSertifikatController::class, 'store'])->name('pengajuan.store');
        Route::post('/pengajuan/{id}/pilih-metode', [PengajuanSertifikatController::class, 'pilihMetode'])->name('pengajuan.pilih_metode');
        Route::post('/pengajuan/{id}/store-wawancara', [PengajuanSertifikatController::class, 'storeWawancara'])->name('pengajuan.store_wawancara');

        // GENERATE LISENSI PDF
        Route::get('/dokumen/lisensi/{id}/generate', [PerawatDrhController::class, 'generateLisensi'])->name('lisensi.generate');
    });

    Route::post('/webhook', [TelegramController::class, 'webhook'])->name('webhook');
});
