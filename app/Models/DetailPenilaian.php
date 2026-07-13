<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPenilaian extends Model
{
    use HasFactory;

    protected $table = 'detail_penilaian';

    // TAMBAHKAN DUA KOLOM BARU DI DALAM ARRAY FILLABLE INI
    protected $fillable = [
        'riwayat_id',
        'soal_id',
        'jawaban_yt',
        'skor',
        'temuan_ketidaksesuaian', // <--- Wajib ditambah
        'catatan'                 // <--- Wajib ditambah
    ];

    public function riwayat()
    {
        return $this->belongsTo(RiwayatPenilaian::class, 'riwayat_id');
    }

    public function soal()
    {
        return $this->belongsTo(SoalPerisai::class, 'soal_id');
    }
}