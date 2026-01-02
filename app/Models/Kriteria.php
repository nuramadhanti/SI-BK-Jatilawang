<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kriteria extends Model
{
    protected $table = 'kriterias';

    protected $fillable = [
        'nama',
        'deskripsi',
        'bobot',
        'urutan',
        'aktif',
    ];

    protected $casts = [
        'bobot' => 'decimal:2',
        'aktif' => 'boolean',
    ];

    public function subKriterias()
    {
        return $this->hasMany(SubKriteria::class)
            ->where('aktif', true)
            ->orderBy('urutan');
    }

    public function permohonanKriterias()
    {
        return $this->hasMany(PermohonanKriteria::class);
    }
}
