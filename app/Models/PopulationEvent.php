<?php

namespace App\Models;

use App\Models\Citizen;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PopulationEvent extends Model
{
    use HasFactory;

    protected $table = 'population_events';

    protected $fillable = [
        // =========================
        // UMUM (dipakai semua peristiwa)
        // =========================
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

        // pelapor (UMUM) ✅ hanya sekali
        'pelapor',
        'hubungan_pelapor',

        'created_by',
        'status_verifikasi',
        'catatan_verifikasi',

        // verifier info
        'verified_by',
        'verified_at',

        // apply/revert tracking
        'previous_status_dasar',
        'status_applied_at',
        'status_applied_by',

        // =========================
        // MENINGGAL
        // =========================
        'tempat_meninggal',
        'jam_kematian',
        'penyebab_kematian',
        'yang_menyatakan_kematian',
        'nomor_akta_kematian',
        'file_akta_kematian_path',

        // =========================
        // PINDAH
        // =========================
        'tujuan_pindah',
        'alamat_tujuan',

        // =========================
        // LAHIR (lama)
        // =========================
        'tempat_lahir',
        'jam_lahir',
        'penolong_kelahiran',

        // =========================
        // DATANG
        // =========================
        'tanggal_datang',
        'alamat_asal',
        'desa_asal',
        'kecamatan_asal',
        'kabupaten_asal',
        'provinsi_asal',

        'alamat_sekarang_tujuan',
        'dusun_tujuan',
        'rw_tujuan',
        'rt_tujuan',

        'status_datang',
        'rencana_tinggal',

        // =========================
        // LAHIR (Versi baru)
        // =========================
        'ibu_citizen_id',
        'nik_ibu',
        'no_kk_ibu',
        'nama_ibu',

        'ayah_citizen_id',
        'nik_ayah',
        'no_kk_ayah',
        'nama_ayah',

        'nama_bayi',
        'jenis_kelamin_bayi',
        'tempat_lahir_bayi',
        'tanggal_lahir_bayi',
        'jam_lahir_bayi',
        'anak_ke',
        'berat_lahir',
        'panjang_lahir',

        'status_lahir',
    ];

    protected $casts = [
        'tanggal_peristiwa' => 'date',
        'tanggal_lapor'     => 'date',
        'tanggal_datang'    => 'date',

        'jam_kematian'      => 'datetime:H:i',
        'jam_lahir'         => 'datetime:H:i',

        'verified_at'       => 'datetime',
        'status_applied_at' => 'datetime',

        // lahir versi baru
        'tanggal_lahir_bayi' => 'date',
        'jam_lahir_bayi'     => 'datetime:H:i',
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
