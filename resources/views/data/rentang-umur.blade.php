{{-- resources/views/data/rentang-umur.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Data Penduduk Menurut Rentang Umur - 2025') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">

            {{-- info singkat --}}
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    Halaman ini menampilkan data penduduk Desa Patimban berdasarkan kelompok rentang umur.
                </div>
            </div>

            {{-- tabel data --}}
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- tombol tambah data (admin) --}}
                    <div class="flex justify-end mb-4">
                        <a href="{{ route('rentang-umur.create') }}"
                            class="px-4 py-2 text-sm text-white bg-blue-600 rounded hover:bg-blue-700">
                            + Tambah Data Rentang Umur
                        </a>
                    </div>

                    {{-- pesan sukses --}}
                    @if (session('success'))
                        <div class="p-3 mb-4 text-sm text-green-700 rounded bg-green-50">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-left">
                            <thead>
                                <tr class="text-gray-700 bg-gray-100 border-b">
                                    <th class="px-3 py-2">No</th>
                                    <th class="px-3 py-2">Kelompok Umur</th>
                                    <th class="px-3 py-2">Laki-laki</th>
                                    <th class="px-3 py-2">Perempuan</th>
                                    <th class="px-3 py-2">Total</th>
                                    <th class="px-3 py-2">Tahun</th>
                                    <th class="px-3 py-2">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($items as $index => $item)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="px-3 py-2">{{ $index + 1 }}</td>
                                        <td class="px-3 py-2 font-medium">{{ $item->kategori }}</td>
                                        <td class="px-3 py-2">{{ $item->laki_laki ?? '-' }}</td>
                                        <td class="px-3 py-2">{{ $item->perempuan ?? '-' }}</td>
                                        <td class="px-3 py-2 font-semibold">{{ $item->total ?? '-' }}</td>
                                        <td class="px-3 py-2">{{ $item->tahun ?? '-' }}</td>
                                        <td class="px-3 py-2">
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('rentang-umur.edit', $item->id) }}"
                                                    class="px-3 py-1 text-xs text-white rounded bg-amber-500 hover:bg-amber-600">
                                                    Edit
                                                </a>
                                                <form action="{{ route('rentang-umur.destroy', $item->id) }}" method="POST"
                                                    onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="px-3 py-1 text-xs text-white bg-red-600 rounded hover:bg-red-700">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-3 py-4 text-center text-gray-500">
                                            Belum ada data rentang umur. Silakan tambahkan data terlebih dahulu.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>