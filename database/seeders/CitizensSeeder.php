<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Citizen;

class CitizensSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'nik'                      => '3276010101010001',
                'no_kk'                    => '3276010101010000',
                'nama'                     => 'Ahmad Sulaiman',
                'jenis_kelamin'            => 'L',
                'tempat_lahir'             => 'Patimban',
                'tanggal_lahir'           => '1990-05-12',
                'agama'                    => 'Islam',
                'pendidikan_dalam_kk'      => 'SMA',
                'pendidikan_sedang_ditempuh' => null,
                'pekerjaan'                => 'Wiraswasta',
                'status_perkawinan'        => 'Kawin',
                'hubungan_dalam_keluarga'  => 'Kepala Keluarga',
                'kewarganegaraan'          => 'WNI',
                'dusun'                    => 'Patimban',
                'rw'                       => '01',
                'rt'                       => '001',
                'alamat'                   => 'Dusun Patimban RT 001 RW 01',
                'alamat_sekarang'          => 'Dusun Patimban RT 001 RW 01',
                'status_dasar'             => 'aktif',
                'suku'                     => null,
                'latitude'                 => null,
                'longitude'                => null,
                'keterangan'               => null,
            ],
            [
                'nik'                      => '3276010101010002',
                'no_kk'                    => '3276010101010000',
                'nama'                     => 'Siti Rahmawati',
                'jenis_kelamin'            => 'P',
                'tempat_lahir'             => 'Patimban',
                'tanggal_lahir'           => '1993-08-22',
                'agama'                    => 'Islam',
                'pendidikan_dalam_kk'      => 'SMA',
                'pendidikan_sedang_ditempuh' => null,
                'pekerjaan'                => 'Ibu Rumah Tangga',
                'status_perkawinan'        => 'Kawin',
                'hubungan_dalam_keluarga'  => 'Istri',
                'kewarganegaraan'          => 'WNI',
                'dusun'                    => 'Patimban',
                'rw'                       => '01',
                'rt'                       => '001',
                'alamat'                   => 'Dusun Patimban RT 001 RW 01',
                'alamat_sekarang'          => 'Dusun Patimban RT 001 RW 01',
                'status_dasar'             => 'aktif',
                'suku'                     => null,
                'latitude'                 => null,
                'longitude'                => null,
                'keterangan'               => null,
            ],
            [
                'nik'                      => '3276010101010003',
                'no_kk'                    => '3276010101010003',
                'nama'                     => 'Budi Santoso',
                'jenis_kelamin'            => 'L',
                'tempat_lahir'             => 'Subang',
                'tanggal_lahir'           => '2001-01-15',
                'agama'                    => 'Islam',
                'pendidikan_dalam_kk'      => 'SMA',
                'pendidikan_sedang_ditempuh' => 'Kuliah',
                'pekerjaan'                => 'Mahasiswa',
                'status_perkawinan'        => 'Belum Kawin',
                'hubungan_dalam_keluarga'  => 'Anak',
                'kewarganegaraan'          => 'WNI',
                'dusun'                    => 'Terungtum',
                'rw'                       => '02',
                'rt'                       => '004',
                'alamat'                   => 'Dusun Terungtum RT 004 RW 02',
                'alamat_sekarang'          => 'Dusun Terungtum RT 004 RW 02',
                'status_dasar'             => 'aktif',
                'suku'                     => null,
                'latitude'                 => null,
                'longitude'                => null,
                'keterangan'               => null,
            ],
            [
                'nik'                      => '3276010101010004',
                'no_kk'                    => '3276010101010004',
                'nama'                     => 'Rina Kurniasih',
                'jenis_kelamin'            => 'P',
                'tempat_lahir'             => 'Subang',
                'tanggal_lahir'           => '1985-11-03',
                'agama'                    => 'Islam',
                'pendidikan_dalam_kk'      => 'Diploma',
                'pendidikan_sedang_ditempuh' => null,
                'pekerjaan'                => 'Guru Honorer',
                'status_perkawinan'        => 'Kawin',
                'hubungan_dalam_keluarga'  => 'Kepala Keluarga',
                'kewarganegaraan'          => 'WNI',
                'dusun'                    => 'Genteng',
                'rw'                       => '03',
                'rt'                       => '007',
                'alamat'                   => 'Dusun Genteng RT 007 RW 03',
                'alamat_sekarang'          => 'Dusun Genteng RT 007 RW 03',
                'status_dasar'             => 'aktif',
                'suku'                     => null,
                'latitude'                 => null,
                'longitude'                => null,
                'keterangan'               => null,
            ],
        ];

        foreach ($data as $item) {
            Citizen::updateOrCreate(
                ['nik' => $item['nik']],
                $item
            );
        }
    }
}
