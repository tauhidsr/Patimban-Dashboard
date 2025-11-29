{{-- resources/views/dashboard.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ __('Dashboard Kependudukan Desa Patimban') }}
                </h2>
                <p class="mt-1 text-xs text-gray-500">
                    Ringkasan singkat kondisi kependudukan & navigasi cepat ke modul utama.
                </p>
            </div>

            {{-- Badge kecil info tahun --}}
            <span class="inline-flex items-center px-3 py-1 text-xs font-medium text-blue-700 border border-blue-100 rounded-full bg-blue-50">
                Data Tahun 2025
            </span>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">

            {{-- Ringkasan Statistik --}}
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                <div class="p-4 border border-blue-100 shadow-sm bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl">
                    <p class="text-xs font-medium tracking-wide text-blue-700 uppercase">
                        Total Penduduk
                    </p>
                    <p class="mt-2 text-3xl font-bold text-blue-900">
                        {{ number_format($stats['total_penduduk'] ?? 0) }}
                    </p>
                    <p class="mt-1 text-xs text-blue-700/70">
                        Jumlah jiwa terdaftar dalam sistem.
                    </p>
                </div>

                <div class="p-4 bg-white border shadow-sm rounded-xl">
                    <p class="text-xs font-medium tracking-wide text-gray-500 uppercase">
                        Jumlah KK
                    </p>
                    <p class="mt-2 text-2xl font-bold text-gray-800">
                        {{ number_format($stats['total_kk'] ?? 0) }}
                    </p>
                    <p class="mt-1 text-xs text-gray-500">
                        Kepala keluarga terdata.
                    </p>
                </div>

                <div class="p-4 bg-white border shadow-sm rounded-xl">
                    <p class="text-xs font-medium tracking-wide uppercase text-sky-600">
                        Laki-laki
                    </p>
                    <p class="mt-2 text-2xl font-bold text-gray-800">
                        {{ number_format($stats['total_laki'] ?? 0) }}
                    </p>
                    <p class="mt-1 text-xs text-gray-500">
                        Total penduduk laki-laki.
                    </p>
                </div>

                <div class="p-4 bg-white border shadow-sm rounded-xl">
                    <p class="text-xs font-medium tracking-wide uppercase text-rose-600">
                        Perempuan
                    </p>
                    <p class="mt-2 text-2xl font-bold text-gray-800">
                        {{ number_format($stats['total_perempuan'] ?? 0) }}
                    </p>
                    <p class="mt-1 text-xs text-gray-500">
                        Total penduduk perempuan.
                    </p>
                </div>
            </div>

            {{-- Rasio L/P + Navigasi Cepat --}}
            <div class="grid grid-cols-1 gap-4 xl:grid-cols-3">
                {{-- Card Rasio --}}
                <div class="p-6 bg-white border shadow-sm rounded-xl xl:col-span-2">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">
                                Rasio Penduduk Laki-laki & Perempuan
                            </h3>
                            <p class="mt-1 text-xs text-gray-500">
                                Visualisasi komposisi penduduk berdasarkan jenis kelamin.
                            </p>
                        </div>
                    </div>

                    <div class="grid items-center gap-4 md:grid-cols-3">
                        <div class="md:col-span-2">
                            <canvas id="ratioChart" height="140"></canvas>
                        </div>
                        <div class="space-y-3 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="inline-flex items-center gap-1">
                                    <span class="inline-block w-3 h-3 bg-blue-500 rounded-full"></span>
                                    Laki-laki
                                </span>
                                <span class="font-semibold">
                                    {{ number_format($stats['total_laki'] ?? 0) }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="inline-flex items-center gap-1">
                                    <span class="inline-block w-3 h-3 bg-orange-500 rounded-full"></span>
                                    Perempuan
                                </span>
                                <span class="font-semibold">
                                    {{ number_format($stats['total_perempuan'] ?? 0) }}
                                </span>
                            </div>

                            @php
                                $total = (int) ($stats['total_laki'] ?? 0) + (int) ($stats['total_perempuan'] ?? 0);
                                $ratioL = $total ? round((($stats['total_laki'] ?? 0) / $total) * 100) : 0;
                                $ratioP = $total ? round((($stats['total_perempuan'] ?? 0) / $total) * 100) : 0;
                            @endphp

                            <div class="pt-2 mt-2 border-t border-gray-100">
                                <p class="text-[11px] text-gray-500">
                                    Perbandingan persentase:
                                </p>
                                <p class="mt-1 text-sm font-semibold text-gray-800">
                                    {{ $ratioL }}% Laki-laki &middot; {{ $ratioP }}% Perempuan
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Navigasi Cepat --}}
                <div class="p-6 bg-white border shadow-sm rounded-xl">
                    <h3 class="mb-3 text-lg font-semibold text-gray-800">
                        Navigasi Cepat
                    </h3>
                    <p class="mb-3 text-xs text-gray-500">
                        Pilih modul untuk melihat detail data dan melakukan pengelolaan.
                    </p>

                    <div class="space-y-3 text-sm">
                        <div>
                            <p class="mb-1 text-[11px] font-semibold tracking-wide text-gray-500 uppercase">
                                Data Ringkasan
                            </p>
                            <ul class="space-y-1">
                                <li>
                                    <a href="{{ route('citizens.index') }}" class="inline-flex items-center gap-2 text-blue-600 hover:underline">
                                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                        Data Penduduk (Master Citizen)
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('data.populasi') }}" class="inline-flex items-center gap-2 text-blue-600 hover:underline">
                                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                                        Data Populasi per Wilayah
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('rentang-umur.index') }}" class="inline-flex items-center gap-2 text-blue-600 hover:underline">
                                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Data Rentang Umur
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="pt-2 border-t border-gray-100">
                            <p class="mb-1 text-[11px] font-semibold tracking-wide text-gray-500 uppercase">
                                Pendidikan & Pekerjaan
                            </p>
                            <ul class="space-y-1">
                                <li>
                                    <a href="{{ route('data.pendidikan-kk') }}" class="inline-flex items-center gap-2 text-blue-600 hover:underline">
                                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                                        Pendidikan dalam KK
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('data.pendidikan-ditempuh') }}" class="inline-flex items-center gap-2 text-blue-600 hover:underline">
                                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-indigo-400"></span>
                                        Pendidikan yang Ditempuh
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('data.pekerjaan') }}" class="inline-flex items-center gap-2 text-blue-600 hover:underline">
                                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Pekerjaan
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="pt-2 border-t border-gray-100">
                            <p class="mb-1 text-[11px] font-semibold tracking-wide text-gray-500 uppercase">
                                Sosial & GIS
                            </p>
                            <ul class="space-y-1">
                                <li>
                                    <a href="{{ route('data.agama') }}" class="inline-flex items-center gap-2 text-blue-600 hover:underline">
                                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-purple-500"></span>
                                        Agama
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('data.jenis-kelamin') }}" class="inline-flex items-center gap-2 text-blue-600 hover:underline">
                                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        Jenis Kelamin
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('map.index') }}" class="inline-flex items-center gap-2 text-blue-600 hover:underline">
                                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-teal-500"></span>
                                        Peta Sebaran Penduduk (GIS Mini)
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Chart.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('ratioChart');

        const laki = {{ (int) ($stats['total_laki'] ?? 0) }};
        const perempuan = {{ (int) ($stats['total_perempuan'] ?? 0) }};

        if (ctx) {
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Laki-laki', 'Perempuan'],
                    datasets: [{
                        data: [laki, perempuan],
                        backgroundColor: [
                            'rgba(59,130,246,0.6)',
                            'rgba(234,88,12,0.6)'
                        ],
                        borderColor: [
                            'rgba(59,130,246,1)',
                            'rgba(234,88,12,1)'
                        ],
                        borderWidth: 1,
                    }]
                },
                options: {
                    responsive: true,
                    cutout: '60%',
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: { enabled: true }
                    }
                }
            });
        }
    </script>
</x-app-layout>
