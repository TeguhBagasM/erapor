<?php

namespace App\Services;

use App\Models\Nilai;
use App\Models\Siswa;
use Illuminate\Support\Facades\DB;

class NilaiService
{
    /**
     * Store bulk nilai
     */
    public function storeBulkNilai(array $data): array
    {
        try {
            DB::beginTransaction();

            $successCount = 0;
            $failedCount = 0;
            $errors = [];

            foreach ($data['nilai'] as $index => $nilaiData) {
                try {
                    $nilaiData['mata_pelajaran_id'] = $data['mata_pelajaran_id'];
                    $nilaiData['guru_id'] = $data['guru_id'];
                    $nilaiData['tahun_ajaran_id'] = $data['tahun_ajaran_id'];

                    Nilai::updateOrCreate(
                        [
                            'siswa_id' => $nilaiData['siswa_id'],
                            'mata_pelajaran_id' => $data['mata_pelajaran_id'],
                            'guru_id' => $data['guru_id'],
                            'tahun_ajaran_id' => $data['tahun_ajaran_id'],
                        ],
                        [
                            'nilai_angka' => $nilaiData['nilai_angka'],
                            'nilai_huruf' => $nilaiData['nilai_huruf'],
                        ]
                    );

                    $successCount++;
                } catch (\Exception $e) {
                    $failedCount++;
                    $errors[] = [
                        'row' => $index + 1,
                        'message' => $e->getMessage(),
                    ];
                }
            }

            DB::commit();

            return [
                'success' => true,
                'successCount' => $successCount,
                'failedCount' => $failedCount,
                'errors' => $errors,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get nilai by kelas and tahun ajaran
     */
    public function getNilaiByKelasAndTahun($kelasId, $tahunAjaranId)
    {
        return Siswa::where('kelas_id', $kelasId)
            ->with([
                'nilai' => function ($query) use ($tahunAjaranId) {
                    $query->where('tahun_ajaran_id', $tahunAjaranId)
                        ->with(['mataPelajaran', 'guru']);
                },
            ])
            ->get();
    }

    /**
     * Convert nilai angka to huruf
     */
    public function getNilaiHuruf(float $nilaiAngka): string
    {
        if ($nilaiAngka >= 85) {
            return 'A';
        } elseif ($nilaiAngka >= 70) {
            return 'B';
        } elseif ($nilaiAngka >= 60) {
            return 'C';
        } elseif ($nilaiAngka >= 50) {
            return 'D';
        } else {
            return 'E';
        }
    }
}
