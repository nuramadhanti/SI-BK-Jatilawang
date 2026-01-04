<?php

namespace Database\Seeders;

use App\Models\SubKriteria;
use Illuminate\Database\Seeder;

class UpdateGuidanceTextSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update Tingkat Urgensi (Kriteria ID 1)
        $guidanceTexts = [
            'Cukup Mendesak' => '💡 Pilih ini jika: Masalah sudah ada cukup lama tetapi tidak mengganggu aktivitas sehari-hari Anda secara signifikan. Contoh: Sedikit kesulitan di satu mata pelajaran, sedikit stress tentang tugas.',
            'Mendesak' => '💡 Pilih ini jika: Masalah mulai mengganggu aktivitas dan perlu perhatian dalam waktu dekat. Contoh: Masalah dengan teman yang semakin serius, nilai turun, konflik dengan orang tua.',
            'Sangat Mendesak' => '💡 Pilih ini jika: Masalah sangat mengganggu dan memerlukan bantuan segera. Contoh: Bullying intensif, depresi berat, masalah keluarga yang sudah tidak tahan.',
            'Kritikal' => '💡 Pilih ini jika: Ini kondisi DARURAT yang memerlukan bantuan SEGERA. Contoh: Pikiran untuk bunuh diri, kekerasan, trauma berat, kondisi yang sangat mengancam kesehatan atau keselamatan.',
            
            // Dampak Masalah
            'Dampak Ringan' => '💡 Pilih ini jika: Masalah hanya sedikit mempengaruhi aktivitas Anda. Presensi dan nilai tetap baik, hubungan sosial normal.',
            'Dampak Sedang' => '💡 Pilih ini jika: Masalah mulai mempengaruhi prestasi atau hubungan sosial Anda. Nilai mulai menurun, mulai jarang sekolah, atau ada masalah dengan teman.',
            'Dampak Berat' => '💡 Pilih ini jika: Masalah sangat mempengaruhi prestasi akademik, kehadiran sering terganggu, sulit fokus belajar, atau hubungan sosial terganggu parah.',
            'Dampak Sangat Berat' => '💡 Pilih ini jika: Masalah memiliki dampak EKSTREM - berhenti sekolah, tidak bisa belajar sama sekali, isolasi total, atau kesehatan fisik/mental sangat terganggu.',
            
            // Kategori Masalah
            'Masalah Pribadi' => '💡 Pilih ini jika: Masalah berkaitan dengan diri sendiri seperti kepercayaan diri rendah, cemas, sulit tidur, atau perasaan sedih ringan.',
            'Masalah Akademik' => '💡 Pilih ini jika: Masalah berkaitan dengan belajar seperti kesulitan memahami pelajaran, nilai turun, motivasi belajar rendah, atau persiapan ujian.',
            'Masalah Sosial' => '💡 Pilih ini jika: Masalah berkaitan dengan teman atau hubungan sosial seperti konflik dengan teman, bullying, kesulitan bergaul, atau masalah dengan guru.',
            'Masalah Kesehatan Mental' => '💡 Pilih ini jika: Masalah berkaitan dengan kesehatan mental seperti depresi, anxiety berat, trauma, pikiran untuk bunuh diri, atau gangguan mental lainnya.',
            
            // Riwayat Konseling
            'Pertama Kali' => '💡 Pilih ini jika: Ini adalah pertama kalinya Anda melaporkan masalah ke guru BK. Artinya Anda baru mencari bantuan profesional.',
            'Pernah Konseling 1x' => '💡 Pilih ini jika: Anda sudah pernah berkonseling 1 kali dengan guru BK sebelumnya untuk masalah yang sama atau berbeda.',
            'Pernah Konseling 2-3x' => '💡 Pilih ini jika: Anda sudah berkonseling 2-3 kali dengan guru BK. Ini menunjukkan bahwa masalah Anda berulang atau belum sepenuhnya terselesaikan.',
            'Pernah Konseling >3x' => '💡 Pilih ini jika: Anda sudah berkonseling lebih dari 3 kali dengan guru BK. Ini menunjukkan masalah yang serius dan memerlukan intervensi lebih intensif.',
        ];

        foreach ($guidanceTexts as $label => $guidance) {
            SubKriteria::where('label', $label)->update(['guidance_text' => $guidance]);
        }

        $this->command->info('Guidance texts have been updated successfully!');
    }
}
