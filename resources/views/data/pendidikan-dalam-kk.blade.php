{{-- resources/views/data/pendidikan-dalam-kk.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Data Pendidikan dalam KK') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    Halaman ini akan menampilkan rekap pendidikan terakhir yang tercatat dalam Kartu Keluarga
                    (tabel dan grafik akan ditambahkan kemudian, menggunakan data EducationInKK).
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
