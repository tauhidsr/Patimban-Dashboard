<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    {{-- 🚨 ALERT WAJIB GANTI PASSWORD --}}
    @if (session('force_password_change'))
        <div class="p-4 mt-4 text-sm text-red-800 bg-red-100 border border-red-200 rounded-lg">
            <div class="font-semibold">Perhatian</div>
            <div class="mt-1">
                {{ session('force_password_change') }}
            </div>
        </div>
    @endif

    {{-- ✅ ALERT SUKSES + AUTO REDIRECT --}}
    @if (session('status') === 'password-updated')
        <div class="p-4 mt-4 text-sm text-green-800 bg-green-100 border border-green-200 rounded-lg">
            <div class="font-semibold">Berhasil</div>
            <div class="mt-1">
                Password berhasil diperbarui. Mengarahkan ke dashboard...
            </div>
        </div>

        <script>
            setTimeout(function () {
                window.location.href = "{{ route('dashboard') }}";
            }, 1200);
        </script>
    @endif

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" :value="__('Current Password')" />
            <x-text-input
                id="update_password_current_password"
                name="current_password"
                type="password"
                class="block w-full mt-1"
                autocomplete="current-password"
                required
            />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('New Password')" />
            <x-text-input
                id="update_password_password"
                name="password"
                type="password"
                class="block w-full mt-1"
                autocomplete="new-password"
                required
            />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" />
            <x-text-input
                id="update_password_password_confirmation"
                name="password_confirmation"
                type="password"
                class="block w-full mt-1"
                autocomplete="new-password"
                required
            />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>
        </div>
    </form>
</section>
