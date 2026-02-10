<?php

namespace App\Exports;

use App\Models\Siswa;
use App\Models\MataPelajaran;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class NilaiTemplate implements WithMultipleSheets
{
    public function sheets(): array
    {
        // Ambil data dari DB untuk contoh & referensi
        $siswaList = Siswa::with('kelas')->orderBy('nama_siswa')->get();
        $mapelList = MataPelajaran::orderBy('kode_mapel')->get();

        $contohNis = $siswaList->first() ? $siswaList->first()->nis : '001';
        $contohMapel = $mapelList->first() ? $mapelList->first()->kode_mapel : 'MTK101';

        return [
            // Sheet 1: Template data
            new class($contohNis, $contohMapel) implements FromArray, WithHeadings, WithStyles, ShouldAutoSize, WithTitle {
                private string $nis;
                private string $mapel;
                public function __construct(string $nis, string $mapel) {
                    $this->nis = $nis;
                    $this->mapel = $mapel;
                }
                public function title(): string { return 'Template Nilai'; }
                public function headings(): array { return ['nis', 'kode_mapel', 'nilai_angka']; }
                public function array(): array {
                    return [
                        [$this->nis, $this->mapel, 85],
                        [$this->nis, $this->mapel, 78],
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

            // Sheet 2: Referensi daftar siswa
            new class($siswaList) implements FromArray, WithHeadings, WithStyles, ShouldAutoSize, WithTitle {
                private $siswaList;
                public function __construct($siswaList) { $this->siswaList = $siswaList; }
                public function title(): string { return 'Daftar Siswa'; }
                public function headings(): array { return ['NIS (gunakan di kolom nis)', 'Nama Siswa', 'Kelas']; }
                public function array(): array {
                    return $this->siswaList->map(fn($s) => [
                        $s->nis,
                        $s->nama_siswa,
                        $s->kelas->nama_kelas ?? '-',
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

            // Sheet 3: Referensi daftar mata pelajaran
            new class($mapelList) implements FromArray, WithHeadings, WithStyles, ShouldAutoSize, WithTitle {
                private $mapelList;
                public function __construct($mapelList) { $this->mapelList = $mapelList; }
                public function title(): string { return 'Daftar Mapel'; }
                public function headings(): array { return ['Kode Mapel (gunakan di kolom kode_mapel)', 'Nama Mata Pelajaran']; }
                public function array(): array {
                    return $this->mapelList->map(fn($m) => [
                        $m->kode_mapel,
                        $m->nama_mapel,
                    ])->toArray();
                }
                public function styles(Worksheet $sheet) {
                    return [
                        1 => [
                            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'BF8F00']],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        ],
                    ];
                }
            },
        ];
    }
}
