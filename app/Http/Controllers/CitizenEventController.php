<?php

namespace App\Http\Controllers;

use App\Models\CitizenEvent;
use Illuminate\Http\Request;

class CitizenEventController extends Controller
{
    /**
     * Tampilkan daftar peristiwa kependudukan.
     */
    public function index(Request $request)
    {
        $status = $request->input('status'); // contoh filter status_verifikasi di masa depan

        $events = CitizenEvent::with('citizen')
            ->when($status, function ($query) use ($status) {
                $query->where('status_verifikasi', $status);
            })
            ->orderByDesc('tanggal_peristiwa')
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('citizen_events.index', [
            'events' => $events,
            'status' => $status,
        ]);
    }
}
