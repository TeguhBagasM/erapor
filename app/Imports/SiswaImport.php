<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\Kelas;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;

class SiswaImport implements ToModel, WithHeadingRow, SkipsUnknownSheets
{
    private $errors = [];
    private $successCount = 0;
    private $rowNumber = 0;

    /**
     * Hanya import sheet pertama, skip sheet referensi
     */
    public function onUnknownSheet($sheetName)
    {
        // Skip sheet referensi tanpa error
    }

    /**
     * Map Excel row ke Siswa Model
     */
    public function model(array $row)
    {
        $this->rowNumber++;

        try {
            // Validasi field wajib
            $nis = trim(strval($row['nis'] ?? ''));
            $namaSiswa = trim(strval($row['nama_siswa'] ?? ''));
            $kelasNama = trim(strval($row['kelas'] ?? ''));

            // Skip baris yang sepenuhnya kosong
            if (empty($nis) && empty($namaSiswa) && empty($kelasNama)) {
                return null;
            }

            if (empty($nis)) {
                $this->errors[] = ['row' => $this->rowNumber, 'nis' => null, 'message' => 'NIS harus diisi'];
                return null;
            }
            if (empty($namaSiswa)) {
                $this->errors[] = ['row' => $this->rowNumber, 'nis' => $nis, 'message' => 'Nama siswa harus diisi'];
                return null;
            }
            if (empty($kelasNama)) {
                $this->errors[] = ['row' => $this->rowNumber, 'nis' => $nis, 'message' => 'Kelas harus diisi'];
                return null;
            }

            // Validasi kelas ada (case-insensitive, trimmed)
            $kelas = Kelas::whereRaw('LOWER(nama_kelas) = ?', [strtolower($kelasNama)])->first();
            if (!$kelas) {
                $available = Kelas::pluck('nama_kelas')->implode(', ');
                $this->errors[] = [
                    'row' => $this->rowNumber,
                    'nis' => $row['nis'] ?? null,
                    'message' => "Kelas '{$kelasNama}' tidak ditemukan. Kelas tersedia: {$available}",
                ];
                return null;
            }

            // Check jika siswa sudah ada (by NIS)
            $existingSiswa = Siswa::where('nis', $nis)->first();
            if ($existingSiswa) {
                // Update jika sudah ada
                $existingSiswa->update([
                    'nama_siswa' => $namaSiswa,
                    'kelas_id' => $kelas->id,
                ]);
                $this->successCount++;
                return null;
            }

            // Create siswa baru
            $this->successCount++;
            return new Siswa([
                'nis' => $nis,
                'nama_siswa' => $namaSiswa,
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
