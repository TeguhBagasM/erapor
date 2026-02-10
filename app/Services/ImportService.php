<?php

namespace App\Services;

/**
 * Service untuk handle import file
 * Siap untuk diperluas dengan logic parsing Excel
 */
class ImportService
{
    /**
     * Process import file
     * TODO: Implement dengan PhpSpreadsheet atau Laravel Excel
     */
    public function processImport($file, string $tipeImport, ?int $tahunAjaranId = null)
    {
        // Placeholder untuk future implementation
        // Bisa gunakan Laravel Excel atau PhpSpreadsheet untuk parsing
        
        return [
            'success' => true,
            'message' => 'Import file siap diproses',
            'filename' => $file->getClientOriginalName(),
        ];
    }
}
