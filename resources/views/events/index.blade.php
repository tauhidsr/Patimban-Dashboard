<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            Daftar Peristiwa Kependudukan
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            <div class="p-6 bg-white shadow-sm sm:rounded-lg">

                {{-- Alert sukses --}}
                @if (session('success'))
                    <div class="p-3 mb-4 text-sm text-green-800 bg-green-100 border border-green-300 rounded">
                        {{ session('success') }}
                    </div>
                @endif


                {{-- Tombol tambah peristiwa --}}
                <div class="flex items-center justify-between mb-4">
                    <p class="text-sm text-gray-600">
                        Menampilkan riwayat peristiwa kependudukan (lahir, datang, pindah, meninggal, hilang,
                        sementara).
                    </p>

                    <a href="{{ route('events.create') }}"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded hover:bg-blue-700">
                        + Tambah Peristiwa
                    </a>
                </div>

                {{-- Tabel peristiwa --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-left">
                        <thead>
                            <tr class="text-gray-700 bg-gray-100 border-b">
                                <th class="px-3 py-2">No</th>
                                <th class="px-3 py-2">Tanggal Peristiwa</th>
                                <th class="px-3 py-2">Jenis</th>
                                <th class="px-3 py-2">Nama</th>
                                <th class="px-3 py-2">NIK</th>
                                <th class="px-3 py-2">No KK</th>
                                <th class="px-3 py-2">Status Verifikasi</th>
                                <th class="px-3 py-2">Dibuat Oleh</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($events as $index => $event)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-3 py-2">
                                        {{ $events->firstItem() + $index }}
                                    </td>
                                    <td class="px-3 py-2">
                                        {{ $event->tanggal_peristiwa?->format('d-m-Y') ?? '-' }}
                                    </td>
                                    <td class="px-3 py-2">
                                        <span class="px-2 py-1 text-xs font-semibold rounded
                                                    @if($event->jenis_peristiwa === 'meninggal')
                                                        bg-red-100 text-red-700
                                                    @elseif($event->jenis_peristiwa === 'pindah')
                                                        bg-yellow-100 text-yellow-700
                                                    @elseif($event->jenis_peristiwa === 'lahir')
                                                        bg-green-100 text-green-700
                                                    @elseif($event->jenis_peristiwa === 'datang')
                                                        bg-blue-100 text-blue-700
                                                    @elseif(str_starts_with($event->jenis_peristiwa, 'sementara'))
                                                        bg-purple-100 text-purple-700
                                                    @else
                                                        bg-gray-100 text-gray-700
                                                    @endif
                                                ">
                                            {{ ucfirst(str_replace('_', ' ', $event->jenis_peristiwa)) }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2">
                                        {{ $event->nama ?? '-' }}
                                    </td>
                                    <td class="px-3 py-2">
                                        {{ $event->nik ?? '-' }}
                                    </td>
                                    <td class="px-3 py-2">
                                        {{ $event->no_kk ?? '-' }}
                                    </td>
                                    <td class="px-3 py-2">
                                        @php
                                            $badgeClass = match ($event->status_verifikasi) {
                                                'disetujui' => 'bg-green-100 text-green-700',
                                                'ditolak' => 'bg-red-100 text-red-700',
                                                default => 'bg-yellow-100 text-yellow-700',
                                            };
                                        @endphp
                                        <span class="px-2 py-1 text-xs font-semibold rounded {{ $badgeClass }}">
                                            {{ ucfirst($event->status_verifikasi) }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2">

                                        {{-- sementara cukup tampilkan ID user, nanti bisa kita join ke nama user --}}
                                        {{ $event->created_by ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-3 py-4 text-center text-gray-500">
                                        Belum ada peristiwa tercatat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- pagination --}}
                <div class="mt-4">
                    {{ $events->links() }}
                </div>

            </div>

        </div>
    </div>
</x-app-layout>