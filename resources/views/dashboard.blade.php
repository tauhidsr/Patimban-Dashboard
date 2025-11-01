<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Data Kependudukan - Desa Patimban') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Card sambutan --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    Selamat datang, {{ Auth::user()->name }} 👋 <br>
                    <span class="text-sm text-gray-500">
                        Silakan pilih menu data kependudukan untuk melihat informasi populasi, pendidikan, pekerjaan, dan lainnya.
                    </span>
                </div>
            </div>

            {{-- Grid menu data --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <a href="{{ route('data.populasi') }}" class="bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition">
                    <h3 class="font-semibold text-gray-800">Populasi per Wilayah</h3>
                    <p class="text-sm text-gray-500">Lihat jumlah penduduk per dusun/RT/RW.</p>
                </a>
                <a href="{{ route('data.rentang-umur') }}" class="bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition">
                    <h3 class="font-semibold text-gray-800">Rentang Umur</h3>
                    <p class="text-sm text-gray-500">Distribusi usia penduduk.</p>
                </a>
                <a href="{{ route('data.pendidikan-kk') }}" class="bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition">
                    <h3 class="font-semibold text-gray-800">Pendidikan dalam KK</h3>
                    <p class="text-sm text-gray-500">Pendidikan terakhir yang tercatat.</p>
                </a>
                <a href="{{ route('data.pendidikan-ditempuh') }}" class="bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition">
                    <h3 class="font-semibold text-gray-800">Pendidikan yang Ditempuh</h3>
                    <p class="text-sm text-gray-500">Siapa saja yang sedang sekolah.</p>
                </a>
                <a href="{{ route('data.pekerjaan') }}" class="bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition">
                    <h3 class="font-semibold text-gray-800">Pekerjaan</h3>
                    <p class="text-sm text-gray-500">Sebaran pekerjaan penduduk.</p>
                </a>
                <a href="{{ route('data.agama') }}" class="bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition">
                    <h3 class="font-semibold text-gray-800">Agama</h3>
                    <p class="text-sm text-gray-500">Komposisi agama penduduk.</p>
                </a>
                <a href="{{ route('data.jenis-kelamin') }}" class="bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition">
                    <h3 class="font-semibold text-gray-800">Jenis Kelamin</h3>
                    <p class="text-sm text-gray-500">Laki-laki dan perempuan.</p>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
