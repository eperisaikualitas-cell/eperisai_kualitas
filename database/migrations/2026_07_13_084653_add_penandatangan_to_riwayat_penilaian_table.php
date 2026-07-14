<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('riwayat_penilaian', function (Blueprint $table) {
            // Menambahkan kolom untuk menyimpan ID pegawai yang tanda tangan
            $table->unsignedBigInteger('penandatangan_id')->nullable()->after('predikat');
        });
    }

    public function down(): void
    {
        Schema::table('riwayat_penilaian', function (Blueprint $table) {
            $table->dropColumn('penandatangan_id');
        });
    }
};