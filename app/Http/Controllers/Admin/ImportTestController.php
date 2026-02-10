<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

/**
 * API Test Controller untuk testing import features
 * 
 * Digunakan untuk testing via API tanpa perlu frontend
 * 
 * Routes (tambahkan di routes/api.php atau routes/web.php):
 * 
 * Route::middleware(['auth', 'isAdmin'])->prefix('admin/test')->group(function () {
 *     Route::get('import-siswa', [ImportTestController::class, 'testSiswaImport']);
 *     Route::get('import-nilai', [ImportTestController::class, 'testNilaiImport']);
 * });
 */
class ImportTestController extends Controller
{
    /**
     * Test endpoint untuk import siswa
     * 
     * Menunjukkan:
     * 1. Download template siswa
     * 2. Format data import siswa
     * 3. Validasi yang dilakukan
     * 
     * Access: GET /admin/test/import-siswa
     */
    public function testSiswaImport()
    {
        return response()->json([
            'fitur' => 'Import Siswa',
            'endpoint' => 'POST /admin/import/siswa',
            'instructions' => [
                '1. Download template: GET /admin/template/siswa',
                '2. Isi dengan data siswa (nis, nama_siswa, kelas)',
                '3. Upload file ke endpoint di atas',
            ],
            'format_data' => [
                '''nis,nama_siswa,kelas
001,Andi Wijaya,X IPA 1
002,Budi Santoso,X IPA 1
003,Citra Dewi,X IPS 1'''
            ],
            'validasi' => [
                'nis' => 'required, numeric, unique',
                'nama_siswa' => 'required, string, max:100',
                'kelas' => 'required, harus ada di sistem'
            ],
            'response_example' => [
                'success' => true,
                'message' => 'Import siswa berhasil! 3 data berhasil diproses',
                'success_count' => 3,
                'errors' => []
            ],
            'curl_example' => 'curl -X POST http://localhost/admin/import/siswa -H "Authorization: Bearer TOKEN" -F "file=@template_siswa.xlsx" -F "tipe_import=siswa"'
        ]);
    }

    /**
     * Test endpoint untuk import nilai
     * 
     * Menunjukkan:
     * 1. Download template nilai
     * 2. Format data import nilai
     * 3. Validasi yang dilakukan
     * 4. Auto-calculation nilai_huruf
     * 
     * Access: GET /admin/test/import-nilai
     */
    public function testNilaiImport()
    {
        return response()->json([
            'fitur' => 'Import Nilai',
            'endpoint' => 'POST /admin/import/nilai',
            'instructions' => [
                '1. Download template: GET /admin/template/nilai',
                '2. Isi dengan data nilai (nis, kode_mapel, nilai_angka)',
                '3. Upload file ke endpoint di atas dengan tahun_ajaran_id',
            ],
            'format_data' => [
                '''nis,kode_mapel,nilai_angka
001,MTK101,85
001,BHS101,78
002,MTK101,76
002,BHS101,88'''
            ],
            'validasi' => [
                'nis' => 'required, numeric, harus ada di tabel siswa',
                'kode_mapel' => 'required, harus ada di tabel mata_pelajaran',
                'nilai_angka' => 'required, numeric, min:0, max:100'
            ],
            'auto_calculation' => [
                'nilai_huruf' => 'Generated otomatis dari nilai_angka',
                'skala' => [
                    'A' => '>= 85',
                    'B' => '>= 70',
                    'C' => '>= 60',
                    'D' => '>= 50',
                    'E' => '< 50'
                ]
            ],
            'response_example' => [
                'success' => true,
                'message' => 'Import nilai berhasil! 4 data berhasil diproses',
                'success_count' => 4,
                'errors' => []
            ],
            'curl_example' => 'curl -X POST http://localhost/admin/import/nilai -H "Authorization: Bearer TOKEN" -F "file=@template_nilai.xlsx" -F "tipe_import=nilai" -F "tahun_ajaran_id=1"'
        ]);
    }
}
