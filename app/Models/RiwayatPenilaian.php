<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatPenilaian extends Model
{
    protected $table = 'riwayat_penilaian';
    protected $fillable = ['nama_satker', 'jenis_satker', 'total_nilai', 'predikat', 'penandatangan_id', 'status'];

    public function detailPenilaian()
    {
        return $this->hasMany(DetailPenilaian::class, 'riwayat_id');
    }
}