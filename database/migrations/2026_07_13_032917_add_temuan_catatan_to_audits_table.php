<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detail_penilaian', function (Blueprint $table) {
            // Menempatkan kolom baru setelah kolom 'komentar'
            $table->text('temuan_ketidaksesuaian')->nullable()->after('komentar');
            $table->text('catatan')->nullable()->after('temuan_ketidaksesuaian');
        });
    }

    public function down(): void
    {
        Schema::table('detail_penilaian', function (Blueprint $table) {
            $table->dropColumn(['temuan_ketidaksesuaian', 'catatan']);
        });
    }
};