<?php

namespace App\Services;

use App\Imports\SiswaImport;
use App\Imports\NilaiImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class ImportService
{
    private RaporService $raporService;

    public function __construct(RaporService $raporService)
    {
        $this->raporService = $raporService;
    }

    /**
     * Import siswa dari Excel file
     */
    public function importSiswa($file): array
    {
        try {
            DB::beginTransaction();

            $import = new SiswaImport();
            Excel::import($import, $file);

            DB::commit();

            return [
                'success' => true,
                'type' => 'siswa',
                'success_count' => $import->getSuccessCount(),
                'errors' => $import->getErrors(),
                'message' => "Import siswa berhasil! {$import->getSuccessCount()} data berhasil diproses",
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'type' => 'siswa',
                'message' => 'Gagal import siswa: ' . $e->getMessage(),
                'errors' => [],
            ];
        }
    }

    /**
     * Import nilai dari Excel file
     */
    public function importNilai($file, int $guruId, int $tahunAjaranId): array
    {
        try {
            DB::beginTransaction();

            $import = new NilaiImport($guruId, $tahunAjaranId, $this->raporService);
            Excel::import($import, $file);

            DB::commit();

            return [
                'success' => true,
                'type' => 'nilai',
                'success_count' => $import->getSuccessCount(),
                'errors' => $import->getErrors(),
                'message' => "Import nilai berhasil! {$import->getSuccessCount()} data berhasil diproses",
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'type' => 'nilai',
                'message' => 'Gagal import nilai: ' . $e->getMessage(),
                'errors' => [],
            ];
        }
    }

    /**
     * Process import file (legacy method)
     */
    public function processImport($file, string $tipeImport, ?int $tahunAjaranId = null, ?int $guruId = null)
    {
        if ($tipeImport === 'siswa') {
            return $this->importSiswa($file);
        } elseif ($tipeImport === 'nilai') {
            if (!$guruId || !$tahunAjaranId) {
                return [
                    'success' => false,
                    'message' => 'guruId dan tahunAjaranId diperlukan untuk import nilai',
                ];
            }
            return $this->importNilai($file, $guruId, $tahunAjaranId);
        }

        return [
            'success' => false,
            'message' => 'Tipe import tidak dikenal: ' . $tipeImport,
        ];
    }
}
