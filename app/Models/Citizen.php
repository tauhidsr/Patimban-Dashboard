<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Citizen extends Model
{
    use HasFactory;
    
    protected $table = 'citizens';

    // field yang boleh diisi (create/ update)

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

    // casting otomasis ke tipe data yang tepat

    protected $casts = [
        'tanggal_lahir'=> 'date',
    ];

    /**
     * Nanti di sini bisa kita tambahkan relasi ke:
     * - tabel dusun / rw / rt
     * - tabel events (riwayat peristiwa)
     *
     * Untuk sekarang kita biarkan simpel dulu.
     */
}
