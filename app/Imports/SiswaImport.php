<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\Kelas;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;

class SiswaImport implements ToModel, WithHeadingRow, WithValidation
{
    private $errors = [];
    private $successCount = 0;
    private $rowNumber = 0;

    /**
     * Map Excel row ke Siswa Model
     */
    public function model(array $row)
    {
        $this->rowNumber++;

        try {
            // Validasi kelas ada
            $kelas = Kelas::where('nama_kelas', $row['kelas'])->first();
            if (!$kelas) {
                $this->errors[] = [
                    'row' => $this->rowNumber,
                    'nis' => $row['nis'] ?? null,
                    'message' => "Kelas '{$row['kelas']}' tidak ditemukan",
                ];
                return null;
            }

            // Check jika siswa sudah ada (by NIS)
            $existingSiswa = Siswa::where('nis', $row['nis'])->first();
            if ($existingSiswa) {
                // Update jika sudah ada
                $existingSiswa->update([
                    'nama_siswa' => $row['nama_siswa'],
                    'kelas_id' => $kelas->id,
                ]);
                $this->successCount++;
                return null;
            }

            // Create siswa baru
            $this->successCount++;
            return new Siswa([
                'nis' => $row['nis'],
                'nama_siswa' => $row['nama_siswa'],
                'kelas_id' => $kelas->id,
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
            '*.nis' => [
                'required',
                'numeric',
            ],
            '*.nama_siswa' => 'required|string|max:100',
            '*.kelas' => 'required|string|max:50',
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
            '*.nama_siswa.required' => 'Nama siswa harus diisi',
            '*.nama_siswa.max' => 'Nama siswa maksimal 100 karakter',
            '*.kelas.required' => 'Kelas harus diisi',
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
