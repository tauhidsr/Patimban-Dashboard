<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PopulationEvent extends Model
{
    use HasFactory;

    protected $table = 'population_events';

    // field yang boleh diisi
    protected $fillable = [
        'citizen_id',
        'nik',
        'no_kk',
        'nama',
        'jenis_peristiwa',
        'dusun_id',
        'rw_id',
        'rt_id',
        'tanggal_peristiwa',
        'tanggal_lapor',
        'catatan_peristiwa',
        'created_by',
        'status_verifikasi',
        'catatan_verifikasi',

        // MENINGGAL
        'tempat_meninggal',
        'jam_kematian',
        'penyebab_kematian',
        'yang_menyatakan_kematian',
        'nomor_akta_kematian',
        'file_akta_kematian_path',

        // PINDAH
        'tujuan_pindah',
        'alamat_tujuan',

        // LAHIR
        'tempat_lahir',
        'jam_lahir',
        'penolong_kelahiran',

        // DATANG
        'asal_datang_kategori',
        'alamat_asli',
        'alasan_datang',
    ];

    // casting otomatis
    protected $casts = [
        'tanggal_peristiwa' => 'date',
        'tanggal_lapor'     => 'date',
        'jam_kematian'      => 'datetime:H:i',
        'jam_lahir'         => 'datetime:H:i',
    ];

    // relasi ke master penduduk
    public function citizen()
    {
        return $this->belongsTo(Citizen::class);
    }

    // relasi ke user yang menginput peristiwa
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
