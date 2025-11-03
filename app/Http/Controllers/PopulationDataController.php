<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PopulationArea;

class PopulationDataController extends Controller
{
    // 🔹 LIST data populasi per wilayah
    public function populasiPerWilayah()
    {
        $areas = PopulationArea::orderBy('nama_wilayah')->get();

        return view('data.populasi-per-wilayah', [
            'areas' => $areas,
        ]);
    }

    // 🔹 FORM tambah data
    public function createPopulasiPerWilayah()
    {
        return view('data.populasi-per-wilayah-create');
    }

    // 🔹 SIMPAN data baru
    public function storePopulasiPerWilayah(Request $request)
    {
        $validated = $request->validate([
            'nama_wilayah'    => 'required|string|max:255',
            'kk'              => 'nullable|integer',
            'laki_laki'       => 'nullable|integer',
            'perempuan'       => 'nullable|integer',
            'jumlah_penduduk' => 'nullable|integer',
            'tahun'           => 'nullable|integer',
        ]);

        if (empty($validated['jumlah_penduduk'])) {
            $validated['jumlah_penduduk'] =
                (int)($validated['laki_laki'] ?? 0) +
                (int)($validated['perempuan'] ?? 0);
        }

        PopulationArea::create($validated);

        return redirect()->route('data.populasi')->with('success', 'Data berhasil ditambahkan.');
    }

    // ✏️ 🔹 FORM EDIT data
    public function editPopulasiPerWilayah($id)
    {
        $area = PopulationArea::findOrFail($id);

        return view('data.populasi-per-wilayah-edit', [
            'area' => $area,
        ]);
    }

    // 💾 🔹 UPDATE data
    public function updatePopulasiPerWilayah(Request $request, $id)
    {
        $area = PopulationArea::findOrFail($id);

        $validated = $request->validate([
            'nama_wilayah'    => 'required|string|max:255',
            'kk'              => 'nullable|integer',
            'laki_laki'       => 'nullable|integer',
            'perempuan'       => 'nullable|integer',
            'jumlah_penduduk' => 'nullable|integer',
            'tahun'           => 'nullable|integer',
        ]);

        if (empty($validated['jumlah_penduduk'])) {
            $validated['jumlah_penduduk'] =
                (int)($validated['laki_laki'] ?? 0) +
                (int)($validated['perempuan'] ?? 0);
        }

        $area->update($validated);

        return redirect()->route('data.populasi')->with('success', 'Data berhasil diperbarui.');
    }

    // 🔹 HAPUS data
    public function destroyPopulasiPerWilayah($id)
{
    $area = \App\Models\PopulationArea::findOrFail($id);
    $area->delete();

    return redirect()->route('data.populasi')->with('success', 'Data berhasil dihapus.');
}


    // 🔹 halaman lain (masih placeholder)
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
