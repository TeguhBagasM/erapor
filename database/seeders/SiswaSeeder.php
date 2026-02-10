<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Siswa;

class SiswaSeeder extends Seeder
{
    public function run()
    {
        // X TI 1 (kelas_id = 1)
        Siswa::create(['nis' => '10001', 'nama_siswa' => 'Andi Pratama', 'kelas_id' => 1]);
        Siswa::create(['nis' => '10002', 'nama_siswa' => 'Bima Sakti', 'kelas_id' => 1]);
        Siswa::create(['nis' => '10003', 'nama_siswa' => 'Citra Dewi', 'kelas_id' => 1]);
        Siswa::create(['nis' => '10004', 'nama_siswa' => 'Dian Permata', 'kelas_id' => 1]);
        Siswa::create(['nis' => '10005', 'nama_siswa' => 'Eko Saputra', 'kelas_id' => 1]);

        // X TI 2 (kelas_id = 2)
        Siswa::create(['nis' => '10006', 'nama_siswa' => 'Fajar Nugroho', 'kelas_id' => 2]);
        Siswa::create(['nis' => '10007', 'nama_siswa' => 'Gita Puspita', 'kelas_id' => 2]);
        Siswa::create(['nis' => '10008', 'nama_siswa' => 'Hendra Wijaya', 'kelas_id' => 2]);
        Siswa::create(['nis' => '10009', 'nama_siswa' => 'Indah Sari', 'kelas_id' => 2]);
        Siswa::create(['nis' => '10010', 'nama_siswa' => 'Joko Susilo', 'kelas_id' => 2]);

        // X AK 1 (kelas_id = 3)
        Siswa::create(['nis' => '10011', 'nama_siswa' => 'Kartika Sari', 'kelas_id' => 3]);
        Siswa::create(['nis' => '10012', 'nama_siswa' => 'Lukman Hakim', 'kelas_id' => 3]);
        Siswa::create(['nis' => '10013', 'nama_siswa' => 'Maya Anggraini', 'kelas_id' => 3]);
        Siswa::create(['nis' => '10014', 'nama_siswa' => 'Nanda Putra', 'kelas_id' => 3]);
        Siswa::create(['nis' => '10015', 'nama_siswa' => 'Putri Rahayu', 'kelas_id' => 3]);

        // XI TI 1 (kelas_id = 4)
        Siswa::create(['nis' => '10016', 'nama_siswa' => 'Rizky Maulana', 'kelas_id' => 4]);
        Siswa::create(['nis' => '10017', 'nama_siswa' => 'Sinta Bella', 'kelas_id' => 4]);
        Siswa::create(['nis' => '10018', 'nama_siswa' => 'Teguh Prasetyo', 'kelas_id' => 4]);

        // XI AK 1 (kelas_id = 5)
        Siswa::create(['nis' => '10019', 'nama_siswa' => 'Umar Faruq', 'kelas_id' => 5]);
        Siswa::create(['nis' => '10020', 'nama_siswa' => 'Vina Melati', 'kelas_id' => 5]);
        Siswa::create(['nis' => '10021', 'nama_siswa' => 'Wulan Dari', 'kelas_id' => 5]);

        // XI AP 1 (kelas_id = 6)
        Siswa::create(['nis' => '10022', 'nama_siswa' => 'Yusuf Mardani', 'kelas_id' => 6]);
        Siswa::create(['nis' => '10023', 'nama_siswa' => 'Zahra Putri', 'kelas_id' => 6]);
        Siswa::create(['nis' => '10024', 'nama_siswa' => 'Arif Rahman', 'kelas_id' => 6]);
    }
}
