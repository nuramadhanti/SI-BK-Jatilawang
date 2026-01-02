<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermohonanKriteria extends Model
{
    protected $table = 'permohonan_kriteria';

    protected $fillable = [
        'permohonan_konseling_id',
        'kriteria_id',
        'sub_kriteria_id',
        'skor',
    ];

    public function permohonanKonseling()
    {
        return $this->belongsTo(PermohonanKonseling::class);
    }

    public function kriteria()
    {
        return $this->belongsTo(Kriteria::class);
    }

    public function subKriteria()
    {
        return $this->belongsTo(SubKriteria::class);
    }
}
