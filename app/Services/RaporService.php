<?php

namespace App\Services;

use App\Models\Siswa;
use App\Models\Nilai;
use App\Models\TahunAjaran;
use Exception;

class RaporService
{
    /**
     * Konversi nilai angka ke huruf (A/B/C/D/E)
     */
    public function convertNilaiToHuruf(float $nilaiAngka): string
    {
        $this->validateNilaiRange($nilaiAngka);

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

    /**
     * Validasi rentang nilai (0-100)
     */
    public function validateNilaiRange(float $nilaiAngka): bool
    {
        if ($nilaiAngka < 0 || $nilaiAngka > 100) {
            throw new Exception("Nilai harus berada di antara 0 dan 100, diterima: {$nilaiAngka}");
        }
        return true;
    }

    /**
     * Rekap nilai per siswa untuk satu tahun ajaran
     */
    public function rekapNilaiSiswa(int $siswaId, int $tahunAjaranId): array
    {
        $siswa = Siswa::with('kelas.jurusan')->findOrFail($siswaId);
        $tahunAjaran = TahunAjaran::findOrFail($tahunAjaranId);

        $nilai = Nilai::where('siswa_id', $siswaId)
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->with(['mataPelajaran', 'guru.user'])
            ->get();

        // Hitung statistik
        $nilaiAngkaList = $nilai->pluck('nilai_angka');
        $rataRata = $nilaiAngkaList->count() > 0 ? $nilaiAngkaList->avg() : 0;
        $nilaiTertinggi = $nilaiAngkaList->count() > 0 ? $nilaiAngkaList->max() : 0;
        $nilaiTerendah = $nilaiAngkaList->count() > 0 ? $nilaiAngkaList->min() : 0;

        // Format nilai dengan detail
        $nilaiDetail = $nilai->map(function ($item) {
            return [
                'mata_pelajaran_id' => $item->mata_pelajaran_id,
                'mata_pelajaran' => $item->mataPelajaran->nama_mapel,
                'guru_id' => $item->guru_id,
                'guru' => $item->guru->nama_guru,
                'nilai_angka' => floatval($item->nilai_angka),
                'nilai_huruf' => $item->nilai_huruf,
                'predikat' => $this->getPredikat(floatval($item->nilai_angka)),
            ];
        })->values()->all();

        return [
            'siswa_id' => $siswa->id,
            'siswa' => [
                'nis' => $siswa->nis,
                'nama' => $siswa->nama_siswa,
                'kelas_id' => $siswa->kelas_id,
                'kelas' => $siswa->kelas->nama_kelas,
                'jurusan' => $siswa->kelas->jurusan->nama_jurusan,
            ],
            'tahun_ajaran_id' => $tahunAjaran->id,
            'tahun_ajaran' => $tahunAjaran->tahun_ajaran,
            'semester' => $tahunAjaran->semester,
            'nilai' => $nilaiDetail,
            'statistik' => [
                'total_mapel' => $nilaiAngkaList->count(),
                'rata_rata' => round($rataRata, 2),
                'nilai_tertinggi' => round($nilaiTertinggi, 2),
                'nilai_terendah' => round($nilaiTerendah, 2),
                'lulus' => $rataRata >= 70,
            ],
        ];
    }

    /**
     * Generate data rapor per semester untuk satu siswa
     */
    public function generateRaporSemester(int $siswaId, int $tahunAjaranId): array
    {
        $rekap = $this->rekapNilaiSiswa($siswaId, $tahunAjaranId);
        $rataRata = $rekap['statistik']['rata_rata'];

        // Format untuk display rapor
        $nilaiFormatted = array_map(function ($nilai) {
            return [
                'mata_pelajaran' => $nilai['mata_pelajaran'],
                'guru' => $nilai['guru'],
                'nilai_angka' => $nilai['nilai_angka'],
                'nilai_huruf' => $nilai['nilai_huruf'],
                'predikat' => $nilai['predikat'],
            ];
        }, $rekap['nilai']);

        return [
            'no_induk' => $rekap['siswa']['nis'],
            'nama_siswa' => $rekap['siswa']['nama'],
            'kelas' => $rekap['siswa']['kelas'],
            'jurusan' => $rekap['siswa']['jurusan'],
            'tahun_ajaran' => $rekap['tahun_ajaran'],
            'semester' => $rekap['semester'],
            'nilai' => $nilaiFormatted,
            'nilai_akhir' => [
                'total_mapel' => $rekap['statistik']['total_mapel'],
                'rata_rata' => $rekap['statistik']['rata_rata'],
                'nilai_huruf_akhir' => $this->convertNilaiToHuruf($rataRata),
                'predikat_akhir' => $this->getPredikat($rataRata),
                'status_kelulusan' => $rekap['statistik']['lulus'] ? 'LULUS' : 'TIDAK LULUS',
            ],
            'tanggal_cetak' => date('d-m-Y'),
        ];
    }

    /**
     * Get rapor data untuk dashboard/view (backward compatibility)
     */
    public function getRaporData(int $siswaId, int $tahunAjaranId): array
    {
        $rekap = $this->rekapNilaiSiswa($siswaId, $tahunAjaranId);

        return [
            'siswa' => Siswa::with('kelas.jurusan')->find($siswaId),
            'tahun_ajaran' => TahunAjaran::find($tahunAjaranId),
            'nilai' => Nilai::where('siswa_id', $siswaId)
                ->where('tahun_ajaran_id', $tahunAjaranId)
                ->with(['mataPelajaran', 'guru.user'])
                ->get(),
            'rata_rata' => $rekap['statistik']['rata_rata'],
            'total_mapel' => $rekap['statistik']['total_mapel'],
            'lulus' => $rekap['statistik']['lulus'],
        ];
    }

    /**
     * Get predikat dari nilai angka (Sangat Baik, Baik, Cukup, Kurang, Sangat Kurang)
     */
    public function getPredikat(float $nilaiAngka): string
    {
        $this->validateNilaiRange($nilaiAngka);

        if ($nilaiAngka >= 85) {
            return 'Sangat Baik';
        } elseif ($nilaiAngka >= 75) {
            return 'Baik';
        } elseif ($nilaiAngka >= 65) {
            return 'Cukup';
        } elseif ($nilaiAngka >= 55) {
            return 'Kurang';
        } else {
            return 'Sangat Kurang';
        }
    }

    /**
     * Export rapor sebagai array untuk PDF/printing
     */
    public function exportRaporData(int $siswaId, int $tahunAjaranId): array
    {
        return $this->generateRaporSemester($siswaId, $tahunAjaranId);
    }
}
