<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MataPelajaran;

class MataPelajaranSeeder extends Seeder
{
    public function run()
    {
        MataPelajaran::create(['kode_mapel' => 'MTK', 'nama_mapel' => 'Matematika']);
        MataPelajaran::create(['kode_mapel' => 'BHS', 'nama_mapel' => 'Bahasa Indonesia']);
        MataPelajaran::create(['kode_mapel' => 'ENG', 'nama_mapel' => 'Bahasa Inggris']);
        MataPelajaran::create(['kode_mapel' => 'PKN', 'nama_mapel' => 'Pendidikan Kewarganegaraan']);
        MataPelajaran::create(['kode_mapel' => 'IPA', 'nama_mapel' => 'Ilmu Pengetahuan Alam']);
        MataPelajaran::create(['kode_mapel' => 'IPS', 'nama_mapel' => 'Ilmu Pengetahuan Sosial']);
    }
}
