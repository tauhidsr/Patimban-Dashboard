<?php

namespace App\Http\Controllers;

use App\Models\PopulationEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PopulationEventController extends Controller
{
    // Halaman list peristiwa (dengan filter & search)
    public function index(Request $request)
    {
        $query = PopulationEvent::query()->orderBy('id', 'desc');

        // simpan nilai filter dari request
        $filters = [
            'jenis'  => $request->get('jenis'),
            'status' => $request->get('status'),
            'q'      => $request->get('q'),
        ];

        // filter jenis peristiwa
        if (!empty($filters['jenis'])) {
            $query->where('jenis_peristiwa', $filters['jenis']);
        }

        // filter status verifikasi
        if (!empty($filters['status'])) {
            $query->where('status_verifikasi', $filters['status']);
        }

        // pencarian nama / NIK / No KK
        if (!empty($filters['q'])) {
            $q = $filters['q'];
            $query->where(function ($sub) use ($q) {
                $sub->where('nama', 'like', "%{$q}%")
                    ->orWhere('nik', 'like', "%{$q}%")
                    ->orWhere('no_kk', 'like "%{$q}%"');
            });
        }

        $events = $query->paginate(20)->withQueryString();

        return view('events.index', compact('events', 'filters'));
    }

    // form pilih jenis peristiwa (nanti bisa lahir/datang/dll)
    public function create()
    {
        return view('events.create');
    }

    // simpan peristiwa umum (belum dipakai, nanti)
    public function store(Request $request)
    {
        // sementara kosong -> nanti diisi untuk jenis lain
    }

    // form peristiwa MENINGGAL
    public function createMeninggal()
    {
        return view('events.form-meninggal');
    }

    // simpan peristiwa MENINGGAL
    public function storeMeninggal(Request $request)
    {
        // validasi input
        $validated = $request->validate([
            'no_kk'                     => 'required|string|max:20',
            'nik'                       => 'required|string|max:20',
            'nama'                      => 'required|string|max:150',
            'tanggal_peristiwa'         => 'required|date',
            'tanggal_lapor'             => 'nullable|date',
            'tempat_meninggal'          => 'nullable|string|max:255',
            'jam_kematian'              => 'nullable|date_format:H:i',
            'penyebab_kematian'         => 'nullable|in:sakit_biasa_tua,wabah_penyakit,kecelakaan,kriminalitas,bunuh_diri,lainnya',
            'yang_menyatakan_kematian'  => 'nullable|in:dokter,tenaga_kesehatan,kepolisian,lainnya',
            'nomor_akta_kematian'       => 'nullable|string|max:100',
            'file_akta_kematian_path'   => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'catatan_peristiwa'         => 'nullable|string',
        ]);

        // kalau tanggal lapor kosong → isi otomatis hari ini
        if (empty($validated['tanggal_lapor'])) {
            $validated['tanggal_lapor'] = now()->toDateString();
        }

        // handle upload file akta (opsional)
        $filePath = null;
        if ($request->hasFile('file_akta_kematian_path')) {
            $filePath = $request->file('file_akta_kematian_path')->store('akta-kematian', 'public');
        }

        // simpan ke tabel population_events
        PopulationEvent::create([
            'citizen_id'               => null, // nanti dihubungkan ke master penduduk
            'nik'                      => $validated['nik'],
            'no_kk'                    => $validated['no_kk'],
            'nama'                     => $validated['nama'],
            'jenis_peristiwa'          => 'meninggal',

            'tanggal_peristiwa'        => $validated['tanggal_peristiwa'],
            'tanggal_lapor'            => $validated['tanggal_lapor'],
            'catatan_peristiwa'        => $validated['catatan_peristiwa'] ?? null,

            'created_by'               => Auth::id(),
            'status_verifikasi'        => 'menunggu',

            'tempat_meninggal'         => $validated['tempat_meninggal'] ?? null,
            'jam_kematian'             => $validated['jam_kematian'] ?? null,
            'penyebab_kematian'        => $validated['penyebab_kematian'] ?? null,
            'yang_menyatakan_kematian' => $validated['yang_menyatakan_kematian'] ?? null,
            'nomor_akta_kematian'      => $validated['nomor_akta_kematian'] ?? null,
            'file_akta_kematian_path'  => $filePath,
        ]);

        return redirect()
            ->route('events.index')
            ->with('success', 'Peristiwa meninggal berhasil dicatat dan menunggu verifikasi admin.');
    }

    // detail peristiwa
    public function show($id)
    {
        $event = PopulationEvent::findOrFail($id);

        return view('events.show', [
            'event' => $event,
        ]);
    }

    // VERIFIKASI peristiwa oleh admin
    public function verify(Request $request, $id)
    {
        $event = PopulationEvent::findOrFail($id);

        // validasi input
        $validated = $request->validate([
            'status_verifikasi'   => 'required|in:menunggu,disetujui,ditolak',
            'catatan_verifikasi'  => 'nullable|string',
        ]);

        $event->status_verifikasi  = $validated['status_verifikasi'];
        $event->catatan_verifikasi = $validated['catatan_verifikasi'] ?? null;
        $event->save();

        return redirect()
            ->route('events.show', $event->id)
            ->with('success', 'Status verifikasi peristiwa berhasil diperbarui.');
    }
}
