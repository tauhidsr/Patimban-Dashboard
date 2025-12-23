<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            Form Peristiwa Meninggal
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="p-6 bg-white shadow-sm sm:rounded-lg">

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

                {{-- WARNING besar (frontend) --}}
                <div id="statusBlock" class="hidden p-4 mb-4 border rounded-lg">
                    <div class="font-semibold" id="statusTitle">-</div>
                    <div class="mt-1 text-sm" id="statusDesc">-</div>
                </div>

                <form id="formMeninggal" action="{{ route('events.meninggal.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <h3 class="mb-2 text-lg font-semibold text-gray-700">
                        Identitas Penduduk
                    </h3>

                    {{-- No KK (readonly biar ikut terkirim) --}}
                    <div>
                        <label class="block mb-1 text-sm font-medium">No KK</label>
                        <input id="no_kk" type="text" name="no_kk" value="{{ old('no_kk') }}"
                            class="w-full border-gray-300 rounded bg-gray-50" readonly>
                        @error('no_kk')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- NIK (Tom Select) --}}
                    <div>
                        <label class="block mb-1 text-sm font-medium">NIK</label>
                        <select id="nik" name="nik" class="w-full border-gray-300 rounded">
                            @if(old('nik'))
                                <option value="{{ old('nik') }}" selected>{{ old('nik') }}</option>
                            @endif
                        </select>

                        <p class="mt-1 text-xs text-gray-500">
                            Ketik minimal 3 karakter (NIK / Nama / No KK), lalu pilih penduduk.
                        </p>

                        <p id="nikError" class="hidden mt-1 text-xs text-red-600"></p>

                        {{-- Badge status kecil --}}
                        <div id="nikBadge" class="hidden mt-2 text-xs">
                            <span id="nikBadgeInner" class="inline-flex items-center px-2 py-1 border rounded">
                                -
                            </span>
                        </div>

                        @error('nik')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nama (readonly biar ikut terkirim) --}}
                    <div>
                        <label class="block mb-1 text-sm font-medium">Nama</label>
                        <input id="nama" type="text" name="nama" value="{{ old('nama') }}"
                            class="w-full border-gray-300 rounded bg-gray-50" readonly>
                        @error('nama')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Dusun / RW / RT (tampil saja) --}}
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div>
                            <label class="block mb-1 text-sm font-medium">Dusun</label>
                            <input id="dusun" type="text" value="{{ old('dusun') }}"
                                class="w-full border-gray-300 rounded bg-gray-50" readonly>
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium">RW</label>
                            <input id="rw" type="text" value="{{ old('rw') }}"
                                class="w-full border-gray-300 rounded bg-gray-50" readonly>
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium">RT</label>
                            <input id="rt" type="text" value="{{ old('rt') }}"
                                class="w-full border-gray-300 rounded bg-gray-50" readonly>
                        </div>
                    </div>

                    <h3 class="pt-4 mb-2 text-lg font-semibold text-gray-700">
                        Detail Peristiwa Meninggal
                    </h3>

                    <div>
                        <label class="block mb-1 text-sm font-medium">Tanggal Peristiwa</label>
                        <input type="date" name="tanggal_peristiwa" value="{{ old('tanggal_peristiwa') }}"
                            class="w-full border-gray-300 rounded">
                        @error('tanggal_peristiwa')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block mb-1 text-sm font-medium">Tanggal Lapor</label>
                        <input type="date" name="tanggal_lapor" value="{{ old('tanggal_lapor') }}"
                            class="w-full border-gray-300 rounded">
                        @error('tanggal_lapor')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block mb-1 text-sm font-medium">Tempat Meninggal</label>
                        <input type="text" name="tempat_meninggal" value="{{ old('tempat_meninggal') }}"
                            class="w-full border-gray-300 rounded">
                        @error('tempat_meninggal')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block mb-1 text-sm font-medium">Jam Kematian</label>
                        <input type="time" name="jam_kematian" value="{{ old('jam_kematian') }}"
                            class="w-full border-gray-300 rounded">
                        @error('jam_kematian')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block mb-1 text-sm font-medium">Penyebab Kematian</label>
                        <select name="penyebab_kematian" class="w-full border-gray-300 rounded">
                            <option value="">-- pilih --</option>
                            <option value="sakit_biasa_tua" {{ old('penyebab_kematian') === 'sakit_biasa_tua' ? 'selected' : '' }}>Sakit Biasa / Tua</option>
                            <option value="wabah_penyakit" {{ old('penyebab_kematian') === 'wabah_penyakit' ? 'selected' : '' }}>Wabah Penyakit</option>
                            <option value="kecelakaan" {{ old('penyebab_kematian') === 'kecelakaan' ? 'selected' : '' }}>Kecelakaan</option>
                            <option value="kriminalitas" {{ old('penyebab_kematian') === 'kriminalitas' ? 'selected' : '' }}>Kriminalitas</option>
                            <option value="bunuh_diri" {{ old('penyebab_kematian') === 'bunuh_diri' ? 'selected' : '' }}>Bunuh Diri</option>
                            <option value="lainnya" {{ old('penyebab_kematian') === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        @error('penyebab_kematian')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block mb-1 text-sm font-medium">Yang Menyatakan Kematian</label>
                        <select name="yang_menyatakan_kematian" class="w-full border-gray-300 rounded">
                            <option value="">-- pilih --</option>
                            <option value="dokter" {{ old('yang_menyatakan_kematian') === 'dokter' ? 'selected' : '' }}>Dokter</option>
                            <option value="tenaga_kesehatan" {{ old('yang_menyatakan_kematian') === 'tenaga_kesehatan' ? 'selected' : '' }}>Tenaga Kesehatan</option>
                            <option value="kepolisian" {{ old('yang_menyatakan_kematian') === 'kepolisian' ? 'selected' : '' }}>Kepolisian</option>
                            <option value="lainnya" {{ old('yang_menyatakan_kematian') === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        @error('yang_menyatakan_kematian')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block mb-1 text-sm font-medium">Nomor Akta Kematian</label>
                        <input type="text" name="nomor_akta_kematian" value="{{ old('nomor_akta_kematian') }}"
                            class="w-full border-gray-300 rounded">
                        @error('nomor_akta_kematian')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Upload Akta (INI yang tadi hilang) --}}
                    <div>
                        <label class="block mb-1 text-sm font-medium">Upload Akta Kematian (Opsional)</label>
                        <input type="file" name="file_akta_kematian_path" class="w-full border-gray-300 rounded">
                        @error('file_akta_kematian_path')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block mb-1 text-sm font-medium">Catatan Peristiwa</label>
                        <textarea name="catatan_peristiwa" rows="3" class="w-full border-gray-300 rounded">{{ old('catatan_peristiwa') }}</textarea>
                        @error('catatan_peristiwa')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end pt-4">
                        <button id="btnSubmit" type="submit"
                            class="px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">
                            Simpan Peristiwa
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Tom Select CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    <script>
        (function () {
            const nikSelect = document.getElementById('nik');
            const noKk = document.getElementById('no_kk');
            const nama = document.getElementById('nama');
            const dusun = document.getElementById('dusun');
            const rw = document.getElementById('rw');
            const rt = document.getElementById('rt');

            const btnSubmit = document.getElementById('btnSubmit');
            const nikError = document.getElementById('nikError');
            const nikBadge = document.getElementById('nikBadge');
            const nikBadgeInner = document.getElementById('nikBadgeInner');

            const statusBlock = document.getElementById('statusBlock');
            const statusTitle = document.getElementById('statusTitle');
            const statusDesc = document.getElementById('statusDesc');

            function setSubmit(enabled) {
                if (!btnSubmit) return;
                btnSubmit.disabled = !enabled;
            }

            function showStatusBlock(type, title, desc) {
                statusBlock.classList.remove('hidden');

                statusBlock.className = 'p-4 mb-4 border rounded-lg';
                if (type === 'ok') {
                    statusBlock.classList.add('bg-green-50', 'border-green-200', 'text-green-900');
                } else if (type === 'warn') {
                    statusBlock.classList.add('bg-yellow-50', 'border-yellow-200', 'text-yellow-900');
                } else {
                    statusBlock.classList.add('bg-red-50', 'border-red-200', 'text-red-900');
                }

                statusTitle.textContent = title;
                statusDesc.textContent = desc;
            }

            function hideStatusBlock() {
                statusBlock.classList.add('hidden');
                statusTitle.textContent = '-';
                statusDesc.textContent = '-';
            }

            function setBadge(type, text) {
                nikBadge.classList.remove('hidden');
                nikBadgeInner.className = 'inline-flex items-center px-2 py-1 rounded border text-xs';

                if (type === 'ok') {
                    nikBadgeInner.classList.add('bg-green-100', 'text-green-800', 'border-green-300');
                } else {
                    nikBadgeInner.classList.add('bg-red-100', 'text-red-800', 'border-red-300');
                }

                nikBadgeInner.textContent = text;
            }

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

            function clearAll() {
                noKk.value = '';
                nama.value = '';
                dusun.value = '';
                rw.value = '';
                rt.value = '';
                setError('');
                nikBadge.classList.add('hidden');
                hideStatusBlock();
                setSubmit(false);
            }

            async function fetchCitizen(nik) {
                const res = await fetch(`{{ url('/api/citizens/by-nik') }}/${encodeURIComponent(nik)}`, {
                    headers: { 'Accept': 'application/json' }
                });

                if (!res.ok) {
                    clearAll();
                    setError('NIK tidak ditemukan. Pastikan penduduk sudah terdaftar.');
                    setBadge('error', 'Penduduk tidak ditemukan');
                    showStatusBlock('error', 'Tidak bisa lanjut', 'Penduduk tidak ditemukan di data penduduk.');
                    return;
                }

                const json = await res.json();
                const d = json.data || {};

                noKk.value = d.no_kk ?? '';
                nama.value = d.nama ?? '';
                dusun.value = d.dusun ?? '';
                rw.value = d.rw ?? '';
                rt.value = d.rt ?? '';

                // status_dasar (harapnya ada di response API; kalau belum ada, aku jelasin di step backend berikut)
                const statusDasar = (d.status_dasar ?? '').toString().toLowerCase();

                setError('');
                setBadge('ok', `Penduduk ditemukan — status ${statusDasar ? statusDasar.toUpperCase() : 'TERDAFTAR'}`);

                if (statusDasar && statusDasar !== 'aktif') {
                    setSubmit(false);
                    showStatusBlock(
                        'warn',
                        '⚠ Tidak bisa input peristiwa untuk penduduk ini',
                        `Status penduduk saat ini: ${statusDasar.toUpperCase()}. Peristiwa meninggal hanya boleh untuk status AKTIF.`
                    );
                } else {
                    setSubmit(true);
                    showStatusBlock(
                        'ok',
                        '✅ Penduduk AKTIF',
                        'Form bisa disimpan. Pastikan tanggal peristiwa dan tanggal lapor sesuai.'
                    );
                }
            }

            setSubmit(false);

            new TomSelect(nikSelect, {
                valueField: 'value',
                labelField: 'text',
                searchField: 'text',
                preload: false,
                create: false,
                placeholder: 'Cari NIK / Nama / No KK...',
                load: async function (query, cb) {
                    setError('');
                    if (!query || query.length < 3) return cb();

                    try {
                        const res = await fetch(`{{ url('/api/citizens/search') }}?q=${encodeURIComponent(query)}`, {
                            headers: { 'Accept': 'application/json' }
                        });
                        const json = await res.json();
                        cb(json.results || []);
                    } catch (e) {
                        cb();
                    }
                },
                onChange(value) {
                    if (!value) {
                        clearAll();
                        return;
                    }
                    fetchCitizen(value);
                }
            });

            // autofill saat old('nik') ada
            const oldNik = @json(old('nik'));
            if (oldNik) {
                fetchCitizen(oldNik);
            }
        })();
    </script>
</x-app-layout>
