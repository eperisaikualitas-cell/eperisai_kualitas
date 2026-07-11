<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('soal_perisai', function (Blueprint $table) {
            $table->id();
            $table->string('kategori'); // Isinya: Fasilitas, Paspor, Izin Tinggal, TPI
            $table->text('pertanyaan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('soal_perisai');
    }
};