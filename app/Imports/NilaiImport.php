<?php

namespace App\Imports;

use App\Models\Nilai;
use App\Models\Siswa;
use App\Models\MataPelajaran;
use App\Models\Guru;
use App\Models\TahunAjaran;
use App\Services\RaporService;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;

class NilaiImport implements ToModel, WithHeadingRow, SkipsUnknownSheets
{
    private $errors = [];
    private $successCount = 0;
    private $rowNumber = 0;
    private $tahunAjaranId;
    private $raporService;

    public function __construct(int $tahunAjaranId, RaporService $raporService = null)
    {
        $this->tahunAjaranId = $tahunAjaranId;
        $this->raporService = $raporService ?? new RaporService();
    }

    /**
     * Hanya import sheet pertama, skip sheet referensi
     */
    public function onUnknownSheet($sheetName)
    {
        // Skip sheet referensi tanpa error
    }

    /**
     * Map Excel row ke Nilai Model
     * Format: nis, kode_mapel, nilai_angka
     * guru_id otomatis dari pivot kelas_mata_pelajaran
     */
    public function model(array $row)
    {
        $this->rowNumber++;

        try {
            // Validasi field wajib
            $nis = trim(strval($row['nis'] ?? ''));
            $kodeMapel = trim(strval($row['kode_mapel'] ?? ''));
            $nilaiRaw = $row['nilai_angka'] ?? '';

            // Skip baris yang sepenuhnya kosong
            if (empty($nis) && empty($kodeMapel) && ($nilaiRaw === '' || $nilaiRaw === null)) {
                return null;
            }

            if (empty($nis)) {
                $this->errors[] = ['row' => $this->rowNumber, 'nis' => null, 'message' => 'NIS harus diisi'];
                return null;
            }
            if (empty($kodeMapel)) {
                $this->errors[] = ['row' => $this->rowNumber, 'nis' => $nis, 'message' => 'Kode mapel harus diisi'];
                return null;
            }
            if ($nilaiRaw === '' || $nilaiRaw === null) {
                $this->errors[] = ['row' => $this->rowNumber, 'nis' => $nis, 'message' => 'Nilai angka harus diisi'];
                return null;
            }

            // Cari siswa by NIS
            $siswa = Siswa::where('nis', $nis)->first();
            if (!$siswa) {
                $this->errors[] = [
                    'row' => $this->rowNumber,
                    'nis' => $nis,
                    'message' => "Siswa dengan NIS '{$nis}' tidak ditemukan",
                ];
                return null;
            }

            // Cari mata pelajaran by kode_mapel (case-insensitive, trimmed)
            $mataPelajaran = MataPelajaran::whereRaw('LOWER(kode_mapel) = ?', [strtolower($kodeMapel)])->first();
            if (!$mataPelajaran) {
                $available = MataPelajaran::pluck('kode_mapel')->implode(', ');
                $this->errors[] = [
                    'row' => $this->rowNumber,
                    'nis' => $nis,
                    'message' => "Mapel '{$kodeMapel}' tidak ditemukan. Kode tersedia: {$available}",
                ];
                return null;
            }

            // Auto-resolve guru_id dari pivot kelas_mata_pelajaran
            $kelasMapel = DB::table('kelas_mata_pelajaran')
                ->where('kelas_id', $siswa->kelas_id)
                ->where('mata_pelajaran_id', $mataPelajaran->id)
                ->first();

            if (!$kelasMapel) {
                $this->errors[] = [
                    'row' => $this->rowNumber,
                    'nis' => $nis,
                    'message' => "Mapel '{$mataPelajaran->nama_mapel}' belum ditugaskan ke kelas siswa ini",
                ];
                return null;
            }

            $guruId = $kelasMapel->guru_id;

            // Validasi nilai angka numeric
            $nilaiAngka = floatval($row['nilai_angka']);
            if ($nilaiAngka < 0 || $nilaiAngka > 100) {
                $this->errors[] = [
                    'row' => $this->rowNumber,
                    'nis' => $nis,
                    'message' => "Nilai harus antara 0-100, diterima: {$nilaiAngka}",
                ];
                return null;
            }

            // Auto-generate nilai_huruf
            $nilaiHuruf = $this->raporService->convertNilaiToHuruf($nilaiAngka);

            // Check if nilai sudah ada (unique: siswa, mapel, guru, tahun_ajaran)
            $existingNilai = Nilai::where([
                ['siswa_id', '=', $siswa->id],
                ['mata_pelajaran_id', '=', $mataPelajaran->id],
                ['guru_id', '=', $guruId],
                ['tahun_ajaran_id', '=', $this->tahunAjaranId],
            ])->first();

            if ($existingNilai) {
                // Update jika sudah ada
                $existingNilai->update([
                    'nilai_angka' => $nilaiAngka,
                    'nilai_huruf' => $nilaiHuruf,
                ]);
                $this->successCount++;
                return null;
            }

            // Create nilai baru
            $this->successCount++;
            return new Nilai([
                'siswa_id' => $siswa->id,
                'mata_pelajaran_id' => $mataPelajaran->id,
                'guru_id' => $guruId,
                'tahun_ajaran_id' => $this->tahunAjaranId,
                'nilai_angka' => $nilaiAngka,
                'nilai_huruf' => $nilaiHuruf,
            ]);
        } catch (\Exception $e) {
            $this->errors[] = [
                'row' => $this->rowNumber,
                'nis' => $row['nis'] ?? null,
                'message' => $e->getMessage(),
            ];
            return null;
        }
    }

    /**
     * Get import errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get success count
     */
    public function getSuccessCount(): int
    {
        return $this->successCount;
    }
}
