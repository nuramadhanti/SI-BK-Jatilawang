<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Hapus kolom-kolom lama dari permohonan_konseling
        Schema::table('permohonan_konseling', function (Blueprint $table) {
            $columnsToDelete = [
                'tingkat_urgensi_label',
                'tingkat_urgensi_skor',
                'dampak_masalah_label',
                'dampak_masalah_skor',
                'kategori_masalah_label',
                'kategori_masalah_skor',
                'riwayat_konseling_label',
                'riwayat_konseling_skor',
            ];

            foreach ($columnsToDelete as $column) {
                if (Schema::hasColumn('permohonan_konseling', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        // Hapus tabel kategori_konseling yang sudah tidak digunakan
        Schema::dropIfExists('kategori_konseling');
    }

    public function down(): void
    {
        // Restore tabel kategori_konseling
        Schema::create('kategori_konseling', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->integer('skor_prioritas')->default(5);
            $table->timestamps();
        });

        // Restore kolom-kolom di permohonan_konseling
        Schema::table('permohonan_konseling', function (Blueprint $table) {
            $table->string('tingkat_urgensi_label')->nullable();
            $table->integer('tingkat_urgensi_skor')->nullable();
            $table->string('dampak_masalah_label')->nullable();
            $table->integer('dampak_masalah_skor')->nullable();
            $table->string('kategori_masalah_label')->nullable();
            $table->integer('kategori_masalah_skor')->nullable();
            $table->string('riwayat_konseling_label')->nullable();
            $table->integer('riwayat_konseling_skor')->nullable();
        });
    }
};
