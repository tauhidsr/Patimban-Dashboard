{{-- resources/views/events/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Detail Peristiwa Kependudukan') }}
        </h2>
    </x-slot>

    @php
        // =========================
        // CONFIG / HELPERS
        // =========================
        $mapJenis = [
            'lahir' => 'Lahir',
            'datang' => 'Datang',
            'pindah' => 'Pindah',
            'meninggal' => 'Meninggal',
            'hilang' => 'Hilang',
            'sementara_masuk' => 'Sementara Masuk',
            'sementara_keluar' => 'Sementara Keluar',
        ];
        $labelJenis = $mapJenis[$event->jenis_peristiwa] ?? $event->jenis_peristiwa;

        // event yang memang mengubah status_dasar penduduk
        $jenisMengubahStatus = in_array($event->jenis_peristiwa, ['meninggal', 'pindah', 'hilang'], true);

        // helper format jam aman
        $fmtTime = function ($val) {
            if (!$val)
                return '-';
            if (is_string($val))
                return substr($val, 0, 5);

            try {
                return $val->format('H:i');
            } catch (\Throwable $e) {
                return (string) $val;
            }
        };

        // mapping status lahir
        $mapStatusLahir = [
            'menunggu_verifikasi' => 'Menunggu Verifikasi',
            'menunggu_nik' => 'Menunggu NIK Bayi',
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak',
        ];

        $isLahir = ($event->jenis_peristiwa === 'lahir');

        // ✅ aman walau controller belum ngirim variabel
        $ibuCitizen = $ibuCitizen ?? null;
        $ayahCitizen = $ayahCitizen ?? null;
        $umurIbu = $umurIbu ?? null;

        $namaHeader = $isLahir
            ? ($event->nama_bayi ?: ('Bayi dari ' . ($event->nama_ibu ?? '-')))
            : ($event->nama ?? '-');

        $subHeader = $isLahir
            ? ('NIK Ibu: ' . ($event->nik_ibu ?? '-') . ' · No KK Ibu: ' . ($event->no_kk_ibu ?? '-'))
            : ('NIK: ' . ($event->nik ?? '-') . ' · No KK: ' . ($event->no_kk ?? '-'));

        // badge jenis peristiwa
        $badgeJenisClass = 'bg-gray-100 text-gray-800';
        if ($event->jenis_peristiwa === 'meninggal')
            $badgeJenisClass = 'bg-red-100 text-red-800';
        elseif ($event->jenis_peristiwa === 'pindah')
            $badgeJenisClass = 'bg-yellow-100 text-yellow-800';
        elseif (in_array($event->jenis_peristiwa, ['lahir', 'datang'], true))
            $badgeJenisClass = 'bg-green-100 text-green-800';

        // badge status verifikasi
        $badgeVerifClass = 'bg-red-100 text-red-800';
        if ($event->status_verifikasi === 'menunggu')
            $badgeVerifClass = 'bg-orange-100 text-orange-800';
        elseif ($event->status_verifikasi === 'disetujui')
            $badgeVerifClass = 'bg-green-100 text-green-800';

        // badge status lahir
        $badgeStatusLahirClass = 'bg-gray-100 text-gray-800';
        if (($event->status_lahir ?? null) === 'menunggu_nik')
            $badgeStatusLahirClass = 'bg-yellow-100 text-yellow-800';
        elseif (($event->status_lahir ?? null) === 'selesai')
            $badgeStatusLahirClass = 'bg-green-100 text-green-800';
        elseif (($event->status_lahir ?? null) === 'ditolak')
            $badgeStatusLahirClass = 'bg-red-100 text-red-800';
    @endphp

    <div class="py-6">
        <div class="max-w-4xl mx-auto space-y-6 sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">

                {{-- =========================
                HEADER
                ========================== --}}
                <div class="flex items-start justify-between gap-4 px-6 py-4 border-b">
                    <div>
                        <p class="text-sm text-gray-500">
                            {{ $isLahir ? 'Identitas Utama (Bayi)' : 'Nama Penduduk' }}
                        </p>

                        <p class="text-lg font-semibold text-gray-900">
                            {{ $namaHeader }}
                        </p>

                        <p class="mt-1 text-sm text-gray-500">
                            {{ $subHeader }}
                        </p>

                        @if($isLahir && ($event->tanggal_lahir_bayi || $event->jenis_kelamin_bayi))
                            <p class="mt-1 text-xs text-gray-500">
                                @if($event->tanggal_lahir_bayi)
                                    Tgl lahir: {{ $event->tanggal_lahir_bayi->format('d-m-Y') }}
                                @endif

                                @if($event->tanggal_lahir_bayi && $event->jenis_kelamin_bayi)
                                    &middot;
                                @endif

                                @if($event->jenis_kelamin_bayi)
                                    JK:
                                    {{ $event->jenis_kelamin_bayi === 'L' ? 'Laki-laki' : ($event->jenis_kelamin_bayi === 'P' ? 'Perempuan' : '-') }}
                                @endif
                            </p>
                        @endif
                    </div>

                    <div class="text-right">
                        <span
                            class="inline-flex mb-1 px-3 py-1 text-xs font-semibold rounded-full {{ $badgeJenisClass }}">
                            {{ $labelJenis }}
                        </span>

                        <div>
                            <span
                                class="inline-flex px-3 py-1 text-xs font-semibold rounded-full {{ $badgeVerifClass }}">
                                Status: {{ ucfirst($event->status_verifikasi) }}
                            </span>
                        </div>

                        @if($isLahir && $event->status_lahir)
                            <div class="mt-2">
                                <span
                                    class="inline-flex px-3 py-1 text-xs font-semibold rounded-full {{ $badgeStatusLahirClass }}">
                                    Status Lahir: {{ $mapStatusLahir[$event->status_lahir] ?? $event->status_lahir }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- =========================
                BODY
                ========================== --}}
                <div class="px-6 py-4 space-y-4">

                    {{-- WARNING hanya untuk event yang mengubah status penduduk --}}
                    @if ($event->status_verifikasi === 'disetujui' && $jenisMengubahStatus)
                        <div class="p-4 border border-red-300 rounded-lg bg-red-50">
                            <div class="flex gap-3">
                                <div class="text-xl">⚠️</div>
                                <div>
                                    <p class="font-semibold text-red-800">
                                        Perhatian – Peristiwa Sudah Disetujui
                                    </p>
                                    <p class="mt-1 text-sm text-red-700">
                                        Peristiwa ini <strong>sudah mengubah status penduduk</strong>.
                                        Jika verifikasi diubah, sistem akan mencoba <strong>mengembalikan status ke
                                            sebelumnya</strong>
                                        dan mencatat log pembatalan.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- tanggal --}}
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">
                                {{ $isLahir ? 'Tanggal Lahir Bayi' : 'Tanggal Peristiwa' }}
                            </p>
                            <p class="mt-1 text-sm text-gray-800">
                                @if($isLahir)
                                    {{ $event->tanggal_lahir_bayi ? $event->tanggal_lahir_bayi->format('d-m-Y') : '-' }}
                                @else
                                    {{ $event->tanggal_peristiwa ? $event->tanggal_peristiwa->format('d-m-Y') : '-' }}
                                @endif
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Tanggal Lapor</p>
                            <p class="mt-1 text-sm text-gray-800">
                                {{ $event->tanggal_lapor ? $event->tanggal_lapor->format('d-m-Y') : '-' }}
                            </p>
                        </div>
                    </div>

                    {{-- =========================
                    DETAIL KHUSUS LAHIR
                    ========================== --}}
                    @if($isLahir)
                        {{-- Data Bayi --}}
                        <div class="pt-4 mt-2 border-t">
                            <h3 class="mb-2 text-sm font-semibold text-gray-700">Data Bayi (sementara)</h3>

                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase">Nama Bayi</p>
                                    <p class="mt-1 text-sm text-gray-800">{{ $event->nama_bayi ?? '-' }}</p>
                                </div>

                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase">Jenis Kelamin</p>
                                    <p class="mt-1 text-sm text-gray-800">
                                        @if($event->jenis_kelamin_bayi === 'L') Laki-laki
                                        @elseif($event->jenis_kelamin_bayi === 'P') Perempuan
                                        @else - @endif
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase">Tempat Lahir</p>
                                    <p class="mt-1 text-sm text-gray-800">{{ $event->tempat_lahir_bayi ?? '-' }}</p>
                                </div>

                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase">Jam Lahir</p>
                                    <p class="mt-1 text-sm text-gray-800">{{ $fmtTime($event->jam_lahir_bayi) }}</p>
                                </div>

                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase">Anak Ke-</p>
                                    <p class="mt-1 text-sm text-gray-800">{{ $event->anak_ke ?? '-' }}</p>
                                </div>

                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase">Berat / Panjang</p>
                                    <p class="mt-1 text-sm text-gray-800">
                                        {{ $event->berat_lahir ? $event->berat_lahir . ' kg' : '-' }}
                                        &middot;
                                        {{ $event->panjang_lahir ? $event->panjang_lahir . ' cm' : '-' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Data Ibu --}}
                        <div class="pt-4 mt-2 border-t">
                            <h3 class="mb-2 text-sm font-semibold text-gray-700">Data Ibu (wajib)</h3>

                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase">NIK Ibu</p>
                                    <p class="mt-1 text-sm text-gray-800">{{ $ibuCitizen->nik ?? $event->nik_ibu ?? '-' }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase">Nama Ibu</p>
                                    <p class="mt-1 text-sm text-gray-800">{{ $ibuCitizen->nama ?? $event->nama_ibu ?? '-' }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase">Umur Ibu</p>
                                    <p class="mt-1 text-sm text-gray-800">
                                        {{ $umurIbu !== null ? $umurIbu . ' tahun' : '-' }}</p>
                                </div>

                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase">No KK Ibu</p>
                                    <p class="mt-1 text-sm text-gray-800">
                                        {{ $ibuCitizen->no_kk ?? $event->no_kk_ibu ?? '-' }}</p>
                                </div>

                                <div class="sm:col-span-2">
                                    <p class="text-xs font-semibold text-gray-500 uppercase">Alamat Ibu</p>
                                    <p class="mt-1 text-sm text-gray-800 whitespace-pre-line">
                                        {{ $ibuCitizen->alamat ?? $event->alamat_ibu ?? '-' }}
                                    </p>
                                </div>

                                <div class="sm:col-span-2">
                                    <p class="text-xs font-semibold text-gray-500 uppercase">Wilayah (Ibu)</p>
                                    <p class="mt-1 text-sm text-gray-800">
                                        Dusun: {{ $ibuCitizen->dusun ?? '-' }}
                                        · RW: {{ $ibuCitizen->rw ?? '-' }}
                                        · RT: {{ $ibuCitizen->rt ?? '-' }}
                                    </p>

                                    @if(!$ibuCitizen)
                                        <p
                                            class="p-2 mt-1 text-xs text-yellow-700 border border-yellow-200 rounded bg-yellow-50">
                                            Data ibu belum ditemukan di master penduduk (Citizen). Pastikan NIK ibu valid &
                                            terdaftar.
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Data Ayah --}}
                        <div class="pt-4 mt-2 border-t">
                            <h3 class="mb-2 text-sm font-semibold text-gray-700">Data Ayah (opsional)</h3>

                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase">NIK Ayah</p>
                                    <p class="mt-1 text-sm text-gray-800">{{ $ayahCitizen->nik ?? $event->nik_ayah ?? '-' }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase">Nama Ayah</p>
                                    <p class="mt-1 text-sm text-gray-800">
                                        {{ $ayahCitizen->nama ?? $event->nama_ayah ?? '-' }}</p>
                                </div>

                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase">No KK Ayah</p>
                                    <p class="mt-1 text-sm text-gray-800">
                                        {{ $ayahCitizen->no_kk ?? $event->no_kk_ayah ?? '-' }}</p>
                                </div>

                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase">Status Perkawinan (Ayah)</p>
                                    <p class="mt-1 text-sm text-gray-800">{{ $ayahCitizen->status_perkawinan ?? '-' }}</p>
                                </div>

                                <div class="sm:col-span-2">
                                    <p class="text-xs font-semibold text-gray-500 uppercase">Wilayah (Ayah)</p>
                                    <p class="mt-1 text-sm text-gray-800">
                                        Dusun: {{ $ayahCitizen->dusun ?? '-' }}
                                        · RW: {{ $ayahCitizen->rw ?? '-' }}
                                        · RT: {{ $ayahCitizen->rt ?? '-' }}
                                    </p>

                                    @php
                                        $ayahRequested = !empty($event->nik_ayah) || !empty($event->ayah_citizen_id);
                                    @endphp

                                    @if($ayahRequested && !$ayahCitizen)
                                        <p
                                            class="p-2 mt-1 text-xs text-yellow-700 border border-yellow-200 rounded bg-yellow-50">
                                            Data ayah tidak ditemukan di master penduduk (Citizen). Pastikan NIK ayah benar &
                                            terdaftar.
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Data Pelapor --}}
                        <div class="pt-4 mt-2 border-t">
                            <h3 class="mb-2 text-sm font-semibold text-gray-700">Data Pelapor</h3>

                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase">Pelapor</p>
                                    <p class="mt-1 text-sm text-gray-800">{{ $event->pelapor ?? '-' }}</p>
                                </div>

                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase">Hubungan Pelapor</p>
                                    <p class="mt-1 text-sm text-gray-800">
                                        {{ $event->hubungan_pelapor ? ucfirst($event->hubungan_pelapor) : '-' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- =========================
                    DETAIL KHUSUS MENINGGAL
                    ========================== --}}
                    @if($event->jenis_peristiwa === 'meninggal')
                        <div class="pt-4 mt-2 border-t">
                            <h3 class="mb-2 text-sm font-semibold text-gray-700">Detail Peristiwa Meninggal</h3>

                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase">Tempat Meninggal</p>
                                    <p class="mt-1 text-sm text-gray-800">{{ $event->tempat_meninggal ?? '-' }}</p>
                                </div>

                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase">Jam Kematian</p>
                                    <p class="mt-1 text-sm text-gray-800">{{ $fmtTime($event->jam_kematian) }}</p>
                                </div>

                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase">Penyebab</p>
                                    <p class="mt-1 text-sm text-gray-800">{{ $event->penyebab_kematian ?? '-' }}</p>
                                </div>

                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase">Yang Menyatakan</p>
                                    <p class="mt-1 text-sm text-gray-800">{{ $event->yang_menyatakan_kematian ?? '-' }}</p>
                                </div>

                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase">Nomor Akta Kematian</p>
                                    <p class="mt-1 text-sm text-gray-800">{{ $event->nomor_akta_kematian ?? '-' }}</p>
                                </div>

                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase">File Akta</p>
                                    <p class="mt-1 text-sm text-gray-800">
                                        @if($event->file_akta_kematian_path)
                                            <a href="{{ asset('storage/' . $event->file_akta_kematian_path) }}"
                                                class="text-blue-600 hover:underline" target="_blank">
                                                Lihat Dokumen
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- =========================
                    DETAIL KHUSUS DATANG
                    ========================== --}}
                    @if($event->jenis_peristiwa === 'datang')
                        <div class="pt-4 mt-2 border-t">
                            <h3 class="mb-2 text-sm font-semibold text-gray-700">
                                Detail Peristiwa Datang
                            </h3>

                            <div class="grid gap-4 sm:grid-cols-2">

                                {{-- tanggal datang --}}
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase">Tanggal Datang</p>
                                    <p class="mt-1 text-sm text-gray-800">
                                        {{ $event->tanggal_datang ? $event->tanggal_datang->format('d-m-Y') : '-' }}
                                    </p>
                                </div>

                                {{-- status datang --}}
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase">Status Datang</p>
                                    <p class="mt-1 text-sm text-gray-800">
                                        @php $sd = $event->status_datang ? strtolower($event->status_datang) : null; @endphp
                                        @if($sd === 'tetap')
                                            Tetap
                                        @elseif($sd === 'sementara')
                                            Sementara
                                        @else
                                            -
                                        @endif
                                    </p>
                                </div>

                                {{-- alasan datang --}}
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase">Alasan Datang</p>
                                    <p class="mt-1 text-sm text-gray-800">
                                        @php $ad = $event->alasan_datang ? strtolower($event->alasan_datang) : null; @endphp
                                        @if($ad === 'kerja')
                                            Kerja
                                        @elseif($ad === 'nikah')
                                            Nikah
                                        @elseif($ad === 'keluarga')
                                            Keluarga
                                        @elseif($ad === 'lainnya')
                                            Lainnya
                                        @else
                                            -
                                        @endif
                                    </p>
                                </div>

                                {{-- rencana tinggal --}}
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase">Rencana Tinggal</p>
                                    <p class="mt-1 text-sm text-gray-800">
                                        {{ $event->rencana_tinggal ?? '-' }}
                                    </p>
                                </div>
                            </div>

                            {{-- asal --}}
                            <div class="grid gap-4 mt-4 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <p class="text-xs font-semibold text-gray-500 uppercase">Asal Penduduk</p>
                                    <div class="p-3 mt-1 border rounded bg-gray-50">
                                        <div class="text-sm text-gray-800 whitespace-pre-line">
                                            <span class="font-semibold">Alamat:</span> {{ $event->alamat_asal ?? '-' }}
                                        </div>
                                        <div class="mt-1 text-sm text-gray-800">
                                            <span class="font-semibold">Desa:</span> {{ $event->desa_asal ?? '-' }}
                                            · <span class="font-semibold">Kecamatan:</span>
                                            {{ $event->kecamatan_asal ?? '-' }}
                                        </div>
                                        <div class="mt-1 text-sm text-gray-800">
                                            <span class="font-semibold">Kabupaten:</span>
                                            {{ $event->kabupaten_asal ?? '-' }}
                                            · <span class="font-semibold">Provinsi:</span>
                                            {{ $event->provinsi_asal ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- tujuan --}}
                            <div class="grid gap-4 mt-4 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <p class="text-xs font-semibold text-gray-500 uppercase">Tujuan / Domisili Baru
                                        (Patimban)</p>
                                    <div class="p-3 mt-1 border rounded bg-gray-50">
                                        <div class="text-sm text-gray-800 whitespace-pre-line">
                                            <span class="font-semibold">Alamat Sekarang:</span>
                                            {{ $event->alamat_sekarang_tujuan ?? '-' }}
                                        </div>
                                        <div class="mt-1 text-sm text-gray-800">
                                            <span class="font-semibold">Dusun:</span> {{ $event->dusun_tujuan ?? '-' }}
                                            · <span class="font-semibold">RW:</span> {{ $event->rw_tujuan ?? '-' }}
                                            · <span class="font-semibold">RT:</span> {{ $event->rt_tujuan ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- pelapor --}}
                            <div class="grid gap-4 mt-4 sm:grid-cols-2">
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase">Pelapor</p>
                                    <p class="mt-1 text-sm text-gray-800">{{ $event->pelapor ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase">Hubungan Pelapor</p>
                                    <p class="mt-1 text-sm text-gray-800">
                                        {{ $event->hubungan_pelapor ? ucfirst($event->hubungan_pelapor) : '-' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif


                    {{-- =========================
                    STATUS VERIFIKASI ADMIN
                    ========================== --}}
                    <div class="pt-4 mt-2 border-t">
                        <p class="text-xs font-semibold text-gray-500 uppercase">Status Verifikasi Admin</p>

                        <p class="mt-1 text-sm text-gray-800">
                            {{ ucfirst($event->status_verifikasi) }}
                            @if ($event->catatan_verifikasi)
                                — <span class="text-gray-600">"{{ $event->catatan_verifikasi }}"</span>
                            @endif
                        </p>

                        @if ($event->verified_by && $event->verified_at)
                            <p class="mt-1 text-xs text-gray-600">
                                Diverifikasi oleh
                                <span
                                    class="font-medium text-gray-800">{{ optional($event->verifier)->name ?? 'Admin' }}</span>
                                pada
                                <span
                                    class="font-medium text-gray-800">{{ $event->verified_at->format('d-m-Y H:i') }}</span>
                            </p>
                        @endif
                    </div>

                    {{-- =========================
                    FORM VERIFIKASI (ADMIN)
                    ========================== --}}
                    @can('verify', $event)
                        <div class="pt-4 mt-2 border-t">
                            <p class="mb-2 text-xs font-semibold text-gray-500 uppercase">Ubah Status Verifikasi</p>

                            @if ($errors->any())
                                <div class="p-2 mb-2 text-xs text-red-800 bg-red-100 border border-red-200 rounded">
                                    <ul class="pl-4 list-disc">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form id="form-verifikasi" action="{{ route('events.verify', $event->id) }}" method="POST"
                                class="space-y-3 sm:flex sm:items-end sm:space-y-0 sm:space-x-3">
                                @csrf

                                <div>
                                    <label class="block mb-1 text-xs font-medium text-gray-700">Status</label>

                                    <select name="status_verifikasi" class="text-sm border-gray-300 rounded">
                                        <option value="menunggu" {{ $event->status_verifikasi === 'menunggu' ? 'selected' : '' }}>
                                            Menunggu
                                        </option>
                                        <option value="disetujui" {{ $event->status_verifikasi === 'disetujui' ? 'selected' : '' }}>
                                            Disetujui
                                        </option>
                                        <option value="ditolak" {{ $event->status_verifikasi === 'ditolak' ? 'selected' : '' }}>
                                            Ditolak
                                        </option>
                                    </select>

                                    @if($isLahir)
                                        <p class="mt-1 text-xs text-gray-500">
                                            Saat <span class="font-semibold">Disetujui</span>, sistem akan mengubah status lahir
                                            menjadi
                                            <span class="font-semibold">Menunggu NIK Bayi</span>.
                                        </p>
                                    @endif
                                </div>

                                <div class="flex-1">
                                    <label class="block mb-1 text-xs font-medium text-gray-700">
                                        Catatan Verifikasi (opsional)
                                    </label>

                                    <input type="text" name="catatan_verifikasi"
                                        value="{{ old('catatan_verifikasi', $event->catatan_verifikasi) }}"
                                        class="w-full text-sm border-gray-300 rounded"
                                        placeholder="misal: data sudah sesuai dengan surat keterangan">
                                </div>

                                <div>
                                    <button type="submit"
                                        class="px-4 py-2 text-xs font-semibold text-white bg-green-600 rounded hover:bg-green-700">
                                        Simpan Verifikasi
                                    </button>
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="pt-4 mt-2 border-t">
                            <div class="p-3 text-sm text-gray-700 border border-gray-200 rounded-lg bg-gray-50">
                                <div class="font-semibold text-gray-800">Info</div>
                                <div class="mt-1 text-xs text-gray-600">
                                    Anda tidak memiliki akses untuk mengubah status verifikasi peristiwa.
                                </div>
                            </div>
                        </div>
                    @endcan

                    {{-- =========================
                    CATATAN PERISTIWA
                    ========================== --}}
                    <div class="pt-4 mt-2 border-t">
                        <p class="text-xs font-semibold text-gray-500 uppercase">Catatan Peristiwa</p>
                        <p class="mt-1 text-sm text-gray-800 whitespace-pre-line">
                            {{ $event->catatan_peristiwa ?? '-' }}
                        </p>
                    </div>
                </div>

                {{-- =========================
                FOOTER
                ========================== --}}
                <div class="flex items-center justify-between px-6 py-4 border-t bg-gray-50">
                    <a href="{{ route('events.index') }}" class="text-sm text-gray-600 hover:underline">
                        ← Kembali ke daftar peristiwa
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Konfirmasi saat ubah verifikasi (hanya jika event mengubah status_dasar dan status saat ini disetujui) --}}
    @can('verify', $event)
        @if ($event->status_verifikasi === 'disetujui' && $jenisMengubahStatus)
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const form = document.getElementById('form-verifikasi');
                    if (!form) return;

                    form.addEventListener('submit', function (e) {
                        const ok = confirm(
                            'PERHATIAN!\n\n' +
                            'Peristiwa ini sudah DISUTUJUI dan telah mengubah status penduduk.\n' +
                            'Jika Anda mengubah status verifikasi, sistem akan mencoba mengembalikan status ke kondisi sebelumnya.\n\n' +
                            'Apakah Anda yakin ingin melanjutkan?'
                        );

                        if (!ok) e.preventDefault();
                    });
                });
            </script>
        @endif
    @endcan
</x-app-layout>