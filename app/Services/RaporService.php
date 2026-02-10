<?php

namespace App\Services;

use App\Models\Siswa;
use App\Models\Nilai;
use App\Models\TahunAjaran;

class RaporService
{
    /**
     * Get rapor data untuk satu siswa di tahun ajaran tertentu
     */
    public function getRaporData(int $siswaId, int $tahunAjaranId): array
    {
        $siswa = Siswa::with('kelas.jurusan')->findOrFail($siswaId);
        $tahunAjaran = TahunAjaran::findOrFail($tahunAjaranId);

        $nilai = Nilai::where('siswa_id', $siswaId)
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->with(['mataPelajaran', 'guru.user'])
            ->get();

        $rataRata = $nilai->count() > 0 ? $nilai->avg('nilai_angka') : 0;

        return [
            'siswa' => $siswa,
            'tahun_ajaran' => $tahunAjaran,
            'nilai' => $nilai,
            'rata_rata' => round($rataRata, 2),
            'total_mapel' => $nilai->count(),
            'lulus' => $rataRata >= 70,
        ];
    }

    /**
     * Get predikat dari nilai angka
     */
    public function getPredikat(float $nilaiAngka): string
    {
        if ($nilaiAngka >= 85) return 'Sangat Baik';
        if ($nilaiAngka >= 75) return 'Baik';
        if ($nilaiAngka >= 65) return 'Cukup';
        if ($nilaiAngka >= 55) return 'Kurang';
        return 'Sangat Kurang';
    }

    /**
     * Export rapor sebagai array untuk PDF
     */
    public function exportRaporData(int $siswaId, int $tahunAjaranId): array
    {
        $rapor = $this->getRaporData($siswaId, $tahunAjaranId);

        $nilaiWithPredikat = $rapor['nilai']->map(function ($nilai) {
            return [
                'mata_pelajaran' => $nilai->mataPelajaran->nama_mapel,
                'guru' => $nilai->guru->nama_guru,
                'nilai_angka' => $nilai->nilai_angka,
                'nilai_huruf' => $nilai->nilai_huruf,
                'predikat' => $this->getPredikat($nilai->nilai_angka),
            ];
        });

        return [
            'siswa' => [
                'nis' => $rapor['siswa']->nis,
                'nama' => $rapor['siswa']->nama_siswa,
                'kelas' => $rapor['siswa']->kelas->nama_kelas,
                'jurusan' => $rapor['siswa']->kelas->jurusan->nama_jurusan,
            ],
            'tahun_ajaran' => $rapor['tahun_ajaran']->tahun_ajaran,
            'semester' => $rapor['tahun_ajaran']->semester,
            'nilai' => $nilaiWithPredikat,
            'statistik' => [
                'rata_rata' => $rapor['rata_rata'],
                'total_mapel' => $rapor['total_mapel'],
                'lulus' => $rapor['lulus'] ? 'LULUS' : 'TIDAK LULUS',
                'predikat_akhir' => $this->getPredikat($rapor['rata_rata']),
            ],
        ];
    }
}
