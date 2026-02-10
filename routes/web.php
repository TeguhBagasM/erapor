<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\NilaiController;
use App\Http\Controllers\WaliKelas\RaporController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// ===== ADMIN ROUTES =====
// Akses master data, user management, dan semua fitur
Route::middleware(['auth', 'isAdmin'])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');
    
    // User Management
    Route::resource('users', UserController::class);
    
    // Nilai Management
    Route::get('nilai/create', [NilaiController::class, 'create'])->name('nilai.create');
    Route::post('nilai', [NilaiController::class, 'store'])->name('nilai.store');
    Route::get('nilai/import', [NilaiController::class, 'showImportForm'])->name('nilai.import-form');
    Route::post('nilai/import', [NilaiController::class, 'import'])->name('nilai.import');
});

// ===== WALI KELAS ROUTES =====
// Akses input nilai dan rapor untuk kelas mereka saja
Route::middleware(['auth', 'role:wali_kelas'])->prefix('wali-kelas')->group(function () {
    Route::get('/', [RaporController::class, 'dashboard'])->name('wali_kelas.dashboard');
    
    // Rapor Management
    Route::get('rapor', [RaporController::class, 'listRapor'])->name('wali_kelas.rapor.list');
    Route::get('rapor/{siswaId}/{tahunAjaranId}', [RaporController::class, 'viewRapor'])->name('wali_kelas.rapor.view');
    Route::get('rapor/{siswaId}/{tahunAjaranId}/download', [RaporController::class, 'downloadRapor'])->name('wali_kelas.rapor.download');
    
    // Statistik Kelas
    Route::get('statistik', [RaporController::class, 'statistikKelas'])->name('wali_kelas.statistik');
});

// ===== USER ROUTES =====
// Authenticated users
Route::middleware(['auth'])->group(function () {
    // Tambahkan route user lainnya di sini
});
