<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Guru;

class KelasMapelSeeder extends Seeder
{
    public function run()
    {
        $allMapels = MataPelajaran::all();
        $allKelas = Kelas::all();
        $allGuru = Guru::all();

        // Semua kelas mendapat semua mata pelajaran
        // Guru didistribusikan secara round-robin
        foreach ($allKelas as $kelas) {
            foreach ($allMapels as $i => $mapel) {
                $guru = $allGuru[$i % $allGuru->count()] ?? null;

                DB::table('kelas_mata_pelajaran')->insert([
                    'kelas_id' => $kelas->id,
                    'mata_pelajaran_id' => $mapel->id,
                    'guru_id' => $guru?->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
