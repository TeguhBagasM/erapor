<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class NilaiTemplate implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    /**
     * Template data untuk nilai
     */
    public function array(): array
    {
        return [
            ['001', 'MTK101', 85],        // nis, kode_mapel, nilai_angka
            ['001', 'BHS101', 78],
            ['001', 'IPA101', 92],
            ['002', 'MTK101', 76],
            ['002', 'BHS101', 88],
            ['002', 'IPA101', 81],
        ];
    }

    /**
     * Heading row
     */
    public function headings(): array
    {
        return [
            'nis',           // Nomor Induk Siswa (harus ada di tabel siswa)
            'kode_mapel',    // Kode mata pelajaran (harus ada di tabel mata_pelajaran)
            'nilai_angka',   // Nilai numeric (0-100), huruf digenerate otomatis
        ];
    }

    /**
     * Style template
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Header row styling
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '366092'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }
}
