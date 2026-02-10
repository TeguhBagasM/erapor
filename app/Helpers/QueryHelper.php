<?php

// FILE: app/Helpers/QueryHelper.php
// Helper functions untuk common queries

namespace App\Helpers;

use App\Models\Siswa;
use App\Models\Nilai;
use App\Models\Kelas;
use Illuminate\Support\Collection;

class QueryHelper
{
    /**
     * Get siswa dengan nilai untuk kelas tertentu
     */
    public static function getSiswaWithNilai(int $kelasId, int $tahunAjaranId): Collection
    {
        return Siswa::where('kelas_id', $kelasId)
            ->with([
                'nilai' => function ($query) use ($tahunAjaranId) {
                    $query->where('tahun_ajaran_id', $tahunAjaranId)
                        ->with(['mataPelajaran', 'guru']);
                }
            ])
            ->get();
    }

    /**
     * Get nilai siswa dengan semua relasi
     */
    public static function getNilaiWithRelations(int $siswaId, int $tahunAjaranId)
    {
        return Nilai::where('siswa_id', $siswaId)
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->with(['mataPelajaran', 'guru.user', 'tahunAjaran'])
            ->get();
    }

    /**
     * Get siswa untuk wali kelas tertentu
     */
    public static function getSiswaForWaliKelas($user)
    {
        $kelasIds = $user->kelasAsWaliKelas()->pluck('id');
        
        return Siswa::whereIn('kelas_id', $kelasIds)
            ->with('kelas')
            ->get();
    }

    /**
     * Get kelas dengan detail lengkap
     */
    public static function getKelasWithDetail()
    {
        return Kelas::with(['jurusan', 'waliKelas', 'siswa'])
            ->get();
    }

    /**
     * Get nilai rata-rata siswa
     */
    public static function getRataRataSiswa(int $siswaId, int $tahunAjaranId): float
    {
        $nilai = Nilai::where('siswa_id', $siswaId)
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->avg('nilai_angka');

        return $nilai ? round($nilai, 2) : 0;
    }

    /**
     * Check apakah siswa lulus
     */
    public static function isSiswaLulus(int $siswaId, int $tahunAjaranId): bool
    {
        return self::getRataRataSiswa($siswaId, $tahunAjaranId) >= 70;
    }
}

/*
USAGE EXAMPLES:

// Di Controller
use App\Helpers\QueryHelper;

// Get siswa with nilai
$siswaNilai = QueryHelper::getSiswaWithNilai($kelasId, $tahunAjaranId);

// Get nilai with relations
$nilai = QueryHelper::getNilaiWithRelations($siswaId, $tahunAjaranId);

// Get siswa for wali kelas
$siswaNilai = QueryHelper::getSiswaForWaliKelas($user);

// Get rata-rata
$rataRata = QueryHelper::getRataRataSiswa($siswaId, $tahunAjaranId);

// Check lulus
if (QueryHelper::isSiswaLulus($siswaId, $tahunAjaranId)) {
    // siswa lulus
}
*/
