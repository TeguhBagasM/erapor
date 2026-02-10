<?php

namespace App\Imports;

use App\Models\Nilai;
use App\Models\Siswa;
use App\Models\MataPelajaran;
use App\Models\Guru;
use App\Models\TahunAjaran;
use App\Services\RaporService;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class NilaiImport implements ToModel, WithHeadingRow, WithValidation
{
    private $errors = [];
    private $successCount = 0;
    private $rowNumber = 0;
    private $guruId;
    private $tahunAjaranId;
    private $raporService;

    public function __construct(int $guruId, int $tahunAjaranId, RaporService $raporService = null)
    {
        $this->guruId = $guruId;
        $this->tahunAjaranId = $tahunAjaranId;
        $this->raporService = $raporService ?? new RaporService();
    }

    /**
     * Map Excel row ke Nilai Model
     * Format: nis, kode_mapel, nilai_angka
     */
    public function model(array $row)
    {
        $this->rowNumber++;

        try {
            // Cari siswa by NIS
            $siswa = Siswa::where('nis', $row['nis'])->first();
            if (!$siswa) {
                $this->errors[] = [
                    'row' => $this->rowNumber,
                    'nis' => $row['nis'] ?? null,
                    'message' => "Siswa dengan NIS '{$row['nis']}' tidak ditemukan",
                ];
                return null;
            }

            // Cari mata pelajaran by kode_mapel
            $mataPelajaran = MataPelajaran::where('kode_mapel', $row['kode_mapel'])->first();
            if (!$mataPelajaran) {
                $this->errors[] = [
                    'row' => $this->rowNumber,
                    'nis' => $row['nis'],
                    'message' => "Mata pelajaran dengan kode '{$row['kode_mapel']}' tidak ditemukan",
                ];
                return null;
            }

            // Validasi nilai angka numeric
            $nilaiAngka = floatval($row['nilai_angka']);
            if ($nilaiAngka < 0 || $nilaiAngka > 100) {
                $this->errors[] = [
                    'row' => $this->rowNumber,
                    'nis' => $row['nis'],
                    'message' => "Nilai harus antara 0-100, diterima: {$nilaiAngka}",
                ];
                return null;
            }

            // Auto-generate nilai_huruf
            $nilaiHuruf = $this->raporService->convertNilaiToHuruf($nilaiAngka);

            // Check if nilai sudah ada
            $existingNilai = Nilai::where([
                ['siswa_id', '=', $siswa->id],
                ['mata_pelajaran_id', '=', $mataPelajaran->id],
                ['guru_id', '=', $this->guruId],
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
                'guru_id' => $this->guruId,
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
     * Validasi rules untuk setiap baris
     */
    public function rules(): array
    {
        return [
            '*.nis' => 'required|numeric',
            '*.kode_mapel' => 'required|string|max:20',
            '*.nilai_angka' => 'required|numeric|min:0|max:100',
        ];
    }

    /**
     * Custom error messages
     */
    public function customValidationMessages()
    {
        return [
            '*.nis.required' => 'NIS harus diisi',
            '*.nis.numeric' => 'NIS harus angka',
            '*.kode_mapel.required' => 'Kode mata pelajaran harus diisi',
            '*.nilai_angka.required' => 'Nilai harus diisi',
            '*.nilai_angka.numeric' => 'Nilai harus angka',
            '*.nilai_angka.min' => 'Nilai minimal 0',
            '*.nilai_angka.max' => 'Nilai maksimal 100',
        ];
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
