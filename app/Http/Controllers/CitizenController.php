<?php

namespace App\Http\Controllers;

use App\Models\Citizen;
use Illuminate\Http\Request;

class CitizenController extends Controller
{
    /**
     * Tampilkan daftar warga (read-only dulu).
     */
    public function index(Request $request)
    {
        // opsional: simple search
        $search = $request->input('q');

        $query = Citizen::query();

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
            ->withQueryString(); // biar paginasi bawa query ?q

        return view('citizens.index', compact('citizens', 'search'));
    }
}
