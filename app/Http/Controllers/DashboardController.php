<?php

namespace App\Http\Controllers;

use App\Models\PopulationArea;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_penduduk'  => (int) PopulationArea::sum('jumlah_penduduk'),
            'total_kk'        => (int) PopulationArea::sum('kk'),
            'total_laki'      => (int) PopulationArea::sum('laki_laki'),
            'total_perempuan' => (int) PopulationArea::sum('perempuan'),
        ];

        // hindari pembagian nol saat hitung rasio
        $total = max(1, $stats['total_penduduk']);
        $stats['rasio_laki']      = round(($stats['total_laki'] / $total) * 100, 1);
        $stats['rasio_perempuan'] = round(($stats['total_perempuan'] / $total) * 100, 1);

        return view('dashboard', compact('stats'));
    }
}
