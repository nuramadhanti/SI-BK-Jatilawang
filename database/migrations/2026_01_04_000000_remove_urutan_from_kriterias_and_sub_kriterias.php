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
        // Remove urutan column from kriterias table
        if (Schema::hasTable('kriterias') && Schema::hasColumn('kriterias', 'urutan')) {
            Schema::table('kriterias', function (Blueprint $table) {
                $table->dropColumn('urutan');
            });
        }

        // Remove urutan column from sub_kriterias table
        if (Schema::hasTable('sub_kriterias') && Schema::hasColumn('sub_kriterias', 'urutan')) {
            Schema::table('sub_kriterias', function (Blueprint $table) {
                $table->dropColumn('urutan');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore urutan column to kriterias table
        if (Schema::hasTable('kriterias') && !Schema::hasColumn('kriterias', 'urutan')) {
            Schema::table('kriterias', function (Blueprint $table) {
                $table->integer('urutan')->nullable()->after('bobot');
            });
        }

        // Restore urutan column to sub_kriterias table
        if (Schema::hasTable('sub_kriterias') && !Schema::hasColumn('sub_kriterias', 'urutan')) {
            Schema::table('sub_kriterias', function (Blueprint $table) {
                $table->integer('urutan')->nullable()->after('skor');
            });
        }
    }
};
