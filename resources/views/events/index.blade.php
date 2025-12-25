{{-- resources/views/events/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Peristiwa Kependudukan') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">

            {{-- flash success --}}
            @if (session('success'))
                <div class="p-3 text-sm text-green-800 bg-green-100 border border-green-200 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            {{-- ✅ ALERT KECIL: MENUNGGU VERIFIKASI ADMIN --}}
            @php
                $pendingCount = $events->getCollection()
                    ->where('status_verifikasi', 'menunggu')
                    ->count();
            @endphp

            @if ($pendingCount > 0)
                <div class="p-3 border border-orange-200 rounded-lg bg-orange-50">
                    <div class="flex items-start gap-2">
                        <div class="text-lg">⏳</div>
                        <div class="text-sm text-orange-800">
                            Ada <span class="font-semibold">{{ $pendingCount }}</span> peristiwa pada halaman ini yang
                            <span class="font-semibold">menunggu verifikasi admin</span>.
                            <span class="text-orange-700">
                                Operator hanya dapat mencatat peristiwa, keputusan akhir oleh admin desa.
                            </span>
                        </div>
                    </div>
                </div>
            @endif
            {{-- END ALERT --}}

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="flex items-center justify-between px-6 py-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-800">
                        Daftar Peristiwa Penduduk
                    </h3>

                    <a href="{{ route('events.create') }}"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded hover:bg-blue-700">
                        + Tambah Peristiwa
                    </a>
                </div>

                {{-- filter & pencarian --}}
                <div class="px-6 py-3 border-b bg-gray-50">
                    <form method="GET" action="{{ route('events.index') }}"
                        class="grid gap-2 text-sm sm:grid-cols-4 sm:items-end">
                        {{-- jenis peristiwa --}}
                        <div>
                            <label class="block mb-1 text-xs font-medium text-gray-700">
                                Jenis Peristiwa
                            </label>
                            <select name="jenis" class="w-full border-gray-300 rounded">
                                <option value="">Semua</option>
                                <option value="lahir" {{ ($filters['jenis'] ?? '') === 'lahir' ? 'selected' : '' }}>Lahir</option>
                                <option value="datang" {{ ($filters['jenis'] ?? '') === 'datang' ? 'selected' : '' }}>Datang</option>
                                <option value="pindah" {{ ($filters['jenis'] ?? '') === 'pindah' ? 'selected' : '' }}>Pindah</option>
                                <option value="meninggal" {{ ($filters['jenis'] ?? '') === 'meninggal' ? 'selected' : '' }}>Meninggal</option>
                                <option value="hilang" {{ ($filters['jenis'] ?? '') === 'hilang' ? 'selected' : '' }}>Hilang</option>
                                <option value="sementara_masuk" {{ ($filters['jenis'] ?? '') === 'sementara_masuk' ? 'selected' : '' }}>Sementara Masuk</option>
                                <option value="sementara_keluar" {{ ($filters['jenis'] ?? '') === 'sementara_keluar' ? 'selected' : '' }}>Sementara Keluar</option>
                            </select>
                        </div>

                        {{-- status verifikasi --}}
                        <div>
                            <label class="block mb-1 text-xs font-medium text-gray-700">
                                Status Verifikasi
                            </label>
                            <select name="status" class="w-full border-gray-300 rounded">
                                <option value="">Semua</option>
                                <option value="menunggu" {{ ($filters['status'] ?? '') === 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                                <option value="disetujui" {{ ($filters['status'] ?? '') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                                <option value="ditolak" {{ ($filters['status'] ?? '') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div>

                        {{-- pencarian nama/NIK/KK --}}
                        <div>
                            <label class="block mb-1 text-xs font-medium text-gray-700">
                                Cari Nama / NIK / No KK
                            </label>
                            <input type="text" name="q" value="{{ $filters['q'] ?? '' }}"
                                class="w-full border-gray-300 rounded" placeholder="misal: Siti / 3504XXXXXXXX">
                        </div>

                        {{-- tombol --}}
                        <div class="flex gap-2 mt-2 sm:mt-0">
                            <button type="submit"
                                class="flex-1 px-3 py-2 text-xs font-semibold text-white bg-blue-600 rounded hover:bg-blue-700">
                                Terapkan
                            </button>
                            <a href="{{ route('events.index') }}"
                                class="px-3 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-100">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>

                <div class="p-0 overflow-x-auto">
                    <table class="min-w-full text-sm text-left">
                        <thead>
                            <tr class="text-xs font-semibold tracking-wide text-gray-600 uppercase bg-gray-100 border-b">
                                <th class="px-4 py-2">No</th>
                                <th class="px-4 py-2">Nama</th>
                                <th class="px-4 py-2">NIK</th>
                                <th class="px-4 py-2">Jenis Peristiwa</th>
                                <th class="px-4 py-2">Tanggal Peristiwa</th>
                                <th class="px-4 py-2">Status</th>
                                <th class="px-4 py-2">Dicatat Oleh</th>
                                <th class="px-4 py-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $mapJenis = [
                                    'lahir' => 'Lahir',
                                    'datang' => 'Datang',
                                    'pindah' => 'Pindah',
                                    'meninggal' => 'Meninggal',
                                    'hilang' => 'Hilang',
                                    'sementara_masuk' => 'Sementara Masuk',
                                    'sementara_keluar' => 'Sementara Keluar',
                                ];
                            @endphp

                            @forelse ($events as $index => $event)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-2 align-top">
                                        {{ ($events->currentPage() - 1) * $events->perPage() + $index + 1 }}
                                    </td>

                                    <td class="px-4 py-2 align-top">
                                        {{ $event->nama ?? '-' }}
                                    </td>

                                    <td class="px-4 py-2 align-top">
                                        {{ $event->nik ?? '-' }}
                                    </td>

                                    <td class="px-4 py-2 align-top">
                                        @php
                                            $labelJenis = $mapJenis[$event->jenis_peristiwa] ?? $event->jenis_peristiwa;
                                        @endphp
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                            @if($event->jenis_peristiwa === 'meninggal')
                                                bg-red-100 text-red-800
                                            @elseif($event->jenis_peristiwa === 'pindah')
                                                bg-yellow-100 text-yellow-800
                                            @elseif(in_array($event->jenis_peristiwa, ['lahir', 'datang']))
                                                bg-green-100 text-green-800
                                            @else
                                                bg-gray-100 text-gray-800
                                            @endif">
                                            {{ $labelJenis }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-2 align-top">
                                        {{ $event->tanggal_peristiwa ? $event->tanggal_peristiwa->format('d-m-Y') : '-' }}
                                    </td>

                                    {{-- ✅ STATUS + INFO VERIFIKASI --}}
                                    <td class="px-4 py-2 align-top">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                            @if($event->status_verifikasi === 'menunggu')
                                                bg-orange-100 text-orange-800
                                            @elseif($event->status_verifikasi === 'disetujui')
                                                bg-green-100 text-green-800
                                            @else
                                                bg-red-100 text-red-800
                                            @endif">
                                            {{ ucfirst($event->status_verifikasi) }}
                                        </span>

                                        {{-- ✅ baris kecil “diverifikasi oleh siapa + kapan” --}}
                                        @if(in_array($event->status_verifikasi, ['disetujui','ditolak']) && $event->verifier && $event->verified_at)
                                            <div class="mt-1 text-[11px] text-gray-500">
                                                Diverifikasi oleh <span class="font-medium text-gray-700">{{ $event->verifier->name }}</span>
                                                • {{ \Carbon\Carbon::parse($event->verified_at)->timezone(config('app.timezone'))->format('d-m-Y H:i') }}
                                            </div>
                                        @elseif(in_array($event->status_verifikasi, ['disetujui','ditolak']) && $event->verifier)
                                            <div class="mt-1 text-[11px] text-gray-500">
                                                Diverifikasi oleh <span class="font-medium text-gray-700">{{ $event->verifier->name }}</span>
                                            </div>
                                        @elseif($event->status_verifikasi === 'menunggu')
                                            <div class="mt-1 text-[11px] text-gray-500">
                                                Menunggu verifikasi admin
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Dicatat Oleh --}}
                                    <td class="px-4 py-2 align-top">
                                        @if($event->creator)
                                            <div class="text-sm text-gray-800">{{ $event->creator->name }}</div>
                                            <div class="text-[11px] text-gray-500">({{ $event->creator->role ?? 'user' }})</div>
                                        @else
                                            {{ $event->created_by ? 'User ID: ' . $event->created_by : '-' }}
                                        @endif
                                    </td>

                                    {{-- tombol aksi --}}
                                    <td class="px-4 py-2 align-top">
                                        <a href="{{ route('events.show', $event->id) }}"
                                            class="inline-flex px-3 py-1 text-xs font-medium text-white bg-indigo-600 rounded hover:bg-indigo-700">
                                            Lihat
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-6 text-sm text-center text-gray-500">
                                        Belum ada peristiwa yang tercatat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- pagination --}}
                @if ($events->hasPages())
                    <div class="px-6 py-4 border-t">
                        {{ $events->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
