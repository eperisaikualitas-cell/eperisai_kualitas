<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
    {
        // HAPUS HURUF 's' DI NAMA TABELNYA
        Schema::table('riwayat_penilaian', function (Blueprint $table) {
            $table->string('status')->default('selesai')->after('predikat');
        });
    }

    public function down()
    {
        // HAPUS HURUF 's' DI NAMA TABELNYA
        Schema::table('riwayat_penilaian', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};