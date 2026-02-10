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
use App\Http\Controllers\Admin\KelasMapelController;
use App\Http\Controllers\WaliKelas\RaporController;
use App\Http\Controllers\WaliKelas\NilaiController as WaliKelasNilaiController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
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

    // Kelas - Mata Pelajaran Management
    Route::get('kelas-mapel', [KelasMapelController::class, 'index'])->name('kelas-mapel.index');
    Route::get('kelas-mapel/{kelas}/edit', [KelasMapelController::class, 'edit'])->name('kelas-mapel.edit');
    Route::put('kelas-mapel/{kelas}', [KelasMapelController::class, 'update'])->name('kelas-mapel.update');

    // Nilai Management
    Route::get('nilai/create', [NilaiController::class, 'create'])->name('nilai.create');
    Route::post('nilai', [NilaiController::class, 'store'])->name('nilai.store');
    Route::post('nilai/grid', [NilaiController::class, 'storeGrid'])->name('nilai.store-grid');

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
    Route::post('nilai/grid', [WaliKelasNilaiController::class, 'storeGrid'])->name('nilai.store-grid');
    Route::get('nilai/{siswaId}', [WaliKelasNilaiController::class, 'show'])->name('nilai.show');

    // Rapor Management
    Route::get('rapor', [RaporController::class, 'listRapor'])->name('rapor.list');
    Route::get('rapor/cetak-kelas', [RaporController::class, 'cetakKelas'])->name('rapor.cetak-kelas');
    Route::get('rapor/{siswaId}/{tahunAjaranId}', [RaporController::class, 'viewRapor'])->name('rapor.view');
    Route::get('rapor/{siswaId}/{tahunAjaranId}/download', [RaporController::class, 'downloadRapor'])->name('rapor.download');

    // Statistik Kelas
    Route::get('statistik', [RaporController::class, 'statistikKelas'])->name('statistik');
});

// ===== USER ROUTES =====
Route::middleware(['auth'])->group(function () {
    // API-like route for loading siswa by kelas (used by AJAX)
    Route::get('api/kelas/{kelas}/siswa', function (\App\Models\Kelas $kelas) {
        return response()->json($kelas->siswa()->orderBy('nama_siswa')->get());
    })->name('api.kelas.siswa');

    // API: Grid data — siswa + mapel (from pivot) + existing nilai
    Route::get('api/kelas/{kelas}/grid-nilai', function (\App\Models\Kelas $kelas, \Illuminate\Http\Request $request) {
        $tahunAjaranId = $request->query('tahun_ajaran_id');
        if (!$tahunAjaranId) {
            return response()->json(['error' => 'tahun_ajaran_id required'], 422);
        }

        // Siswa in kelas
        $siswa = $kelas->siswa()->orderBy('nama_siswa')->get(['id', 'nis', 'nama_siswa']);

        // Mapel assigned to kelas (from pivot) with guru
        $mapels = $kelas->mataPelajarans()
            ->withPivot('guru_id')
            ->get()
            ->map(function ($m) {
                $guru = $m->pivot->guru_id ? \App\Models\Guru::find($m->pivot->guru_id) : null;
                return [
                    'id' => $m->id,
                    'nama_mapel' => $m->nama_mapel,
                    'guru_id' => $m->pivot->guru_id,
                    'guru_nama' => $guru ? $guru->nama_guru : '-',
                ];
            });

        // Existing nilai for this kelas + tahun ajaran, keyed by siswa_id-mapel_id
        $nilaiExisting = \App\Models\Nilai::whereIn('siswa_id', $siswa->pluck('id'))
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->whereIn('mata_pelajaran_id', $mapels->pluck('id'))
            ->get()
            ->keyBy(fn($n) => $n->siswa_id . '-' . $n->mata_pelajaran_id)
            ->map(fn($n) => ['nilai_angka' => $n->nilai_angka, 'nilai_huruf' => $n->nilai_huruf]);

        return response()->json([
            'siswa' => $siswa,
            'mapels' => $mapels->values(),
            'nilai' => $nilaiExisting,
        ]);
    })->name('api.kelas.grid-nilai');
});
