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
                        <select name="role" class="w-full border-gray-300 rounded" required>
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
</x-app-layout>
