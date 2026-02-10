<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class GuruSeeder extends Seeder
{
    public function run()
    {
        // Buat user wali kelas dulu
        $wali1 = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@gmail.com',
            'password' => Hash::make('password'),
            'role_id' => 2, // wali_kelas
        ]);

        $wali2 = User::create([
            'name' => 'Siti Aminah',
            'email' => 'siti@gmail.com',
            'password' => Hash::make('password'),
            'role_id' => 2,
        ]);

        $wali3 = User::create([
            'name' => 'Ahmad Fauzi',
            'email' => 'ahmad@gmail.com',
            'password' => Hash::make('password'),
            'role_id' => 2,
        ]);

        // Buat data guru
        Guru::create(['nip' => '198501012010011001', 'nama_guru' => 'Budi Santoso', 'user_id' => $wali1->id]);
        Guru::create(['nip' => '198703152011012002', 'nama_guru' => 'Siti Aminah', 'user_id' => $wali2->id]);
        Guru::create(['nip' => '199002202012011003', 'nama_guru' => 'Ahmad Fauzi', 'user_id' => $wali3->id]);
        Guru::create(['nip' => '198806102013011004', 'nama_guru' => 'Dewi Lestari']);
        Guru::create(['nip' => '199105252014012005', 'nama_guru' => 'Rina Wati']);
    }
}
