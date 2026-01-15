<?php

namespace App\Http\Controllers;

use App\Models\Citizen;
use App\Traits\AppliesCitizenScope;
use Illuminate\Http\Request;

class CitizenLookupController extends Controller
{
    use AppliesCitizenScope;

    public function byNik(string $nik)
    {
        $nik  = trim($nik);
        $user = request()->user();

        // ✅ operator tapi belum punya dusun → 403 (jelas)
        if (($user->role ?? 'viewer') === 'operator' && empty($user->dusun)) {
            return response()->json([
                'found' => false,
                'code' => 'SCOPE_NOT_SET',
                'message' => 'Akun operator belum memiliki scope wilayah (dusun). Hubungi admin.',
            ], 403);
        }

        $query = Citizen::query()
            ->select([
                'id',
                'nik',
                'no_kk',
                'nama',
                'jenis_kelamin',
                'tanggal_lahir',
                'status_perkawinan',
                'agama',
                'pendidikan_dalam_kk',
                'pekerjaan',
                'dusun',
                'rw',
                'rt',
                'status_dasar',
            ])
            ->where('nik', $nik);

        $this->scopeCitizenForOperator($query, $user, '', true);

        $citizen = $query->first();

        if (!$citizen) {
            $msg = 'NIK tidak ditemukan.';
            if (($user->role ?? 'viewer') === 'operator') {
                $msg = 'NIK tidak ditemukan atau tidak termasuk wilayah Anda.';
            }

            return response()->json([
                'found' => false,
                'code' => 'NOT_FOUND',
                'message' => $msg,
            ], 404);
        }

        return response()->json([
            'found' => true,
            'data' => $citizen,
        ]);
    }

    public function search(Request $request)
    {
        $q    = trim((string) $request->query('q', ''));
        $user = $request->user();

        if (mb_strlen($q) < 3) {
            return response()->json(['results' => []]);
        }

        // ✅ tomselect: operator scope kosong → hasil kosong (bukan 403)
        if (($user->role ?? 'viewer') === 'operator' && empty($user->dusun)) {
            return response()->json(['results' => []]);
        }

        $query = Citizen::query()
            ->select(['nik', 'no_kk', 'nama'])
            ->where(function ($sub) use ($q) {
                $sub->where('nik', 'like', "%{$q}%")
                    ->orWhere('nama', 'like', "%{$q}%")
                    ->orWhere('no_kk', 'like', "%{$q}%");
            });

        $this->scopeCitizenForOperator($query, $user, '', false);

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

        return response()->json(['results' => $results]);
    }
}
