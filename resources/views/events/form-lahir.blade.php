{{-- resources/views/events/form-lahir.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Form Peristiwa Lahir
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="p-6 bg-white shadow-sm sm:rounded-lg">

                @php
                    $user = auth()->user();
                    $role = $user->role ?? 'viewer';

                    // ✅ policy-based permission
                    $canCreate = $user ? $user->can('create', \App\Models\PopulationEvent::class) : false;
                @endphp

                {{-- ✅ Guard UI (viewer terkunci) --}}
                @if(!$canCreate)
                    <div class="p-4 mb-4 text-sm text-red-800 bg-red-100 border border-red-200 rounded-lg">
                        Anda tidak memiliki akses untuk mencatat peristiwa. Hanya <span class="font-semibold">admin/operator</span>.
                        <div class="mt-2">
                            <a href="{{ route('events.index') }}" class="text-blue-700 hover:underline">
                                ← Kembali ke daftar peristiwa
                            </a>
                        </div>
                    </div>
                @endif

                {{-- Notifikasi error backend --}}
                @if ($errors->any())
                    <div class="p-4 mb-4 border border-red-200 rounded-lg bg-red-50">
                        <div class="font-semibold text-red-800">Periksa input kamu:</div>
                        <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="formLahir"
                      action="{{ route('events.lahir.store') }}"
                      method="POST"
                      class="space-y-4">
                    @csrf

                    {{-- ✅ Identitas penduduk (TomSelect reusable) --}}
                    @include('events.partials.identity-tomselect', [
                        'canCreate' => $canCreate,
                        'eventLabel' => 'Lahir',
                        'onlyActive' => true,
                    ])

                    <h3 class="pt-4 mb-2 text-lg font-semibold text-gray-700">
                        Detail Peristiwa Lahir
                    </h3>

                    <div>
                        <label class="block mb-1 text-sm font-medium">Tanggal Peristiwa</label>
                        <input id="tanggal_peristiwa" type="date" name="tanggal_peristiwa"
                               value="{{ old('tanggal_peristiwa', now()->toDateString()) }}"
                               class="w-full border-gray-300 rounded" {{ $canCreate ? '' : 'disabled' }}>
                        @error('tanggal_peristiwa')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block mb-1 text-sm font-medium">Tanggal Lapor</label>
                        <input id="tanggal_lapor" type="date" name="tanggal_lapor"
                               value="{{ old('tanggal_lapor') }}"
                               class="w-full border-gray-300 rounded" {{ $canCreate ? '' : 'disabled' }}>
                        <p class="mt-1 text-xs text-gray-500">
                            Jika kosong, sistem otomatis isi tanggal hari ini saat simpan.
                        </p>
                        @error('tanggal_lapor')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block mb-1 text-sm font-medium">Tempat Lahir (Opsional)</label>
                        <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir') }}"
                               class="w-full border-gray-300 rounded" {{ $canCreate ? '' : 'disabled' }}>
                        @error('tempat_lahir')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block mb-1 text-sm font-medium">Jam Lahir (Opsional)</label>
                        <input type="time" name="jam_lahir" value="{{ old('jam_lahir') }}"
                               class="w-full border-gray-300 rounded" {{ $canCreate ? '' : 'disabled' }}>
                        @error('jam_lahir')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block mb-1 text-sm font-medium">Penolong Kelahiran (Opsional)</label>
                        <select name="penolong_kelahiran" class="w-full border-gray-300 rounded" {{ $canCreate ? '' : 'disabled' }}>
                            <option value="">-- pilih --</option>
                            <option value="dokter" {{ old('penolong_kelahiran') === 'dokter' ? 'selected' : '' }}>Dokter</option>
                            <option value="bidan" {{ old('penolong_kelahiran') === 'bidan' ? 'selected' : '' }}>Bidan</option>
                            <option value="tenaga_kesehatan" {{ old('penolong_kelahiran') === 'tenaga_kesehatan' ? 'selected' : '' }}>Tenaga Kesehatan</option>
                            <option value="dukun" {{ old('penolong_kelahiran') === 'dukun' ? 'selected' : '' }}>Dukun</option>
                            <option value="lainnya" {{ old('penolong_kelahiran') === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        @error('penolong_kelahiran')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block mb-1 text-sm font-medium">Catatan Peristiwa</label>
                        <textarea name="catatan_peristiwa" rows="3"
                                  class="w-full border-gray-300 rounded"
                                  {{ $canCreate ? '' : 'disabled' }}>{{ old('catatan_peristiwa') }}</textarea>
                        @error('catatan_peristiwa')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between pt-4">
                        <a href="{{ route('events.index') }}" class="text-sm text-gray-600 hover:underline">
                            ← Kembali
                        </a>

                        <button id="btnSubmit" type="submit"
                                class="px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
                                {{ $canCreate ? '' : 'disabled' }}>
                            Simpan Peristiwa
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
