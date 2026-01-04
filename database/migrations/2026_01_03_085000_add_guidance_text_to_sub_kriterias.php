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
        Schema::table('sub_kriterias', function (Blueprint $table) {
            $table->longText('guidance_text')->nullable()->after('deskripsi')->comment('Guidance text untuk membantu siswa memilih opsi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sub_kriterias', function (Blueprint $table) {
            $table->dropColumn('guidance_text');
        });
    }
};
