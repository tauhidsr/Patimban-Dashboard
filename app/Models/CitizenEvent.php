<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CitizenEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'citizen_id',
        'nik',
        'nama',
        'jenis_peristiwa',
        'tanggal_peristiwa',
        'keterangan',
        'dusun',
        'rw',
        'rt',
        'status_verifikasi',
        'created_by',
        'verified_by',
    ];

    public function citizen()
    {
        return $this->belongsTo(Citizen::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
