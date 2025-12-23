{{-- resources/views/citizen_events/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Peristiwa Kependudukan Warga') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">

            {{-- Kartu info --}}
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-base font-semibold text-gray-800">
                        Daftar Peristiwa Kependudukan
                    </h3>
                    <p class="mt-1 text-sm text-gray-600">
                        Menampilkan daftar peristiwa terkait warga (lahir, meninggal, pindah, datang, dll).
                    </p>
                </div>
            </div>

            {{-- Filter --}}
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="GET" action="{{ route('citizen-events.index') }}" class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">

                        <div>
                            <h3 class="text-sm font-semibold text-gray-800">
                                Filter Peristiwa
                            </h3>
                            <p class="text-xs text-gray-500">
                                (Opsional) Filter berdasarkan status verifikasi.
                            </p>
                        </div>

                        <div class="flex items-center gap-2 text-sm">
                            <select name="status"
                                    class="text-sm border-gray-300 rounded md:min-w-[180px]">
                                <option value="">Semua Status</option>
                                <option value="pending"   {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="verified"  {{ $status === 'verified' ? 'selected' : '' }}>Verified</option>
                                <option value="rejected"  {{ $status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>

                            <button type="submit"
                                    class="px-3 py-2 text-white bg-indigo-600 rounded hover:bg-indigo-700">
                                Terapkan
                            </button>

                            <a href="{{ route('citizen-events.index') }}"
                               class="px-3 py-2 text-gray-700 border border-gray-300 rounded hover:bg-gray-50">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Tabel --}}
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                            <tr class="text-left text-gray-600 border-b">
                                <th class="px-3 py-2">No</th>
                                <th class="px-3 py-2">Nama</th>
                                <th class="px-3 py-2">NIK</th>
                                <th class="px-3 py-2">Jenis</th>
                                <th class="px-3 py-2">Tanggal</th>
                                <th class="px-3 py-2">Status</th>
                                <th class="px-3 py-2">Keterangan</th>
                            </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-100">
                            @forelse ($events as $index => $event)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-2">
                                        {{ $events->firstItem() + $index }}
                                    </td>

                                    <td class="px-3 py-2">
                                        @if($event->citizen)
                                            <div class="font-semibold text-gray-800">
                                                {{ $event->citizen->nama }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $event->citizen->dusun ?? '-' }}
                                                @if($event->citizen->rw || $event->citizen->rt)
                                                    (RW {{ $event->citizen->rw ?? '-' }} / RT {{ $event->citizen->rt ?? '-' }})
                                                @endif
                                            </div>
                                        @else
                                            <div class="font-semibold text-gray-700">
                                                {{ $event->nama ?? '-' }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                Warga tidak terhubung (citizen_id kosong)
                                            </div>
                                        @endif
                                    </td>

                                    <td class="px-3 py-2 font-mono text-xs">
                                        {{ optional($event->citizen)->nik ?? '-' }}
                                    </td>

                                    <td class="px-3 py-2">
                                        <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded-full
                                            @switch($event->jenis_peristiwa)
                                                @case('lahir') bg-green-100 text-green-700 @break
                                                @case('meninggal') bg-red-100 text-red-700 @break
                                                @case('pindah') bg-amber-100 text-amber-700 @break
                                                @case('datang') bg-blue-100 text-blue-700 @break
                                                @default bg-gray-100 text-gray-700
                                            @endswitch
                                        ">
                                            {{ ucfirst($event->jenis_peristiwa) }}
                                        </span>
                                    </td>

                                    <td class="px-3 py-2 text-gray-700">
                                        {{ \Illuminate\Support\Carbon::parse($event->tanggal_peristiwa)->format('d/m/Y') }}
                                    </td>

                                    <td class="px-3 py-2">
                                        <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded-full
                                            @switch($event->status_verifikasi)
                                                @case('verified') bg-green-100 text-green-700 @break
                                                @case('rejected') bg-red-100 text-red-700 @break
                                                @default bg-yellow-100 text-yellow-700
                                            @endswitch
                                        ">
                                            {{ ucfirst($event->status_verifikasi ?? 'pending') }}
                                        </span>
                                    </td>

                                    <td class="px-3 py-2 text-gray-700">
                                        {{ $event->keterangan ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-3 py-8 text-center text-gray-500">
                                        Belum ada data peristiwa.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $events->links() }}
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
