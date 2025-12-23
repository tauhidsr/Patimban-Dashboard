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
            ->select(['id', 'nik', 'no_kk', 'nama', 'dusun', 'rw', 'rt', 'status_dasar'])
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


    public function search(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        if (mb_strlen($q) < 3) {
            return response()->json(['results' => []]);
        }

        $citizens = Citizen::query()
            ->select(['nik', 'no_kk', 'nama'])
            ->where(function ($sub) use ($q) {
                $sub->where('nik', 'like', "%{$q}%")
                    ->orWhere('nama', 'like', "%{$q}%")
                    ->orWhere('no_kk', 'like', "%{$q}%");
            })
            ->orderBy('nama')
            ->limit(20)
            ->get();

        $results = $citizens->map(function ($c) {
            return [
                'value' => $c->nik,
                'text'  => "{$c->nik} — {$c->nama} (KK: {$c->no_kk})",
            ];
        });

        return response()->json([
            'results' => $results,
        ]);
    }
}
