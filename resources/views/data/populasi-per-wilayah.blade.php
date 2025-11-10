{{-- resources/views/data/populasi-per-wilayah.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Data Populasi per Wilayah (Dusun) - 2025') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
            {{-- Info singkat --}}
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    Data berikut menampilkan jumlah penduduk per dusun di Desa Patimban tahun 2025.
                </div>
            </div>

            {{-- Grafik batang --}}
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="mb-4 text-lg font-semibold">Grafik Jumlah Penduduk per Dusun</h3>
                    <canvas id="populationChart" height="120"></canvas>
                </div>
            </div>

            {{-- Tabel data --}}
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- tombol tambah data --}}
                    <div class="flex justify-end mb-4">
                        <a href="{{ route('data.populasi.create') }}"
                        class="px-4 py-2 text-sm text-white bg-blue-600 rounded hover:bg-blue-700">
                            + Tambah Data
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-left">
                            <thead>
                                <tr class="text-gray-700 bg-gray-100 border-b">
                                    <th class="px-3 py-2">No</th>
                                    <th class="px-3 py-2">Dusun</th>
                                    <th class="px-3 py-2">KK</th>
                                    <th class="px-3 py-2">Laki-laki</th>
                                    <th class="px-3 py-2">Perempuan</th>
                                    <th class="px-3 py-2">Total (L+P)</th>
                                    <th class="px-3 py-2">Tahun</th>
                                    <th class="px-3 py-2">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($areas as $index => $area)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="px-3 py-2">{{ $index + 1 }}</td>
                                        <td class="px-3 py-2 font-medium">{{ $area->nama_wilayah }}</td>
                                        <td class="px-3 py-2">{{ $area->kk ?? '-' }}</td>
                                        <td class="px-3 py-2">{{ $area->laki_laki ?? '-' }}</td>
                                        <td class="px-3 py-2">{{ $area->perempuan ?? '-' }}</td>
                                        <td class="px-3 py-2 font-semibold">{{ $area->jumlah_penduduk }}</td>
                                        <td class="px-3 py-2">{{ $area->tahun ?? '-' }}</td>
                                        <td class="px-3 py-2">
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('data.populasi.edit', $area->id) }}"
                                                class="px-3 py-1 text-xs text-white rounded bg-amber-500 hover:bg-amber-600">
                                                    Edit
                                                </a>
                                                <form action="{{ route('data.populasi.destroy', $area->id) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Yakin hapus data {{ $area->nama_wilayah }}?');">
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
                                        <td colspan="8" class="px-3 py-4 text-center text-gray-500">
                                            Belum ada data penduduk. Silakan isi dari seeder atau form input.
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

    {{-- Script Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('populationChart');
        const labels = @json($areas->pluck('nama_wilayah'));
        const data = @json($areas->pluck('jumlah_penduduk'));

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Penduduk (L+P)',
                    data: data,
                    backgroundColor: 'rgba(59, 130, 246, 0.6)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1,
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    </script>
</x-app-layout>
