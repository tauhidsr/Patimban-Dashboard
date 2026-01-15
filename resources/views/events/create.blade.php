{{-- resources/views/events/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            Tambah Peristiwa Kependudukan
        </h2>
    </x-slot>

    @php
        $user = auth()->user();
        $role = $user->role ?? 'viewer';

        // policy di controller sudah mengamankan create, ini hanya untuk UI
        $canCreate = in_array($role, ['admin', 'operator'], true);

        // safety: kalau route belum ada -> '#'
        $href = fn($name) => \Illuminate\Support\Facades\Route::has($name) ? route($name) : '#';

        $items = [
            [
                'key' => 'lahir',
                'label' => 'Kelahiran',
                'desc' => 'Catat kelahiran (data ibu sebagai pengikat utama).',
                'ready' => true,
                'href' => $href('events.lahir.create'),
                'color' => 'green',
                'icon' => '🍼',
            ],
            [
                'key' => 'datang',
                'label' => 'Datang',
                'desc' => 'Penduduk datang dari luar wilayah.',
                'ready' => true, // ✅ AKTIFKAN
                'href' => $href('events.datang.create'), // ✅ ROUTE DATANG
                'color' => 'green',
                'icon' => '📥',
            ],
            [
                'key' => 'pindah',
                'label' => 'Pindah',
                'desc' => 'Penduduk pindah domisili.',
                'ready' => false,
                'href' => '#',
                'color' => 'gray',
                'icon' => '🚚',
            ],
            [
                'key' => 'meninggal',
                'label' => 'Meninggal',
                'desc' => 'Catat peristiwa meninggal (mengubah status penduduk saat disetujui).',
                'ready' => true,
                'href' => $href('events.meninggal.create'),
                'color' => 'red',
                'icon' => '🕊️',
            ],
            [
                'key' => 'hilang',
                'label' => 'Hilang',
                'desc' => 'Catat peristiwa hilang (mengubah status penduduk saat disetujui).',
                'ready' => true,
                'href' => $href('events.hilang.create'),
                'color' => 'yellow',
                'icon' => '🧭',
            ],
            [
                'key' => 'sementara',
                'label' => 'Penduduk Sementara (1×24 jam)',
                'desc' => 'Pencatatan penduduk sementara (tamu/pendatang singkat).',
                'ready' => false,
                'href' => '#',
                'color' => 'gray',
                'icon' => '⏱️',
            ],
        ];

        $activeLabels = collect($items)->where('ready', true)->pluck('label')->implode(', ');
    @endphp

    <div class="py-6">
        <div class="max-w-4xl mx-auto space-y-6 sm:px-6 lg:px-8">

            {{-- flash --}}
            @if (session('success'))
                <div class="p-3 text-sm text-green-800 bg-green-100 border border-green-200 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="p-3 text-sm text-red-800 bg-red-100 border border-red-200 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <div class="p-6 bg-white shadow-sm sm:rounded-lg">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">
                            Pilih Jenis Peristiwa
                        </h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Pilih jenis peristiwa yang ingin dicatat.
                        </p>
                        <p class="mt-1 text-xs text-gray-500">
                            Catatan: Operator hanya bisa mencatat peristiwa sesuai wilayah (dusun/RW/RT) miliknya.
                        </p>
                    </div>

                    <div class="text-right">
                        <div class="text-xs text-gray-500">Role Anda</div>
                        <div class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full
                            @if($role === 'admin') bg-purple-100 text-purple-800
                            @elseif($role === 'operator') bg-blue-100 text-blue-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ $role }}
                        </div>
                    </div>
                </div>

                @if(!$canCreate)
                    <div class="p-4 mt-4 text-sm text-red-800 bg-red-100 border border-red-200 rounded-lg">
                        Anda tidak memiliki akses untuk menambah peristiwa.
                        Hanya <span class="font-semibold">admin/operator</span> yang dapat mencatat peristiwa.
                    </div>
                @endif

                {{-- grid cards --}}
                <div class="grid gap-3 mt-6 sm:grid-cols-2">
                    @foreach($items as $it)
                        @php
                            $isReady = (bool) $it['ready'];
                            $isClickable = $isReady && $canCreate && ($it['href'] !== '#');

                            $badgeText = $isReady ? 'Tersedia' : 'Coming soon';

                            $badgeClass = 'bg-gray-200 text-gray-700';
                            if ($isReady) {
                                if ($it['color'] === 'green')
                                    $badgeClass = 'bg-green-100 text-green-700';
                                elseif ($it['color'] === 'red')
                                    $badgeClass = 'bg-red-100 text-red-700';
                                elseif ($it['color'] === 'yellow')
                                    $badgeClass = 'bg-yellow-100 text-yellow-800';
                                else
                                    $badgeClass = 'bg-green-100 text-green-700';
                            }

                            $cardBase = 'border rounded-lg p-4 transition flex gap-3';
                            $cardState = $isClickable
                                ? 'border-gray-200 hover:bg-gray-50'
                                : 'border-gray-200 bg-gray-50 opacity-80 cursor-not-allowed';

                            // kalau ready tapi viewer => terkunci
                            $lockInfo = (!$canCreate && $isReady) ? 'Terkunci (viewer)' : null;
                        @endphp

                        @if($isClickable)
                            <a href="{{ $it['href'] }}" class="{{ $cardBase }} {{ $cardState }}">
                                <div class="text-2xl leading-none">{{ $it['icon'] }}</div>
                                <div class="flex-1">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="font-semibold text-gray-800">
                                            {{ $it['label'] }}
                                        </div>
                                        <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full {{ $badgeClass }}">
                                            {{ $badgeText }}
                                        </span>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-600">
                                        {{ $it['desc'] }}
                                    </p>
                                </div>
                            </a>
                        @else
                            <div class="{{ $cardBase }} {{ $cardState }}">
                                <div class="text-2xl leading-none">{{ $it['icon'] }}</div>
                                <div class="flex-1">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="font-semibold text-gray-800">
                                            {{ $it['label'] }}
                                        </div>

                                        <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full {{ $badgeClass }}">
                                            {{ $lockInfo ? 'Terkunci' : $badgeText }}
                                        </span>
                                    </div>

                                    <p class="mt-1 text-xs text-gray-600">
                                        {{ $it['desc'] }}
                                    </p>

                                    @if($lockInfo)
                                        <p class="mt-2 text-[11px] text-gray-500">
                                            Anda hanya bisa melihat data. Hubungi admin jika butuh akses input.
                                        </p>
                                    @elseif(!$isReady)
                                        <p class="mt-2 text-[11px] text-gray-500">
                                            Fitur sedang disiapkan.
                                        </p>
                                    @elseif($it['href'] === '#')
                                        <p class="mt-2 text-[11px] text-gray-500">
                                            Route belum tersedia.
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                <div class="flex items-center justify-between mt-6">
                    <a href="{{ route('events.index') }}" class="text-sm text-gray-600 hover:underline">
                        ← Kembali ke daftar peristiwa
                    </a>

                    <div class="text-xs text-gray-500">
                        Saat ini yang aktif:
                        <span class="font-semibold text-gray-700">{{ $activeLabels ?: '-' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
