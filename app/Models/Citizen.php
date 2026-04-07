<?php

namespace App\Models;

use App\Models\PopulationEvent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CitizenEvent;


class Citizen extends Model
{
    use HasFactory;

    protected $fillable = [
        'nik',
        'no_kk',
        'nama',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'agama',
        'pendidikan_dalam_kk',
        'pendidikan_sedang_ditempuh',
        'pekerjaan',
        'status_perkawinan',
        'hubungan_dalam_keluarga',
        'kewarganegaraan',
        'dusun',
        'rw',
        'rt',
        'alamat',
        'alamat_sekarang',
        'status_dasar',
        'suku',
        'latitude',
        'longitude',
        'keterangan',
    ];

    protected function casts(): array
{
    return [
        'tanggal_lahir' => 'date',
        'latitude' => 'float',
        'longitude' => 'float',
    ];
}

    /**
     * Relasi ke tabel citizen_events (peristiwa kependudukan)
     */
    public function citizenEvents()
    {
        return $this->hasMany(CitizenEvent::class);
    }

    /**
     * Relasi lama ke PopulationEvent (boleh dipakai atau dihapus nanti)
     */
    public function events()
    {
        return $this->hasMany(PopulationEvent::class, 'citizen_id');
    }
}
