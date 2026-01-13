<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            Tambah Peristiwa Kependudukan
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            {{-- Flash info --}}
            @if (session('success'))
                <div class="p-3 mb-4 text-sm text-green-800 bg-green-100 border border-green-200 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="p-3 mb-4 text-sm text-red-800 bg-red-100 border border-red-200 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <div class="p-6 bg-white shadow-sm sm:rounded-lg">
                @php
                    $user = auth()->user();
                    $role = $user->role ?? 'viewer';

                    $canCreate = in_array($role, ['admin','operator'], true);

                    // ✅ kalau route belum ada (safety), fallback '#'
                    $meninggalHref = \Illuminate\Support\Facades\Route::has('events.meninggal.create')
                        ? route('events.meninggal.create')
                        : '#';

                    $items = [
                        ['label' => 'Kelahiran', 'ready' => false, 'href' => '#'],
                        ['label' => 'Datang', 'ready' => false, 'href' => '#'],
                        ['label' => 'Pindah', 'ready' => false, 'href' => '#'],
                        ['label' => 'Meninggal', 'ready' => true,  'href' => $meninggalHref],
                        ['label' => 'Hilang', 'ready' => false, 'href' => '#'],
                        ['label' => 'Penduduk Sementara (1×24 jam)', 'ready' => false, 'href' => '#'],
                    ];
                @endphp

                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Pilih Jenis Peristiwa</h3>
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
                            @endif
                        ">
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

                <div class="mt-5 space-y-2 text-sm">
                    @foreach($items as $it)

                        {{-- ✅ READY --}}
                        @if($it['ready'])
                            @if($canCreate)
                                <a href="{{ $it['href'] }}"
                                   class="flex items-center justify-between px-4 py-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                                    <span class="font-medium text-blue-700">• {{ $it['label'] }}</span>
                                    <span class="text-xs font-semibold text-green-700 bg-green-100 px-2 py-0.5 rounded-full">
                                        Tersedia
                                    </span>
                                </a>
                            @else
                                {{-- viewer: tampilkan tapi disabled --}}
                                <div class="flex items-center justify-between px-4 py-3 border border-gray-200 rounded-lg opacity-75 bg-gray-50">
                                    <span class="text-gray-600">• {{ $it['label'] }}</span>
                                    <span class="text-xs font-semibold text-gray-700 bg-gray-200 px-2 py-0.5 rounded-full">
                                        Terkunci
                                    </span>
                                </div>
                            @endif

                        {{-- ✅ COMING SOON --}}
                        @else
                            <div class="flex items-center justify-between px-4 py-3 border border-gray-200 rounded-lg bg-gray-50">
                                <span class="text-gray-600">• {{ $it['label'] }}</span>
                                <span class="text-xs font-semibold text-gray-700 bg-gray-200 px-2 py-0.5 rounded-full">
                                    Coming soon
                                </span>
                            </div>
                        @endif

                    @endforeach
                </div>

                <div class="flex items-center justify-between mt-6">
                    <a href="{{ route('events.index') }}" class="text-sm text-gray-600 hover:underline">
                        ← Kembali ke daftar peristiwa
                    </a>

                    <div class="text-xs text-gray-500">
                        Saat ini yang aktif: <span class="font-semibold text-gray-700">Meninggal</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
