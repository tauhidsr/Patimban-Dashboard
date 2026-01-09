<?php

namespace App\Http\Controllers;

use App\Models\Citizen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CitizenLookupController extends Controller
{
    /**
     * Helper: scope wilayah untuk operator (dusun/rw/rt).
     * Admin/viewer bebas.
     */
    private function applyCitizenScopeForOperator($query, $user)
    {
        if (($user->role ?? 'viewer') !== 'operator') {
            return $query;
        }

        if (!empty($user->dusun)) {
            $query->where('dusun', $user->dusun);
        }

        if (!empty($user->rw)) {
            $query->where('rw', $user->rw);
        }

        if (!empty($user->rt)) {
            $query->where('rt', $user->rt);
        }

        return $query;
    }

    public function byNik(string $nik)
    {
        $nik = trim($nik);
        $user = Auth::user();

        $query = Citizen::query()
            ->select(['id', 'nik', 'no_kk', 'nama', 'dusun', 'rw', 'rt', 'status_dasar'])
            ->where('nik', $nik);

        // ✅ Scope wilayah (operator)
        if ($user) {
            $this->applyCitizenScopeForOperator($query, $user);
        }

        $citizen = $query->first();

        if (!$citizen) {
            // kalau operator lookup NIK luar wilayah, hasilnya "tidak ditemukan" (aman)
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
        $user = $request->user() ?? Auth::user();

        if (mb_strlen($q) < 3) {
            return response()->json(['results' => []]);
        }

        $query = Citizen::query()
            ->select(['nik', 'no_kk', 'nama'])
            ->where(function ($sub) use ($q) {
                $sub->where('nik', 'like', "%{$q}%")
                    ->orWhere('nama', 'like', "%{$q}%")
                    ->orWhere('no_kk', 'like', "%{$q}%");
            });

        // ✅ Scope wilayah (operator)
        if ($user) {
            $this->applyCitizenScopeForOperator($query, $user);
        }

        $citizens = $query
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
