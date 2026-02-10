<?php

namespace App\Exports;

use App\Models\Kelas;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class SiswaTemplate implements WithMultipleSheets
{
    public function sheets(): array
    {
        // Ambil kelas dari DB untuk contoh & referensi
        $kelasList = Kelas::with('jurusan')->orderBy('nama_kelas')->get();
        $firstKelas = $kelasList->first();
        $contohKelas = $firstKelas ? $firstKelas->nama_kelas : 'X IPA 1';

        return [
            // Sheet 1: Template data
            new class($contohKelas) implements FromArray, WithHeadings, WithStyles, ShouldAutoSize, WithTitle {
                private string $contohKelas;
                public function __construct(string $contohKelas) { $this->contohKelas = $contohKelas; }
                public function title(): string { return 'Template Siswa'; }
                public function headings(): array { return ['nis', 'nama_siswa', 'kelas']; }
                public function array(): array {
                    return [
                        ['001', 'Andi Wijaya', $this->contohKelas],
                        ['002', 'Budi Santoso', $this->contohKelas],
                        ['003', 'Citra Dewi', $this->contohKelas],
                    ];
                }
                public function styles(Worksheet $sheet) {
                    return [
                        1 => [
                            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '366092']],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        ],
                    ];
                }
            },

            // Sheet 2: Referensi kelas yang tersedia
            new class($kelasList) implements FromArray, WithHeadings, WithStyles, ShouldAutoSize, WithTitle {
                private $kelasList;
                public function __construct($kelasList) { $this->kelasList = $kelasList; }
                public function title(): string { return 'Referensi Kelas'; }
                public function headings(): array { return ['Nama Kelas (gunakan di kolom kelas)', 'Jurusan']; }
                public function array(): array {
                    return $this->kelasList->map(fn($k) => [
                        $k->nama_kelas,
                        $k->jurusan->nama_jurusan ?? '-',
                    ])->toArray();
                }
                public function styles(Worksheet $sheet) {
                    return [
                        1 => [
                            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '548235']],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        ],
                    ];
                }
            },
        ];
    }
}
