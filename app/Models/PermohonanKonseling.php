<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermohonanKonseling extends Model
{
    protected $table = 'permohonan_konseling';

    protected $fillable = [
        'siswa_id',
        'tanggal_pengajuan',
        'deskripsi_permasalahan',
        'status',
        'rangkuman',
        'alasan_penolakan',
        'tanggal_disetujui',
        'tempat',
        'nama_konselor',

        // Kriteria Penilaian
        'tingkat_urgensi_label',
        'tingkat_urgensi_skor',

        'dampak_masalah_label',
        'dampak_masalah_skor',

        'kategori_masalah_label',
        'kategori_masalah_skor',

        'riwayat_konseling_label',
        'riwayat_konseling_skor',

        'skor_prioritas',
        'report_type',
        'bukti_masalah',
    ];

    // Relasi
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function guruBk()
    {
        return $this->belongsTo(Guru::class, 'approved_by');
    }

    public function permohonanKriterias()
    {
        return $this->hasMany(PermohonanKriteria::class);
    }

    public function kriterias()
    {
        return $this->belongsToMany(
            Kriteria::class,
            'permohonan_kriteria',
            'permohonan_konseling_id',
            'kriteria_id'
        )->withPivot('sub_kriteria_id', 'skor')->withTimestamps();
    }

    /**
     * Hitung skor akhir dari kriteria yang dipilih
     * 
     * RUMUS: Skor Akhir = (k1 × bobot) + (k2 × bobot) + (k3 × bobot) + (k4 × bobot) + ...
     * 
     * Dimana:
     * - k1, k2, k3, dst = skor sub-kriteria yang dipilih
     * - bobot = bobot kriteria dari database (nilai 0-1)
     * 
     * Contoh:
     * - Kriteria 1 (Urgensi): Skor=90, Bobot=0.25 → 90 × 0.25 = 22.5
     * - Kriteria 2 (Dampak): Skor=70, Bobot=0.25 → 70 × 0.25 = 17.5
     * - Kriteria 3 (Kategori): Skor=40, Bobot=0.25 → 40 × 0.25 = 10
     * - Kriteria 4 (Riwayat): Skor=20, Bobot=0.25 → 20 × 0.25 = 5
     * - TOTAL SKOR AKHIR = 22.5 + 17.5 + 10 + 5 = 55
     * 
     * @param array $kriteriaData Array berisi skor dan bobot setiap kriteria
     * @return float Skor akhir yang sudah dihitung
     */
    public static function hitungSkorAkhir($kriteriaData)
    {
        $totalSkor = 0;
        $breakdown = [];
        
        foreach ($kriteriaData as $index => $kriteria) {
            $skorTerbobot = $kriteria['skor'] * $kriteria['bobot'];
            $totalSkor += $skorTerbobot;
            $breakdown[] = "k" . ($index + 1) . ": {$kriteria['skor']} × {$kriteria['bobot']} = {$skorTerbobot}";
        }
        
        return round($totalSkor, 2);
    }

    /**
     * Hitung skor prioritas dari permohonan kriteria
     * 
     * Sama dengan rumus hitungSkorAkhir namun data diambil dari relasi permohonanKriterias
     * RUMUS: Skor Akhir = (k1 × bobot) + (k2 × bobot) + (k3 × bobot) + ...
     * 
     * @return float Skor prioritas permohonan
     */
    public function hitungSkorPrioritas()
    {
        $totalSkor = 0;
        foreach ($this->permohonanKriterias as $pk) {
            $bobot = $pk->kriteria->bobot;
            $skor = $pk->skor;
            $totalSkor += ($skor * $bobot);
        }
        return round($totalSkor, 2);
    }

    /**
     * Dapatkan breakdown skor per kriteria
     * Berguna untuk menampilkan detail kalkulasi
     * 
     * @return array Breakdown detail skor per kriteria
     */
    public function getBreakdownSkor()
    {
        $breakdown = [];
        foreach ($this->permohonanKriterias as $pk) {
            $bobot = $pk->kriteria->bobot;
            $skor = $pk->skor;
            $skorTerbobot = $skor * $bobot;
            
            $breakdown[] = [
                'kriteria_id' => $pk->kriteria_id,
                'kriteria_nama' => $pk->kriteria->nama,
                'sub_kriteria_label' => $pk->subKriteria->label,
                'skor_sub_kriteria' => $skor,
                'bobot' => $bobot,
                'skor_terbobot' => round($skorTerbobot, 2),
            ];
        }
        return $breakdown;
    }

    /**
     * Dapatkan rumus perhitungan skor dalam format readable
     * 
     * @return string Rumus dalam format: "(90 × 0.25) + (70 × 0.25) + ... = 55"
     */
    public function getRumusSkorAkhir()
    {
        $breakdown = $this->getBreakdownSkor();
        
        if (empty($breakdown)) {
            return "Tidak ada kriteria yang dipilih";
        }
        
        $terms = [];
        foreach ($breakdown as $item) {
            $terms[] = "({$item['skor_sub_kriteria']} × {$item['bobot']})";
        }
        
        $rumus = implode(" + ", $terms) . " = " . $this->skor_prioritas;
        return $rumus;
    }

    /**
     * Dapatkan detail breakdown dalam format HTML (untuk display di view)
     * 
     * @return string HTML table atau list breakdown skor
     */
    public function getBreakdownSkorHtml()
    {
        $breakdown = $this->getBreakdownSkor();
        
        if (empty($breakdown)) {
            return "<span class='text-muted'>Tidak ada kriteria</span>";
        }
        
        $html = "<table class='table table-sm table-borderless'>";
        $html .= "<tbody>";
        
        $totalManual = 0;
        foreach ($breakdown as $item) {
            $html .= "<tr>";
            $html .= "<td>{$item['kriteria_nama']}</td>";
            $html .= "<td class='text-center'>{$item['skor_sub_kriteria']} × {$item['bobot']}</td>";
            $html .= "<td class='text-right'><strong>{$item['skor_terbobot']}</strong></td>";
            $html .= "</tr>";
            $totalManual += $item['skor_terbobot'];
        }
        
        $html .= "<tr class='border-top'>";
        $html .= "<td colspan='2' class='text-right'><strong>Skor Akhir:</strong></td>";
        $html .= "<td class='text-right'><strong class='text-primary'>" . round($totalManual, 2) . "</strong></td>";
        $html .= "</tr>";
        $html .= "</tbody>";
        $html .= "</table>";
        
        return $html;
    }
}
