<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="flex items-center shrink-0">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block w-auto text-gray-800 fill-current h-9" />
                    </a>
                </div>

                @php
                    $user = auth()->user();
                    $role = $user->role ?? null;

                    $isAdmin    = auth()->check() && $role === 'admin';
                    $isOperator = auth()->check() && $role === 'operator';
                    $isViewer   = auth()->check() && ($role === 'viewer' || $role === null);

                    $mustChange = auth()->check() && (($user->must_change_password ?? false) === true);

                    $scopeWilayah = trim(
                        ($user->dusun ?? '') .
                        (!empty($user->rw) ? " / RW {$user->rw}" : '') .
                        (!empty($user->rt) ? " / RT {$user->rt}" : '')
                    );
                @endphp

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">

                    {{-- ✅ Saat wajib ganti password: kunci menu (hanya Profile via dropdown) --}}
                    @if(!$mustChange)
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>

                        <x-nav-link :href="route('citizens.index')" :active="request()->routeIs('citizens.*')">
                            {{ __('Data Penduduk') }}
                        </x-nav-link>

                        <x-nav-link :href="route('citizen-events.index')" :active="request()->routeIs('citizen-events.*')">
                            {{ __('Log Perubahan') }}
                        </x-nav-link>

                        <x-nav-link :href="route('events.index')" :active="request()->routeIs('events.*')">
                            {{ __('Peristiwa') }}
                        </x-nav-link>

                        <x-nav-link :href="route('map.index')" :active="request()->routeIs('map.*')">
                            {{ __('Peta') }}
                        </x-nav-link>

                        {{-- ✅ Admin only --}}
                        @if($isAdmin)
                            <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                                {{ __('Manajemen Akun') }}
                            </x-nav-link>
                        @endif
                    @else
                        <span class="inline-flex items-center self-center px-3 py-1 text-xs font-semibold text-red-800 bg-red-100 border border-red-200 rounded-full">
                            Wajib ganti password
                        </span>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="56">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out bg-white border border-transparent rounded-md hover:text-gray-700 focus:outline-none">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="w-4 h-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        {{-- Info ringkas akun --}}
                        <div class="px-4 pt-3 pb-2 space-y-1 text-xs text-gray-500">
                            <div>
                                Role:
                                <span class="font-semibold text-gray-700">{{ $role ?? '-' }}</span>
                            </div>

                            @if($isOperator || $isViewer)
                                <div>
                                    Wilayah:
                                    <span class="font-semibold text-gray-700">{{ $scopeWilayah !== '' ? $scopeWilayah : '-' }}</span>
                                </div>
                            @endif

                            @if(!empty($user->jabatan))
                                <div>
                                    Jabatan:
                                    <span class="font-semibold text-gray-700">{{ $user->jabatan }}</span>
                                </div>
                            @endif

                            @if($mustChange)
                                <div class="pt-1 text-red-600">
                                    Wajib ganti password sebelum akses menu lain.
                                </div>
                            @endif
                        </div>

                        <div class="border-t border-gray-100"></div>

                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="flex items-center -me-2 sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 text-gray-400 transition duration-150 ease-in-out rounded-md hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500">
                    <svg class="w-6 h-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @if(!$mustChange)
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('citizens.index')" :active="request()->routeIs('citizens.*')">
                    {{ __('Data Penduduk') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('citizen-events.index')" :active="request()->routeIs('citizen-events.*')">
                    {{ __('Log Perubahan') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('events.index')" :active="request()->routeIs('events.*')">
                    {{ __('Peristiwa') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('map.index')" :active="request()->routeIs('map.*')">
                    {{ __('Peta') }}
                </x-responsive-nav-link>

                @if($isAdmin)
                    <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                        {{ __('Manajemen Akun') }}
                    </x-responsive-nav-link>
                @endif
            @else
                <div class="px-4 py-2 text-xs font-semibold text-red-800 bg-red-100 border border-red-200 rounded-lg">
                    Wajib ganti password sebelum akses menu lain.
                </div>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="text-base font-medium text-gray-800">{{ Auth::user()->name }}</div>
                <div class="text-sm font-medium text-gray-500">{{ Auth::user()->email }}</div>

                <div class="mt-2 space-y-1 text-xs text-gray-500">
                    <div>
                        Role: <span class="font-semibold text-gray-700">{{ $role ?? '-' }}</span>
                    </div>

                    <div>
                        Wilayah: <span class="font-semibold text-gray-700">{{ $scopeWilayah !== '' ? $scopeWilayah : '-' }}</span>
                    </div>

                    @if(!empty($user->jabatan))
                        <div>
                            Jabatan: <span class="font-semibold text-gray-700">{{ $user->jabatan }}</span>
                        </div>
                    @endif
                </div>

                @if($mustChange)
                    <div class="mt-2 text-xs text-red-600">
                        Wajib ganti password sebelum akses menu lain.
                    </div>
                @endif
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
