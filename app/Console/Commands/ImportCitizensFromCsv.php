<?php

namespace App\Console\Commands;

use App\Models\Citizen;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ImportCitizensFromCsv extends Command
{
    /**
     * Nama perintah Artisan.
     *
     * Contoh menjalankan:
     * php artisan citizens:import
     */
    protected $signature = 'citizens:import {path? : Path CSV relatif dari storage/app (default: import/citizens.csv)}';

    /**
     * Deskripsi singkat perintah.
     */
    protected $description = 'Import data warga (citizens) dari file CSV ke tabel citizens';

    public function handle(): int
    {
        $relativePath = $this->argument('path') ?? 'import/citizens.csv';
        $fullPath = storage_path('app/' . $relativePath);

        if (! file_exists($fullPath)) {
            $this->error("File tidak ditemukan: {$fullPath}");
            return self::FAILURE;
        }

        $this->info("Memulai import dari: {$fullPath}");

        $handle = fopen($fullPath, 'r');

        if (! $handle) {
            $this->error('Gagal membuka file CSV.');
            return self::FAILURE;
        }

        // Baca header
        $header = fgetcsv($handle, 0, ',');

        if (! $header) {
            $this->error('Header CSV kosong atau tidak valid.');
            fclose($handle);
            return self::FAILURE;
        }

        // Normalisasi header: lower case & trim
        $header = array_map(function ($h) {
            return strtolower(trim($h));
        }, $header);

        // Mapping index kolom
        $colIndex = array_flip($header);

        $rowCount = 0;
        $inserted = 0;
        $updated = 0;

        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            $rowCount++;

            // Helper untuk ambil nilai berdasarkan nama kolom
            $get = function (string $name) use ($colIndex, $row) {
                if (! isset($colIndex[$name])) {
                    return null;
                }

                $value = $row[$colIndex[$name]] ?? null;
                $value = is_string($value) ? trim($value) : $value;

                return $value === '' ? null : $value;
            };

            $nik = $get('nik');

            if (! $nik) {
                $this->warn("Baris {$rowCount}: nik kosong, dilewati.");
                continue;
            }

            $tanggalLahirRaw = $get('tanggal_lahir');
            $tanggalLahir = null;

            if ($tanggalLahirRaw) {
                try {
                    // Coba parse fleksibel
                    $tanggalLahir = Carbon::parse($tanggalLahirRaw)->format('Y-m-d');
                } catch (\Throwable $e) {
                    $this->warn("Baris {$rowCount}: tanggal_lahir tidak dapat diparse ({$tanggalLahirRaw}), diset null.");
                    $tanggalLahir = null;
                }
            }

            $lat = $get('latitude');
            $lng = $get('longitude');

            $data = [
                'no_kk'                    => $get('no_kk'),
                'nama'                     => $get('nama'),
                'jenis_kelamin'            => $get('jenis_kelamin'), // 'L' / 'P'
                'tempat_lahir'             => $get('tempat_lahir'),
                'tanggal_lahir'            => $tanggalLahir,
                'agama'                    => $get('agama'),
                'pendidikan_dalam_kk'      => $get('pendidikan_dalam_kk'),
                'pendidikan_sedang_ditempuh'=> $get('pendidikan_sedang_ditempuh'),
                'pekerjaan'                => $get('pekerjaan'),
                'status_perkawinan'        => $get('status_perkawinan'),
                'hubungan_dalam_keluarga'  => $get('hubungan_dalam_keluarga'),
                'kewarganegaraan'          => $get('kewarganegaraan'),
                'dusun'                    => $get('dusun'),
                'rw'                       => $get('rw'),
                'rt'                       => $get('rt'),
                'alamat'                   => $get('alamat'),
                'alamat_sekarang'          => $get('alamat_sekarang'),
                'status_dasar'             => $get('status_dasar'),
                'suku'                     => $get('suku'),
                'latitude'                 => $lat !== null ? (float) $lat : null,
                'longitude'                => $lng !== null ? (float) $lng : null,
                'keterangan'               => $get('keterangan'),
            ];

            // Buat import idempotent (berdasarkan NIK)
            $citizen = Citizen::where('nik', $nik)->first();

            if ($citizen) {
                $citizen->update($data);
                $updated++;
            } else {
                Citizen::create(array_merge(['nik' => $nik], $data));
                $inserted++;
            }
        }

        fclose($handle);

        $this->info("Selesai. Total baris dibaca: {$rowCount}");
        $this->info("Ditambahkan: {$inserted} | Diperbarui: {$updated}");

        return self::SUCCESS;
    }
}
