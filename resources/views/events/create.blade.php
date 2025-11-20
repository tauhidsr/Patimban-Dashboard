<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            Tambah Peristiwa Kependudukan
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="p-6 bg-white shadow-sm sm:rounded-lg">

                <p class="mb-3 text-gray-600">Pilih jenis peristiwa yang ingin dicatat:</p>

                <div class="space-y-2 text-sm">
                    <a href="#" class="text-blue-600 hover:underline">• Kelahiran</a>
                    <a href="#" class="text-blue-600 hover:underline">• Datang</a>
                    <a href="#" class="text-blue-600 hover:underline">• Pindah</a>
                    <a href="{{ route('events.meninggal.create') }}" class="text-blue-600 hover:underline">• Meninggal</a>
                    <a href="#" class="text-blue-600 hover:underline">• Hilang</a>
                    <a href="#" class="text-blue-600 hover:underline">• Penduduk Sementara (1×24 jam)</a>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>