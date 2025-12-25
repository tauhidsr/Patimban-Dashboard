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
        $query = PopulationEvent::query()
            ->with([
                'creator:id,name,role',
                'verifier:id,name,role',
            ])
            ->orderBy('id', 'desc');

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
            'nik' => $request->filled('nik') ? trim($request->nik) : null,
        ]);

        $validated = $request->validate(
            [
                // ✅ operator cukup pilih NIK (harus ada di master citizen)
                'nik'               => 'required|string|max:20|exists:citizens,nik',

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
                'nik'               => 'NIK',
                'tanggal_peristiwa' => 'Tanggal peristiwa',
                'tanggal_lapor'     => 'Tanggal lapor',
            ]
        );

        // ✅ pastikan penduduk ada & masih AKTIF (biar tidak dobel/peristiwa salah)
        $citizen = Citizen::query()
            ->where('nik', $validated['nik'])
            ->select(['id', 'nik', 'nama', 'no_kk', 'status_dasar'])
            ->first();

        if (!$citizen) {
            return back()
                ->withErrors(['nik' => 'NIK tidak ditemukan. Pastikan penduduk sudah terdaftar di data penduduk.'])
                ->withInput();
        }

        if (($citizen->status_dasar ?? '') !== 'aktif') {
            $status = strtoupper((string) $citizen->status_dasar);
            return back()
                ->withErrors(['nik' => "Penduduk ini tidak bisa dicatat peristiwa meninggal karena statusnya sudah: {$status}."])
                ->withInput();
        }

        // ✅ sinkronkan no_kk & nama dari master (operator tidak bisa manipulasi)
        $validated['no_kk'] = $citizen->no_kk;
        $validated['nama']  = $citizen->nama;


        // ✅ ambil data master citizen sebagai sumber kebenaran
        $citizen = Citizen::where('nik', $validated['nik'])->firstOrFail();

        if (empty($validated['tanggal_lapor'])) {
            $validated['tanggal_lapor'] = now()->toDateString();
        }

        $filePath = null;
        if ($request->hasFile('file_akta_kematian_path')) {
            $filePath = $request->file('file_akta_kematian_path')->store('akta-kematian', 'public');
        }

        PopulationEvent::create([
            'citizen_id'               => $citizen->id,
            'nik'                      => $citizen->nik,
            'no_kk'                    => $citizen->no_kk,
            'nama'                     => $citizen->nama,
            'jenis_peristiwa'          => 'meninggal',

            'dusun_id'                 => null,
            'rw_id'                    => null,
            'rt_id'                    => null,

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
                'status_verifikasi'  => 'required|in:menunggu,disetujui,ditolak',
                'catatan_verifikasi' => 'nullable|string',
            ],
            [
                'status_verifikasi.required' => 'Status verifikasi wajib dipilih.',
                'status_verifikasi.in'       => 'Status verifikasi tidak valid.',
            ],
            [
                'status_verifikasi' => 'Status verifikasi',
            ]
        );

        $oldStatus = $event->status_verifikasi;
        $newStatus = $validated['status_verifikasi'];

        // update status verifikasi event
        $event->status_verifikasi  = $newStatus;
        $event->catatan_verifikasi = $validated['catatan_verifikasi'] ?? null;

        // ✅ Step A3: isi/clear verified_by + verified_at
        if (in_array($newStatus, ['disetujui', 'ditolak'], true)) {
            $event->verified_by = Auth::id();
            $event->verified_at = now();
        } else {
            $event->verified_by = null;
            $event->verified_at = null;
        }

        $event->save();

        // ⛔ kalau status tidak berubah, stop di sini
        if ($oldStatus === $newStatus) {
            return redirect()
                ->route('events.show', $event->id)
                ->with('success', 'Status verifikasi tidak berubah.');
        }

        // ambil citizen
        $citizen = Citizen::where('nik', $event->nik)->first();

        if (!$citizen) {
            return redirect()
                ->route('events.show', $event->id)
                ->with('error', 'Penduduk tidak ditemukan di master citizen.');
        }

        // pastikan relasi citizen_id tersimpan
        if (empty($event->citizen_id)) {
            $event->citizen_id = $citizen->id;
            $event->saveQuietly();
        }

        // mapping jenis peristiwa -> status citizen
        $map = [
            'meninggal' => 'meninggal',
            'pindah'    => 'pindah',
        ];

        /**
         * =====================================================
         * A) MENJADI DISETUJUI (APPLY)
         * =====================================================
         */
        if ($newStatus === 'disetujui' && empty($event->status_applied_at)) {

            // simpan status sebelumnya
            $event->previous_status_dasar = $citizen->status_dasar;
            $event->status_applied_at     = now();
            $event->status_applied_by     = Auth::id();
            $event->saveQuietly();

            // apply status ke citizen
            if (isset($map[$event->jenis_peristiwa])) {
                $citizen->status_dasar = $map[$event->jenis_peristiwa];
                $citizen->save();
            }

            // log verified
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

            return redirect()
                ->route('events.show', $event->id)
                ->with('success', 'Peristiwa disetujui. Status penduduk berhasil diperbarui.');
        }

        /**
         * =====================================================
         * B) DISETUJUI → DITOLAK / MENUNGGU (REVERT)
         * =====================================================
         */
        if ($oldStatus === 'disetujui' && $newStatus !== 'disetujui' && !empty($event->status_applied_at)) {

            // ⛔ SAFETY: hanya event TERAKHIR yang boleh direvert
            $lastApplied = PopulationEvent::query()
                ->where('citizen_id', $citizen->id)
                ->whereNotNull('status_applied_at')
                ->orderByDesc('status_applied_at')
                ->first();

            if ($lastApplied && (int) $lastApplied->id !== (int) $event->id) {
                return redirect()
                    ->route('events.show', $event->id)
                    ->with('error', 'Tidak bisa membatalkan karena sudah ada peristiwa lain yang disetujui setelah ini.');
            }

            // revert status citizen
            $citizen->status_dasar = $event->previous_status_dasar ?? 'aktif';
            $citizen->save();

            // reset penanda apply
            $event->status_applied_at = null;
            $event->status_applied_by = null;
            $event->saveQuietly();

            // log pembatalan (audit trail)
            CitizenEvent::create([
                'citizen_id'        => $citizen->id,
                'nik'               => $citizen->nik,
                'nama'              => $citizen->nama,
                'jenis_peristiwa'   => $event->jenis_peristiwa,
                'tanggal_peristiwa' => $event->tanggal_peristiwa,
                'keterangan'        => 'Persetujuan peristiwa dibatalkan. Status penduduk dikembalikan ke status sebelumnya.',
                'dusun'             => $citizen->dusun,
                'rw'                => $citizen->rw,
                'rt'                => $citizen->rt,
                'status_verifikasi' => 'voided',
                'created_by'        => $event->created_by,
                'verified_by'       => Auth::id(),
            ]);

            return redirect()
                ->route('events.show', $event->id)
                ->with('success', 'Persetujuan dibatalkan. Status penduduk berhasil dikembalikan.');
        }

        /**
         * =====================================================
         * C) MENUNGGU ↔ DITOLAK (tidak sentuh citizen)
         * =====================================================
         */
        return redirect()
            ->route('events.show', $event->id)
            ->with('success', 'Status verifikasi berhasil diperbarui.');
    }
}
