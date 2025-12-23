<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            Form Peristiwa Meninggal
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="p-6 bg-white shadow-sm sm:rounded-lg">

                {{-- ✅ Notifikasi error validasi --}}
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

                {{-- form meninggal --}}
                <form action="{{ route('events.meninggal.store') }}" method="POST" enctype="multipart/form-data"
                    class="space-y-4">
                    @csrf

                    <h3 class="mb-2 text-lg font-semibold text-gray-700">
                        Identitas Penduduk
                    </h3>

                    {{-- no KK --}}
                    <div>
                        <label class="block mb-1 text-sm font-medium">No KK</label>
                        <input id="no_kk" type="text" name="no_kk" value="{{ old('no_kk') }}"
                            class="w-full border-gray-300 rounded bg-gray-50" readonly>
                        @error('no_kk')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- NIK --}}
                    <div>
                        <label class="block mb-1 text-sm font-medium">NIK</label>
                        <input id="nik" type="text" name="nik" value="{{ old('nik') }}"
                            class="w-full border-gray-300 rounded" autocomplete="off">

                        <p id="nikHelp" class="mt-1 text-xs text-gray-500">
                            Ketik NIK, sistem akan mengisi No KK & Nama otomatis.
                        </p>

                        <p id="nikError" class="hidden mt-1 text-xs text-red-600"></p>

                        @error('nik')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- nama --}}
                    <div>
                        <label class="block mb-1 text-sm font-medium">Nama</label>
                        <input id="nama" type="text" name="nama" value="{{ old('nama') }}"
                            class="w-full border-gray-300 rounded bg-gray-50" readonly>
                        @error('nama')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- tanggal peristiwa --}}
                    <div>
                        <label class="block mb-1 text-sm font-medium">Tanggal Peristiwa</label>
                        <input type="date" name="tanggal_peristiwa" value="{{ old('tanggal_peristiwa') }}"
                            class="w-full border-gray-300 rounded">

                        {{-- ✅ ini yang bikin operator paham kalau tanggal masa depan ditolak --}}
                        @error('tanggal_peristiwa')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- tanggal lapor --}}
                    <div>
                        <label class="block mb-1 text-sm font-medium">Tanggal Lapor</label>
                        <input type="date" name="tanggal_lapor" value="{{ old('tanggal_lapor') }}"
                            class="w-full border-gray-300 rounded">
                        @error('tanggal_lapor')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- tempat meninggal --}}
                    <div>
                        <label class="block mb-1 text-sm font-medium">Tempat Meninggal</label>
                        <input type="text" name="tempat_meninggal" value="{{ old('tempat_meninggal') }}"
                            class="w-full border-gray-300 rounded">
                        @error('tempat_meninggal')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- jam kematian --}}
                    <div>
                        <label class="block mb-1 text-sm font-medium">Jam Kematian</label>
                        <input type="time" name="jam_kematian" value="{{ old('jam_kematian') }}"
                            class="w-full border-gray-300 rounded">
                        @error('jam_kematian')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- penyebab kematian --}}
                    <div>
                        <label class="block mb-1 text-sm font-medium">Penyebab Kematian</label>
                        <select name="penyebab_kematian" class="w-full border-gray-300 rounded">
                            <option value="">-- pilih --</option>
                            <option value="sakit_biasa_tua" {{ old('penyebab_kematian') === 'sakit_biasa_tua' ? 'selected' : '' }}>Sakit Biasa / Tua</option>
                            <option value="wabah_penyakit" {{ old('penyebab_kematian') === 'wabah_penyakit' ? 'selected' : '' }}>Wabah Penyakit</option>
                            <option value="kecelakaan" {{ old('penyebab_kematian') === 'kecelakaan' ? 'selected' : '' }}>
                                Kecelakaan</option>
                            <option value="kriminalitas" {{ old('penyebab_kematian') === 'kriminalitas' ? 'selected' : '' }}>Kriminalitas</option>
                            <option value="bunuh_diri" {{ old('penyebab_kematian') === 'bunuh_diri' ? 'selected' : '' }}>
                                Bunuh Diri</option>
                            <option value="lainnya" {{ old('penyebab_kematian') === 'lainnya' ? 'selected' : '' }}>Lainnya
                            </option>
                        </select>
                        @error('penyebab_kematian')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- yang menyatakan kematian --}}
                    <div>
                        <label class="block mb-1 text-sm font-medium">Yang Menyatakan Kematian</label>
                        <select name="yang_menyatakan_kematian" class="w-full border-gray-300 rounded">
                            <option value="">-- pilih --</option>
                            <option value="dokter" {{ old('yang_menyatakan_kematian') === 'dokter' ? 'selected' : '' }}>
                                Dokter</option>
                            <option value="tenaga_kesehatan" {{ old('yang_menyatakan_kematian') === 'tenaga_kesehatan' ? 'selected' : '' }}>Tenaga Kesehatan</option>
                            <option value="kepolisian" {{ old('yang_menyatakan_kematian') === 'kepolisian' ? 'selected' : '' }}>Kepolisian</option>
                            <option value="lainnya" {{ old('yang_menyatakan_kematian') === 'lainnya' ? 'selected' : '' }}>
                                Lainnya</option>
                        </select>
                        @error('yang_menyatakan_kematian')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- nomor akta kematian --}}
                    <div>
                        <label class="block mb-1 text-sm font-medium">Nomor Akta Kematian</label>
                        <input type="text" name="nomor_akta_kematian" value="{{ old('nomor_akta_kematian') }}"
                            class="w-full border-gray-300 rounded">
                        @error('nomor_akta_kematian')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- file akta kematian --}}
                    <div>
                        <label class="block mb-1 text-sm font-medium">Upload Akta Kematian (Opsional)</label>
                        <input type="file" name="file_akta_kematian_path" class="w-full border-gray-300 rounded">
                        @error('file_akta_kematian_path')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- catatan peristiwa --}}
                    <div>
                        <label class="block mb-1 text-sm font-medium">Catatan Peristiwa</label>
                        <textarea name="catatan_peristiwa" class="w-full border-gray-300 rounded"
                            rows="3">{{ old('catatan_peristiwa') }}</textarea>
                        @error('catatan_peristiwa')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- tombol --}}
                    <div class="flex justify-end pt-4">
                        <button type="submit" class="px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-700">
                            Simpan Peristiwa
                        </button>
                    </div>

                </form>

            </div>

        </div>
    </div>
    <script>
        (function () {
            const nikInput = document.getElementById('nik');
            const noKkInput = document.getElementById('no_kk');
            const namaInput = document.getElementById('nama');
            const nikError = document.getElementById('nikError');

            let t = null;

            function setError(msg) {
                if (!nikError) return;
                if (!msg) {
                    nikError.classList.add('hidden');
                    nikError.textContent = '';
                    return;
                }
                nikError.textContent = msg;
                nikError.classList.remove('hidden');
            }

            function clearAutoFill() {
                noKkInput.value = '';
                namaInput.value = '';
            }

            async function fetchCitizen(nik) {
                const url = `{{ url('/api/citizens/by-nik') }}/${encodeURIComponent(nik)}`;
                const res = await fetch(url, { headers: { 'Accept': 'application/json' } });

                if (!res.ok) {
                    clearAutoFill();
                    setError('NIK tidak ditemukan. Pastikan penduduk sudah terdaftar.');
                    return;
                }

                const json = await res.json();
                if (!json.found || !json.data) {
                    clearAutoFill();
                    setError('NIK tidak ditemukan. Pastikan penduduk sudah terdaftar.');
                    return;
                }

                setError('');
                noKkInput.value = json.data.no_kk ?? '';
                namaInput.value = json.data.nama ?? '';
            }

            if (!nikInput) return;

            nikInput.addEventListener('input', function () {
                const nik = (nikInput.value || '').trim();

                setError('');

                if (t) clearTimeout(t);

                if (nik.length < 8) { // biar gak spam request saat baru ngetik sedikit
                    clearAutoFill();
                    return;
                }

                t = setTimeout(() => fetchCitizen(nik), 350);
            });
        })();
    </script>
</x-app-layout>