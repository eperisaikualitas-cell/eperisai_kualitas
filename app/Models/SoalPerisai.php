<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoalPerisai extends Model
{
    protected $table = 'soal_perisai';
    protected $fillable = ['kategori', 'pertanyaan'];

    public function detailPenilaian()
    {
        return $this->hasMany(DetailPenilaian::class, 'soal_id');
    }
}