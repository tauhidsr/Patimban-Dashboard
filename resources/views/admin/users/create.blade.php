<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Buat Akun Operator / Viewer
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="p-6 bg-white shadow-sm sm:rounded-lg">

                @if ($errors->any())
                    <div class="p-4 mb-4 border border-red-200 rounded-lg bg-red-50">
                        <div class="font-semibold text-red-800">Periksa input:</div>
                        <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block mb-1 text-sm font-medium">Nama</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                            class="w-full border-gray-300 rounded" required>
                    </div>

                    <div>
                        <label class="block mb-1 text-sm font-medium">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                            class="w-full border-gray-300 rounded" required>
                    </div>

                    <div>
                        <label class="block mb-1 text-sm font-medium">Role</label>
                        <select id="roleSelect" name="role" class="w-full border-gray-300 rounded" required>
                            <option value="operator" {{ old('role', 'operator') === 'operator' ? 'selected' : '' }}>
                                operator
                            </option>
                            <option value="viewer" {{ old('role') === 'viewer' ? 'selected' : '' }}>
                                viewer
                            </option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">
                            Opsi A: Admin hanya membuat akun <span class="font-semibold">operator/viewer</span> dari menu ini.
                            Akun admin dibuat oleh perangkat desa (seeder/manual).
                        </p>
                    </div>

                    {{-- Scope wilayah (untuk operator) --}}
                    <div id="scopeBox" class="p-4 border border-gray-200 rounded-lg bg-gray-50">
                        <div class="text-sm font-semibold text-gray-800">Scope Wilayah (untuk Operator)</div>
                        <p class="mt-1 text-xs text-gray-600">
                            Isi sesuai jabatan: Kadus (Dusun saja), Ketua RW (Dusun + RW), Ketua RT (Dusun + RW + RT).
                        </p>

                        <div class="grid grid-cols-1 gap-3 mt-3 sm:grid-cols-3">
                            <div>
                                <label class="block mb-1 text-sm font-medium">Dusun <span class="text-red-600">*</span></label>
                                <input type="text" name="dusun" value="{{ old('dusun') }}"
                                    class="w-full border-gray-300 rounded" placeholder="contoh: Siwalan">
                                <p class="mt-1 text-xs text-gray-500">Wajib untuk operator.</p>
                            </div>

                            <div>
                                <label class="block mb-1 text-sm font-medium">RW (opsional)</label>
                                <input type="text" name="rw" value="{{ old('rw') }}"
                                    class="w-full border-gray-300 rounded" placeholder="contoh: 01">
                            </div>

                            <div>
                                <label class="block mb-1 text-sm font-medium">RT (opsional)</label>
                                <input type="text" name="rt" value="{{ old('rt') }}"
                                    class="w-full border-gray-300 rounded" placeholder="contoh: 02">
                                <p class="mt-1 text-xs text-gray-500">Jika RT diisi, RW wajib diisi.</p>
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="block mb-1 text-sm font-medium">Jabatan (opsional)</label>
                            <input type="text" name="jabatan" value="{{ old('jabatan') }}"
                                class="w-full border-gray-300 rounded" placeholder="contoh: Kadus / Ketua RW / Ketua RT">
                        </div>
                    </div>

                    <div>
                        <label class="block mb-1 text-sm font-medium">Password (opsional)</label>
                        <input type="text" name="password" value="{{ old('password') }}"
                            class="w-full border-gray-300 rounded" placeholder="Kosongkan untuk auto-generate">
                        <p class="mt-1 text-xs text-gray-500">
                            Jika dikosongkan, sistem akan membuat password sementara dan menampilkannya 1x setelah submit.
                        </p>
                    </div>

                    <div class="flex justify-between pt-2">
                        <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-600 hover:underline">
                            ← Kembali
                        </a>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-semibold text-white bg-blue-600 rounded hover:bg-blue-700">
                            Simpan
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const roleSelect = document.getElementById('roleSelect');
            const scopeBox = document.getElementById('scopeBox');

            function toggleScope() {
                const role = roleSelect.value;
                // viewer (kades) tidak perlu scope
                scopeBox.style.display = (role === 'operator') ? 'block' : 'none';
            }

            toggleScope();
            roleSelect.addEventListener('change', toggleScope);
        });
    </script>
</x-app-layout>
