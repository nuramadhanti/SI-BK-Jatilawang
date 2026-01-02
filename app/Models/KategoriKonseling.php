<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriKonseling extends Model
{
    protected $fillable = ['nama', 'skor_prioritas'];
    protected $table = 'kategori_konseling';

    public function permohonan()
    {
        return $this->hasMany(PermohonanKonseling::class, 'kategori_id');
    }
}
