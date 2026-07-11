<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_penilaian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('riwayat_id')->constrained('riwayat_penilaian')->onDelete('cascade');
            $table->foreignId('soal_id')->constrained('soal_perisai')->onDelete('cascade');
            $table->string('jawaban_yt')->default('Ya');
            $table->integer('skor')->default(5);
            $table->text('komentar')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_penilaian');
    }
};