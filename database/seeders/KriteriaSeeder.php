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
            'aktif' => true,
        ]);

        SubKriteria::create([
            'kriteria_id' => $urgensi->id,
            'label' => 'Cukup Mendesak',
            'skor' => 20,
            'deskripsi' => 'Masalah yang dapat ditangani dalam waktu santai',
            'guidance_text' => '💡 Pilih ini jika: Masalah sudah ada cukup lama tetapi tidak mengganggu aktivitas sehari-hari Anda secara signifikan. Contoh: Sedikit kesulitan di satu mata pelajaran, sedikit stress tentang tugas.',
            'aktif' => true,
        ]);

        SubKriteria::create([
            'kriteria_id' => $urgensi->id,
            'label' => 'Mendesak',
            'skor' => 40,
            'deskripsi' => 'Masalah yang perlu ditangani segera',
            'guidance_text' => '💡 Pilih ini jika: Masalah mulai mengganggu aktivitas dan perlu perhatian dalam waktu dekat. Contoh: Masalah dengan teman yang semakin serius, nilai turun, konflik dengan orang tua.',
            'aktif' => true,
        ]);

        SubKriteria::create([
            'kriteria_id' => $urgensi->id,
            'label' => 'Sangat Mendesak',
            'skor' => 70,
            'deskripsi' => 'Masalah yang sangat urgent dan memerlukan intervensi langsung',
            'guidance_text' => '💡 Pilih ini jika: Masalah sangat mengganggu dan memerlukan bantuan segera. Contoh: Bullying intensif, depresi berat, masalah keluarga yang sudah tidak tahan.',
            'aktif' => true,
        ]);

        SubKriteria::create([
            'kriteria_id' => $urgensi->id,
            'label' => 'Kritikal',
            'skor' => 90,
            'deskripsi' => 'Masalah dalam kondisi darurat yang memerlukan tindakan segera',
            'guidance_text' => '💡 Pilih ini jika: Ini kondisi DARURAT yang memerlukan bantuan SEGERA. Contoh: Pikiran untuk bunuh diri, kekerasan, trauma berat, kondisi yang sangat mengancam kesehatan atau keselamatan.',
            'aktif' => true,
        ]);

        // Kriteria 2: Dampak Masalah (Bobot: 0.3)
        $dampak = Kriteria::create([
            'nama' => 'dampak_masalah',
            'deskripsi' => 'Dampak Masalah terhadap Siswa',
            'bobot' => 0.3,
            'aktif' => true,
        ]);

        SubKriteria::create([
            'kriteria_id' => $dampak->id,
            'label' => 'Dampak Ringan',
            'skor' => 20,
            'deskripsi' => 'Masalah memiliki dampak minimal terhadap kehidupan siswa',
            'guidance_text' => '💡 Pilih ini jika: Masalah hanya sedikit mempengaruhi aktivitas Anda. Presensi dan nilai tetap baik, hubungan sosial normal.',
            'aktif' => true,
        ]);

        SubKriteria::create([
            'kriteria_id' => $dampak->id,
            'label' => 'Dampak Sedang',
            'skor' => 40,
            'deskripsi' => 'Masalah memiliki dampak cukup berarti terhadap akademik atau sosial',
            'guidance_text' => '💡 Pilih ini jika: Masalah mulai mempengaruhi prestasi atau hubungan sosial Anda. Nilai mulai menurun, mulai jarang sekolah, atau ada masalah dengan teman.',
            'aktif' => true,
        ]);

        SubKriteria::create([
            'kriteria_id' => $dampak->id,
            'label' => 'Dampak Berat',
            'skor' => 70,
            'deskripsi' => 'Masalah berdampak signifikan terhadap performa akademik dan kesejahteraan',
            'guidance_text' => '💡 Pilih ini jika: Masalah sangat mempengaruhi prestasi akademik, kehadiran sering terganggu, sulit fokus belajar, atau hubungan sosial terganggu parah.',
            'aktif' => true,
        ]);

        SubKriteria::create([
            'kriteria_id' => $dampak->id,
            'label' => 'Dampak Sangat Berat',
            'skor' => 90,
            'deskripsi' => 'Masalah berdampak serius pada kesehatan mental, akademik, dan sosial siswa',
            'guidance_text' => '💡 Pilih ini jika: Masalah memiliki dampak EKSTREM - berhenti sekolah, tidak bisa belajar sama sekali, isolasi total, atau kesehatan fisik/mental sangat terganggu.',
            'aktif' => true,
        ]);

        // Kriteria 3: Kategori Masalah (Bobot: 0.2)
        $kategori = Kriteria::create([
            'nama' => 'kategori_masalah',
            'deskripsi' => 'Kategori Masalah yang Dihadapi',
            'bobot' => 0.2,
            'aktif' => true,
        ]);

        SubKriteria::create([
            'kriteria_id' => $kategori->id,
            'label' => 'Masalah Pribadi',
            'skor' => 20,
            'deskripsi' => 'Masalah personal atau emosional ringan',
            'guidance_text' => '💡 Pilih ini jika: Masalah berkaitan dengan diri sendiri seperti kepercayaan diri rendah, cemas, sulit tidur, atau perasaan sedih ringan.',
            'aktif' => true,
        ]);

        SubKriteria::create([
            'kriteria_id' => $kategori->id,
            'label' => 'Masalah Akademik',
            'skor' => 40,
            'deskripsi' => 'Masalah yang berkaitan dengan prestasi akademik',
            'guidance_text' => '💡 Pilih ini jika: Masalah berkaitan dengan belajar seperti kesulitan memahami pelajaran, nilai turun, motivasi belajar rendah, atau persiapan ujian.',
            'aktif' => true,
        ]);

        SubKriteria::create([
            'kriteria_id' => $kategori->id,
            'label' => 'Masalah Sosial',
            'skor' => 70,
            'deskripsi' => 'Masalah yang berkaitan dengan hubungan sosial dan pergaulan',
            'guidance_text' => '💡 Pilih ini jika: Masalah berkaitan dengan teman atau hubungan sosial seperti konflik dengan teman, bullying, kesulitan bergaul, atau masalah dengan guru.',
            'aktif' => true,
        ]);

        SubKriteria::create([
            'kriteria_id' => $kategori->id,
            'label' => 'Masalah Kesehatan Mental',
            'skor' => 90,
            'deskripsi' => 'Masalah kesehatan mental yang serius',
            'guidance_text' => '💡 Pilih ini jika: Masalah berkaitan dengan kesehatan mental seperti depresi, anxiety berat, trauma, pikiran untuk bunuh diri, atau gangguan mental lainnya.',
            'aktif' => true,
        ]);

        // Kriteria 4: Riwayat Konseling (Bobot: 0.1)
        $riwayat = Kriteria::create([
            'nama' => 'riwayat_konseling',
            'deskripsi' => 'Riwayat Masalah Sebelumnya',
            'bobot' => 0.1,
            'aktif' => true,
        ]);

        SubKriteria::create([
            'kriteria_id' => $riwayat->id,
            'label' => 'Pertama Kali',
            'skor' => 20,
            'deskripsi' => 'Masalah pertama kali dilaporkan',
            'guidance_text' => '💡 Pilih ini jika: Ini adalah pertama kalinya Anda melaporkan masalah ke guru BK. Artinya Anda baru mencari bantuan profesional.',
            'aktif' => true,
        ]);

        SubKriteria::create([
            'kriteria_id' => $riwayat->id,
            'label' => 'Pernah Konseling 1x',
            'skor' => 40,
            'deskripsi' => 'Siswa pernah konseling satu kali sebelumnya',
            'guidance_text' => '💡 Pilih ini jika: Anda sudah pernah berkonseling 1 kali dengan guru BK sebelumnya untuk masalah yang sama atau berbeda.',
            'aktif' => true,
        ]);

        SubKriteria::create([
            'kriteria_id' => $riwayat->id,
            'label' => 'Pernah Konseling 2-3x',
            'skor' => 70,
            'deskripsi' => 'Siswa pernah konseling 2-3 kali sebelumnya',
            'guidance_text' => '💡 Pilih ini jika: Anda sudah berkonseling 2-3 kali dengan guru BK. Ini menunjukkan bahwa masalah Anda berulang atau belum sepenuhnya terselesaikan.',
            'aktif' => true,
        ]);

        SubKriteria::create([
            'kriteria_id' => $riwayat->id,
            'label' => 'Pernah Konseling >3x',
            'skor' => 90,
            'deskripsi' => 'Siswa sering konseling (lebih dari 3 kali)',
            'guidance_text' => '💡 Pilih ini jika: Anda sudah berkonseling lebih dari 3 kali dengan guru BK. Ini menunjukkan masalah yang serius dan memerlukan intervensi lebih intensif.',
            'aktif' => true,
        ]);
    }
}

