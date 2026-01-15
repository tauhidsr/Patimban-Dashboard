{{-- resources/views/events/partials/identity-tomselect.blade.php --}}
@php
    /**
     * Required vars from parent:
     * - $canCreate (bool)
     * - $eventLabel (string) ex: 'Meninggal' / 'Hilang' / 'Lahir'
     *
     * Optional:
     * - $onlyActive (bool) default true (blok kalau status_dasar != aktif)
     * - $prefix (string) default 'pe' (biar id input unik per form)
     * - $submitButtonId (string) default 'btnSubmit' (id tombol submit di parent)
     *
     * === New optional for reuse (ibu/ayah) ===
     * - $title (string) heading, default 'Identitas Penduduk'
     * - $labelNik (string) label untuk NIK, default 'NIK'
     * - $requiredNik (bool) default true
     * - $lockSubmit (bool) default true (kalau false, komponen ini tidak mengunci submit)
     *
     * - $nameNik (string) default 'nik'
     * - $nameNoKk (string) default 'no_kk'
     * - $nameNama (string) default 'nama'
     *
     * - $showAge (bool) default false => menampilkan umur jika API mengembalikan tanggal_lahir
     */
    $eventLabel      = $eventLabel ?? 'Peristiwa';
    $onlyActive      = $onlyActive ?? true;
    $prefix          = $prefix ?? 'pe';
    $submitButtonId  = $submitButtonId ?? 'btnSubmit';

    $title           = $title ?? 'Identitas Penduduk';
    $labelNik        = $labelNik ?? 'NIK';

    $requiredNik     = $requiredNik ?? true;
    $lockSubmit      = $lockSubmit ?? true;

    $nameNik         = $nameNik ?? 'nik';
    $nameNoKk        = $nameNoKk ?? 'no_kk';
    $nameNama        = $nameNama ?? 'nama';

    $showAge         = $showAge ?? false;

    // unik untuk wrapper (biar JS scoped aman)
    $wrapId = "identityWrap_{$prefix}";
@endphp

<div id="{{ $wrapId }}">
    {{-- ✅ ALERT/TOAST kecil otomatis --}}
    <div data-role="statusBlock" class="hidden p-4 mb-4 border rounded-lg">
        <div class="font-semibold" data-role="statusTitle">-</div>
        <div class="mt-1 text-sm" data-role="statusDesc">-</div>
    </div>

    <div class="flex items-start justify-between gap-4">
        <div>
            <h3 class="mb-1 text-lg font-semibold text-gray-700">
                {{ $title }}
            </h3>
            <p class="text-xs text-gray-500">
                Ketik minimal 3 karakter (NIK / Nama / No KK), lalu pilih penduduk.
            </p>
        </div>

        @if($requiredNik)
            <div class="text-xs font-semibold text-red-600">Wajib</div>
        @else
            <div class="text-xs font-semibold text-gray-500">Opsional</div>
        @endif
    </div>

    {{-- No KK --}}
    <div class="mt-3">
        <label class="block mb-1 text-sm font-medium">No KK</label>
        <input data-role="no_kk"
               id="{{ $prefix }}_no_kk"
               type="text"
               name="{{ $nameNoKk }}"
               value="{{ old($nameNoKk) }}"
               class="w-full border-gray-300 rounded bg-gray-50"
               readonly>
        @error($nameNoKk)
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- NIK (Tom Select) --}}
    <div class="mt-3">
        <label class="block mb-1 text-sm font-medium">{{ $labelNik }} @if($requiredNik)<span class="text-red-600">*</span>@endif</label>

        <select data-role="nik"
                id="{{ $prefix }}_nik"
                name="{{ $nameNik }}"
                class="w-full border-gray-300 rounded"
                {{ $canCreate ? '' : 'disabled' }}>
            @if(old($nameNik))
                <option value="{{ old($nameNik) }}" selected>{{ old($nameNik) }}</option>
            @endif
        </select>

        <p data-role="nikError" class="hidden mt-1 text-xs text-red-600"></p>

        {{-- Badge status kecil --}}
        <div data-role="nikBadge" class="hidden mt-2 text-xs">
            <span data-role="nikBadgeInner" class="inline-flex items-center px-2 py-1 border rounded">
                -
            </span>
        </div>

        @error($nameNik)
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Nama --}}
    <div class="mt-3">
        <label class="block mb-1 text-sm font-medium">Nama</label>
        <input data-role="nama"
               id="{{ $prefix }}_nama"
               type="text"
               name="{{ $nameNama }}"
               value="{{ old($nameNama) }}"
               class="w-full border-gray-300 rounded bg-gray-50"
               readonly>
        @error($nameNama)
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Umur (opsional tampil, jika API mengembalikan tanggal_lahir) --}}
    @if($showAge)
        <div class="mt-3">
            <label class="block mb-1 text-sm font-medium">Umur (Auto)</label>
            <input data-role="umur"
                   id="{{ $prefix }}_umur"
                   type="text"
                   value=""
                   class="w-full border-gray-300 rounded bg-gray-50"
                   readonly>
            <p class="mt-1 text-xs text-gray-500">
                Umur dihitung dari tanggal lahir (jika tersedia di data penduduk).
            </p>
        </div>
    @endif

    {{-- Dusun / RW / RT (tampil saja) --}}
    <div class="grid grid-cols-1 gap-4 mt-3 md:grid-cols-3">
        <div>
            <label class="block mb-1 text-sm font-medium">Dusun</label>
            <input data-role="dusun"
                   id="{{ $prefix }}_dusun"
                   type="text"
                   value="{{ old("{$prefix}_dusun") }}"
                   class="w-full border-gray-300 rounded bg-gray-50"
                   readonly>
        </div>
        <div>
            <label class="block mb-1 text-sm font-medium">RW</label>
            <input data-role="rw"
                   id="{{ $prefix }}_rw"
                   type="text"
                   value="{{ old("{$prefix}_rw") }}"
                   class="w-full border-gray-300 rounded bg-gray-50"
                   readonly>
        </div>
        <div>
            <label class="block mb-1 text-sm font-medium">RT</label>
            <input data-role="rt"
                   id="{{ $prefix }}_rt"
                   type="text"
                   value="{{ old("{$prefix}_rt") }}"
                   class="w-full border-gray-300 rounded bg-gray-50"
                   readonly>
        </div>
    </div>
</div>

{{-- ✅ Load Tom Select sekali saja --}}
@once
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
@endonce

<script>
    (function () {
        const canCreate = @json($canCreate);
        const eventLabel = @json($eventLabel);
        const onlyActive = @json($onlyActive);
        const wrapId = @json($wrapId);
        const submitButtonId = @json($submitButtonId);

        const requiredNik = @json($requiredNik);
        const lockSubmit = @json($lockSubmit);
        const showAge = @json($showAge);

        const wrap = document.getElementById(wrapId);
        if (!wrap) return;

        // scoped elements
        const nikSelect = wrap.querySelector('[data-role="nik"]');
        const noKk = wrap.querySelector('[data-role="no_kk"]');
        const nama = wrap.querySelector('[data-role="nama"]');
        const dusun = wrap.querySelector('[data-role="dusun"]');
        const rw = wrap.querySelector('[data-role="rw"]');
        const rt = wrap.querySelector('[data-role="rt"]');
        const umur = wrap.querySelector('[data-role="umur"]');

        // tombol submit ada di parent form
        const btnSubmit = document.getElementById(submitButtonId);

        const nikError = wrap.querySelector('[data-role="nikError"]');
        const nikBadge = wrap.querySelector('[data-role="nikBadge"]');
        const nikBadgeInner = wrap.querySelector('[data-role="nikBadgeInner"]');

        const statusBlock = wrap.querySelector('[data-role="statusBlock"]');
        const statusTitle = wrap.querySelector('[data-role="statusTitle"]');
        const statusDesc = wrap.querySelector('[data-role="statusDesc"]');

        let isNoResultHintActive = false;

        function setSubmit(enabled) {
            if (!btnSubmit) return;
            if (!lockSubmit) return; // ✅ komponen opsional tidak mengunci submit
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
            } else if (type === 'warn') {
                nikBadgeInner.classList.add('bg-yellow-100', 'text-yellow-800', 'border-yellow-300');
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

        function fillIdentity(d) {
            noKk.value = d.no_kk ?? '';
            nama.value = d.nama ?? '';
            dusun.value = d.dusun ?? '';
            rw.value = d.rw ?? '';
            rt.value = d.rt ?? '';

            // umur (opsional)
            if (showAge && umur) {
                const tl = d.tanggal_lahir ?? d.tgl_lahir ?? null;
                if (tl) {
                    const age = calcAge(tl);
                    umur.value = (age === null) ? '' : `${age} tahun`;
                } else {
                    umur.value = '';
                }
            }
        }

        function clearIdentity() {
            noKk.value = '';
            nama.value = '';
            dusun.value = '';
            rw.value = '';
            rt.value = '';
            if (showAge && umur) umur.value = '';
        }

        function showNoResultsToast(query) {
            isNoResultHintActive = true;

            clearIdentity();
            setSubmit(requiredNik ? false : true);
            setError('');

            setBadge('warn', 'Tidak ada hasil');
            showStatusBlock(
                'warn',
                'Tidak ada hasil pencarian',
                `Tidak ditemukan penduduk untuk kata kunci "${query}". Bisa jadi NIK tidak terdaftar atau di luar wilayah Anda.`
            );
        }

        function clearNoResultsToast() {
            if (!isNoResultHintActive) return;

            isNoResultHintActive = false;
            nikBadge.classList.add('hidden');
            hideStatusBlock();

            // reset submit (kalau wajib, kunci sampai valid)
            if (requiredNik) setSubmit(false);
        }

        async function safeReadJson(res) {
            try {
                return await res.json();
            } catch (e) {
                return null;
            }
        }

        function calcAge(dateString) {
            // dateString diharapkan YYYY-MM-DD
            const d = new Date(dateString);
            if (isNaN(d.getTime())) return null;

            const now = new Date();
            let age = now.getFullYear() - d.getFullYear();
            const m = now.getMonth() - d.getMonth();
            if (m < 0 || (m === 0 && now.getDate() < d.getDate())) {
                age--;
            }
            return age;
        }

        async function fetchCitizen(nik) {
            isNoResultHintActive = false;

            // default: jika required & lock -> disable submit sampai valid
            if (requiredNik) setSubmit(false);

            setError('');
            hideStatusBlock();

            const res = await fetch(`{{ url('/api/citizens/by-nik') }}/${encodeURIComponent(nik)}`, {
                headers: { 'Accept': 'application/json' }
            });

            if (!res.ok) {
                clearIdentity();

                const json = await safeReadJson(res);
                const msg = (json && json.message) ? json.message : 'Terjadi kesalahan saat mengambil data penduduk.';

                if (res.status === 403) {
                    setError(msg);
                    setBadge('error', 'Akses ditolak');
                    showStatusBlock('error', 'Tidak bisa lanjut', msg);
                    if (requiredNik) setSubmit(false);
                    return;
                }

                if (res.status === 404) {
                    setError(msg);
                    setBadge('error', 'Penduduk tidak ditemukan');
                    showStatusBlock('error', 'Tidak bisa lanjut', msg);
                    if (requiredNik) setSubmit(false);
                    return;
                }

                setError(msg);
                setBadge('error', 'Gagal memuat data');
                showStatusBlock('error', 'Terjadi kesalahan', msg);
                if (requiredNik) setSubmit(false);
                return;
            }

            const json = await safeReadJson(res);
            const d = (json && json.data) ? json.data : {};
            fillIdentity(d);

            const statusDasar = (d.status_dasar ?? '').toString().toLowerCase();

            setError('');
            setBadge('ok', `Penduduk ditemukan — status ${statusDasar ? statusDasar.toUpperCase() : 'TERDAFTAR'}`);

            if (onlyActive && statusDasar && statusDasar !== 'aktif') {
                if (requiredNik) setSubmit(false);
                setBadge('warn', `Tidak bisa diproses — status ${statusDasar.toUpperCase()}`);
                showStatusBlock(
                    'warn',
                    '⚠ Tidak bisa diproses',
                    `Status penduduk saat ini: ${statusDasar.toUpperCase()}. Peristiwa ${eventLabel} hanya boleh untuk status AKTIF.`
                );
            } else {
                if (requiredNik) setSubmit(true);
                showStatusBlock(
                    'ok',
                    '✅ Data penduduk valid',
                    requiredNik ? 'Form bisa disimpan. Pastikan detail peristiwa sudah benar.' : 'Data opsional berhasil dipilih.'
                );
            }
        }

        // default: kalau wajib -> submit disabled sampai valid
        if (requiredNik) setSubmit(false);

        if (!canCreate || !nikSelect) {
            return;
        }

        let ts = new TomSelect(nikSelect, {
            valueField: 'value',
            labelField: 'text',
            searchField: 'text',
            preload: false,
            create: false,
            placeholder: 'Cari NIK / Nama / No KK...',

            render: {
                no_results: function() {
                    return `<div class="p-2 text-sm text-gray-600 no-results">
                        Tidak ada hasil. Bisa jadi NIK tidak terdaftar atau di luar wilayah Anda.
                    </div>`;
                }
            },

            load: async function (query, cb) {
                clearNoResultsToast();
                setError('');

                if (!query || query.length < 3) {
                    nikBadge.classList.add('hidden');
                    hideStatusBlock();
                    if (requiredNik) setSubmit(false);
                    return cb();
                }

                try {
                    const res = await fetch(`{{ url('/api/citizens/search') }}?q=${encodeURIComponent(query)}`, {
                        headers: { 'Accept': 'application/json' }
                    });
                    const json = await res.json();
                    const results = json.results || [];

                    cb(results);

                    if (results.length === 0) {
                        showNoResultsToast(query);
                    }
                } catch (e) {
                    cb();
                }
            },

            onChange(value) {
                if (!value) {
                    clearIdentity();
                    setError('');
                    nikBadge.classList.add('hidden');
                    hideStatusBlock();

                    if (requiredNik) setSubmit(false);
                    return;
                }
                fetchCitizen(value);
            }
        });

        function tryFetchIfLooksLikeNik(raw) {
            const v = (raw || '').trim();
            if (/^\d{16}$/.test(v)) {
                fetchCitizen(v);
            }
        }

        ts.on('blur', () => {
            tryFetchIfLooksLikeNik(ts.control_input?.value);
        });

        ts.control_input?.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                tryFetchIfLooksLikeNik(ts.control_input?.value);
            }
        });

        const oldNik = @json(old($nameNik));
        if (oldNik) {
            ts.addOption({ value: oldNik, text: oldNik });
            ts.setValue(oldNik, true);
            fetchCitizen(oldNik);
        } else {
            // jika tidak wajib, tidak perlu mengunci submit dari komponen ini
            if (!requiredNik && lockSubmit) setSubmit(true);
        }
    })();
</script>
