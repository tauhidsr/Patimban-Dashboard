<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PopulationArea;

class PopulationAreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'nama_wilayah'     => 'Dusun Patimban',
                'kk'               => 491,
                'jumlah_penduduk'  => 1357,   // L+P
                'laki_laki'        => 650,
                'perempuan'        => 707,
                'tahun'            => 2025,
            ],
            [
                'nama_wilayah'     => 'Dusun Terungtum',
                'kk'               => 716,
                'jumlah_penduduk'  => 2175,
                'laki_laki'        => 1077,
                'perempuan'        => 1098,
                'tahun'            => 2025,
            ],
            [
                'nama_wilayah'     => 'Dusun Genteng',
                'kk'               => 544,
                'jumlah_penduduk'  => 1693,
                'laki_laki'        => 870,
                'perempuan'        => 823,
                'tahun'            => 2025,
            ],
            [
                'nama_wilayah'     => 'Dusun Siwalan',
                'kk'               => 684,
                'jumlah_penduduk'  => 2117,
                'laki_laki'        => 1041,
                'perempuan'        => 1076,
                'tahun'            => 2025,
            ],
            [
                'nama_wilayah'     => 'Dusun Galian',
                'kk'               => 572,
                'jumlah_penduduk'  => 1753,
                'laki_laki'        => 860,
                'perempuan'        => 893,
                'tahun'            => 2025,
            ],
        ];

        foreach ($data as $item) {
            PopulationArea::create($item);
        }
    }
}
