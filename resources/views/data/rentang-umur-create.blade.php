{{-- resources/views/data/rentang-umur-create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Tambah Data Rentang Umur Penduduk') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('rentang-umur.store') }}" method="POST" class="space-y-4">
                        @csrf

                        {{-- kelompok umur --}}
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">
                                Kelompok Rentang Umur
                            </label>
                            <select name="kategori" class="w-full border-gray-300 rounded" required>
                                <option value="" disabled selected>Pilih kelompok umur</option>
                                <option value="7 s/d 16 Tahun">7 s/d 16 Tahun</option>
                                <option value="Di atas 18 Tahun">Di atas 18 Tahun</option>
                                <option value="Belum Mengisi">Belum Mengisi</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-400">
                                Pilih salah satu kategori sesuai tabel data desa.
                            </p>
                        </div>

                        {{-- L & P --}}
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">
                                    Laki-laki
                                </label>
                                <input type="number" name="laki_laki" class="w-full border-gray-300 rounded"
                                    placeholder="misal: 709">
                            </div>
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">
                                    Perempuan
                                </label>
                                <input type="number" name="perempuan" class="w-full border-gray-300 rounded"
                                    placeholder="misal: 710">
                            </div>
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">
                                    Total (L+P)
                                </label>
                                <input type="number" name="total" class="w-full border-gray-300 rounded"
                                    placeholder="otomatis kalau L & P diisi">
                                <p class="mt-1 text-xs text-gray-400">
                                    Boleh kosong. Kalau L & P diisi, akan dihitung otomatis.
                                </p>
                            </div>
                        </div>

                        {{-- tahun --}}
                        <div class="max-w-xs">
                            <label class="block mb-1 text-sm font-medium text-gray-700">
                                Tahun
                            </label>
                            <input type="number" name="tahun" value="2025" class="w-full border-gray-300 rounded">
                        </div>

                        {{-- tombol --}}
                        <div class="flex items-center justify-between pt-2">
                            <a href="{{ route('rentang-umur.index') }}" class="text-sm text-gray-500 hover:underline">
                                ← Kembali
                            </a>
                            <button type="submit"
                                class="px-4 py-2 text-sm text-white bg-blue-600 rounded hover:bg-blue-700">
                                Simpan Data
                            </button>
                        </div>

                        {{-- error --}}
                        @if ($errors->any())
                            <div class="p-3 mt-4 text-sm text-red-700 rounded bg-red-50">
                                <strong>Terjadi kesalahan:</strong>
                                <ul class="list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- success --}}
                        @if (session('success'))
                            <div class="p-3 mt-4 text-sm text-green-700 rounded bg-green-50">
                                {{ session('success') }}
                            </div>
                        @endif

                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>