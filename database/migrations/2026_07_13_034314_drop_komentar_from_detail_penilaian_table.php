<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detail_penilaian', function (Blueprint $table) {
            // Menghapus kolom komentar
            $table->dropColumn('komentar');
        });
    }

    public function down(): void
    {
        Schema::table('detail_penilaian', function (Blueprint $table) {
            // Mengembalikan kolom komentar jika terjadi rollback
            $table->text('komentar')->nullable()->after('skor');
        });
    }
};