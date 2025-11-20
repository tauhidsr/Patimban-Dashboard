{{-- resources/views/events/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Detail Peristiwa Kependudukan') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto space-y-6 sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="flex items-center justify-between px-6 py-4 border-b">
                    <div>
                        <p class="text-sm text-gray-500">Nama Penduduk</p>
                        <p class="text-lg font-semibold text-gray-900">
                            {{ $event->nama ?? '-' }}
                        </p>
                        <p class="text-sm text-gray-500">
                            NIK: {{ $event->nik ?? '-' }} &middot;
                            No KK: {{ $event->no_kk ?? '-' }}
                        </p>
                    </div>

                    @php
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
                    @endphp

                    <div class="text-right">
                        <span class="inline-flex mb-1 px-3 py-1 text-xs font-semibold rounded-full
                            @if($event->jenis_peristiwa === 'meninggal')
                                bg-red-100 text-red-800
                            @elseif($event->jenis_peristiwa === 'pindah')
                                bg-yellow-100 text-yellow-800
                            @elseif(in_array($event->jenis_peristiwa, ['lahir', 'datang']))
                                bg-green-100 text-green-800
                            @else
                                bg-gray-100 text-gray-800
                            @endif
                        ">
                            {{ $labelJenis }}
                        </span>
                        <div>
                            <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full
                                @if($event->status_verifikasi === 'menunggu')
                                    bg-orange-100 text-orange-800
                                @elseif($event->status_verifikasi === 'disetujui')
                                    bg-green-100 text-green-800
                                @else
                                    bg-red-100 text-red-800
                                @endif
                            ">
                                Status: {{ ucfirst($event->status_verifikasi) }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 space-y-4">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Tanggal Peristiwa</p>
                            <p class="mt-1 text-sm text-gray-800">
                                {{ optional($event->tanggal_peristiwa)->format('d-m-Y') ?? '-' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Tanggal Lapor</p>
                            <p class="mt-1 text-sm text-gray-800">
                                {{ optional($event->tanggal_lapor)->format('d-m-Y') ?? '-' }}
                            </p>
                        </div>
                    </div>

                    {{-- Detail khusus meninggal (kalau jenisnya meninggal) --}}
                    @if($event->jenis_peristiwa === 'meninggal')
                        <div class="pt-4 mt-2 border-t">
                            <h3 class="mb-2 text-sm font-semibold text-gray-700">
                                Detail Peristiwa Meninggal
                            </h3>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase">Tempat Meninggal</p>
                                    <p class="mt-1 text-sm text-gray-800">
                                        {{ $event->tempat_meninggal ?? '-' }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase">Jam Kematian</p>
                                    <p class="mt-1 text-sm text-gray-800">
                                        {{ $event->jam_kematian ?? '-' }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase">Penyebab</p>
                                    <p class="mt-1 text-sm text-gray-800">
                                        {{ $event->penyebab_kematian ?? '-' }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase">Yang Menyatakan</p>
                                    <p class="mt-1 text-sm text-gray-800">
                                        {{ $event->yang_menyatakan_kematian ?? '-' }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase">Nomor Akta Kematian</p>
                                    <p class="mt-1 text-sm text-gray-800">
                                        {{ $event->nomor_akta_kematian ?? '-' }}
                                    </p>
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

                    {{-- Catatan umum --}}
                    <div class="pt-4 mt-2 border-t">
                        <p class="text-xs font-semibold text-gray-500 uppercase">Catatan Peristiwa</p>
                        <p class="mt-1 text-sm text-gray-800 whitespace-pre-line">
                            {{ $event->catatan_peristiwa ?? '-' }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center justify-between px-6 py-4 border-t bg-gray-50">
                    <a href="{{ route('events.index') }}" class="text-sm text-gray-600 hover:underline">
                        ← Kembali ke daftar peristiwa
                    </a>
                    {{-- nanti di sini bisa kita tambahkan tombol Setujui / Tolak untuk admin --}}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>