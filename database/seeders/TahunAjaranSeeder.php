<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TahunAjaran;

class TahunAjaranSeeder extends Seeder
{
    public function run()
    {
        TahunAjaran::create([
            'tahun_ajaran' => '2025/2026',
            'semester' => 'ganjil',
            'is_active' => true,
        ]);

        TahunAjaran::create([
            'tahun_ajaran' => '2025/2026',
            'semester' => 'genap',
            'is_active' => false,
        ]);
    }
}
