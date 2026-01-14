<?php

namespace App\Http\Controllers;

use App\Models\CitizenEvent;
use Illuminate\Http\Request;

class CitizenEventController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $status = trim((string) $request->query('status', ''));

        $query = CitizenEvent::query()
            ->with(['citizen:id,nik,nama,dusun,rw,rt'])
            ->orderByDesc('id');

        // ✅ scope wilayah (operator dibatasi dusun/rw/rt)
        $user = $request->user();
        if ($user && ($user->role ?? null) === 'operator') {
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

        // ✅ filter status (sesuai dropdown view kamu: pending/verified/rejected)
        if ($status !== '') {
            $query->where('status_verifikasi', $status);
        }

        // ✅ search
        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('nik', 'like', "%{$q}%")
                    ->orWhere('nama', 'like', "%{$q}%")
                    ->orWhere('jenis_peristiwa', 'like', "%{$q}%")
                    ->orWhere('dusun', 'like', "%{$q}%")
                    ->orWhere('rw', 'like', "%{$q}%")
                    ->orWhere('rt', 'like', "%{$q}%")
                    ->orWhere('keterangan', 'like', "%{$q}%");
            });
        }

        $events = $query->paginate(20)->withQueryString();

        return view('citizen_events.index', compact('events', 'q', 'status'));
    }
}
