<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PopulationDataController extends Controller
{
    public function populasiPerWilayah()
    {
        return view('data.populasi-per-wilayah');
    }

    public function rentangUmur()
    {
        return view('data.rentang-umur');
    }

    public function pendidikanDalamKK()
    {
        return view('data.pendidikan-dalam-kk');
    }

    public function pendidikanDitempuh()
    {
        return view('data.pendidikan-ditempuh');
    }

    public function pekerjaan()
    {
        return view('data.pekerjaan');
    }

    public function agama()
    {
        return view('data.agama');
    }

    public function jenisKelamin()
    {
        return view('data.jenis-kelamin');
    }
}
