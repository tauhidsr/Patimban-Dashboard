<?php

namespace App\Http\Controllers;

use App\Models\Citizen;
use Illuminate\Http\Request;

class CitizenController extends Controller
{
    /**
     * Tampilkan daftar warga (read-only dulu).
     * - admin/viewer: lihat semua
     * - operator: hanya warga sesuai scope dusun/rw/rt miliknya
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $search = trim((string) $request->query('q', ''));

        $query = Citizen::query();

        // =========================
        // SCOPE WILAYAH (operator)
        // =========================
        if (($user->role ?? 'viewer') === 'operator') {
            // ✅ safety: operator WAJIB punya minimal dusun
            if (empty($user->dusun)) {
                abort(403, 'Akun operator belum memiliki scope wilayah (dusun). Hubungi admin.');
            }

            $query->where('dusun', $user->dusun);

            if (!empty($user->rw)) {
                $query->where('rw', $user->rw);
            }
            if (!empty($user->rt)) {
                $query->where('rt', $user->rt);
            }
        }

        // =========================
        // SEARCH
        // =========================
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('nik', 'like', "%{$search}%")
                    ->orWhere('nama', 'like', "%{$search}%")
                    ->orWhere('no_kk', 'like', "%{$search}%")
                    ->orWhere('dusun', 'like', "%{$search}%")
                    ->orWhere('rw', 'like', "%{$search}%")
                    ->orWhere('rt', 'like', "%{$search}%");
            });
        }

        $citizens = $query
            ->orderBy('nama')
            ->paginate(25)
            ->withQueryString();

        return view('citizens.index', compact('citizens', 'search'));
    }
}
