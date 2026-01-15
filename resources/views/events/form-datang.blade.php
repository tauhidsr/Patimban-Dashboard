{{-- resources/views/events/form-datang.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Form Peristiwa Datang
        </h2>
    </x-slot>

    @php
        $user = auth()->user();
        $role = $user->role ?? 'viewer';

        // policy-based permission (sesuai pola form hilang/meninggal)
        $canCreate = $user ? $user->can('create', \App\Models\PopulationEvent::class) : false;
    @endphp

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="p-6 bg-white shadow-sm sm:rounded-lg">

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

                <form id="formDatang"
                      action="{{ route('events.datang.store') }}"
                      method="POST"
                      class="space-y-6">
                    @csrf

                    {{-- =========================================================
                         A) IDENTITAS (NIK -> cek DB, kalau tidak ada -> input citizen baru)
                    ========================================================== --}}

                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">A. Identitas Penduduk (Wajib)</h3>
                            <p class="mt-1 text-xs text-gray-500">
                                Masukkan NIK. Jika NIK belum ada di database, kamu bisa input penduduk baru (minimal).
                            </p>
                        </div>
                        <div class="text-xs font-semibold text-red-600">Wajib</div>
                    </div>

                    {{-- status alert --}}
                    <div id="lookupAlert" class="hidden p-3 text-sm border rounded-lg"></div>

                    {{-- mode: existing / new (dipakai backend nanti) --}}
                    <input type="hidden" name="citizen_mode" id="citizen_mode" value="{{ old('citizen_mode', 'existing') }}">

                    <div class="grid gap-4 sm:grid-cols-3">
                        <div class="sm:col-span-2">
                            <label class="block mb-1 text-sm font-medium">NIK <span class="text-red-600">*</span></label>
                            <input type="text"
                                   name="nik"
                                   id="nik"
                                   value="{{ old('nik') }}"
                                   maxlength="20"
                                   placeholder="16 digit NIK"
                                   class="w-full border-gray-300 rounded"
                                   {{ $canCreate ? '' : 'disabled' }}>
                            <p class="mt-1 text-xs text-gray-500">
                                Tips: isi 16 digit. Klik <b>Cek NIK</b> untuk ambil data jika sudah terdaftar.
                            </p>
                        </div>
                        <div class="flex items-end">
                            <button type="button"
                                    id="btnCekNik"
                                    class="w-full px-4 py-2 text-sm font-semibold text-white bg-indigo-600 rounded hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed"
                                    {{ $canCreate ? '' : 'disabled' }}>
                                Cek NIK
                            </button>
                        </div>
                    </div>

                    {{-- auto dari citizen (readonly) --}}
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block mb-1 text-sm font-medium">Nama (Auto)</label>
                            <input type="text" name="nama_auto" id="nama_auto"
                                   value="{{ old('nama_auto') }}"
                                   class="w-full border-gray-300 rounded bg-gray-50" readonly>
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium">No KK (Auto)</label>
                            <input type="text" name="no_kk_auto" id="no_kk_auto"
                                   value="{{ old('no_kk_auto') }}"
                                   class="w-full border-gray-300 rounded bg-gray-50" readonly>
                        </div>

                        <div>
                            <label class="block mb-1 text-sm font-medium">Jenis Kelamin (Auto)</label>
                            <input type="text" name="jenis_kelamin_auto" id="jenis_kelamin_auto"
                                   value="{{ old('jenis_kelamin_auto') }}"
                                   class="w-full border-gray-300 rounded bg-gray-50" readonly>
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium">Tanggal Lahir (Auto)</label>
                            <input type="text" name="tanggal_lahir_auto" id="tanggal_lahir_auto"
                                   value="{{ old('tanggal_lahir_auto') }}"
                                   class="w-full border-gray-300 rounded bg-gray-50" readonly>
                        </div>

                        <div>
                            <label class="block mb-1 text-sm font-medium">Status Perkawinan (Auto)</label>
                            <input type="text" name="status_perkawinan_auto" id="status_perkawinan_auto"
                                   value="{{ old('status_perkawinan_auto') }}"
                                   class="w-full border-gray-300 rounded bg-gray-50" readonly>
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium">Agama (Auto)</label>
                            <input type="text" name="agama_auto" id="agama_auto"
                                   value="{{ old('agama_auto') }}"
                                   class="w-full border-gray-300 rounded bg-gray-50" readonly>
                        </div>

                        <div>
                            <label class="block mb-1 text-sm font-medium">Pendidikan (Auto)</label>
                            <input type="text" name="pendidikan_auto" id="pendidikan_auto"
                                   value="{{ old('pendidikan_auto') }}"
                                   class="w-full border-gray-300 rounded bg-gray-50" readonly>
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium">Pekerjaan (Auto)</label>
                            <input type="text" name="pekerjaan_auto" id="pekerjaan_auto"
                                   value="{{ old('pekerjaan_auto') }}"
                                   class="w-full border-gray-300 rounded bg-gray-50" readonly>
                        </div>
                    </div>

                    {{-- ✅ Panel input citizen baru (muncul kalau NIK tidak ditemukan) --}}
                    <div id="panelCitizenBaru" class="hidden p-4 border border-yellow-200 rounded-lg bg-yellow-50">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h4 class="font-semibold text-yellow-900">NIK belum terdaftar — Input Penduduk Baru (Minimal)</h4>
                                <p class="mt-1 text-xs text-yellow-800">
                                    Isi data minimal untuk membuat penduduk baru di master <b>citizens</b>,
                                    lalu peristiwa datang akan disimpan dengan citizen tersebut.
                                </p>
                            </div>
                            <span class="px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 border border-yellow-200 rounded-full">
                                Mode: Citizen Baru
                            </span>
                        </div>

                        <div class="grid gap-4 mt-4 sm:grid-cols-2">
                            <div>
                                <label class="block mb-1 text-sm font-medium">Nama <span class="text-red-600">*</span></label>
                                <input type="text" name="nama" id="nama"
                                       value="{{ old('nama') }}"
                                       class="w-full border-gray-300 rounded"
                                       {{ $canCreate ? '' : 'disabled' }}>
                            </div>

                            <div>
                                <label class="block mb-1 text-sm font-medium">No KK (opsional)</label>
                                <input type="text" name="no_kk" id="no_kk"
                                       value="{{ old('no_kk') }}"
                                       class="w-full border-gray-300 rounded"
                                       {{ $canCreate ? '' : 'disabled' }}>
                            </div>

                            <div>
                                <label class="block mb-1 text-sm font-medium">Jenis Kelamin <span class="text-red-600">*</span></label>
                                <select name="jenis_kelamin" id="jenis_kelamin"
                                        class="w-full border-gray-300 rounded"
                                        {{ $canCreate ? '' : 'disabled' }}>
                                    <option value="">-- pilih --</option>
                                    <option value="L" {{ old('jenis_kelamin') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('jenis_kelamin') === 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>

                            <div>
                                <label class="block mb-1 text-sm font-medium">Tanggal Lahir <span class="text-red-600">*</span></label>
                                <input type="date" name="tanggal_lahir" id="tanggal_lahir"
                                       value="{{ old('tanggal_lahir') }}"
                                       class="w-full border-gray-300 rounded"
                                       {{ $canCreate ? '' : 'disabled' }}>
                            </div>

                            <div>
                                <label class="block mb-1 text-sm font-medium">Status Perkawinan <span class="text-red-600">*</span></label>
                                <input type="text" name="status_perkawinan" id="status_perkawinan"
                                       value="{{ old('status_perkawinan') }}"
                                       class="w-full border-gray-300 rounded"
                                       placeholder="misal: kawin / belum kawin"
                                       {{ $canCreate ? '' : 'disabled' }}>
                            </div>

                            <div>
                                <label class="block mb-1 text-sm font-medium">Agama <span class="text-red-600">*</span></label>
                                <input type="text" name="agama" id="agama"
                                       value="{{ old('agama') }}"
                                       class="w-full border-gray-300 rounded"
                                       placeholder="misal: Islam"
                                       {{ $canCreate ? '' : 'disabled' }}>
                            </div>

                            <div>
                                <label class="block mb-1 text-sm font-medium">Pendidikan (opsional)</label>
                                <input type="text" name="pendidikan_dalam_kk" id="pendidikan_dalam_kk"
                                       value="{{ old('pendidikan_dalam_kk') }}"
                                       class="w-full border-gray-300 rounded"
                                       {{ $canCreate ? '' : 'disabled' }}>
                            </div>

                            <div>
                                <label class="block mb-1 text-sm font-medium">Pekerjaan (opsional)</label>
                                <input type="text" name="pekerjaan" id="pekerjaan"
                                       value="{{ old('pekerjaan') }}"
                                       class="w-full border-gray-300 rounded"
                                       {{ $canCreate ? '' : 'disabled' }}>
                            </div>
                        </div>
                    </div>

                    {{-- =========================================================
                         B) ASAL PENDUDUK
                    ========================================================== --}}
                    <div class="pt-4 border-t">
                        <h3 class="text-lg font-semibold text-gray-800">B. Asal Penduduk</h3>
                        <p class="mt-1 text-xs text-gray-500">Data asal bisa manual (nanti bisa kamu upgrade ke dropdown).</p>

                        <div class="grid gap-4 mt-4 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label class="block mb-1 text-sm font-medium">Alamat Asal <span class="text-red-600">*</span></label>
                                <textarea name="alamat_asal" rows="2"
                                          class="w-full border-gray-300 rounded"
                                          {{ $canCreate ? '' : 'disabled' }}>{{ old('alamat_asal') }}</textarea>
                            </div>

                            <div>
                                <label class="block mb-1 text-sm font-medium">Desa Asal <span class="text-red-600">*</span></label>
                                <input type="text" name="desa_asal" value="{{ old('desa_asal') }}"
                                       class="w-full border-gray-300 rounded" {{ $canCreate ? '' : 'disabled' }}>
                            </div>

                            <div>
                                <label class="block mb-1 text-sm font-medium">Kecamatan Asal <span class="text-red-600">*</span></label>
                                <input type="text" name="kecamatan_asal" value="{{ old('kecamatan_asal') }}"
                                       class="w-full border-gray-300 rounded" {{ $canCreate ? '' : 'disabled' }}>
                            </div>

                            <div>
                                <label class="block mb-1 text-sm font-medium">Kabupaten Asal <span class="text-red-600">*</span></label>
                                <input type="text" name="kabupaten_asal" value="{{ old('kabupaten_asal') }}"
                                       class="w-full border-gray-300 rounded" {{ $canCreate ? '' : 'disabled' }}>
                            </div>

                            <div>
                                <label class="block mb-1 text-sm font-medium">Provinsi Asal <span class="text-red-600">*</span></label>
                                <input type="text" name="provinsi_asal" value="{{ old('provinsi_asal') }}"
                                       class="w-full border-gray-300 rounded" {{ $canCreate ? '' : 'disabled' }}>
                            </div>
                        </div>
                    </div>

                    {{-- =========================================================
                         C) TUJUAN / DOMISILI BARU (PATIMBAN)
                    ========================================================== --}}
                    <div class="pt-4 border-t">
                        <h3 class="text-lg font-semibold text-gray-800">C. Tujuan / Domisili Baru (Patimban)</h3>
                        <p class="mt-1 text-xs text-gray-500">
                            Field wilayah ini menentukan scope operator (dusun/RW/RT tujuan).
                        </p>

                        <div class="grid gap-4 mt-4 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label class="block mb-1 text-sm font-medium">Alamat Sekarang (Tujuan) <span class="text-red-600">*</span></label>
                                <textarea name="alamat_sekarang_tujuan" rows="2"
                                          class="w-full border-gray-300 rounded"
                                          {{ $canCreate ? '' : 'disabled' }}>{{ old('alamat_sekarang_tujuan') }}</textarea>
                            </div>

                            <div>
                                <label class="block mb-1 text-sm font-medium">Dusun <span class="text-red-600">*</span></label>
                                <input type="text" name="dusun_tujuan" value="{{ old('dusun_tujuan') }}"
                                       class="w-full border-gray-300 rounded" {{ $canCreate ? '' : 'disabled' }}>
                            </div>

                            <div>
                                <label class="block mb-1 text-sm font-medium">RW <span class="text-red-600">*</span></label>
                                <input type="text" name="rw_tujuan" value="{{ old('rw_tujuan') }}"
                                       class="w-full border-gray-300 rounded" {{ $canCreate ? '' : 'disabled' }}>
                            </div>

                            <div>
                                <label class="block mb-1 text-sm font-medium">RT <span class="text-red-600">*</span></label>
                                <input type="text" name="rt_tujuan" value="{{ old('rt_tujuan') }}"
                                       class="w-full border-gray-300 rounded" {{ $canCreate ? '' : 'disabled' }}>
                            </div>
                        </div>
                    </div>

                    {{-- =========================================================
                         D) DATA PERISTIWA DATANG
                    ========================================================== --}}
                    <div class="pt-4 border-t">
                        <h3 class="text-lg font-semibold text-gray-800">D. Data Peristiwa Datang</h3>

                        <div class="grid gap-4 mt-4 sm:grid-cols-2">
                            <div>
                                <label class="block mb-1 text-sm font-medium">Tanggal Datang <span class="text-red-600">*</span></label>
                                <input type="date" name="tanggal_datang"
                                       value="{{ old('tanggal_datang', now()->toDateString()) }}"
                                       class="w-full border-gray-300 rounded"
                                       {{ $canCreate ? '' : 'disabled' }}>
                            </div>

                            <div>
                                <label class="block mb-1 text-sm font-medium">Tanggal Lapor</label>
                                <input type="date" name="tanggal_lapor"
                                       value="{{ old('tanggal_lapor') }}"
                                       class="w-full border-gray-300 rounded"
                                       {{ $canCreate ? '' : 'disabled' }}>
                                <p class="mt-1 text-xs text-gray-500">Jika kosong, sistem otomatis isi hari ini saat simpan.</p>
                            </div>

                            <div>
                                <label class="block mb-1 text-sm font-medium">Alasan Datang</label>
                                <select name="alasan_datang" class="w-full border-gray-300 rounded" {{ $canCreate ? '' : 'disabled' }}>
                                    <option value="">-- pilih --</option>
                                    <option value="kerja" {{ old('alasan_datang') === 'kerja' ? 'selected' : '' }}>Kerja</option>
                                    <option value="nikah" {{ old('alasan_datang') === 'nikah' ? 'selected' : '' }}>Nikah</option>
                                    <option value="keluarga" {{ old('alasan_datang') === 'keluarga' ? 'selected' : '' }}>Keluarga</option>
                                    <option value="lainnya" {{ old('alasan_datang') === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                            </div>

                            <div>
                                <label class="block mb-1 text-sm font-medium">Status Datang</label>
                                <select name="status_datang" class="w-full border-gray-300 rounded" {{ $canCreate ? '' : 'disabled' }}>
                                    <option value="">-- pilih --</option>
                                    <option value="tetap" {{ old('status_datang') === 'tetap' ? 'selected' : '' }}>Tetap</option>
                                    <option value="sementara" {{ old('status_datang') === 'sementara' ? 'selected' : '' }}>Sementara</option>
                                </select>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block mb-1 text-sm font-medium">Rencana Tinggal (opsional)</label>
                                <input type="text" name="rencana_tinggal"
                                       value="{{ old('rencana_tinggal') }}"
                                       class="w-full border-gray-300 rounded"
                                       placeholder="misal: 6 bulan / 1 tahun / menetap"
                                       {{ $canCreate ? '' : 'disabled' }}>
                            </div>

                            <div>
                                <label class="block mb-1 text-sm font-medium">Pelapor</label>
                                <input type="text" name="pelapor" value="{{ old('pelapor') }}"
                                       class="w-full border-gray-300 rounded" {{ $canCreate ? '' : 'disabled' }}>
                            </div>

                            <div>
                                <label class="block mb-1 text-sm font-medium">Hubungan Pelapor</label>
                                <input type="text" name="hubungan_pelapor" value="{{ old('hubungan_pelapor') }}"
                                       class="w-full border-gray-300 rounded"
                                       placeholder="misal: yang bersangkutan / keluarga / RT"
                                       {{ $canCreate ? '' : 'disabled' }}>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block mb-1 text-sm font-medium">Keterangan / Catatan Peristiwa (opsional)</label>
                                <textarea name="catatan_peristiwa" rows="3"
                                          class="w-full border-gray-300 rounded"
                                          {{ $canCreate ? '' : 'disabled' }}>{{ old('catatan_peristiwa') }}</textarea>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-4">
                            <a href="{{ route('events.create') }}" class="text-sm text-gray-600 hover:underline">
                                ← Kembali
                            </a>

                            <button id="btnSubmit" type="submit"
                                    class="px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
                                    {{ $canCreate ? '' : 'disabled' }}>
                                Simpan Peristiwa Datang
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>

    {{-- =========================
         JS Lookup NIK (found / not found)
    ========================== --}}
    <script>
        (function () {
            const canCreate = @json($canCreate);

            const nik = document.getElementById('nik');
            const btn = document.getElementById('btnCekNik');
            const alertBox = document.getElementById('lookupAlert');

            const citizenMode = document.getElementById('citizen_mode');
            const panelCitizenBaru = document.getElementById('panelCitizenBaru');

            // auto fields
            const namaAuto = document.getElementById('nama_auto');
            const noKkAuto = document.getElementById('no_kk_auto');
            const jkAuto = document.getElementById('jenis_kelamin_auto');
            const tlAuto = document.getElementById('tanggal_lahir_auto');
            const spAuto = document.getElementById('status_perkawinan_auto');
            const agamaAuto = document.getElementById('agama_auto');
            const pendAuto = document.getElementById('pendidikan_auto');
            const pekAuto = document.getElementById('pekerjaan_auto');

            // citizen baru fields
            const nama = document.getElementById('nama');
            const noKk = document.getElementById('no_kk');
            const jk = document.getElementById('jenis_kelamin');
            const tl = document.getElementById('tanggal_lahir');
            const sp = document.getElementById('status_perkawinan');
            const agama = document.getElementById('agama');
            const pend = document.getElementById('pendidikan_dalam_kk');
            const pek = document.getElementById('pekerjaan');

            function showAlert(type, msg) {
                alertBox.classList.remove('hidden');
                alertBox.className = 'p-3 text-sm border rounded-lg';

                if (type === 'ok') {
                    alertBox.classList.add('bg-green-50', 'border-green-200', 'text-green-800');
                } else if (type === 'warn') {
                    alertBox.classList.add('bg-yellow-50', 'border-yellow-200', 'text-yellow-900');
                } else {
                    alertBox.classList.add('bg-red-50', 'border-red-200', 'text-red-800');
                }
                alertBox.textContent = msg;
            }

            function clearAuto() {
                namaAuto.value = '';
                noKkAuto.value = '';
                jkAuto.value = '';
                tlAuto.value = '';
                spAuto.value = '';
                agamaAuto.value = '';
                pendAuto.value = '';
                pekAuto.value = '';
            }

            function setModeExisting() {
                citizenMode.value = 'existing';
                panelCitizenBaru.classList.add('hidden');

                // clear form citizen baru agar tidak ikut terkirim “kotor”
                if (nama) nama.value = '';
                if (noKk) noKk.value = '';
                if (jk) jk.value = '';
                if (tl) tl.value = '';
                if (sp) sp.value = '';
                if (agama) agama.value = '';
                if (pend) pend.value = '';
                if (pek) pek.value = '';
            }

            function setModeNew() {
                citizenMode.value = 'new';
                panelCitizenBaru.classList.remove('hidden');

                // prefill nik baru tidak perlu (nik tetap di input utama)
                // biarkan user isi data minimal
            }

            async function safeJson(res) {
                try { return await res.json(); } catch (e) { return null; }
            }

            async function cekNik() {
                if (!canCreate) return;

                const v = (nik.value || '').trim();
                if (!v) {
                    showAlert('error', 'NIK wajib diisi.');
                    clearAuto();
                    setModeExisting();
                    return;
                }

                showAlert('warn', 'Mengecek NIK...');
                clearAuto();

                const url = `{{ url('/api/citizens/by-nik') }}/${encodeURIComponent(v)}`;
                const res = await fetch(url, { headers: { 'Accept': 'application/json' } });

                if (!res.ok) {
                    const json = await safeJson(res);
                    const msg = (json && json.message) ? json.message : 'NIK tidak ditemukan.';

                    // 403: scope operator tidak set / akses ditolak
                    if (res.status === 403) {
                        setModeExisting();
                        showAlert('error', msg);
                        return;
                    }

                    // 404: tidak ditemukan -> mode citizen baru
                    if (res.status === 404) {
                        setModeNew();
                        showAlert('warn', 'NIK belum terdaftar. Silakan isi data penduduk baru (minimal) di panel kuning.');
                        return;
                    }

                    // selain itu
                    setModeExisting();
                    showAlert('error', msg);
                    return;
                }

                const json = await safeJson(res);
                const d = (json && json.data) ? json.data : {};

                // found -> mode existing
                setModeExisting();
                showAlert('ok', '✅ Penduduk ditemukan. Data identitas terisi otomatis.');

                namaAuto.value = d.nama ?? '';
                noKkAuto.value = d.no_kk ?? '';
                jkAuto.value = (d.jenis_kelamin === 'L') ? 'Laki-laki' : (d.jenis_kelamin === 'P' ? 'Perempuan' : (d.jenis_kelamin ?? ''));
                tlAuto.value = d.tanggal_lahir ?? '';
                spAuto.value = d.status_perkawinan ?? '';
                agamaAuto.value = d.agama ?? '';
                pendAuto.value = d.pendidikan_dalam_kk ?? '';
                pekAuto.value = d.pekerjaan ?? '';
            }

            if (btn) btn.addEventListener('click', cekNik);

            // enter untuk cek nik
            if (nik) {
                nik.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        cekNik();
                    }
                });
            }

            // jika ada old() nik -> auto cek (biar nyaman saat validation error balik)
            const oldNik = @json(old('nik'));
            if (oldNik && canCreate) {
                nik.value = oldNik;
                cekNik();
            }
        })();
    </script>
</x-app-layout>
