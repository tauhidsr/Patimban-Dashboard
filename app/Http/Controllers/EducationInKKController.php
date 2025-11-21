<?php

namespace App\Http\Controllers;

use App\Models\EducationInKK;
use Illuminate\Http\Request;

class EducationInKKController extends Controller
{
    // list pendidikan dalam KK
    public function index()
    {
        // urutkan semua, "BELUM MENGISI" di bawah
        $items = EducationInKK::orderByRaw("CASE WHEN kategori = 'BELUM MENGISI' THEN 2 ELSE 1 END")
            ->orderBy('id')
            ->get();

        // ringkasan total
        $summary = [
            'total_laki'      => $items->sum('laki_laki'),
            'total_perempuan' => $items->sum('perempuan'),
            'total_jiwa'      => $items->sum('total'),
        ];

        return view('data.pendidikan-dalam-kk', [
            'items'   => $items,
            'summary' => $summary,
        ]);
    }

    // form tambah data
    public function create()
    {
        return view('data.pendidikan-dalam-kk-create');
    }

    // simpan data
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori'   => 'required|string|max:255',
            'laki_laki'  => 'nullable|integer',
            'perempuan'  => 'nullable|integer',
            'total'      => 'nullable|integer',
            'tahun'      => 'nullable|integer',
        ]);

        // total hitung otomatis L+P
        if (empty($validated['total'])) {
            $validated['total'] =
                (int) ($validated['laki_laki'] ?? 0) +
                (int) ($validated['perempuan'] ?? 0);
        }

        EducationInKK::create($validated);

        return redirect()
            ->route('pendidikan-kk.index')
            ->with('success', 'Data pendidikan dalam KK berhasil ditambahkan');
    }

    // form edit data
    public function edit($id)
    {
        $item = EducationInKK::findOrFail($id);

        return view('data.pendidikan-dalam-kk-edit', [
            'item' => $item,
        ]);
    }

    // update data
    public function update(Request $request, $id)
    {
        $item = EducationInKK::findOrFail($id);

        $validated = $request->validate([
            'kategori'   => 'required|string|max:255',
            'laki_laki'  => 'nullable|integer',
            'perempuan'  => 'nullable|integer',
            'total'      => 'nullable|integer',
            'tahun'      => 'nullable|integer',
        ]);

        if (empty($validated['total'])) {
            $validated['total'] =
                (int) ($validated['laki_laki'] ?? 0) +
                (int) ($validated['perempuan'] ?? 0);
        }

        $item->update($validated);

        return redirect()
            ->route('pendidikan-kk.index')
            ->with('success', 'Data pendidikan dalam KK berhasil diperbarui.');
    }

    // hapus data
    public function destroy($id)
    {
        $item = EducationInKK::findOrFail($id);
        $item->delete();

        return redirect()
            ->route('pendidikan-kk.index')
            ->with('success', 'Data pendidikan dalam KK berhasil dihapus.');
    }
}
