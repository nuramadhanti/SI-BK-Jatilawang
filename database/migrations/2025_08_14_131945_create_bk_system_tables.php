<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        // Tabel Tahun Akademik
        Schema::create('tahun_akademik', function (Blueprint $table) {
            $table->id();
            $table->string('tahun');
            $table->timestamps();
        });

        // Tabel Kelas
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->foreignId('tahun_akademik_id')->constrained('tahun_akademik')->onDelete('cascade');
            $table->timestamps();
        });

        // Tabel Siswa
        Schema::create('siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nisn')->unique();
            $table->string('nis')->unique();
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('no_telp_orangtua');
            $table->string('nama_orangtua');
            $table->text('alamat');
            $table->timestamps();
        });

        // Tabel Orangtua
        Schema::create('orangtua', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nama');
            $table->string('hubungan_dengan_siswa');
            $table->string('no_hp');
            $table->text('alamat');
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->timestamps();
        });

        // Tabel Guru
        Schema::create('guru', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nama');
            $table->string('nip')->unique();
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('no_hp');
            $table->text('alamat');
            $table->enum('role_guru', ['walikelas', 'bk', 'kepala_sekolah']);
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->onDelete('set null'); // Hanya untuk walikelas
            $table->timestamps();
        });

        // Tabel Kategori Konseling (Priority Scheduling)a
        Schema::create('kategori_konseling', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->integer('skor_prioritas')->default(5);
            $table->timestamps();
        });

        // Tabel Permohonan Konseling
        Schema::create('permohonan_konseling', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->foreignId('kategori_id')->constrained('kategori_konseling')->onDelete('cascade');
            $table->date('tanggal_pengajuan');
            $table->text('deskripsi_permasalahan');
            $table->enum('status', ['menunggu', 'disetujui', 'selesai', 'ditolak'])->default('menunggu');
            $table->text('rangkuman')->nullable();
            $table->datetime('tanggal_disetujui')->nullable();
            $table->string('tempat')->nullable();
            $table->float('skor_prioritas')->default(0);
            $table->timestamps();
        });

        // Tabel Notifications
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('permohonan_konseling');
        Schema::dropIfExists('kategori_konseling');
        Schema::dropIfExists('guru');
        Schema::dropIfExists('orangtua');
        Schema::dropIfExists('siswa');
        Schema::dropIfExists('kelas');
        Schema::dropIfExists('tahun_akademik');
        Schema::dropIfExists('users');
    }
};
