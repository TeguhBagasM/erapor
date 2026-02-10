<?php

namespace App\Exports;

use App\Models\Kelas;
use App\Models\MataPelajaran;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class SiswaTemplate implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    /**
     * Template data untuk siswa
     */
    public function array(): array
    {
        return [
            ['001', 'Andi Wijaya', 'X IPA 1'],
            ['002', 'Budi Santoso', 'X IPA 1'],
            ['003', 'Citra Dewi', 'X IPS 1'],
        ];
    }

    /**
     * Heading row
     */
    public function headings(): array
    {
        return [
            'nis',           // Nomor Induk Siswa (unik, numeric)
            'nama_siswa',    // Nama sesuai identitas
            'kelas',         // Nama kelas harus sudah ada di sistem
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
