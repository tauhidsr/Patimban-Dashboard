<?php

namespace App\Http\Controllers;

use App\Models\AgeRange;
use Illuminate\Http\Request;

class AgeRangeController extends Controller
{
    // list data rentang umur
    public function index()
    {
        $items = AgeRange::orderBy('kategori')->get();

        return view('data.rentang-umur', [
            'items' => $items,
        ]);
    }

    // form tambah data
    public function create()
    {
        return view('data.rentang-umur-create');
    }

    // simpan data baru
    public function store(Request $request)
    {
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

        AgeRange::create($validated);

        return redirect()->route('rentang-umur.index')
            ->with('success', 'Data rentang umur berhasil ditambahkan.');
    }

    // form edit
    public function edit($id)
    {
        $item = AgeRange::findOrFail($id);

        return view('data.rentang-umur-edit', [
            'item' => $item,
        ]);
    }

    // update data
    public function update(Request $request, $id)
    {
        $item = AgeRange::findOrFail($id);

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

        return redirect()->route('rentang-umur.index')
            ->with('success', 'Data rentang umur berhasil diperbarui.');
    }

    // hapus data
    public function destroy($id)
    {
        $item = AgeRange::findOrFail($id);
        $item->delete();

        return redirect()->route('rentang-umur.index')
            ->with('success', 'Data rentang umur berhasil dihapus.');
    }
}
