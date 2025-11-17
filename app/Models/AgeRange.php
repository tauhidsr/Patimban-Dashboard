<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgeRange extends Model
{
    use HasFactory;

    // kolom yang boleh diisi lewat create() / update()
    protected $fillable = [
        'kategori',
        'laki_laki',
        'perempuan',
        'total',
        'tahun',
    ];
}
