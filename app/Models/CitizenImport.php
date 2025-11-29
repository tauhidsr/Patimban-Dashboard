<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CitizenImport extends Model
{
    use HasFactory;

    protected $table = 'citizen_imports';

    protected $fillable = [
        'citizen_id',
        'source_file',
        'row_index',
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
        'raw_row',
        'import_status',
        'error_message',
    ];

    protected $casts = [
        'raw_row' => 'array',
        'tanggal_lahir' => 'date',
    ];

    // relasi opsional ke master citizen
    public function citizen()
    {
        return $this->belongsTo(Citizen::class);
    }
}
