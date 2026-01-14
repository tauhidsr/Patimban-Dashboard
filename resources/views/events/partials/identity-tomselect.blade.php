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
     */
    $eventLabel      = $eventLabel ?? 'Peristiwa';
    $onlyActive      = $onlyActive ?? true;
    $prefix          = $prefix ?? 'pe';
    $submitButtonId  = $submitButtonId ?? 'btnSubmit';

    // unik untuk wrapper (biar JS scoped aman)
    $wrapId = "identityWrap_{$prefix}";
@endphp

<div id="{{ $wrapId }}">
    {{-- ✅ ALERT/TOAST kecil otomatis --}}
    <div data-role="statusBlock" class="hidden p-4 mb-4 border rounded-lg">
        <div class="font-semibold" data-role="statusTitle">-</div>
        <div class="mt-1 text-sm" data-role="statusDesc">-</div>
    </div>

    <h3 class="mb-2 text-lg font-semibold text-gray-700">
        Identitas Penduduk
    </h3>

    {{-- No KK (readonly biar ikut terkirim) --}}
    <div>
        <label class="block mb-1 text-sm font-medium">No KK</label>
        <input data-role="no_kk"
               id="{{ $prefix }}_no_kk"
               type="text"
               name="no_kk"
               value="{{ old('no_kk') }}"
               class="w-full border-gray-300 rounded bg-gray-50"
               readonly>
        @error('no_kk')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- NIK (Tom Select) --}}
    <div>
        <label class="block mb-1 text-sm font-medium">NIK</label>
        <select data-role="nik"
                id="{{ $prefix }}_nik"
                name="nik"
                class="w-full border-gray-300 rounded"
                {{ $canCreate ? '' : 'disabled' }}>
            @if(old('nik'))
                <option value="{{ old('nik') }}" selected>{{ old('nik') }}</option>
            @endif
        </select>

        <p class="mt-1 text-xs text-gray-500">
            Ketik minimal 3 karakter (NIK / Nama / No KK), lalu pilih penduduk.
        </p>

        <p data-role="nikError" class="hidden mt-1 text-xs text-red-600"></p>

        {{-- Badge status kecil --}}
        <div data-role="nikBadge" class="hidden mt-2 text-xs">
            <span data-role="nikBadgeInner" class="inline-flex items-center px-2 py-1 border rounded">
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
        <input data-role="nama"
               id="{{ $prefix }}_nama"
               type="text"
               name="nama"
               value="{{ old('nama') }}"
               class="w-full border-gray-300 rounded bg-gray-50"
               readonly>
        @error('nama')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Dusun / RW / RT (tampil saja) --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div>
            <label class="block mb-1 text-sm font-medium">Dusun</label>
            <input data-role="dusun"
                   id="{{ $prefix }}_dusun"
                   type="text"
                   value="{{ old('dusun') }}"
                   class="w-full border-gray-300 rounded bg-gray-50"
                   readonly>
        </div>
        <div>
            <label class="block mb-1 text-sm font-medium">RW</label>
            <input data-role="rw"
                   id="{{ $prefix }}_rw"
                   type="text"
                   value="{{ old('rw') }}"
                   class="w-full border-gray-300 rounded bg-gray-50"
                   readonly>
        </div>
        <div>
            <label class="block mb-1 text-sm font-medium">RT</label>
            <input data-role="rt"
                   id="{{ $prefix }}_rt"
                   type="text"
                   value="{{ old('rt') }}"
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

        const wrap = document.getElementById(wrapId);
        if (!wrap) return;

        // scoped elements
        const nikSelect = wrap.querySelector('[data-role="nik"]');
        const noKk = wrap.querySelector('[data-role="no_kk"]');
        const nama = wrap.querySelector('[data-role="nama"]');
        const dusun = wrap.querySelector('[data-role="dusun"]');
        const rw = wrap.querySelector('[data-role="rw"]');
        const rt = wrap.querySelector('[data-role="rt"]');

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
        }

        function clearIdentity() {
            noKk.value = '';
            nama.value = '';
            dusun.value = '';
            rw.value = '';
            rt.value = '';
        }

        function showNoResultsToast(query) {
            isNoResultHintActive = true;

            clearIdentity();
            setSubmit(false);
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
            setSubmit(false);
        }

        async function safeReadJson(res) {
            try {
                return await res.json();
            } catch (e) {
                return null;
            }
        }

        async function fetchCitizen(nik) {
            isNoResultHintActive = false;

            setSubmit(false);
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
                    return;
                }

                if (res.status === 404) {
                    setError(msg);
                    setBadge('error', 'Penduduk tidak ditemukan');
                    showStatusBlock('error', 'Tidak bisa lanjut', msg);
                    return;
                }

                setError(msg);
                setBadge('error', 'Gagal memuat data');
                showStatusBlock('error', 'Terjadi kesalahan', msg);
                return;
            }

            const json = await safeReadJson(res);
            const d = (json && json.data) ? json.data : {};
            fillIdentity(d);

            const statusDasar = (d.status_dasar ?? '').toString().toLowerCase();

            setError('');
            setBadge('ok', `Penduduk ditemukan — status ${statusDasar ? statusDasar.toUpperCase() : 'TERDAFTAR'}`);

            if (onlyActive && statusDasar && statusDasar !== 'aktif') {
                setSubmit(false);
                setBadge('warn', `Tidak bisa diproses — status ${statusDasar.toUpperCase()}`);
                showStatusBlock(
                    'warn',
                    '⚠ Tidak bisa input peristiwa untuk penduduk ini',
                    `Status penduduk saat ini: ${statusDasar.toUpperCase()}. Peristiwa ${eventLabel} hanya boleh untuk status AKTIF.`
                );
            } else {
                setSubmit(true);
                showStatusBlock(
                    'ok',
                    '✅ Data penduduk valid',
                    'Form bisa disimpan. Pastikan detail peristiwa sudah benar.'
                );
            }
        }

        // default: submit disabled sampai nik valid
        setSubmit(false);

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
                no_results: function(data, escape) {
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
                    setSubmit(false);
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
                    setSubmit(false);
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

        const oldNik = @json(old('nik'));
        if (oldNik) {
            ts.addOption({ value: oldNik, text: oldNik });
            ts.setValue(oldNik, true);
            fetchCitizen(oldNik);
        }
    })();
</script>
