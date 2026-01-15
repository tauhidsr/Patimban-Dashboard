{{-- resources/views/events/form-lahir.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Form Peristiwa Lahir
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="p-6 bg-white shadow-sm sm:rounded-lg">

                @php
                    $user = auth()->user();
                    $role = $user->role ?? 'viewer';
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
                      class="space-y-6">
                    @csrf

                    {{-- =======================================================
                         B. DATA IBU (WAJIB) - pengikat utama
                       ======================================================= --}}
                    <div class="p-4 border border-gray-200 rounded-lg bg-gray-50">
                        @include('events.partials.identity-tomselect', [
                            'canCreate'      => $canCreate,
                            'eventLabel'     => 'Lahir',
                            'onlyActive'     => true,

                            // ✅ make this instance unique
                            'prefix'         => 'ibu',
                            'title'          => 'Data Ibu (Wajib)',
                            'labelNik'       => 'NIK Ibu',
                            'requiredNik'    => true,
                            'lockSubmit'     => true, // ibu wajib -> kunci submit sampai valid

                            // ✅ field names agar tidak bentrok
                            'nameNik'        => 'nik_ibu',
                            'nameNoKk'       => 'no_kk_ibu',
                            'nameNama'       => 'nama_ibu',

                            // tampilkan umur (opsional, kalau API byNik mengembalikan tanggal_lahir)
                            'showAge'        => true,

                            // tombol submit id
                            'submitButtonId' => 'btnSubmit',
                        ])
                    </div>

                    {{-- =======================================================
                         C. DATA AYAH (Opsional tapi dianjurkan)
                       ======================================================= --}}
                    <div class="p-4 border border-gray-200 rounded-lg">
                        @include('events.partials.identity-tomselect', [
                            'canCreate'      => $canCreate,
                            'eventLabel'     => 'Lahir',
                            'onlyActive'     => true,

                            'prefix'         => 'ayah',
                            'title'          => 'Data Ayah (Opsional)',
                            'labelNik'       => 'NIK Ayah (Opsional)',
                            'requiredNik'    => false,
                            'lockSubmit'     => false, // ayah opsional -> tidak mengunci submit

                            'nameNik'        => 'nik_ayah',
                            'nameNoKk'       => 'no_kk_ayah',
                            'nameNama'       => 'nama_ayah',

                            'showAge'        => false,
                            'submitButtonId' => 'btnSubmit',
                        ])
                    </div>

                    {{-- =======================================================
                         A. DATA BAYI (sementara) - TANPA NIK
                       ======================================================= --}}
                    <div class="pt-2">
                        <h3 class="mb-2 text-lg font-semibold text-gray-700">
                            Data Bayi (Sementara)
                        </h3>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label class="block mb-1 text-sm font-medium">Nama Bayi (Opsional)</label>
                                <input type="text" name="nama_bayi" value="{{ old('nama_bayi') }}"
                                       class="w-full border-gray-300 rounded"
                                       placeholder="Boleh kosong / nama sementara"
                                       {{ $canCreate ? '' : 'disabled' }}>
                                @error('nama_bayi')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block mb-1 text-sm font-medium">Jenis Kelamin Bayi <span class="text-red-600">*</span></label>
                                <select name="jenis_kelamin_bayi" class="w-full border-gray-300 rounded" {{ $canCreate ? '' : 'disabled' }}>
                                    <option value="">-- pilih --</option>
                                    <option value="L" {{ old('jenis_kelamin_bayi') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('jenis_kelamin_bayi') === 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                @error('jenis_kelamin_bayi')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block mb-1 text-sm font-medium">Tempat Lahir Bayi (Opsional)</label>
                                <input type="text" name="tempat_lahir_bayi" value="{{ old('tempat_lahir_bayi') }}"
                                       class="w-full border-gray-300 rounded"
                                       placeholder="RS / Rumah / Klinik / dll"
                                       {{ $canCreate ? '' : 'disabled' }}>
                                @error('tempat_lahir_bayi')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block mb-1 text-sm font-medium">Tanggal Lahir Bayi <span class="text-red-600">*</span></label>
                                <input type="date" name="tanggal_lahir_bayi"
                                       value="{{ old('tanggal_lahir_bayi', now()->toDateString()) }}"
                                       class="w-full border-gray-300 rounded"
                                       {{ $canCreate ? '' : 'disabled' }}>
                                @error('tanggal_lahir_bayi')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block mb-1 text-sm font-medium">Jam Lahir Bayi (Opsional)</label>
                                <input type="time" name="jam_lahir_bayi"
                                       value="{{ old('jam_lahir_bayi') }}"
                                       class="w-full border-gray-300 rounded"
                                       {{ $canCreate ? '' : 'disabled' }}>
                                @error('jam_lahir_bayi')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block mb-1 text-sm font-medium">Anak Ke (Opsional)</label>
                                <input type="number" min="1" name="anak_ke"
                                       value="{{ old('anak_ke') }}"
                                       class="w-full border-gray-300 rounded"
                                       placeholder="contoh: 1"
                                       {{ $canCreate ? '' : 'disabled' }}>
                                @error('anak_ke')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block mb-1 text-sm font-medium">Berat Lahir (kg) (Opsional)</label>
                                <input type="number" step="0.01" min="0" name="berat_lahir"
                                       value="{{ old('berat_lahir') }}"
                                       class="w-full border-gray-300 rounded"
                                       placeholder="contoh: 3.25"
                                       {{ $canCreate ? '' : 'disabled' }}>
                                @error('berat_lahir')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block mb-1 text-sm font-medium">Panjang Lahir (cm) (Opsional)</label>
                                <input type="number" step="0.01" min="0" name="panjang_lahir"
                                       value="{{ old('panjang_lahir') }}"
                                       class="w-full border-gray-300 rounded"
                                       placeholder="contoh: 49.50"
                                       {{ $canCreate ? '' : 'disabled' }}>
                                @error('panjang_lahir')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <p class="mt-2 text-xs text-gray-500">
                            Catatan: Bayi belum punya NIK. Setelah verifikasi admin, status peristiwa bisa menjadi <span class="font-semibold">Menunggu NIK</span>,
                            lalu bayi dapat dikonversi menjadi data penduduk (Citizen).
                        </p>
                    </div>

                    {{-- =======================================================
                         D. DATA PERISTIWA
                       ======================================================= --}}
                    <div class="pt-2">
                        <h3 class="mb-2 text-lg font-semibold text-gray-700">
                            Data Peristiwa
                        </h3>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
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
                                <label class="block mb-1 text-sm font-medium">Pelapor (Opsional)</label>
                                <input type="text" name="pelapor"
                                       value="{{ old('pelapor') }}"
                                       class="w-full border-gray-300 rounded"
                                       placeholder="Nama / NIK pelapor"
                                       {{ $canCreate ? '' : 'disabled' }}>
                                @error('pelapor')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block mb-1 text-sm font-medium">Hubungan Pelapor (Opsional)</label>
                                <select name="hubungan_pelapor" class="w-full border-gray-300 rounded" {{ $canCreate ? '' : 'disabled' }}>
                                    <option value="">-- pilih --</option>
                                    <option value="ayah" {{ old('hubungan_pelapor') === 'ayah' ? 'selected' : '' }}>Ayah</option>
                                    <option value="ibu" {{ old('hubungan_pelapor') === 'ibu' ? 'selected' : '' }}>Ibu</option>
                                    <option value="bidan" {{ old('hubungan_pelapor') === 'bidan' ? 'selected' : '' }}>Bidan</option>
                                    <option value="lainnya" {{ old('hubungan_pelapor') === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                                @error('hubungan_pelapor')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block mb-1 text-sm font-medium">Catatan Tambahan (Opsional)</label>
                                <textarea name="catatan_peristiwa" rows="3"
                                          class="w-full border-gray-300 rounded"
                                          {{ $canCreate ? '' : 'disabled' }}>{{ old('catatan_peristiwa') }}</textarea>
                                @error('catatan_peristiwa')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
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
