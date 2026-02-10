<?php

/**
 * ============================================================================
 * Import Features - API Documentation & Usage Guide
 * ============================================================================
 * 
 * System menggunakan maatwebsite/excel untuk import Excel dengan validasi otomatis
 * 
 * FITUR:
 * 1. Import Siswa dari Excel
 * 2. Import Nilai dari Excel  
 * 3. Download Template Excel
 * 
 * ============================================================================
 * 1. DOWNLOAD TEMPLATE SISWA
 * ============================================================================
 * 
 * URL: GET /admin/template/siswa
 * Method: GET
 * File: template_siswa.xlsx
 * 
 * Columns:
 * - nis: Nomor Induk Siswa (numeric, unik)
 * - nama_siswa: Nama sesuai identitas (string, max 100)
 * - kelas: Nama kelas (harus sudah ada di sistem)
 * 
 * Contoh data:
 * nis | nama_siswa | kelas
 * ----|------------|---------
 * 001 | Andi Wijaya | X IPA 1
 * 002 | Budi Santoso | X IPA 1
 * 003 | Citra Dewi | X IPS 1
 * 
 * ============================================================================
 * 2. IMPORT SISWA
 * ============================================================================
 * 
 * URL: POST /admin/import/siswa
 * Method: POST
 * Content-Type: multipart/form-data
 * 
 * Parameters:
 * - file: File Excel/CSV
 * - tipe_import: "siswa" (required)
 * 
 * Example cURL:
 * 
 * curl -X POST http://localhost/admin/import/siswa \
 *   -H "Authorization: Bearer YOUR_TOKEN" \
 *   -F "file=@template_siswa.xlsx" \
 *   -F "tipe_import=siswa"
 * 
 * Response Success:
 * {
 *   "success": true,
 *   "message": "Import siswa berhasil! 3 data berhasil diproses",
 *   "success_count": 3,
 *   "errors": []
 * }
 * 
 * Response Error dengan validasi gagal:
 * {
 *   "success": false,
 *   "message": "Import siswa berhasil! 2 data berhasil diproses",
 *   "success_count": 2,
 *   "errors": [
 *     {
 *       "row": 2,
 *       "nis": "002",
 *       "message": "Kelas 'X IPA 99' tidak ditemukan"
 *     }
 *   ]
 * }
 * 
 * Validasi:
 * - NIS harus diisi dan numeric
 * - Nama siswa harus diisi (max 100 karakter)
 * - Kelas harus ada di sistem
 * - NIS akan di-update jika sudah ada
 * 
 * ============================================================================
 * 3. DOWNLOAD TEMPLATE NILAI
 * ============================================================================
 * 
 * URL: GET /admin/template/nilai
 * Method: GET
 * File: template_nilai.xlsx
 * 
 * Columns:
 * - nis: Nomor Induk Siswa (numeric, harus ada di tabel siswa)
 * - kode_mapel: Kode mata pelajaran (string, harus ada di sistem)
 * - nilai_angka: Nilai numeric (0-100), nilai_huruf digenerate otomatis
 * 
 * Contoh data:
 * nis | kode_mapel | nilai_angka
 * ----|------------|------------
 * 001 | MTK101     | 85
 * 001 | BHS101     | 78
 * 001 | IPA101     | 92
 * 002 | MTK101     | 76
 * 
 * Kode Mapel contoh:
 * - MTK101: Matematika
 * - BHS101: Bahasa Indonesia
 * - IPA101: IPA
 * - ENG101: Bahasa Inggris
 * 
 * ============================================================================
 * 4. IMPORT NILAI
 * ============================================================================
 * 
 * URL: POST /admin/import/nilai
 * Method: POST
 * Content-Type: multipart/form-data
 * 
 * Parameters:
 * - file: File Excel/CSV
 * - tipe_import: "nilai" (required)
 * - tahun_ajaran_id: ID tahun ajaran (required)
 * 
 * Example cURL:
 * 
 * curl -X POST http://localhost/admin/import/nilai \
 *   -H "Authorization: Bearer YOUR_TOKEN" \
 *   -F "file=@template_nilai.xlsx" \
 *   -F "tipe_import=nilai" \
 *   -F "tahun_ajaran_id=1"
 * 
 * Response Success:
 * {
 *   "success": true,
 *   "message": "Import nilai berhasil! 6 data berhasil diproses",
 *   "success_count": 6,
 *   "errors": []
 * }
 * 
 * Response dengan error:
 * {
 *   "success": false,
 *   "message": "Import nilai berhasil! 5 data berhasil diproses",
 *   "success_count": 5,
 *   "errors": [
 *     {
 *       "row": 3,
 *       "nis": "001",
 *       "message": "Siswa dengan NIS '999' tidak ditemukan"
 *     },
 *     {
 *       "row": 4,
 *       "nis": "002",
 *       "message": "Mata pelajaran dengan kode 'XXX101' tidak ditemukan"
 *     },
 *     {
 *       "row": 5,
 *       "nis": "003",
 *       "message": "Nilai harus antara 0-100, diterima: 150"
 *     }
 *   ]
 * }
 * 
 * Validasi:
 * - NIS harus ada di tabel siswa
 * - Kode mapel harus ada di tabel mata_pelajaran
 * - Nilai harus numeric dan between 0-100
 * - Nilai_huruf digenerate otomatis:
 *   * A: >= 85
 *   * B: >= 70
 *   * C: >= 60
 *   * D: >= 50
 *   * E: < 50
 * - Nilai akan di-update jika sudah ada untuk siswa+mapel+guru+tahun_ajaran
 * - Guru diambil dari user yang sedang login
 * 
 * ============================================================================
 * FLOW PENGGUNAAN
 * ============================================================================
 * 
 * 1. Download template
 *    GET /admin/template/siswa
 *    GET /admin/template/nilai
 * 
 * 2. Isi template dengan data
 * 
 * 3. Upload file
 *    POST /admin/import/siswa
 *    POST /admin/import/nilai
 * 
 * 4. Cek response, jika ada error, perbaiki di Excel dan upload ulang
 * 
 * ============================================================================
 * TEKNOLOGI
 * ============================================================================
 * 
 * - maatwebsite/excel: Library untuk parsing dan export Excel
 * - Validasi di Import class: SiswaImport, NilaiImport
 * - Service layer: ImportService untuk orchestration
 * - Auto-calculation: RaporService::convertNilaiToHuruf() untuk nilai_huruf
 * - Transaction-safe: DB::beginTransaction() untuk rollback on error
 * 
 * ============================================================================
 * ERROR HANDLING
 * ============================================================================
 * 
 * Per-row error handling:
 * - Jika ada error di row tertentu, row tersebut di-skip
 * - Data yang valid tetap disimpan
 * - Success count = row yang berhasil (+update)
 * - Errors array menunjukkan row mana yang error dan alasannya
 * 
 * Transaction rollback:
 * - Jika ada exception di luar per-row handling
 * - Seluruh transaksi di-rollback (tidak ada data yang disimpan)
 * - Response error 500 dengan pesan error
 * 
 */

// ============================================================================
// CLASS REFERENCES
// ============================================================================

// App\Imports\SiswaImport
// - Implements: ToModel, WithHeadingRow, WithValidation
// - Methods: model(), rules(), customValidationMessages(), getErrors(), getSuccessCount()

// App\Imports\NilaiImport  
// - Implements: ToModel, WithHeadingRow, WithValidation
// - Constructor: __construct(int $guruId, int $tahunAjaranId, RaporService $raporService)
// - Methods: model(), rules(), customValidationMessages(), getErrors(), getSuccessCount()

// App\Exports\SiswaTemplate
// - Implements: FromArray, WithHeadings, WithStyles, ShouldAutoSize
// - Methods: array(), headings(), styles()

// App\Exports\NilaiTemplate
// - Implements: FromArray, WithHeadings, WithStyles, ShouldAutoSize
// - Methods: array(), headings(), styles()

// App\Services\ImportService
// - Methods: importSiswa(), importNilai(), processImport() [legacy]

// ============================================================================
