<?php

namespace Database\Seeders;

use App\Models\Kriteria;
use App\Models\SubKriteria;
use Illuminate\Database\Seeder;

class KriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Kriteria 1: Tingkat Urgensi (Bobot: 0.4)
        $urgensi = Kriteria::create([
            'nama' => 'tingkat_urgensi',
            'deskripsi' => 'Tingkat Urgensi Permasalahan',
            'bobot' => 0.4,
            'urutan' => 1,
            'aktif' => true,
        ]);

        SubKriteria::create([
            'kriteria_id' => $urgensi->id,
            'label' => 'Cukup Mendesak',
            'skor' => 20,
            'deskripsi' => 'Masalah yang dapat ditangani dalam waktu santai',
            'urutan' => 1,
            'aktif' => true,
        ]);

        SubKriteria::create([
            'kriteria_id' => $urgensi->id,
            'label' => 'Mendesak',
            'skor' => 40,
            'deskripsi' => 'Masalah yang perlu ditangani segera',
            'urutan' => 2,
            'aktif' => true,
        ]);

        SubKriteria::create([
            'kriteria_id' => $urgensi->id,
            'label' => 'Sangat Mendesak',
            'skor' => 70,
            'deskripsi' => 'Masalah yang sangat urgent dan memerlukan intervensi langsung',
            'urutan' => 3,
            'aktif' => true,
        ]);

        SubKriteria::create([
            'kriteria_id' => $urgensi->id,
            'label' => 'Kritikal',
            'skor' => 90,
            'deskripsi' => 'Masalah dalam kondisi darurat yang memerlukan tindakan segera',
            'urutan' => 4,
            'aktif' => true,
        ]);

        // Kriteria 2: Dampak Masalah (Bobot: 0.3)
        $dampak = Kriteria::create([
            'nama' => 'dampak_masalah',
            'deskripsi' => 'Dampak Masalah terhadap Siswa',
            'bobot' => 0.3,
            'urutan' => 2,
            'aktif' => true,
        ]);

        SubKriteria::create([
            'kriteria_id' => $dampak->id,
            'label' => 'Dampak Ringan',
            'skor' => 20,
            'deskripsi' => 'Masalah memiliki dampak minimal terhadap kehidupan siswa',
            'urutan' => 1,
            'aktif' => true,
        ]);

        SubKriteria::create([
            'kriteria_id' => $dampak->id,
            'label' => 'Dampak Sedang',
            'skor' => 40,
            'deskripsi' => 'Masalah memiliki dampak cukup berarti terhadap akademik atau sosial',
            'urutan' => 2,
            'aktif' => true,
        ]);

        SubKriteria::create([
            'kriteria_id' => $dampak->id,
            'label' => 'Dampak Berat',
            'skor' => 70,
            'deskripsi' => 'Masalah berdampak signifikan terhadap performa akademik dan kesejahteraan',
            'urutan' => 3,
            'aktif' => true,
        ]);

        SubKriteria::create([
            'kriteria_id' => $dampak->id,
            'label' => 'Dampak Sangat Berat',
            'skor' => 90,
            'deskripsi' => 'Masalah berdampak serius pada kesehatan mental, akademik, dan sosial siswa',
            'urutan' => 4,
            'aktif' => true,
        ]);

        // Kriteria 3: Kategori Masalah (Bobot: 0.2)
        $kategori = Kriteria::create([
            'nama' => 'kategori_masalah',
            'deskripsi' => 'Kategori Masalah yang Dihadapi',
            'bobot' => 0.2,
            'urutan' => 3,
            'aktif' => true,
        ]);

        SubKriteria::create([
            'kriteria_id' => $kategori->id,
            'label' => 'Masalah Pribadi',
            'skor' => 20,
            'deskripsi' => 'Masalah personal atau emosional ringan',
            'urutan' => 1,
            'aktif' => true,
        ]);

        SubKriteria::create([
            'kriteria_id' => $kategori->id,
            'label' => 'Masalah Akademik',
            'skor' => 40,
            'deskripsi' => 'Masalah yang berkaitan dengan prestasi akademik',
            'urutan' => 2,
            'aktif' => true,
        ]);

        SubKriteria::create([
            'kriteria_id' => $kategori->id,
            'label' => 'Masalah Sosial',
            'skor' => 70,
            'deskripsi' => 'Masalah yang berkaitan dengan hubungan sosial dan pergaulan',
            'urutan' => 3,
            'aktif' => true,
        ]);

        SubKriteria::create([
            'kriteria_id' => $kategori->id,
            'label' => 'Masalah Kesehatan Mental',
            'skor' => 90,
            'deskripsi' => 'Masalah kesehatan mental yang serius',
            'urutan' => 4,
            'aktif' => true,
        ]);

        // Kriteria 4: Riwayat Konseling (Bobot: 0.1)
        $riwayat = Kriteria::create([
            'nama' => 'riwayat_konseling',
            'deskripsi' => 'Riwayat Masalah Sebelumnya',
            'bobot' => 0.1,
            'urutan' => 4,
            'aktif' => true,
        ]);

        SubKriteria::create([
            'kriteria_id' => $riwayat->id,
            'label' => 'Pertama Kali',
            'skor' => 20,
            'deskripsi' => 'Masalah pertama kali dilaporkan',
            'urutan' => 1,
            'aktif' => true,
        ]);

        SubKriteria::create([
            'kriteria_id' => $riwayat->id,
            'label' => 'Pernah Konseling 1x',
            'skor' => 40,
            'deskripsi' => 'Siswa pernah konseling satu kali sebelumnya',
            'urutan' => 2,
            'aktif' => true,
        ]);

        SubKriteria::create([
            'kriteria_id' => $riwayat->id,
            'label' => 'Pernah Konseling 2-3x',
            'skor' => 70,
            'deskripsi' => 'Siswa pernah konseling 2-3 kali sebelumnya',
            'urutan' => 3,
            'aktif' => true,
        ]);

        SubKriteria::create([
            'kriteria_id' => $riwayat->id,
            'label' => 'Pernah Konseling >3x',
            'skor' => 90,
            'deskripsi' => 'Siswa sering konseling (lebih dari 3 kali)',
            'urutan' => 4,
            'aktif' => true,
        ]);
    }
}
