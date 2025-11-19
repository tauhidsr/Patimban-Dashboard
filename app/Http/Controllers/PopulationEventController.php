<?php

namespace App\Http\Controllers;

use App\Models\PopulationEvent;
use Illuminate\Http\Request;

class PopulationEventController extends Controller
{
    // halaman list peristiwa

    public function index()
    {
        // sementara ambil semua (nanti filter per role)
        $events =  PopulationEvent::orderBy('id','desc')->paginate(20);

        return view('events.index', compact('events'));
    }

    // form tambah peristiwa (pilih jenis peristiwa)
    public function create()
    {
        return view('events.create');
    }

    // simpan peristiwa baru (nanti akan dipisah per jenis: lahir, datang, pindah, meninggal, hilang, sementara)
    public function store(Request $request)
    {
        // sementara kosong -> nanti diisi
    }
}
