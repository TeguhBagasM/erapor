<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\NilaiController;
use App\Http\Controllers\Admin\JurusanController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Admin\MataPelajaranController;
use App\Http\Controllers\Admin\TahunAjaranController;
use App\Http\Controllers\WaliKelas\RaporController;
use App\Http\Controllers\WaliKelas\NilaiController as WaliKelasNilaiController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// ===== ADMIN ROUTES =====
Route::middleware(['auth', 'isAdmin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    
    // User Management
    Route::resource('users', UserController::class);
    
    // Master Data CRUD
    Route::resource('jurusans', JurusanController::class);
    Route::resource('kelas', KelasController::class);
    Route::resource('guru', GuruController::class);
    Route::resource('siswa', SiswaController::class);
    Route::resource('mata-pelajaran', MataPelajaranController::class);
    Route::resource('tahun-ajaran', TahunAjaranController::class);
    
    // Nilai Management
    Route::get('nilai/create', [NilaiController::class, 'create'])->name('nilai.create');
    Route::post('nilai', [NilaiController::class, 'store'])->name('nilai.store');
    
    // Import Management
    Route::get('import', [NilaiController::class, 'showImportForm'])->name('import.form');
    Route::post('import/siswa', [NilaiController::class, 'import'])->name('import.siswa');
    Route::post('import/nilai', [NilaiController::class, 'import'])->name('import.nilai');
    
    // Template Downloads
    Route::get('template/siswa', [NilaiController::class, 'downloadTemplateSiswa'])->name('template.siswa');
    Route::get('template/nilai', [NilaiController::class, 'downloadTemplateNilai'])->name('template.nilai');
});

// ===== WALI KELAS ROUTES =====
Route::middleware(['auth', 'role:wali_kelas'])->prefix('wali-kelas')->name('wali_kelas.')->group(function () {
    Route::get('/', [RaporController::class, 'dashboard'])->name('dashboard');
    
    // Nilai Management
    Route::get('nilai', [WaliKelasNilaiController::class, 'index'])->name('nilai.index');
    Route::post('nilai/create', [WaliKelasNilaiController::class, 'create'])->name('nilai.create');
    Route::post('nilai', [WaliKelasNilaiController::class, 'store'])->name('nilai.store');
    Route::get('nilai/{siswaId}', [WaliKelasNilaiController::class, 'show'])->name('nilai.show');
    
    // Rapor Management
    Route::get('rapor', [RaporController::class, 'listRapor'])->name('rapor.list');
    Route::get('rapor/{siswaId}/{tahunAjaranId}', [RaporController::class, 'viewRapor'])->name('rapor.view');
    Route::get('rapor/{siswaId}/{tahunAjaranId}/download', [RaporController::class, 'downloadRapor'])->name('rapor.download');
    
    // Statistik Kelas
    Route::get('statistik', [RaporController::class, 'statistikKelas'])->name('statistik');
});

// ===== USER ROUTES =====
Route::middleware(['auth'])->group(function () {
    // Tambahkan route user lainnya di sini
});
