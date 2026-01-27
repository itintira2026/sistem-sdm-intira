<div x-data="{ open: true, activeMenu: '{{ request()->segment(1) }}' }" class="flex h-screen bg-gray-100">
    <!-- Sidebar -->
    <aside :class="open ? 'w-64' : 'w-20'"
        class="flex flex-col transition-all duration-300 bg-white border-r border-gray-200">
        <!-- Logo & Toggle -->
        <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200">
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-2" x-show="open">
                <x-application-logo class="block w-auto text-gray-800 fill-current h-9" />
            </a>
            <button @click="open = !open" class="p-2 text-gray-500 rounded-lg hover:bg-gray-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        :d="open ? 'M6 18L18 6M6 6l12 12' : 'M4 6h16M4 12h16M4 18h16'" />
                </svg>
            </button>
        </div>

        <!-- Menu Items -->
        <nav class="flex-1 px-2 py-4 space-y-1 overflow-y-auto">

            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}"
                class="flex items-center px-3 py-2 text-sm font-medium rounded-lg group {{ request()->routeIs('dashboard') ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span x-show="open" class="transition-all duration-200">Dashboard</span>
            </a>

            <!-- HR Section -->
            @role('hr|superadmin')
                <div class="pt-2 pb-2 border-t border-gray-200">
                    <p x-show="open" class="px-3 mb-2 text-xs font-semibold tracking-wider text-gray-500 uppercase">HR
                        Management</p>
                </div>

                <!-- Users -->
                <a href="{{ route('users.index') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-lg group {{ request()->routeIs('users.*') ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span x-show="open">User</span>
                </a>

                <!-- Branches -->
                <a href="{{ route('branches.index') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-lg group {{ request()->routeIs('branches.*') ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span x-show="open">Branch</span>
                </a>

                <!-- Gaji -->
                <a href="{{ route('gaji.index') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-lg group {{ request()->routeIs('gaji.*') ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span x-show="open">Gaji</span>
                </a>

                <!-- Presensi -->
                <a href="{{ route('presensi.index') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-lg group {{ request()->routeIs('presensi.*') ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    <span x-show="open">Presensi</span>
                </a>
            @endrole

            <!-- Marketing Section -->
            @role('marketing|superadmin')
                <div class="pt-2 pb-2 border-t border-gray-200">
                    <p x-show="open" class="px-3 mb-2 text-xs font-semibold tracking-wider text-gray-500 uppercase">
                        Marketing</p>
                </div>

                <!-- Daily Content -->
                <a href="{{ route('daily-contents.index') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-lg group {{ request()->routeIs('daily-contents.*') ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                    </svg>
                    <span x-show="open">Daily Content</span>
                </a>
            @endrole

            <!-- Contact 90 Section -->
            @role('fo|manager|superadmin')
                <div class="pt-2 pb-2 border-t border-gray-200">
                    <p x-show="open" class="px-3 mb-2 text-xs font-semibold tracking-wider text-gray-500 uppercase">Contact
                        90</p>
                </div>

                <!-- FO Dashboard -->
                <a href="{{ route('contact90.index') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-lg group {{ request()->routeIs('contact90.index') || request()->routeIs('contact90.create') || request()->routeIs('contact90.edit') ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span x-show="open">My Contacts</span>
                </a>

                @role('manager|superadmin')
                    <!-- Manager Dashboard -->
                    <a href="{{ route('contact90.manager.dashboard') }}"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-lg group {{ request()->routeIs('contact90.manager.*') ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span x-show="open">Manager Dashboard</span>
                    </a>
                @endrole
            @endrole

        </nav>

        <!-- User Profile (Bottom) -->
        <div class="p-3 border-t border-gray-200">
            <div class="flex items-center" x-data="{ showDropdown: false }">
                <img src="{{ Auth::user()->profile_photo ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) }}"
                    alt="{{ Auth::user()->name }}" class="w-8 h-8 rounded-full">
                <div x-show="open" class="flex-1 ml-3">
                    <p class="text-sm font-medium text-gray-700">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-500">{{ Auth::user()->getRoleNames()->first() }}</p>
                </div>
                <button @click="showDropdown = !showDropdown" x-show="open"
                    class="p-1 text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                    </svg>
                </button>

                <!-- Dropdown Menu -->
                <div x-show="showDropdown" @click.away="showDropdown = false"
                    class="absolute z-50 w-48 bg-white rounded-lg shadow-lg bottom-16 left-4 ring-1 ring-black ring-opacity-5">
                    <a href="{{ route('profile.edit') }}"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        Profile
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="block w-full px-4 py-2 text-sm text-left text-red-700 hover:bg-gray-100">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex flex-col flex-1 overflow-hidden">
        <!-- Top Navbar -->
        <header class="h-16 bg-white border-b border-gray-200">
            <div class="h-full px-6 py-2">
                <!-- Page Title -->
                @isset($header)
                    <div>
                        {{ $header }}
                    </div>
                @endisset

                <!-- Right Side -->
                {{-- <div class="flex items-center space-x-4">
                    <!-- Notifications (optional) -->
                    <button class="p-2 text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </button>
                </div> --}}
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
            {{ $slot }}
        </main>
    </div>
</div>
