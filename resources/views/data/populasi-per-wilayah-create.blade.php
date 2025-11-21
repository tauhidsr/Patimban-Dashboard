{{-- resources/views/data/populasi-per-wilayah-create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Tambah Data Populasi per Wilayah') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('data.populasi.store') }}" method="POST" class="space-y-4">
                        @csrf

                        {{-- nama wilayah --}}
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">
                                Nama Wilayah / Dusun / RW / RT
                            </label>
                            <input
                                type="text"
                                name="nama_wilayah"
                                class="w-full border-gray-300 rounded"
                                required
                                placeholder="Misal: Dusun Patimban / RW 001 / RT 001">
                        </div>

                        {{-- KK & tahun --}}
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">
                                    Jumlah KK
                                </label>
                                <input
                                    type="number"
                                    name="kk"
                                    class="w-full border-gray-300 rounded"
                                    placeholder="misal: 491">
                            </div>
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">
                                    Tahun
                                </label>
                                <input
                                    type="number"
                                    name="tahun"
                                    value="2025"
                                    class="w-full border-gray-300 rounded">
                            </div>
                        </div>

                        {{-- L & P dan total --}}
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">
                                    Laki-laki
                                </label>
                                <input
                                    type="number"
                                    name="laki_laki"
                                    class="w-full border-gray-300 rounded"
                                    placeholder="misal: 650">
                            </div>
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">
                                    Perempuan
                                </label>
                                <input
                                    type="number"
                                    name="perempuan"
                                    class="w-full border-gray-300 rounded"
                                    placeholder="misal: 707">
                            </div>
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">
                                    Total (L+P)
                                </label>
                                <input
                                    type="number"
                                    name="jumlah_penduduk"
                                    class="w-full border-gray-300 rounded"
                                    placeholder="otomatis kalau L & P diisi">
                                <p class="mt-1 text-xs text-gray-400">
                                    Boleh kosong. Kalau L & P diisi, akan dihitung otomatis.
                                </p>
                            </div>
                        </div>

                        {{-- latitude & longitude --}}
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">
                                    Latitude (opsional)
                                </label>
                                <input
                                    type="text"
                                    name="latitude"
                                    value="{{ old('latitude') }}"
                                    class="w-full border-gray-300 rounded"
                                    placeholder="-6.4260">
                                @error('latitude')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">
                                    Longitude (opsional)
                                </label>
                                <input
                                    type="text"
                                    name="longitude"
                                    value="{{ old('longitude') }}"
                                    class="w-full border-gray-300 rounded"
                                    placeholder="108.3590">
                                @error('longitude')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- tombol --}}
                        <div class="flex items-center justify-between pt-2">
                            <a href="{{ route('data.populasi') }}" class="text-sm text-gray-500 hover:underline">
                                ← Kembali
                            </a>
                            <button
                                type="submit"
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
