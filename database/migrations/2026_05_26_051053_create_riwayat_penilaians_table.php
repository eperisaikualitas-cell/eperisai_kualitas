<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_penilaian', function (Blueprint $table) {
            $table->id();
            $table->string('nama_satker');
            $table->string('jenis_satker'); // TPI atau NON_TPI
            $table->decimal('total_nilai', 5, 2); // Contoh: 98.50
            $table->string('predikat');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_penilaian');
    }
};