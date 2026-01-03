<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Add indexes untuk optimasi query performance pada kolom yang sering di-filter/sort
     */
    public function up(): void
    {
        Schema::table('permohonan_konseling', function (Blueprint $table) {
            // Index untuk filtering status (menunggu, disetujui, selesai, ditolak)
            if (!Schema::hasIndex('permohonan_konseling', 'permohonan_konseling_status_index')) {
                $table->index('status');
            }

            // Index untuk filtering by student
            if (!Schema::hasIndex('permohonan_konseling', 'permohonan_konseling_siswa_id_index')) {
                $table->index('siswa_id');
            }

            // Index untuk sorting by priority score
            if (!Schema::hasIndex('permohonan_konseling', 'permohonan_konseling_skor_prioritas_index')) {
                $table->index('skor_prioritas');
            }

            // Index untuk sorting by date
            if (!Schema::hasIndex('permohonan_konseling', 'permohonan_konseling_created_at_index')) {
                $table->index('created_at');
            }

            // Composite index untuk common queries: status + skor_prioritas + created_at
            if (!Schema::hasIndex('permohonan_konseling', 'permohonan_konseling_status_skor_created_index')) {
                $table->index(['status', 'skor_prioritas', 'created_at']);
            }
        });

        // Index untuk tabel permohonan_kriteria
        Schema::table('permohonan_kriteria', function (Blueprint $table) {
            if (!Schema::hasIndex('permohonan_kriteria', 'permohonan_kriteria_permohonan_id_index')) {
                $table->index('permohonan_konseling_id');
            }

            if (!Schema::hasIndex('permohonan_kriteria', 'permohonan_kriteria_kriteria_id_index')) {
                $table->index('kriteria_id');
            }

            if (!Schema::hasIndex('permohonan_kriteria', 'permohonan_kriteria_sub_kriteria_id_index')) {
                $table->index('sub_kriteria_id');
            }
        });

        // Index untuk tabel siswa
        Schema::table('siswa', function (Blueprint $table) {
            if (!Schema::hasIndex('siswa', 'siswa_kelas_id_index')) {
                $table->index('kelas_id');
            }

            if (!Schema::hasIndex('siswa', 'siswa_user_id_index')) {
                $table->index('user_id');
            }
        });

        // Index untuk tabel guru
        Schema::table('guru', function (Blueprint $table) {
            if (!Schema::hasIndex('guru', 'guru_kelas_id_index')) {
                $table->index('kelas_id');
            }

            if (!Schema::hasIndex('guru', 'guru_user_id_index')) {
                $table->index('user_id');
            }

            if (!Schema::hasIndex('guru', 'guru_role_guru_index')) {
                $table->index('role_guru');
            }
        });

        // Index untuk tabel kelas
        Schema::table('kelas', function (Blueprint $table) {
            if (!Schema::hasIndex('kelas', 'kelas_tahun_akademik_id_index')) {
                $table->index('tahun_akademik_id');
            }
        });

        // Index untuk tabel sub_kriterias
        Schema::table('sub_kriterias', function (Blueprint $table) {
            if (!Schema::hasIndex('sub_kriterias', 'sub_kriterias_kriteria_id_index')) {
                $table->index('kriteria_id');
            }
        });

        // Index untuk tabel orangtua
        Schema::table('orangtua', function (Blueprint $table) {
            if (!Schema::hasIndex('orangtua', 'orangtua_siswa_id_index')) {
                $table->index('siswa_id');
            }

            if (!Schema::hasIndex('orangtua', 'orangtua_user_id_index')) {
                $table->index('user_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permohonan_konseling', function (Blueprint $table) {
            $table->dropIndex('permohonan_konseling_status_index');
            $table->dropIndex('permohonan_konseling_siswa_id_index');
            $table->dropIndex('permohonan_konseling_skor_prioritas_index');
            $table->dropIndex('permohonan_konseling_created_at_index');
            $table->dropIndex('permohonan_konseling_status_skor_created_index');
        });

        Schema::table('permohonan_kriteria', function (Blueprint $table) {
            $table->dropIndex('permohonan_kriteria_permohonan_id_index');
            $table->dropIndex('permohonan_kriteria_kriteria_id_index');
            $table->dropIndex('permohonan_kriteria_sub_kriteria_id_index');
        });

        Schema::table('siswa', function (Blueprint $table) {
            $table->dropIndex('siswa_kelas_id_index');
            $table->dropIndex('siswa_user_id_index');
        });

        Schema::table('guru', function (Blueprint $table) {
            $table->dropIndex('guru_kelas_id_index');
            $table->dropIndex('guru_user_id_index');
            $table->dropIndex('guru_role_guru_index');
        });

        Schema::table('kelas', function (Blueprint $table) {
            $table->dropIndex('kelas_tahun_akademik_id_index');
        });

        Schema::table('sub_kriterias', function (Blueprint $table) {
            $table->dropIndex('sub_kriterias_kriteria_id_index');
        });

        Schema::table('orangtua', function (Blueprint $table) {
            $table->dropIndex('orangtua_siswa_id_index');
            $table->dropIndex('orangtua_user_id_index');
        });
    }
};
