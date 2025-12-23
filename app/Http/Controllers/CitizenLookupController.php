<?php

namespace App\Http\Controllers;

use App\Models\Citizen;
use Illuminate\Http\Request;

class CitizenLookupController extends Controller
{
    public function byNik(string $nik)
    {
        $nik = trim($nik);

        $citizen = Citizen::query()
            ->select(['id', 'nik', 'no_kk', 'nama', 'dusun', 'rw', 'rt'])
            ->where('nik', $nik)
            ->first();

        if (!$citizen) {
            return response()->json([
                'found' => false,
                'message' => 'NIK tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'found' => true,
            'data' => $citizen,
        ]);
    }
}
