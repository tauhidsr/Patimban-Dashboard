<?php

namespace App\Models;

use App\Models\PopulationEvent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Citizen extends Model
{
    use HasFactory;
    
    protected $table = 'citizens';

    
    // kolom yang boleh diisi lewat create()/update()   
    
    protected $fillable = [
        'nik',
        'no_kk',
        'nama',
        'jenis_kelamin',
        'tanggal_lahir',
        'dusun_id',
        'rw_id',
        'rt_id',
        'status_domisili',
        'status_aktif',
    ];

    // casting otomatis
    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    // relasi ke riwayat peristiwa kependudukan
    public function events()
    {
        return $this->hasMany(PopulationEvent::class, 'citizen_id');
    }
}
