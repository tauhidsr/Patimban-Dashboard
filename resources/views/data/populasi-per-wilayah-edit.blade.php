{{-- resources/views/data/populasi-per-wilayah-edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Edit Data Populasi per Wilayah') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('data.populasi.update', $area->id) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        {{-- Nama wilayah --}}
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Nama Wilayah / Dusun / RW /
                                RT</label>
                            <input type="text" name="nama_wilayah"
                                value="{{ old('nama_wilayah', $area->nama_wilayah) }}"
                                class="w-full border-gray-300 rounded" required>
                        </div>

                        {{-- KK & Tahun --}}
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Jumlah KK</label>
                                <input type="number" name="kk" value="{{ old('kk', $area->kk) }}"
                                    class="w-full border-gray-300 rounded">
                            </div>
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Tahun</label>
                                <input type="number" name="tahun" value="{{ old('tahun', $area->tahun) }}"
                                    class="w-full border-gray-300 rounded">
                            </div>
                        </div>

                        {{-- Laki-laki, Perempuan, Total --}}
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Laki-laki</label>
                                <input type="number" name="laki_laki" value="{{ old('laki_laki', $area->laki_laki) }}"
                                    class="w-full border-gray-300 rounded">
                            </div>
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Perempuan</label>
                                <input type="number" name="perempuan" value="{{ old('perempuan', $area->perempuan) }}"
                                    class="w-full border-gray-300 rounded">
                            </div>
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Total (L+P)</label>
                                <input type="number" name="jumlah_penduduk"
                                    value="{{ old('jumlah_penduduk', $area->jumlah_penduduk) }}"
                                    class="w-full border-gray-300 rounded">
                            </div>
                        </div>

                        {{-- Latitude & Longitude --}}
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">
                                    Latitude (opsional)
                                </label>
                                <input type="text" name="latitude" value="{{ old('latitude', $area->latitude) }}"
                                    class="w-full border-gray-300 rounded" placeholder="-6.4260">
                                @error('latitude')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">
                                    Longitude (opsional)
                                </label>
                                <input type="text" name="longitude" value="{{ old('longitude', $area->longitude) }}"
                                    class="w-full border-gray-300 rounded" placeholder="108.3590">
                                @error('longitude')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Tombol --}}
                        <div class="flex items-center justify-between pt-2">
                            <a href="{{ route('data.populasi') }}" class="text-sm text-gray-500 hover:underline">
                                ← Kembali
                            </a>
                            <button type="submit"
                                class="px-4 py-2 text-sm text-white bg-green-600 rounded hover:bg-green-700">
                                Simpan Perubahan
                            </button>
                        </div>

                        {{-- Error Message --}}
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

                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>