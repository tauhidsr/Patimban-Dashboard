<?php

namespace App\Http\Controllers;

use App\Models\PopulationEvent;
use App\Models\Citizen;
use App\Models\CitizenEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PopulationEventController extends Controller
{
    // Halaman list peristiwa (dengan filter & search)
    public function index(Request $request)
    {
        $query = PopulationEvent::query()->orderBy('id', 'desc');

        $filters = [
            'jenis'  => $request->get('jenis'),
            'status' => $request->get('status'),
            'q'      => $request->get('q'),
        ];

        if (!empty($filters['jenis'])) {
            $query->where('jenis_peristiwa', $filters['jenis']);
        }

        if (!empty($filters['status'])) {
            $query->where('status_verifikasi', $filters['status']);
        }

        if (!empty($filters['q'])) {
            $q = $filters['q'];
            $query->where(function ($sub) use ($q) {
                $sub->where('nama', 'like', "%{$q}%")
                    ->orWhere('nik', 'like', "%{$q}%")
                    ->orWhere('no_kk', 'like', "%{$q}%");
            });
        }

        $events = $query->paginate(20)->withQueryString();

        return view('events.index', compact('events', 'filters'));
    }

    public function create()
    {
        return view('events.create');
    }

    public function store(Request $request)
    {
        // sementara kosong -> nanti diisi untuk jenis lain
    }

    public function createMeninggal()
    {
        return view('events.form-meninggal');
    }

    public function storeMeninggal(Request $request)
    {
        // ✅ rapihin input biar ga gagal gara-gara spasi
        $request->merge([
            'nik'  => $request->filled('nik') ? trim($request->nik) : null,
            'no_kk'=> $request->filled('no_kk') ? trim($request->no_kk) : null,
            'nama' => $request->filled('nama') ? trim($request->nama) : null,
        ]);

        $validated = $request->validate(
            [
                'no_kk'             => 'required|string|max:20',
                'nik'               => 'required|string|max:20|exists:citizens,nik',
                'nama'              => 'required|string|max:150',

                'tanggal_peristiwa' => 'required|date|before_or_equal:today',
                'tanggal_lapor'     => 'nullable|date|before_or_equal:today',

                'tempat_meninggal'  => 'nullable|string|max:255',
                'jam_kematian'      => 'nullable|date_format:H:i',
                'penyebab_kematian' => 'nullable|in:sakit_biasa_tua,wabah_penyakit,kecelakaan,kriminalitas,bunuh_diri,lainnya',
                'yang_menyatakan_kematian' => 'nullable|in:dokter,tenaga_kesehatan,kepolisian,lainnya',
                'nomor_akta_kematian'      => 'nullable|string|max:100',
                'file_akta_kematian_path'  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
                'catatan_peristiwa'        => 'nullable|string',
            ],
            [
                // pesan umum
                'required' => ':attribute wajib diisi.',
                'date'     => ':attribute harus berupa tanggal yang valid.',

                // khusus tanggal
                'tanggal_peristiwa.before_or_equal' => 'Tanggal peristiwa tidak boleh lebih dari hari ini.',
                'tanggal_lapor.before_or_equal'     => 'Tanggal lapor tidak boleh lebih dari hari ini.',

                // khusus nik
                'nik.exists' => 'NIK tidak ditemukan. Pastikan penduduk sudah terdaftar di data penduduk.',

                // file
                'file_akta_kematian_path.mimes' => 'File akta harus berupa JPG, PNG, atau PDF.',
                'file_akta_kematian_path.max'   => 'Ukuran file akta maksimal 2 MB.',
            ],
            [
                // alias field
                'no_kk'             => 'No KK',
                'nik'               => 'NIK',
                'nama'              => 'Nama penduduk',
                'tanggal_peristiwa' => 'Tanggal peristiwa',
                'tanggal_lapor'     => 'Tanggal lapor',
            ]
        );

        if (empty($validated['tanggal_lapor'])) {
            $validated['tanggal_lapor'] = now()->toDateString();
        }

        $filePath = null;
        if ($request->hasFile('file_akta_kematian_path')) {
            $filePath = $request->file('file_akta_kematian_path')->store('akta-kematian', 'public');
        }

        PopulationEvent::create([
            'citizen_id'               => null,
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

    public function show($id)
    {
        $event = PopulationEvent::findOrFail($id);

        return view('events.show', [
            'event' => $event,
        ]);
    }

    // ✅ VERIFIKASI peristiwa oleh admin + update status citizen
    public function verify(Request $request, $id)
    {
        $event = PopulationEvent::findOrFail($id);

        $validated = $request->validate(
            [
                'status_verifikasi'   => 'required|in:menunggu,disetujui,ditolak',
                'catatan_verifikasi'  => 'nullable|string',
            ],
            [
                'status_verifikasi.required' => 'Status verifikasi wajib dipilih.',
                'status_verifikasi.in'       => 'Status verifikasi tidak valid.',
            ],
            [
                'status_verifikasi' => 'Status verifikasi',
            ]
        );

        $event->status_verifikasi  = $validated['status_verifikasi'];
        $event->catatan_verifikasi = $validated['catatan_verifikasi'] ?? null;
        $event->save();

        // ✅ hanya kalau disetujui, sinkron ke master citizen + catat log ringkas
        if ($event->status_verifikasi === 'disetujui') {
            $citizen = Citizen::where('nik', $event->nik)->first();

            if ($citizen) {
                // kaitkan event ini ke master citizen (biar rapih relasinya)
                if (empty($event->citizen_id)) {
                    $event->citizen_id = $citizen->id;
                    $event->saveQuietly();
                }

                // mapping jenis_peristiwa -> status_dasar citizen
                $map = [
                    'meninggal' => 'meninggal',
                    'pindah'    => 'pindah',
                ];

                if (isset($map[$event->jenis_peristiwa])) {
                    $citizen->status_dasar = $map[$event->jenis_peristiwa];
                    $citizen->save();
                }

                // catat log ringkas ke citizen_events (untuk halaman /citizen-events)
                CitizenEvent::create([
                    'citizen_id'        => $citizen->id,
                    'nik'               => $citizen->nik,
                    'nama'              => $citizen->nama,
                    'jenis_peristiwa'   => $event->jenis_peristiwa,
                    'tanggal_peristiwa' => $event->tanggal_peristiwa,
                    'keterangan'        => $event->catatan_peristiwa ?? $event->catatan_verifikasi,
                    'dusun'             => $citizen->dusun,
                    'rw'                => $citizen->rw,
                    'rt'                => $citizen->rt,
                    'status_verifikasi' => 'verified',
                    'created_by'        => $event->created_by,
                    'verified_by'       => Auth::id(),
                ]);
            }
        }

        return redirect()
            ->route('events.show', $event->id)
            ->with('success', 'Status verifikasi peristiwa berhasil diperbarui.');
    }
}
