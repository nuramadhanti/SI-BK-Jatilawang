<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kriterias', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // 'tingkat_urgensi', 'dampak_masalah', 'kategori_masalah', 'riwayat_konseling'
            $table->string('deskripsi')->nullable();
            $table->decimal('bobot', 3, 2); // 0.4, 0.3, 0.2, 0.1
            $table->integer('urutan');
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kriterias');
    }
};
