<?php

namespace App\Http\Controllers;

use App\Models\PopulationEvent;
use App\Models\Citizen;
use App\Models\CitizenEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PopulationEventController extends Controller
{
    /**
     * Helper: scope wilayah untuk operator (dusun/rw/rt).
     * - Untuk event biasa pakai citizen_id
     * - Untuk LAHIR versi baru pakai ibu_citizen_id
     */
    private function applyOperatorScopeWithAliases($query, $user)
    {
        if (($user->role ?? 'viewer') !== 'operator') {
            return $query;
        }

        if (empty($user->dusun)) {
            abort(403, 'Akun operator belum memiliki scope wilayah (dusun). Hubungi admin.');
        }

        // kita pakai COALESCE:
        // - kalau event lahir: ambil wilayah dari ibu (alias ibu)
        // - selain itu: dari subject (alias sub)
        $query->whereRaw('COALESCE(ibu.dusun, sub.dusun) = ?', [$user->dusun]);

        if (!empty($user->rw)) {
            $query->whereRaw('COALESCE(ibu.rw, sub.rw) = ?', [$user->rw]);
        }
        if (!empty($user->rt)) {
            $query->whereRaw('COALESCE(ibu.rt, sub.rt) = ?', [$user->rt]);
        }

        return $query;
    }

    /**
     * Helper: cari citizen by NIK + enforce scope wilayah operator.
     */
    private function findCitizenByNikWithScope(string $nik, $user): ?Citizen
    {
        $citizenQuery = Citizen::query()
            ->select(['id', 'nik', 'nama', 'no_kk', 'status_dasar', 'dusun', 'rw', 'rt', 'tanggal_lahir'])
            ->where('nik', $nik);

        if (($user->role ?? 'viewer') === 'operator') {
            if (empty($user->dusun)) {
                abort(403, 'Akun operator belum memiliki scope wilayah (dusun). Hubungi admin.');
            }
            $citizenQuery->where('dusun', $user->dusun);
            if (!empty($user->rw)) $citizenQuery->where('rw', $user->rw);
            if (!empty($user->rt)) $citizenQuery->where('rt', $user->rt);
        }

        return $citizenQuery->first();
    }

    private function normalizeTanggalLapor(array &$validated): void
    {
        if (empty($validated['tanggal_lapor'])) {
            $validated['tanggal_lapor'] = now()->toDateString();
        }
    }

    private function guardCitizenOrBack(?Citizen $citizen, string $fieldKey = 'nik')
    {
        if (!$citizen) {
            return back()
                ->withErrors([$fieldKey => 'NIK tidak ditemukan / tidak termasuk wilayah Anda.'])
                ->withInput();
        }
        return null;
    }

    private function guardCitizenAktifOrBack(Citizen $citizen, string $eventLabel, string $fieldKey = 'nik')
    {
        if (($citizen->status_dasar ?? '') !== 'aktif') {
            $status = strtoupper((string) $citizen->status_dasar);
            return back()
                ->withErrors([$fieldKey => "Penduduk ini tidak bisa dicatat peristiwa {$eventLabel} karena statusnya sudah: {$status}."])
                ->withInput();
        }
        return null;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', PopulationEvent::class);

        $user = $request->user();

        $query = PopulationEvent::query()
            ->with([
                'creator:id,name,role',
                'verifier:id,name,role',
            ])
            ->orderByDesc('id');

        // ✅ Scope operator: support event biasa + lahir (pakai ibu)
        if (($user->role ?? 'viewer') === 'operator') {
            $query
                ->leftJoin('citizens as sub', 'sub.id', '=', 'population_events.citizen_id')
                ->leftJoin('citizens as ibu', 'ibu.id', '=', 'population_events.ibu_citizen_id');

            $this->applyOperatorScopeWithAliases($query, $user);

            $query->select('population_events.*');
        }

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
                    ->orWhere('no_kk', 'like', "%{$q}%")
                    ->orWhere('nama_ibu', 'like', "%{$q}%")
                    ->orWhere('nik_ibu', 'like', "%{$q}%")
                    ->orWhere('nama_bayi', 'like', "%{$q}%");
            });
        }

        $events = $query->paginate(20)->withQueryString();

        return view('events.index', compact('events', 'filters'));
    }

    public function create()
    {
        $this->authorize('create', PopulationEvent::class);
        return view('events.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', PopulationEvent::class);
        abort(404);
    }

    // =========================
    // MENINGGAL (tidak diubah)
    // =========================
    public function createMeninggal()
    {
        $this->authorize('create', PopulationEvent::class);
        return view('events.form-meninggal');
    }

    public function storeMeninggal(Request $request)
    {
        $this->authorize('create', PopulationEvent::class);

        $user = $request->user();

        $request->merge([
            'nik' => $request->filled('nik') ? trim($request->nik) : null,
        ]);

        $validated = $request->validate(
            [
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
                'required' => ':attribute wajib diisi.',
                'date'     => ':attribute harus berupa tanggal yang valid.',
                'tanggal_peristiwa.before_or_equal' => 'Tanggal peristiwa tidak boleh lebih dari hari ini.',
                'tanggal_lapor.before_or_equal'     => 'Tanggal lapor tidak boleh lebih dari hari ini.',
                'nik.exists' => 'NIK tidak ditemukan. Pastikan penduduk sudah terdaftar di data penduduk.',
                'file_akta_kematian_path.mimes' => 'File akta harus berupa JPG, PNG, atau PDF.',
                'file_akta_kematian_path.max'   => 'Ukuran file akta maksimal 2 MB.',
            ],
            [
                'nik'               => 'NIK',
                'tanggal_peristiwa' => 'Tanggal peristiwa',
                'tanggal_lapor'     => 'Tanggal lapor',
            ]
        );

        $citizen = $this->findCitizenByNikWithScope($validated['nik'], $user);

        if ($resp = $this->guardCitizenOrBack($citizen, 'nik')) return $resp;
        if ($resp = $this->guardCitizenAktifOrBack($citizen, 'meninggal', 'nik')) return $resp;

        $this->normalizeTanggalLapor($validated);

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

    // =========================
    // HILANG (tidak diubah)
    // =========================
    public function createHilang()
    {
        $this->authorize('create', PopulationEvent::class);
        return view('events.form-hilang');
    }

    public function storeHilang(Request $request)
    {
        $this->authorize('create', PopulationEvent::class);

        $user = $request->user();

        $request->merge([
            'nik' => $request->filled('nik') ? trim($request->nik) : null,
        ]);

        $validated = $request->validate(
            [
                'nik'               => 'required|string|max:20|exists:citizens,nik',
                'tanggal_peristiwa' => 'required|date|before_or_equal:today',
                'tanggal_lapor'     => 'nullable|date|before_or_equal:today',
                'catatan_peristiwa' => 'nullable|string',
            ],
            [
                'required' => ':attribute wajib diisi.',
                'date'     => ':attribute harus berupa tanggal yang valid.',
                'tanggal_peristiwa.before_or_equal' => 'Tanggal peristiwa tidak boleh lebih dari hari ini.',
                'tanggal_lapor.before_or_equal'     => 'Tanggal lapor tidak boleh lebih dari hari ini.',
                'nik.exists' => 'NIK tidak ditemukan. Pastikan penduduk sudah terdaftar di data penduduk.',
            ],
            [
                'nik'               => 'NIK',
                'tanggal_peristiwa' => 'Tanggal peristiwa',
                'tanggal_lapor'     => 'Tanggal lapor',
                'catatan_peristiwa' => 'Catatan peristiwa',
            ]
        );

        $citizen = $this->findCitizenByNikWithScope($validated['nik'], $user);

        if ($resp = $this->guardCitizenOrBack($citizen, 'nik')) return $resp;
        if ($resp = $this->guardCitizenAktifOrBack($citizen, 'hilang', 'nik')) return $resp;

        $this->normalizeTanggalLapor($validated);

        PopulationEvent::create([
            'citizen_id'        => $citizen->id,
            'nik'               => $citizen->nik,
            'no_kk'             => $citizen->no_kk,
            'nama'              => $citizen->nama,
            'jenis_peristiwa'   => 'hilang',

            'dusun_id'          => null,
            'rw_id'             => null,
            'rt_id'             => null,

            'tanggal_peristiwa' => $validated['tanggal_peristiwa'],
            'tanggal_lapor'     => $validated['tanggal_lapor'],
            'catatan_peristiwa' => $validated['catatan_peristiwa'] ?? null,

            'created_by'        => Auth::id(),
            'status_verifikasi' => 'menunggu',
        ]);

        return redirect()
            ->route('events.index')
            ->with('success', 'Peristiwa hilang berhasil dicatat dan menunggu verifikasi admin.');
    }

    /**
     * =========================
     * LAHIR (VERSI BARU)
     * =========================
     * - Ibu wajib (nik_ibu)
     * - Ayah opsional (nik_ayah)
     * - Data bayi tanpa NIK
     * - Setelah admin setujui => status_lahir = menunggu_nik
     */
    public function createLahir()
    {
        $this->authorize('create', PopulationEvent::class);
        return view('events.form-lahir');
    }

    public function storeLahir(Request $request)
    {
        $this->authorize('create', PopulationEvent::class);

        $user = $request->user();

        // trim nik ibu/ayah
        $request->merge([
            'nik_ibu'  => $request->filled('nik_ibu') ? trim($request->nik_ibu) : null,
            'nik_ayah' => $request->filled('nik_ayah') ? trim($request->nik_ayah) : null,
        ]);

        $validated = $request->validate(
            [
                // IBU (wajib)
                'nik_ibu' => 'required|string|max:20|exists:citizens,nik',

                // AYAH (opsional)
                'nik_ayah' => 'nullable|string|max:20|exists:citizens,nik',

                // DATA BAYI
                'nama_bayi'          => 'nullable|string|max:150',
                'jenis_kelamin_bayi' => 'required|in:L,P',
                'tempat_lahir_bayi'  => 'nullable|string|max:255',
                'tanggal_lahir_bayi' => 'required|date|before_or_equal:today',
                'jam_lahir_bayi'     => 'nullable|date_format:H:i',
                'anak_ke'            => 'nullable|integer|min:1|max:50',
                'berat_lahir'        => 'nullable|numeric|min:0|max:20',
                'panjang_lahir'      => 'nullable|numeric|min:0|max:100',

                // DATA PERISTIWA
                'tanggal_lapor'      => 'nullable|date|before_or_equal:today',
                'pelapor'            => 'nullable|string|max:150',
                'hubungan_pelapor'   => 'nullable|in:ayah,ibu,bidan,lainnya',
                'catatan_peristiwa'  => 'nullable|string',
            ],
            [
                'required' => ':attribute wajib diisi.',
                'date'     => ':attribute harus berupa tanggal yang valid.',
                'tanggal_lahir_bayi.before_or_equal' => 'Tanggal lahir bayi tidak boleh lebih dari hari ini.',
                'tanggal_lapor.before_or_equal'      => 'Tanggal lapor tidak boleh lebih dari hari ini.',
                'nik_ibu.exists'  => 'NIK ibu tidak ditemukan. Pastikan ibu sudah terdaftar di data penduduk.',
                'nik_ayah.exists' => 'NIK ayah tidak ditemukan. Pastikan ayah sudah terdaftar di data penduduk.',
                'jam_lahir_bayi.date_format' => 'Jam lahir bayi harus format HH:MM.',
            ],
            [
                'nik_ibu'            => 'NIK Ibu',
                'nik_ayah'           => 'NIK Ayah',
                'jenis_kelamin_bayi' => 'Jenis kelamin bayi',
                'tanggal_lahir_bayi' => 'Tanggal lahir bayi',
                'tanggal_lapor'      => 'Tanggal lapor',
            ]
        );

        // cari IBU dengan scope operator
        $ibu = $this->findCitizenByNikWithScope($validated['nik_ibu'], $user);
        if ($resp = $this->guardCitizenOrBack($ibu, 'nik_ibu')) return $resp;
        if ($resp = $this->guardCitizenAktifOrBack($ibu, 'lahir', 'nik_ibu')) return $resp;

        // cari AYAH jika diisi
        $ayah = null;
        if (!empty($validated['nik_ayah'])) {
            $ayah = $this->findCitizenByNikWithScope($validated['nik_ayah'], $user);
            if ($resp = $this->guardCitizenOrBack($ayah, 'nik_ayah')) return $resp;
            if ($resp = $this->guardCitizenAktifOrBack($ayah, 'lahir', 'nik_ayah')) return $resp;
        }

        $this->normalizeTanggalLapor($validated);

        // tanggal_peristiwa kita set = tanggal_lahir_bayi (biar konsisten di list)
        $tanggalPeristiwa = $validated['tanggal_lahir_bayi'];

        PopulationEvent::create([
            'jenis_peristiwa' => 'lahir',

            // identitas event (utama ibu)
            'ibu_citizen_id' => $ibu->id,
            'nik_ibu'        => $ibu->nik,
            'no_kk_ibu'      => $ibu->no_kk,
            'nama_ibu'       => $ibu->nama,

            // ayah opsional
            'ayah_citizen_id' => $ayah?->id,
            'nik_ayah'        => $ayah?->nik,
            'no_kk_ayah'      => $ayah?->no_kk,
            'nama_ayah'       => $ayah?->nama,

            // untuk kolom legacy (biar list/search lama tidak blank total):
            // kita isi 'nama' dengan "Bayi dari <nama ibu>"
            'nama' => 'Bayi dari ' . ($ibu->nama ?? '-'),

            // wilayah mengikuti ibu (untuk display bisa kamu mapping ke master id kalau nanti ada)
            'dusun_id' => null,
            'rw_id'    => null,
            'rt_id'    => null,

            // waktu
            'tanggal_peristiwa'  => $tanggalPeristiwa,
            'tanggal_lapor'      => $validated['tanggal_lapor'],

            // data bayi
            'nama_bayi'          => $validated['nama_bayi'] ?? null,
            'jenis_kelamin_bayi' => $validated['jenis_kelamin_bayi'],
            'tempat_lahir_bayi'  => $validated['tempat_lahir_bayi'] ?? null,
            'tanggal_lahir_bayi' => $validated['tanggal_lahir_bayi'],
            'jam_lahir_bayi'     => $validated['jam_lahir_bayi'] ?? null,
            'anak_ke'            => $validated['anak_ke'] ?? null,
            'berat_lahir'        => $validated['berat_lahir'] ?? null,
            'panjang_lahir'      => $validated['panjang_lahir'] ?? null,

            // data pelapor
            'pelapor'          => $validated['pelapor'] ?? null,
            'hubungan_pelapor' => $validated['hubungan_pelapor'] ?? null,

            // catatan
            'catatan_peristiwa' => $validated['catatan_peristiwa'] ?? null,

            // audit
            'created_by'        => Auth::id(),
            'status_verifikasi' => 'menunggu',
            'status_lahir'      => 'menunggu_verifikasi',
        ]);

        return redirect()
            ->route('events.index')
            ->with('success', 'Peristiwa lahir berhasil dicatat dan menunggu verifikasi admin.');
    }

    // placeholders lain
    // =========================
    // DATANG
    // =========================
    public function createDatang()
    {
        $this->authorize('create', PopulationEvent::class);
        return view('events.form-datang');
    }

    public function storeDatang(Request $request)
    {
        $this->authorize('create', PopulationEvent::class);

        $user = $request->user();

        // normalisasi input
        $request->merge([
            'nik' => $request->filled('nik') ? trim($request->nik) : null,
            'citizen_mode' => $request->filled('citizen_mode') ? trim($request->citizen_mode) : 'existing',
        ]);

        $validated = $request->validate(
            [
                // mode existing/new
                'citizen_mode' => 'required|in:existing,new',

                // identitas utama
                'nik' => 'required|string|max:20',

                // citizen baru (minimal) - hanya wajib jika mode=new
                'nama' => 'required_if:citizen_mode,new|string|max:150',
                'no_kk' => 'nullable|string|max:20',
                'jenis_kelamin' => 'required_if:citizen_mode,new|in:L,P',
                'tanggal_lahir' => 'required_if:citizen_mode,new|date|before_or_equal:today',
                'status_perkawinan' => 'required_if:citizen_mode,new|string|max:100',
                'agama' => 'required_if:citizen_mode,new|string|max:100',
                'pendidikan_dalam_kk' => 'nullable|string|max:100',
                'pekerjaan' => 'nullable|string|max:100',

                // asal
                'alamat_asal' => 'required|string',
                'desa_asal' => 'required|string|max:255',
                'kecamatan_asal' => 'required|string|max:255',
                'kabupaten_asal' => 'required|string|max:255',
                'provinsi_asal' => 'required|string|max:255',

                // tujuan (patimban)
                'alamat_sekarang_tujuan' => 'required|string',
                'dusun_tujuan' => 'required|string|max:255',
                'rw_tujuan' => 'required|string|max:50',
                'rt_tujuan' => 'required|string|max:50',

                // detail datang
                'tanggal_datang' => 'required|date|before_or_equal:today',
                'tanggal_lapor' => 'nullable|date|before_or_equal:today',
                'alasan_datang' => 'nullable|in:kerja,nikah,keluarga,lainnya',
                'status_datang' => 'nullable|in:tetap,sementara',
                'rencana_tinggal' => 'nullable|string|max:255',
                'pelapor' => 'nullable|string|max:150',
                'hubungan_pelapor' => 'nullable|string|max:100',
                'catatan_peristiwa' => 'nullable|string',
            ],
            [
                'required' => ':attribute wajib diisi.',
                'required_if' => ':attribute wajib diisi.',
                'date' => ':attribute harus berupa tanggal yang valid.',
                'before_or_equal' => ':attribute tidak boleh lebih dari hari ini.',
            ],
            [
                'nik' => 'NIK',
                'nama' => 'Nama',
                'jenis_kelamin' => 'Jenis kelamin',
                'tanggal_lahir' => 'Tanggal lahir',
                'status_perkawinan' => 'Status perkawinan',
                'agama' => 'Agama',

                'alamat_asal' => 'Alamat asal',
                'desa_asal' => 'Desa asal',
                'kecamatan_asal' => 'Kecamatan asal',
                'kabupaten_asal' => 'Kabupaten asal',
                'provinsi_asal' => 'Provinsi asal',

                'alamat_sekarang_tujuan' => 'Alamat sekarang (tujuan)',
                'dusun_tujuan' => 'Dusun tujuan',
                'rw_tujuan' => 'RW tujuan',
                'rt_tujuan' => 'RT tujuan',

                'tanggal_datang' => 'Tanggal datang',
                'tanggal_lapor' => 'Tanggal lapor',
            ]
        );

        // ✅ enforce scope operator berdasarkan TUJUAN
        if (($user->role ?? 'viewer') === 'operator') {
            if (empty($user->dusun)) {
                return back()->withErrors([
                    'nik' => 'Akun operator belum memiliki scope wilayah (dusun). Hubungi admin.',
                ])->withInput();
            }

            // operator hanya boleh input sesuai wilayahnya
            if (($validated['dusun_tujuan'] ?? '') !== $user->dusun) {
                return back()->withErrors([
                    'dusun_tujuan' => 'Dusun tujuan harus sesuai scope wilayah operator.',
                ])->withInput();
            }
            if (!empty($user->rw) && ($validated['rw_tujuan'] ?? '') !== $user->rw) {
                return back()->withErrors([
                    'rw_tujuan' => 'RW tujuan harus sesuai scope wilayah operator.',
                ])->withInput();
            }
            if (!empty($user->rt) && ($validated['rt_tujuan'] ?? '') !== $user->rt) {
                return back()->withErrors([
                    'rt_tujuan' => 'RT tujuan harus sesuai scope wilayah operator.',
                ])->withInput();
            }
        }

        // tanggal lapor auto
        $this->normalizeTanggalLapor($validated);

        // tanggal_peristiwa untuk datang kita samakan dengan tanggal_datang (biar konsisten list)
        $tanggalPeristiwa = $validated['tanggal_datang'];

        // =========================
        // 1) Tentukan Citizen (existing / new)
        // =========================
        $citizen = null;

        if ($validated['citizen_mode'] === 'existing') {
            // existing: wajib ada di citizens
            $citizen = Citizen::query()->where('nik', $validated['nik'])->first();

            if (!$citizen) {
                return back()
                    ->withErrors(['nik' => 'NIK tidak ditemukan. Jika warga belum terdaftar, gunakan mode input penduduk baru.'])
                    ->withInput();
            }

            // optional: kalau status bukan aktif, kamu bisa tetap izinkan datang (karena bisa penduduk “pindah balik”)
            // tapi untuk aman, kita blok kalau status_dasar bukan aktif
            if (($citizen->status_dasar ?? '') !== 'aktif') {
                $status = strtoupper((string) $citizen->status_dasar);
                return back()
                    ->withErrors(['nik' => "Penduduk ini tidak bisa dicatat peristiwa Datang karena statusnya: {$status}."])
                    ->withInput();
            }
        } else {
            // new: buat citizen minimal
            // guard: nik tidak boleh sudah ada
            $exists = Citizen::query()->where('nik', $validated['nik'])->exists();
            if ($exists) {
                return back()
                    ->withErrors(['nik' => 'NIK sudah terdaftar. Silakan klik Cek NIK dan gunakan mode existing.'])
                    ->withInput();
            }

            $citizen = Citizen::create([
                'nik' => $validated['nik'],
                'no_kk' => $validated['no_kk'] ?? null,
                'nama' => $validated['nama'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'tanggal_lahir' => $validated['tanggal_lahir'],
                'agama' => $validated['agama'],
                'pendidikan_dalam_kk' => $validated['pendidikan_dalam_kk'] ?? null,
                'pekerjaan' => $validated['pekerjaan'] ?? null,
                'status_perkawinan' => $validated['status_perkawinan'],

                // domisili baru (patimban) -> masuk master
                'alamat_sekarang' => $validated['alamat_sekarang_tujuan'],
                'dusun' => $validated['dusun_tujuan'],
                'rw' => $validated['rw_tujuan'],
                'rt' => $validated['rt_tujuan'],

                'status_dasar' => 'aktif',
            ]);
        }

        // =========================
        // 2) Update domisili citizen ke TUJUAN (opsional tapi realistis)
        // =========================
        // untuk existing pun, saat datang biasanya domisili patimban berubah
        $citizen->alamat_sekarang = $validated['alamat_sekarang_tujuan'];
        $citizen->dusun = $validated['dusun_tujuan'];
        $citizen->rw = $validated['rw_tujuan'];
        $citizen->rt = $validated['rt_tujuan'];
        $citizen->save();

        // =========================
        // 3) Simpan Event Datang
        // =========================
        PopulationEvent::create([
            'citizen_id' => $citizen->id,
            'nik' => $citizen->nik,
            'no_kk' => $citizen->no_kk,
            'nama' => $citizen->nama,
            'jenis_peristiwa' => 'datang',

            'tanggal_peristiwa' => $tanggalPeristiwa,
            'tanggal_datang' => $validated['tanggal_datang'],
            'tanggal_lapor' => $validated['tanggal_lapor'],

            // asal
            'alamat_asal' => $validated['alamat_asal'],
            'desa_asal' => $validated['desa_asal'],
            'kecamatan_asal' => $validated['kecamatan_asal'],
            'kabupaten_asal' => $validated['kabupaten_asal'],
            'provinsi_asal' => $validated['provinsi_asal'],

            // tujuan
            'alamat_sekarang_tujuan' => $validated['alamat_sekarang_tujuan'],
            'dusun_tujuan' => $validated['dusun_tujuan'],
            'rw_tujuan' => $validated['rw_tujuan'],
            'rt_tujuan' => $validated['rt_tujuan'],

            // atribut
            'alasan_datang' => $validated['alasan_datang'] ?? null,
            'status_datang' => $validated['status_datang'] ?? null,
            'rencana_tinggal' => $validated['rencana_tinggal'] ?? null,

            // pelapor umum
            'pelapor' => $validated['pelapor'] ?? null,
            'hubungan_pelapor' => $validated['hubungan_pelapor'] ?? null,

            // catatan
            'catatan_peristiwa' => $validated['catatan_peristiwa'] ?? null,

            // audit
            'created_by' => Auth::id(),
            'status_verifikasi' => 'menunggu',
        ]);

        return redirect()
            ->route('events.index')
            ->with('success', 'Peristiwa datang berhasil dicatat dan menunggu verifikasi admin.');
    }


    public function createPindah()
    {
        $this->authorize('create', PopulationEvent::class);
        abort(404);
    }
    public function storePindah(Request $request)
    {
        $this->authorize('create', PopulationEvent::class);
        abort(404);
    }
    public function createSementara()
    {
        $this->authorize('create', PopulationEvent::class);
        abort(404);
    }
    public function storeSementara(Request $request)
    {
        $this->authorize('create', PopulationEvent::class);
        abort(404);
    }

    public function show($id)
    {
        $event = PopulationEvent::query()
            ->with([
                'creator:id,name,role',
                'verifier:id,name,role',
            ])
            ->findOrFail($id);

        // ✅ Policy: operator dibatasi scope dusun/rw/rt (logic ada di policy)
        $this->authorize('view', $event);

        $ibuCitizen = null;
        $ayahCitizen = null;
        $umurIbu = null;

        if ($event->jenis_peristiwa === 'lahir') {

            // ===== IBU =====
            // Prioritas 1: ibu_citizen_id (kalau kamu sudah simpan)
            if (!empty($event->ibu_citizen_id)) {
                $ibuCitizen = Citizen::query()
                    ->select(['id', 'nik', 'nama', 'no_kk', 'tanggal_lahir', 'alamat', 'dusun', 'rw', 'rt', 'status_perkawinan'])
                    ->where('id', $event->ibu_citizen_id)
                    ->first();
            }

            // Prioritas 2: nik_ibu (kalau kamu simpan nik ibu di event)
            if (!$ibuCitizen && !empty($event->nik_ibu)) {
                $ibuCitizen = Citizen::query()
                    ->select(['id', 'nik', 'nama', 'no_kk', 'tanggal_lahir', 'alamat', 'dusun', 'rw', 'rt', 'status_perkawinan'])
                    ->where('nik', $event->nik_ibu)
                    ->first();
            }

            if ($ibuCitizen && !empty($ibuCitizen->tanggal_lahir)) {
                try {
                    $umurIbu = \Carbon\Carbon::parse($ibuCitizen->tanggal_lahir)->age;
                } catch (\Throwable $e) {
                    $umurIbu = null;
                }
            }

            // ===== AYAH =====
            if (!empty($event->ayah_citizen_id)) {
                $ayahCitizen = Citizen::query()
                    ->select(['id', 'nik', 'nama', 'no_kk', 'tanggal_lahir', 'alamat', 'dusun', 'rw', 'rt', 'status_perkawinan'])
                    ->where('id', $event->ayah_citizen_id)
                    ->first();
            }

            if (!$ayahCitizen && !empty($event->nik_ayah)) {
                $ayahCitizen = Citizen::query()
                    ->select(['id', 'nik', 'nama', 'no_kk', 'tanggal_lahir', 'alamat', 'dusun', 'rw', 'rt', 'status_perkawinan'])
                    ->where('nik', $event->nik_ayah)
                    ->first();
            }
        }

        return view('events.show', [
            'event' => $event,
            'ibuCitizen' => $ibuCitizen,
            'ayahCitizen' => $ayahCitizen,
            'umurIbu' => $umurIbu,
        ]);
    }

    public function verify(Request $request, $id)
    {
        $event = PopulationEvent::findOrFail($id);

        $this->authorize('verify', $event);

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

        $event->status_verifikasi  = $newStatus;
        $event->catatan_verifikasi = $validated['catatan_verifikasi'] ?? null;

        if (in_array($newStatus, ['disetujui', 'ditolak'], true)) {
            $event->verified_by = Auth::id();
            $event->verified_at = now();
        } else {
            $event->verified_by = null;
            $event->verified_at = null;
        }

        // ✅ khusus LAHIR: set status_lahir berdasarkan keputusan admin
        if ($event->jenis_peristiwa === 'lahir') {
            if ($newStatus === 'disetujui') {
                $event->status_lahir = 'menunggu_nik';
            } elseif ($newStatus === 'ditolak') {
                $event->status_lahir = 'ditolak';
            } else {
                $event->status_lahir = 'menunggu_verifikasi';
            }
        }

        $event->save();

        if ($oldStatus === $newStatus) {
            return redirect()
                ->route('events.show', $event->id)
                ->with('success', 'Status verifikasi tidak berubah.');
        }

        // ✅ event yang mengubah status_dasar penduduk (bukan lahir)
        $map = [
            'meninggal' => 'meninggal',
            'pindah'    => 'pindah',
            'hilang'    => 'hilang',
        ];

        // kalau bukan event status_dasar, cukup selesai (lahir masuk sini)
        if (!isset($map[$event->jenis_peristiwa])) {
            return redirect()
                ->route('events.show', $event->id)
                ->with(
                    'success',
                    $event->jenis_peristiwa === 'lahir'
                        ? 'Verifikasi peristiwa lahir berhasil diperbarui. Status lahir: ' . strtoupper((string) $event->status_lahir)
                        : 'Status verifikasi berhasil diperbarui.'
                );
        }

        // =========================
        // Logic lama (status_dasar)
        // =========================
        $citizen = null;

        if (!empty($event->citizen_id)) {
            $citizen = Citizen::find($event->citizen_id);
        } elseif (!empty($event->nik)) {
            $citizen = Citizen::where('nik', $event->nik)->first();
        }

        if (!$citizen) {
            return redirect()
                ->route('events.show', $event->id)
                ->with('error', 'Penduduk tidak ditemukan di master citizen.');
        }

        if (empty($event->citizen_id)) {
            $event->citizen_id = $citizen->id;
            $event->saveQuietly();
        }

        if ($newStatus === 'disetujui' && empty($event->status_applied_at)) {
            $event->previous_status_dasar = $citizen->status_dasar;
            $event->status_applied_at     = now();
            $event->status_applied_by     = Auth::id();
            $event->saveQuietly();

            $citizen->status_dasar = $map[$event->jenis_peristiwa];
            $citizen->save();

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

        if ($oldStatus === 'disetujui' && $newStatus !== 'disetujui' && !empty($event->status_applied_at)) {
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

            $citizen->status_dasar = $event->previous_status_dasar ?? 'aktif';
            $citizen->save();

            $event->status_applied_at = null;
            $event->status_applied_by = null;
            $event->saveQuietly();

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

        return redirect()
            ->route('events.show', $event->id)
            ->with('success', 'Status verifikasi berhasil diperbarui.');
    }
}
