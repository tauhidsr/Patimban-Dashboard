<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PopulationArea extends Model
{
    use HasFactory;

    // Izinkan mass assignment untuk kolom-kolom ini
    protected $fillable = [
        'nama_wilayah',
        'kk',
        'laki_laki',
        'perempuan',
        'jumlah_penduduk',
        'tahun',
    ];
}
