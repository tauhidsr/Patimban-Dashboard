<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Manajemen Akun
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto space-y-4 sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="p-3 text-sm text-green-800 bg-green-100 border border-green-200 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="p-3 text-sm text-red-800 bg-red-100 border border-red-200 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            {{-- ✅ Temp password (tampil 1x) + tombol copy --}}
            @if (session('temp_password'))
                <div class="p-4 text-sm text-yellow-900 bg-yellow-100 border border-yellow-200 rounded-lg">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1">
                            <div class="font-semibold">Password sementara (catat & berikan ke user):</div>

                            <div class="flex items-center gap-2 mt-2">
                                <div id="tempPass"
                                    class="px-3 py-2 font-mono text-base bg-white border border-yellow-200 rounded">
                                    {{ session('temp_password') }}
                                </div>

                                <button type="button" id="btnCopyTempPass"
                                    class="px-3 py-2 text-xs font-semibold text-white bg-yellow-700 rounded hover:bg-yellow-800">
                                    Copy
                                </button>
                            </div>

                            <div id="copyHint" class="hidden mt-2 text-xs text-yellow-800">
                                Berhasil disalin ✅
                            </div>

                            <div class="mt-2 text-xs text-yellow-800">
                                Password ini hanya tampil sekali setelah create/reset.
                            </div>
                        </div>

                        <div class="text-xs text-yellow-800">
                            ⚠️ Jangan share di grup umum.
                        </div>
                    </div>
                </div>
            @endif

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="flex items-center justify-between px-6 py-4 border-b">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Daftar Akun</h3>
                        <p class="text-xs text-gray-500">Admin membuat akun operator/viewer dari sini.</p>
                    </div>
                    <a href="{{ route('admin.users.create') }}"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded hover:bg-blue-700">
                        + Buat Akun
                    </a>
                </div>

                <div class="px-6 py-3 border-b bg-gray-50">
                    <form method="GET" action="{{ route('admin.users.index') }}" class="flex gap-2">
                        <input type="text" name="q" value="{{ $q }}"
                            class="w-full border-gray-300 rounded"
                            placeholder="Cari nama/email/role...">
                        <button class="px-3 py-2 text-xs font-semibold text-white bg-blue-600 rounded hover:bg-blue-700">
                            Cari
                        </button>
                        <a href="{{ route('admin.users.index') }}"
                            class="px-3 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-100">
                            Reset
                        </a>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-left">
                        <thead>
                            <tr class="text-xs font-semibold tracking-wide text-gray-600 uppercase bg-gray-100 border-b">
                                <th class="px-4 py-2">ID</th>
                                <th class="px-4 py-2">Nama</th>
                                <th class="px-4 py-2">Email</th>
                                <th class="px-4 py-2">Role</th>
                                <th class="px-4 py-2">Dibuat</th>
                                <th class="px-4 py-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $u)
                                @php
                                    $isRowAdmin = (($u->role ?? null) === 'admin');
                                    $isSelf = auth()->id() === $u->id;

                                    // ✅ disable tombol kalau admin ATAU akun sendiri
                                    $disableActions = $isRowAdmin || $isSelf;
                                @endphp

                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-2">{{ $u->id }}</td>
                                    <td class="px-4 py-2">
                                        {{ $u->name }}
                                        @if($isSelf)
                                            <span class="ml-2 text-[11px] px-2 py-0.5 rounded bg-gray-100 text-gray-700">
                                                Anda
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2">{{ $u->email }}</td>
                                    <td class="px-4 py-2">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                            @if(($u->role ?? '') === 'admin') bg-purple-100 text-purple-800
                                            @elseif(($u->role ?? '') === 'operator') bg-blue-100 text-blue-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ $u->role ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 text-xs text-gray-600">
                                        {{ $u->created_at?->format('d-m-Y H:i') ?? '-' }}
                                    </td>
                                    <td class="px-4 py-2">
                                        <div class="flex flex-wrap gap-2">
                                            <form method="POST"
                                                action="{{ route('admin.users.resetPassword', $u->id) }}"
                                                onsubmit="return confirm('Reset password untuk akun ini? Password baru akan dibuat otomatis dan hanya tampil sekali.');">
                                                @csrf
                                                <button type="submit"
                                                    class="px-3 py-1 text-xs font-medium text-white bg-indigo-600 rounded hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed"
                                                    {{ $disableActions ? 'disabled' : '' }}>
                                                    Reset Password
                                                </button>
                                            </form>

                                            <form method="POST"
                                                action="{{ route('admin.users.destroy', $u->id) }}"
                                                onsubmit="return confirm('Yakin hapus akun ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="px-3 py-1 text-xs font-medium text-white bg-red-600 rounded hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed"
                                                    {{ $disableActions ? 'disabled' : '' }}>
                                                    Hapus
                                                </button>
                                            </form>

                                            @if($isRowAdmin)
                                                <span class="text-[11px] text-gray-500 self-center">
                                                    (Akun admin tidak bisa di-reset/hapus)
                                                </span>
                                            @elseif($isSelf)
                                                <span class="text-[11px] text-gray-500 self-center">
                                                    (Akun sendiri tidak bisa di-reset/hapus dari menu ini)
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-6 text-sm text-center text-gray-500">
                                        Belum ada akun.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($users->hasPages())
                    <div class="px-6 py-4 border-t">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- ✅ JS kecil: copy temp password --}}
    @if (session('temp_password'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const btn = document.getElementById('btnCopyTempPass');
                const passEl = document.getElementById('tempPass');
                const hint = document.getElementById('copyHint');

                if (!btn || !passEl) return;

                btn.addEventListener('click', async function () {
                    const text = passEl.textContent.trim();

                    try {
                        await navigator.clipboard.writeText(text);
                        if (hint) {
                            hint.classList.remove('hidden');
                            setTimeout(() => hint.classList.add('hidden'), 1500);
                        }
                    } catch (e) {
                        alert('Gagal copy otomatis. Silakan blok password lalu copy manual.');
                    }
                });
            });
        </script>
    @endif
</x-app-layout>
