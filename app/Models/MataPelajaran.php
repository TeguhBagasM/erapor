<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MataPelajaran extends Model
{
    use HasFactory;

    protected $table = 'mata_pelajarans';
    protected $fillable = ['kode_mapel', 'nama_mapel'];

    public function nilai()
    {
        return $this->hasMany(Nilai::class, 'mata_pelajaran_id');
    }

    public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'kelas_mata_pelajaran')
                    ->withPivot('guru_id')
                    ->withTimestamps();
    }
}
