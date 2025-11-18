<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EducationInKK extends Model
{
    use HasFactory;

    protected $fillable = [
        'kategori',
        'laki_laki',
        'perempuan',
        'total',
        'tahun',
    ];
}
