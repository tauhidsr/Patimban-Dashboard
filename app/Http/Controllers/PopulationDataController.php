<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PopulationArea;

class PopulationDataController extends Controller
{
    // list data populasi per wilayah
    public function populasiPerWilayah()
    {
        $areas = PopulationArea::orderBy('nama_wilayah')->get();

        return view('data.populasi-per-wilayah', [
            'areas' => $areas,
        ]);
    }

    // form tambah data
    public function createPopulasiPerWilayah()
    {
        return view('data.populasi-per-wilayah-create');
    }

    // simpan data baru
    public function storePopulasiPerWilayah(Request $request)
    {
        $validated = $request->validate([
            'nama_wilayah'    => ['required', 'string', 'max:255'],
            'kk'              => ['nullable', 'integer', 'min:0'],
            'laki_laki'       => ['nullable', 'integer', 'min:0'],
            'perempuan'       => ['nullable', 'integer', 'min:0'],
            'jumlah_penduduk' => ['nullable', 'integer', 'min:0'],
            'tahun'           => ['nullable', 'integer', 'digits:4', 'min:1900', 'max:2100'],
            'latitude'        => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'       => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        if (empty($validated['jumlah_penduduk'])) {
            $validated['jumlah_penduduk'] =
                (int) ($validated['laki_laki'] ?? 0) +
                (int) ($validated['perempuan'] ?? 0);
        }

        PopulationArea::create($validated);

        return redirect()
            ->route('data.populasi')
            ->with('success', 'Data berhasil ditambahkan.');
    }

    // FORM EDIT data
    public function editPopulasiPerWilayah($id)
    {
        $area = PopulationArea::findOrFail($id);

        return view('data.populasi-per-wilayah-edit', [
            'area' => $area,
        ]);
    }

    // UPDATE data
    public function updatePopulasiPerWilayah(Request $request, $id)
    {
        $area = PopulationArea::findOrFail($id);

        $validated = $request->validate([
            'nama_wilayah'    => ['required', 'string', 'max:255'],
            'kk'              => ['nullable', 'integer', 'min:0'],
            'laki_laki'       => ['nullable', 'integer', 'min:0'],
            'perempuan'       => ['nullable', 'integer', 'min:0'],
            'jumlah_penduduk' => ['nullable', 'integer', 'min:0'],
            'tahun'           => ['nullable', 'integer', 'digits:4', 'min:1900', 'max:2100'],
            'latitude'        => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'       => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        if (empty($validated['jumlah_penduduk'])) {
            $validated['jumlah_penduduk'] =
                (int) ($validated['laki_laki'] ?? 0) +
                (int) ($validated['perempuan'] ?? 0);
        }

        $area->update($validated);

        return redirect()
            ->route('data.populasi')
            ->with('success', 'Data berhasil diperbarui.');
    }

    // HAPUS data
    public function destroyPopulasiPerWilayah($id)
    {
        $area = PopulationArea::findOrFail($id);
        $area->delete();

        return redirect()
            ->route('data.populasi')
            ->with('success', 'Data berhasil dihapus.');
    }

    // halaman lain (masih placeholder)
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
