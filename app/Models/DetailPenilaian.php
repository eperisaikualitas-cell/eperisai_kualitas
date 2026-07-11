<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPenilaian extends Model
{
    protected $table = 'detail_penilaian';
    protected $fillable = ['riwayat_id', 'soal_id', 'jawaban_yt', 'skor', 'komentar'];

    public function riwayat()
    {
        return $this->belongsTo(RiwayatPenilaian::class, 'riwayat_id');
    }

    public function soal()
    {
        return $this->belongsTo(SoalPerisai::class, 'soal_id');
    }
}