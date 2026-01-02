<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubKriteria extends Model
{
    protected $table = 'sub_kriterias';

    protected $fillable = [
        'kriteria_id',
        'label',
        'skor',
        'deskripsi',
        'urutan',
        'aktif',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    public function kriteria()
    {
        return $this->belongsTo(Kriteria::class);
    }

    public function permohonanKriterias()
    {
        return $this->hasMany(PermohonanKriteria::class);
    }
}
