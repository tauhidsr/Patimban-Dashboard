<?php

namespace App\Http\Controllers;

use App\Models\Citizen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CitizenController extends Controller
{
    /**
     * Tampilkan daftar warga (read-only dulu).
     * - admin/viewer: lihat semua
     * - operator: hanya warga sesuai scope dusun/rw/rt miliknya
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // opsional: simple search
        $search = $request->input('q');

        $query = Citizen::query();

        // =========================
        // SCOPE WILAYAH (operator)
        // =========================
        if (($user->role ?? 'viewer') === 'operator') {
            if (!empty($user->dusun)) {
                $query->where('dusun', $user->dusun);
            }

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
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nik', 'like', "%{$search}%")
                    ->orWhere('nama', 'like', "%{$search}%")
                    ->orWhere('dusun', 'like', "%{$search}%");
            });
        }

        $citizens = $query
            ->orderBy('nama')
            ->paginate(25)
            ->withQueryString();

        return view('citizens.index', compact('citizens', 'search'));
    }
}
