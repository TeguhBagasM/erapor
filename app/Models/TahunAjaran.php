<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    use HasFactory;

    protected $table = 'tahun_ajarans';
    protected $fillable = ['tahun_ajaran', 'semester', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function nilai()
    {
        return $this->hasMany(Nilai::class);
    }
}
