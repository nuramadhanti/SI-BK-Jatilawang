<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Siswa extends Model
{
    protected $fillable = [
        'user_id',
        'nisn',
        'nis',
        'kelas_id',
        'jenis_kelamin',
        'no_telp_orangtua',
        'nama_orangtua',
        'alamat'
    ];

    protected $table = 'siswa';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orangtua()
    {
        return $this->hasOne(Orangtua::class, 'siswa_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function permohonan()
    {
        return $this->hasMany(PermohonanKonseling::class);
    }

    /**
     * Hitung total riwayat konseling yang sudah selesai dalam bulan ini
     * 
     * @return int Jumlah permohonan konseling yang status = 'selesai' dalam bulan saat ini
     */
    public function getJumlahRiwayatSelesai()
    {
        $now = Carbon::now();
        
        return $this->permohonan()
            ->where('status', 'selesai')
            ->whereYear('updated_at', $now->year)
            ->whereMonth('updated_at', $now->month)
            ->count();
    }

    /**
     * Ambil sub-kriteria riwayat konseling berdasarkan jumlah selesai
     * 
     * @return \App\Models\SubKriteria|null Sub-kriteria yang sesuai
     */
    public function getSubKriteriaRiwayatOtomatis()
    {
        $jumlahSelesai = $this->getJumlahRiwayatSelesai();
        
        // Cari kriteria riwayat konseling
        $kriteriaRiwayat = Kriteria::where('nama', 'riwayat_konseling')->first();
        
        if (!$kriteriaRiwayat) {
            return null;
        }

        // Tentukan sub-kriteria berdasarkan jumlah selesai
        if ($jumlahSelesai == 0) {
            // Pertama Kali (skor 20)
            return $kriteriaRiwayat->subKriterias()
                ->whereIn('label', ['Pertama Kali', 'pertama kali'])
                ->first();
        } elseif ($jumlahSelesai == 1) {
            // Pernah Konseling 1x (skor 40)
            return $kriteriaRiwayat->subKriterias()
                ->whereIn('label', ['Pernah Konseling 1x', 'pernah konseling 1x'])
                ->first();
        } elseif ($jumlahSelesai >= 2 && $jumlahSelesai <= 3) {
            // Pernah Konseling 2-3x (skor 70)
            return $kriteriaRiwayat->subKriterias()
                ->whereIn('label', ['Pernah Konseling 2-3x', 'pernah konseling 2-3x'])
                ->first();
        } else {
            // Pernah Konseling >3x (skor 90)
            return $kriteriaRiwayat->subKriterias()
                ->whereIn('label', ['Pernah Konseling >3x', 'pernah konseling >3x'])
                ->first();
        }
    }
}
