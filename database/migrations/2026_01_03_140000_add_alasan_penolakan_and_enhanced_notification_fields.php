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
        Schema::table('permohonan_konseling', function (Blueprint $table) {
            // Add alasan_penolakan field
            if (!Schema::hasColumn('permohonan_konseling', 'alasan_penolakan')) {
                $table->text('alasan_penolakan')->nullable()->after('status');
            }

            // Add guru_bk_id for assignment tracking
            if (!Schema::hasColumn('permohonan_konseling', 'guru_bk_id')) {
                $table->foreignId('guru_bk_id')->nullable()->after('siswa_id')->constrained('guru')->onDelete('set null');
            }

            // Add approved_by and approved_at for tracking approval
            if (!Schema::hasColumn('permohonan_konseling', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->after('guru_bk_id')->constrained('guru')->onDelete('set null');
            }

            if (!Schema::hasColumn('permohonan_konseling', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permohonan_konseling', function (Blueprint $table) {
            $table->dropForeign(['guru_bk_id']);
            $table->dropColumn('alasan_penolakan');
            $table->dropColumn('guru_bk_id');
            $table->dropColumn('approved_by');
            $table->dropColumn('approved_at');
        });
    }
};
