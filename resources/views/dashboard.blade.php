{{-- resources/views/dashboard.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Dashboard Kependudukan Desa Patimban') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">

            {{-- Ringkasan Statistik --}}
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                <div class="p-4 bg-white rounded-lg shadow-sm">
                    <p class="text-sm text-gray-500">Total Penduduk</p>
                    <p class="mt-1 text-2xl font-bold text-gray-800">
                        {{ number_format($stats['total_penduduk'] ?? 0) }}
                    </p>
                </div>
                <div class="p-4 bg-white rounded-lg shadow-sm">
                    <p class="text-sm text-gray-500">Jumlah KK</p>
                    <p class="mt-1 text-2xl font-bold text-gray-800">
                        {{ number_format($stats['total_kk'] ?? 0) }}
                    </p>
                </div>
                <div class="p-4 bg-white rounded-lg shadow-sm">
                    <p class="text-sm text-gray-500">Laki-laki</p>
                    <p class="mt-1 text-2xl font-bold text-gray-800">
                        {{ number_format($stats['total_laki'] ?? 0) }}
                    </p>
                </div>
                <div class="p-4 bg-white rounded-lg shadow-sm">
                    <p class="text-sm text-gray-500">Perempuan</p>
                    <p class="mt-1 text-2xl font-bold text-gray-800">
                        {{ number_format($stats['total_perempuan'] ?? 0) }}
                    </p>
                </div>
            </div>

            {{-- Rasio L/P + Quick Links --}}
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
                <div class="p-6 bg-white rounded-lg shadow-sm lg:col-span-2">
                    <h3 class="mb-4 text-lg font-semibold text-gray-800">Rasio L / P</h3>
                    <canvas id="ratioChart" height="120"></canvas>
                </div>

                <div class="p-6 bg-white rounded-lg shadow-sm">
                    <h3 class="mb-3 text-lg font-semibold text-gray-800">Navigasi Cepat</h3>
                    <ul class="space-y-2 text-sm">
                        <li>
                            <a href="{{ route('data.populasi') }}" class="text-blue-600 hover:underline">
                                • Data Populasi per Wilayah
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('data.rentang-umur') }}" class="text-blue-600 hover:underline">
                                • Data Rentang Umur
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('data.pendidikan-kk') }}" class="text-blue-600 hover:underline">
                                • Pendidikan dalam KK
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('data.pendidikan-ditempuh') }}" class="text-blue-600 hover:underline">
                                • Pendidikan yang Ditempuh
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('data.pekerjaan') }}" class="text-blue-600 hover:underline">
                                • Pekerjaan
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('data.agama') }}" class="text-blue-600 hover:underline">
                                • Agama
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('data.jenis-kelamin') }}" class="text-blue-600 hover:underline">
                                • Jenis Kelamin
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </div>

    {{-- Chart.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('ratioChart');

        const laki = {{ (int)($stats['total_laki'] ?? 0) }};
        const perempuan = {{ (int)($stats['total_perempuan'] ?? 0) }};

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Laki-laki', 'Perempuan'],
                datasets: [{
                    data: [laki, perempuan],
                    backgroundColor: ['rgba(59,130,246,0.6)', 'rgba(234,88,12,0.6)'],
                    borderColor: ['rgba(59,130,246,1)', 'rgba(234,88,12,1)'],
                    borderWidth: 1,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: { enabled: true }
                }
            }
        });
    </script>
</x-app-layout>
