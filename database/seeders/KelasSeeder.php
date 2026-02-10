<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kelas;

class KelasSeeder extends Seeder
{
    public function run()
    {
        // wali_kelas_id merujuk ke user_id (bukan guru_id)
        // User id 2 = Budi, 3 = Siti, 4 = Ahmad (dari GuruSeeder)
        Kelas::create(['nama_kelas' => 'X TI 1', 'jurusan_id' => 1, 'wali_kelas_id' => 2]);
        Kelas::create(['nama_kelas' => 'X TI 2', 'jurusan_id' => 1, 'wali_kelas_id' => 3]);
        Kelas::create(['nama_kelas' => 'X AK 1', 'jurusan_id' => 2, 'wali_kelas_id' => 4]);
        Kelas::create(['nama_kelas' => 'XI TI 1', 'jurusan_id' => 1]);
        Kelas::create(['nama_kelas' => 'XI AK 1', 'jurusan_id' => 2]);
        Kelas::create(['nama_kelas' => 'XI AP 1', 'jurusan_id' => 3]);
    }
}
