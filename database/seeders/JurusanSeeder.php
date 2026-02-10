<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jurusan;

class JurusanSeeder extends Seeder
{
    public function run()
    {
        Jurusan::create(['nama_jurusan' => 'Teknologi Informasi']);
        Jurusan::create(['nama_jurusan' => 'Akuntansi']);
        Jurusan::create(['nama_jurusan' => 'Administrasi Perkantoran']);
    }
}
