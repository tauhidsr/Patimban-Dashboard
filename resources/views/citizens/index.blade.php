{{-- resources/views/citizens/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Data Warga Desa Patimban') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">

            {{-- Info singkat --}}
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p class="text-sm text-gray-700">
                        Halaman ini menampilkan daftar warga Desa Patimban berdasarkan data yang
                        tersimpan di tabel <code>citizens</code>. Untuk saat ini bersifat
                        <span class="font-semibold">read-only</span>.
                    </p>
                </div>
            </div>

            {{-- Pencarian --}}
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-4 text-gray-900 border-b border-gray-100">
                    <form method="GET" action="{{ route('citizens.index') }}"
                        class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-800">
                                Pencarian Data Warga
                            </h3>
                            <p class="text-xs text-gray-500">
                                Cari berdasarkan NIK, nama, atau dusun.
                            </p>
                        </div>

                        <div class="flex gap-2 md:min-w-[280px]">
                            <input type="text" name="q" value="{{ $search }}"
                                class="w-full text-sm border-gray-300 rounded" placeholder="Cari NIK / Nama / Dusun...">
                            <button type="submit"
                                class="px-3 py-2 text-sm text-white bg-blue-600 rounded hover:bg-blue-700">
                                Cari
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Tabel --}}
                <div class="p-4 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-left border border-gray-200 divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr class="text-xs font-semibold text-gray-600 uppercase">
                                    <th class="px-3 py-2">No</th>
                                    <th class="px-3 py-2">NIK</th>
                                    <th class="px-3 py-2">Nama</th>
                                    <th class="px-3 py-2">JK</th>
                                    <th class="px-3 py-2">Tgl Lahir</th>
                                    <th class="px-3 py-2">Dusun / RW / RT</th>
                                    <th class="px-3 py-2">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($citizens as $index => $citizen)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-2">
                                            {{ $citizens->firstItem() + $index }}
                                        </td>
                                        <td class="px-3 py-2 font-mono text-xs">
                                            {{ $citizen->nik }}
                                        </td>
                                        <td class="px-3 py-2 font-medium">
                                            {{ $citizen->nama }}
                                        </td>
                                        <td class="px-3 py-2">
                                            @if ($citizen->jenis_kelamin === 'L')
                                                Laki-laki
                                            @elseif ($citizen->jenis_kelamin === 'P')
                                                Perempuan
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-3 py-2">
                                            {{ $citizen->tanggal_lahir ? \Carbon\Carbon::parse($citizen->tanggal_lahir)->format('d-m-Y') : '-' }}
                                        </td>
                                        <td class="px-3 py-2">
                                            {{ $citizen->dusun ?? '-' }}
                                            @if($citizen->rw || $citizen->rt)
                                                <span class="text-xs text-gray-500">
                                                    (RW {{ $citizen->rw ?? '-' }} / RT {{ $citizen->rt ?? '-' }})
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2">
                                            <span class="inline-flex items-center px-2 py-0.5 text-xs rounded-full
                                                    @if($citizen->status_dasar === 'meninggal')
                                                        bg-red-100 text-red-700
                                                    @elseif($citizen->status_dasar === 'pindah')
                                                        bg-amber-100 text-amber-700
                                                    @elseif($citizen->status_dasar === 'hilang')
                                                        bg-gray-200 text-gray-700
                                                    @else
                                                        bg-green-100 text-green-700
                                                    @endif
                                                ">
                                                {{ $citizen->status_dasar ?? 'aktif' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-3 py-4 text-center text-gray-500">
                                            Belum ada data warga di sistem.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-4">
                        {{ $citizens->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>