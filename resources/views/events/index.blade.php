{{-- resources/views/events/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Peristiwa Kependudukan') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">

            {{-- Flash success --}}
            @if (session('success'))
                <div class="p-3 text-sm text-green-800 bg-green-100 border border-green-200 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="flex items-center justify-between px-6 py-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-800">
                        Daftar Peristiwa Penduduk
                    </h3>

                    <a href="{{ route('events.create') }}"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded hover:bg-blue-700">
                        + Tambah Peristiwa
                    </a>
                </div>

                <div class="p-0 overflow-x-auto">
                    <table class="min-w-full text-sm text-left">
                        <thead>
                            <tr
                                class="text-xs font-semibold tracking-wide text-gray-600 uppercase bg-gray-100 border-b">
                                <th class="px-4 py-2">No</th>
                                <th class="px-4 py-2">Nama</th>
                                <th class="px-4 py-2">NIK</th>
                                <th class="px-4 py-2">Jenis Peristiwa</th>
                                <th class="px-4 py-2">Tanggal Peristiwa</th>
                                <th class="px-4 py-2">Status</th>
                                <th class="px-4 py-2">Dicatat Oleh</th>
                                <th class="px-4 py-2">Aksi</th> {{-- 🔹 kolom baru --}}
                            </tr>
                        </thead>
                        <tbody>
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
                            @endphp

                            @forelse ($events as $index => $event)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-2 align-top">
                                        {{ ($events->currentPage() - 1) * $events->perPage() + $index + 1 }}
                                    </td>
                                    <td class="px-4 py-2 align-top">
                                        {{ $event->nama ?? '-' }}
                                    </td>
                                    <td class="px-4 py-2 align-top">
                                        {{ $event->nik ?? '-' }}
                                    </td>
                                    <td class="px-4 py-2 align-top">
                                        @php
                                            $labelJenis = $mapJenis[$event->jenis_peristiwa] ?? $event->jenis_peristiwa;
                                        @endphp
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
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
                                    </td>
                                    <td class="px-4 py-2 align-top">
                                        {{ $event->tanggal_peristiwa ? $event->tanggal_peristiwa->format('d-m-Y') : '-' }}
                                    </td>
                                    <td class="px-4 py-2 align-top">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                @if($event->status_verifikasi === 'menunggu')
                                                    bg-orange-100 text-orange-800
                                                @elseif($event->status_verifikasi === 'disetujui')
                                                    bg-green-100 text-green-800
                                                @else
                                                    bg-red-100 text-red-800
                                                @endif
                                            ">
                                            {{ ucfirst($event->status_verifikasi) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 align-top">
                                        {{ $event->created_by ? 'User ID: ' . $event->created_by : '-' }}
                                    </td>

                                    {{-- 🔹 Tombol aksi --}}
                                    <td class="px-4 py-2 align-top">
                                        <a href="{{ route('events.show', $event->id) }}"
                                            class="inline-flex px-3 py-1 text-xs font-medium text-white bg-indigo-600 rounded hover:bg-indigo-700">
                                            Lihat
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-6 text-sm text-center text-gray-500">
                                        Belum ada peristiwa yang tercatat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($events->hasPages())
                    <div class="px-6 py-4 border-t">
                        {{ $events->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>