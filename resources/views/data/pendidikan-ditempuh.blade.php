{{-- resources/views/data/pendidikan-ditempuh.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Data Pendidikan yang Sedang Ditempuh') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    Halaman ini akan menampilkan rekap penduduk berdasarkan pendidikan yang sedang ditempuh
                    (tabel dan grafik akan ditambahkan kemudian).
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
