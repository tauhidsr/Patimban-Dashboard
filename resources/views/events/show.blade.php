{{-- resources/views/events/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Detail Peristiwa Kependudukan') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto space-y-6 sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">

                {{-- header identitas + jenis peristiwa --}}
                <div class="flex items-center justify-between px-6 py-4 border-b">
                    <div>
                        <p class="text-sm text-gray-500">Nama Penduduk</p>
                        <p class="text-lg font-semibold text-gray-900">
                            {{ $event->nama ?? '-' }}
                        </p>
                        <p class="mt-1 text-sm text-gray-500">
                            NIK: {{ $event->nik ?? '-' }} &middot;
                            No KK: {{ $event->no_kk ?? '-' }}
                        </p>
                    </div>

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
                        $labelJenis = $mapJenis[$event->jenis_peristiwa] ?? $event->jenis_peristiwa;
                    @endphp

                    <div class="text-right">
                        {{-- badge jenis peristiwa --}}
                        <span class="inline-flex mb-1 px-3 py-1 text-xs font-semibold rounded-full
                            @if($event->jenis_peristiwa === 'meninggal')
                                bg-red-100 text-red-800
                            @elseif($event->jenis_peristiwa === 'pindah')
                                bg-yellow-100 text-yellow-800
                            @elseif(in_array($event->jenis_peristiwa, ['lahir', 'datang']))
                                bg-green-100 text-green-800
                            @else
                                bg-gray-100 text-gray-800
                            @endif
                        ">
                            {{ $labelJenis }}
                        </span>

                        {{-- badge status verifikasi --}}
                        <div>
                            <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full
                                @if($event->status_verifikasi === 'menunggu')
                                    bg-orange-100 text-orange-800
                                @elseif($event->status_verifikasi === 'disetujui')
                                    bg-green-100 text-green-800
                                @else
                                    bg-red-100 text-red-800
                                @endif
                            ">
                                Status: {{ ucfirst($event->status_verifikasi) }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- body detail --}}
                <div class="px-6 py-4 space-y-4">

                    {{-- ✅ WARNING BESAR kalau sudah disetujui --}}
                    @if ($event->status_verifikasi === 'disetujui')
                        <div class="p-4 border border-red-300 rounded-lg bg-red-50">
                            <div class="flex gap-3">
                                <div class="text-xl">⚠️</div>
                                <div>
                                    <p class="font-semibold text-red-800">
                                        Perhatian – Peristiwa Sudah Disetujui
                                    </p>
                                    <p class="mt-1 text-sm text-red-700">
                                        Peristiwa ini <strong>sudah mengubah status penduduk</strong>.
                                        Jika verifikasi diubah, sistem akan mencoba <strong>mengembalikan status ke sebelumnya</strong>
                                        dan mencatat log pembatalan.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- tanggal peristiwa & lapor --}}
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Tanggal Peristiwa</p>
                            <p class="mt-1 text-sm text-gray-800">
                                {{ $event->tanggal_peristiwa ? $event->tanggal_peristiwa->format('d-m-Y') : '-' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Tanggal Lapor</p>
                            <p class="mt-1 text-sm text-gray-800">
                                {{ $event->tanggal_lapor ? $event->tanggal_lapor->format('d-m-Y') : '-' }}
                            </p>
                        </div>
                    </div>

                    {{-- Detail KHUSUS MENINGGAL --}}
                    @if($event->jenis_peristiwa === 'meninggal')
                        <div class="pt-4 mt-2 border-t">
                            <h3 class="mb-2 text-sm font-semibold text-gray-700">
                                Detail Peristiwa Meninggal
                            </h3>

                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase">Tempat Meninggal</p>
                                    <p class="mt-1 text-sm text-gray-800">{{ $event->tempat_meninggal ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase">Jam Kematian</p>
                                    <p class="mt-1 text-sm text-gray-800">{{ $event->jam_kematian ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase">Penyebab</p>
                                    <p class="mt-1 text-sm text-gray-800">{{ $event->penyebab_kematian ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase">Yang Menyatakan</p>
                                    <p class="mt-1 text-sm text-gray-800">{{ $event->yang_menyatakan_kematian ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase">Nomor Akta Kematian</p>
                                    <p class="mt-1 text-sm text-gray-800">{{ $event->nomor_akta_kematian ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase">File Akta</p>
                                    <p class="mt-1 text-sm text-gray-800">
                                        @if($event->file_akta_kematian_path)
                                            <a href="{{ asset('storage/' . $event->file_akta_kematian_path) }}"
                                                class="text-blue-600 hover:underline" target="_blank">
                                                Lihat Dokumen
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- ✅ Status verifikasi admin --}}
                    <div class="pt-4 mt-2 border-t">
                        <p class="text-xs font-semibold text-gray-500 uppercase">Status Verifikasi Admin</p>
                        <p class="mt-1 text-sm text-gray-800">
                            {{ ucfirst($event->status_verifikasi) }}
                            @if ($event->catatan_verifikasi)
                                — <span class="text-gray-600">"{{ $event->catatan_verifikasi }}"</span>
                            @endif
                        </p>

                        {{-- ✅ Step A4: tampilkan verifier + waktu --}}
                        @if ($event->verified_by && $event->verified_at)
                            <p class="mt-1 text-xs text-gray-600">
                                Diverifikasi oleh
                                <span class="font-medium text-gray-800">
                                    {{ optional($event->verifier)->name ?? 'Admin' }}
                                </span>
                                pada
                                <span class="font-medium text-gray-800">
                                    {{ $event->verified_at->format('d-m-Y H:i') }}
                                </span>
                            </p>
                        @endif
                    </div>

                    {{-- form ubah verifikasi (admin bisa edit, non-admin read-only) --}}
                    @auth
                        <div class="pt-4 mt-2 border-t">
                            <p class="mb-2 text-xs font-semibold text-gray-500 uppercase">Ubah Status Verifikasi</p>

                            {{-- error verifikasi --}}
                            @if ($errors->any())
                                <div class="p-2 mb-2 text-xs text-red-800 bg-red-100 border border-red-200 rounded">
                                    <ul class="pl-4 list-disc">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @php
                                $isAdmin = auth()->user()->role === 'admin';
                            @endphp

                            <form id="form-verifikasi" action="{{ route('events.verify', $event->id) }}" method="POST"
                                class="space-y-3 sm:flex sm:items-end sm:space-y-0 sm:space-x-3">
                                @csrf

                                <div>
                                    <label class="block mb-1 text-xs font-medium text-gray-700">
                                        Status
                                    </label>

                                    <select name="status_verifikasi"
                                        class="text-sm border-gray-300 rounded {{ $isAdmin ? '' : 'bg-gray-50' }}"
                                        {{ $isAdmin ? '' : 'disabled' }}>
                                        <option value="menunggu" {{ $event->status_verifikasi === 'menunggu' ? 'selected' : '' }}>
                                            Menunggu
                                        </option>
                                        <option value="disetujui" {{ $event->status_verifikasi === 'disetujui' ? 'selected' : '' }}>
                                            Disetujui
                                        </option>
                                        <option value="ditolak" {{ $event->status_verifikasi === 'ditolak' ? 'selected' : '' }}>
                                            Ditolak
                                        </option>
                                    </select>

                                    @if(!$isAdmin)
                                        <p class="mt-1 text-[11px] text-gray-500">
                                            Hanya admin yang dapat mengubah verifikasi.
                                        </p>
                                    @endif
                                </div>

                                <div class="flex-1">
                                    <label class="block mb-1 text-xs font-medium text-gray-700">
                                        Catatan Verifikasi (opsional)
                                    </label>

                                    <input type="text" name="catatan_verifikasi"
                                        value="{{ old('catatan_verifikasi', $event->catatan_verifikasi) }}"
                                        class="w-full text-sm border-gray-300 rounded {{ $isAdmin ? '' : 'bg-gray-50' }}"
                                        placeholder="misal: data sudah sesuai dengan surat keterangan"
                                        {{ $isAdmin ? '' : 'disabled' }}>
                                </div>

                                @if($isAdmin)
                                    <div>
                                        <button type="submit"
                                            class="px-4 py-2 text-xs font-semibold text-white bg-green-600 rounded hover:bg-green-700">
                                            Simpan Verifikasi
                                        </button>
                                    </div>
                                @endif
                            </form>
                        </div>
                    @endauth

                    {{-- ✅ catatan peristiwa --}}
                    <div class="pt-4 mt-2 border-t">
                        <p class="text-xs font-semibold text-gray-500 uppercase">Catatan Peristiwa</p>
                        <p class="mt-1 text-sm text-gray-800 whitespace-pre-line">
                            {{ $event->catatan_peristiwa ?? '-' }}
                        </p>
                    </div>
                </div>

                {{-- ✅ footer navigasi (button kembali) --}}
                <div class="flex items-center justify-between px-6 py-4 border-t bg-gray-50">
                    <a href="{{ route('events.index') }}" class="text-sm text-gray-600 hover:underline">
                        ← Kembali ke daftar peristiwa
                    </a>
                </div>

            </div>
        </div>
    </div>

    {{-- ✅ Konfirmasi JS saat klik “Simpan Verifikasi” (aktif kalau status saat ini disetujui) --}}
    @if ($event->status_verifikasi === 'disetujui')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const form = document.getElementById('form-verifikasi');
                if (!form) return;

                form.addEventListener('submit', function (e) {
                    const ok = confirm(
                        'PERHATIAN!\n\n' +
                        'Peristiwa ini sudah DISUTUJUI dan telah mengubah status penduduk.\n' +
                        'Jika Anda mengubah status verifikasi, sistem akan mencoba mengembalikan status ke kondisi sebelumnya.\n\n' +
                        'Apakah Anda yakin ingin melanjutkan?'
                    );

                    if (!ok) e.preventDefault();
                });
            });
        </script>
    @endif
</x-app-layout>
