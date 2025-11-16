<?php

namespace App\Http\Controllers;

use App\Models\PopulationArea;

class MapController extends Controller
{
    public function index()
    {
        // hanya kirim yang sudah punya koordinat
        $areas = PopulationArea::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->select(
                'id',
                'nama_wilayah',
                'latitude',
                'longitude',
                'jumlah_penduduk',
                'kk',
                'laki_laki',
                'perempuan',
                'tahun'
            )
            ->get();

        return view('map.index', compact('areas'));
    }
}
