<div x-data="{
    sidebarOpen: false,
    desktopOpen: true,
    isMobile: window.innerWidth < 1024
}" @resize.window="isMobile = window.innerWidth < 1024"
    class="flex h-screen overflow-hidden bg-gray-100">

    <!-- Mobile Overlay -->
    <div x-show="sidebarOpen && isMobile" @click="sidebarOpen = false"
        x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 lg:hidden">
    </div>

    <!-- Sidebar -->
    <aside
        :class="{
            'translate-x-0': sidebarOpen || !isMobile,
            '-translate-x-full': !sidebarOpen && isMobile,
            'w-64': desktopOpen && !isMobile,
            'w-20': !desktopOpen && !isMobile
        }"
        class="fixed inset-y-0 left-0 z-50 flex flex-col w-64 transition-all duration-300 bg-white border-r border-gray-200 lg:static lg:translate-x-0">

        <!-- Logo & Toggle -->
        <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200 shrink-0">
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-2" x-show="desktopOpen || isMobile">
                {{-- <x-application-logo class="block w-auto text-gray-800 fill-current h-9" /> --}}
                <img src="{{ asset('assets/logo-intira.png') }}" alt="Logo" class="block w-auto h-9">
            </a>

            <!-- Desktop Toggle -->
            <button @click="desktopOpen = !desktopOpen"
                class="hidden p-2 text-gray-500 rounded-lg lg:block hover:bg-gray-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        :d="desktopOpen ? 'M11 19l-7-7 7-7m8 14l-7-7 7-7' : 'M13 5l7 7-7 7M5 5l7 7-7 7'" />
                </svg>
            </button>

            <!-- Mobile Close -->
            <button @click="sidebarOpen = false" class="p-2 text-gray-500 rounded-lg lg:hidden hover:bg-gray-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Menu Items -->
        <nav class="flex-1 px-2 py-4 space-y-1 overflow-y-auto">

            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}"
                class="flex items-center px-3 py-2 text-sm font-medium rounded-lg group {{ request()->routeIs('dashboard') ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="flex-shrink-0 w-5 h-5" :class="{ 'mr-3': desktopOpen || isMobile }" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span x-show="desktopOpen || isMobile" class="transition-all duration-200">Dashboard</span>
            </a>

            <div class="pt-2 pb-2 border-t border-gray-200" x-show="desktopOpen || isMobile">
                <p class="px-3 mb-2 text-xs font-semibold tracking-wider text-gray-500 uppercase">Karyawan</p>
            </div>

            <a href="{{ route('gaji-saya.show') }}"
                class="flex items-center px-3 py-2 text-sm font-medium rounded-lg group {{ request()->routeIs('gaji-saya.show') ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="flex-shrink-0 w-5 h-5" :class="{ 'mr-3': desktopOpen || isMobile }" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span x-show="desktopOpen || isMobile">Gaji Saya</span>
            </a>

            {{-- <a href="{{ route('presensi-karyawan.index') }}" --}}
            <a href="{{ route('absensi.index') }}"
                class="flex items-center px-3 py-2 text-sm font-medium rounded-lg group {{ request()->routeIs('presensi-karyawan.*') ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="flex-shrink-0 w-5 h-5" :class="{ 'mr-3': desktopOpen || isMobile }" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span x-show="desktopOpen || isMobile">Presensi Online</span>
            </a>

            <a href="{{ route('e-learning-karyawan.index') }}"
                class="flex items-center px-3 py-2 text-sm font-medium rounded-lg group {{ request()->routeIs('e-learning-karyawan.*') ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="flex-shrink-0 w-5 h-5" :class="{ 'mr-3': desktopOpen || isMobile }" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span x-show="desktopOpen || isMobile">E-Learning Karyawan</span>
            </a>

            {{-- ============================================================ --}}
            {{-- HR Management                                                  --}}
            {{-- ============================================================ --}}
            @role('hr|superadmin')
                <div class="pt-2 pb-2 border-t border-gray-200" x-show="desktopOpen || isMobile">
                    <p class="px-3 mb-2 text-xs font-semibold tracking-wider text-gray-500 uppercase">HR Management</p>
                </div>

                <a href="{{ route('users.index') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-lg group {{ request()->routeIs('users.*') ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="flex-shrink-0 w-5 h-5" :class="{ 'mr-3': desktopOpen || isMobile }" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span x-show="desktopOpen || isMobile">User</span>
                </a>

                <a href="{{ route('branches.index') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-lg group {{ request()->routeIs('branches.*') ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="flex-shrink-0 w-5 h-5" :class="{ 'mr-3': desktopOpen || isMobile }" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span x-show="desktopOpen || isMobile">Branch</span>
                </a>

                <a href="{{ route('gaji-pokok.index') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-lg group {{ request()->routeIs('gaji-pokok.*') ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="flex-shrink-0 w-5 h-5" :class="{ 'mr-3': desktopOpen || isMobile }" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span x-show="desktopOpen || isMobile">Gaji</span>
                </a>


                @php
                    $presensiActive =
                        request()->routeIs('presensi.*') ||
                        request()->routeIs('foto-presensi.*') ||
                        request()->routeIs('foto-closing.*');
                @endphp

                <div x-data="{ presensiOpen: {{ $presensiActive ? 'true' : 'false' }} }">

                    {{-- Trigger Dropdown --}}
                    <button type="button" @click="presensiOpen = !presensiOpen"
                        class="flex items-center w-full px-3 py-2 text-sm font-medium rounded-lg transition
            {{ $presensiActive ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}">

                        {{-- Icon --}}
                        <svg class="flex-shrink-0 w-5 h-5" :class="{ 'mr-3': desktopOpen || isMobile }" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>

                        {{-- Label + chevron --}}
                        <span x-show="desktopOpen || isMobile" class="flex-1 text-left">Presensi</span>
                        <svg x-show="desktopOpen || isMobile"
                            class="flex-shrink-0 w-4 h-4 transition-transform duration-200"
                            :class="presensiOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    {{-- Sub Menu --}}
                    <div x-show="presensiOpen && (desktopOpen || isMobile)"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1"
                        class="mt-1 ml-4 pl-3 border-l-2 border-gray-100 space-y-0.5">

                        {{-- Presensi --}}
                        <a href="{{ route('presensi.index') }}"
                            class="flex items-center gap-2.5 px-3 py-2 text-sm rounded-lg transition
                {{ request()->routeIs('presensi.*') ? 'bg-teal-50 text-teal-700 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-800' }}">
                            <svg class="flex-shrink-0 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                            Manajemen Presensi
                        </a>

                        {{-- Foto Presensi --}}
                        <a href="{{ route('foto-presensi.index') }}"
                            class="flex items-center gap-2.5 px-3 py-2 text-sm rounded-lg transition
                {{ request()->routeIs('foto-presensi.*') ? 'bg-teal-50 text-teal-700 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-800' }}">
                            <svg class="flex-shrink-0 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Foto Presensi
                        </a>

                        {{-- Foto Closing --}}
                        <a href="{{ route('foto-closing.index') }}"
                            class="flex items-center gap-2.5 px-3 py-2 text-sm rounded-lg transition
                {{ request()->routeIs('foto-closing.*') ? 'bg-teal-50 text-teal-700 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-800' }}">
                            <svg class="flex-shrink-0 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                            </svg>
                            Foto Closing
                        </a>

                    </div>
                </div>
            @endrole

            {{--
    =================================================================
    FILE TUJUAN : resources/views/layouts/sidebar.blade.php
    AKSI        : TAMBAH blok BARU ini — sarankan taruh SEBELUM section
                  "KPI & Data Resmi" yang sudah ada dari sesi sebelumnya
                  (section itu tetap dipertahankan apa adanya, ini section
                  terpisah khusus untuk fitur Sistem Tugas & KPI Grup A-E).
    =================================================================
--}}

            {{-- ============================================================ --}}
            {{-- SISTEM TUGAS & KPI (BARU)                                      --}}
            {{-- ============================================================ --}}
            <div class="pt-2 pb-2 border-t border-gray-200" x-show="desktopOpen || isMobile">
                <p class="px-3 mb-2 text-xs font-semibold tracking-wider text-gray-500 uppercase">Sistem Tugas & KPI
                </p>
            </div>

            {{-- Tugas Saya & KPI Saya — SEMUA orang punya ini, tetap flat --}}
            <a href="{{ route('tasks.my-tasks') }}"
                class="flex items-center px-3 py-2 text-sm font-medium rounded-lg group {{ request()->routeIs('tasks.my-tasks') ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="flex-shrink-0 w-5 h-5" :class="{ 'mr-3': desktopOpen || isMobile }" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
                <span x-show="desktopOpen || isMobile">Tugas Saya</span>
            </a>

            <a href="{{ route('tasks.my-kpi') }}"
                class="flex items-center px-3 py-2 text-sm font-medium rounded-lg group {{ request()->routeIs('tasks.my-kpi') ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="flex-shrink-0 w-5 h-5" :class="{ 'mr-3': desktopOpen || isMobile }" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span x-show="desktopOpen || isMobile">KPI Saya</span>
            </a>

            {{-- ============================================================ --}}
            {{-- DROPDOWN: Kelola Tugas Tim                                     --}}
            {{-- (Bikin Tugas KPI, Tugas Yang diberikan, Perlu Saya Cek, Perlu Saya Approve) --}}
            {{-- ============================================================ --}}
            @role('manager|korwil|kadiv|ketua|direktur_utama|direktur_bisnis|direktur_operasional|superadmin')
                @php
                    $kelolaTugasActive =
                        request()->routeIs('tasks.create') ||
                        request()->routeIs('tasks.assigned') ||
                        request()->routeIs('task-check-queue.*') ||
                        request()->routeIs('task-approve-queue.*');
                @endphp

                <div x-data="{ kelolaTugasOpen: {{ $kelolaTugasActive ? 'true' : 'false' }} }">
                    <button type="button" @click="kelolaTugasOpen = !kelolaTugasOpen"
                        class="flex items-center w-full px-3 py-2 text-sm font-medium rounded-lg transition
            {{ $kelolaTugasActive ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}">

                        <svg class="flex-shrink-0 w-5 h-5" :class="{ 'mr-3': desktopOpen || isMobile }" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>

                        <span x-show="desktopOpen || isMobile" class="flex-1 text-left">Kelola Tugas Tim</span>
                        <svg x-show="desktopOpen || isMobile"
                            class="flex-shrink-0 w-4 h-4 transition-transform duration-200"
                            :class="kelolaTugasOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="kelolaTugasOpen && (desktopOpen || isMobile)"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1"
                        class="mt-1 ml-4 pl-3 border-l-2 border-gray-100 space-y-0.5">

                        @role('ketua|direktur_utama|direktur_bisnis|direktur_operasional|kadiv|korwil|superadmin')
                            <a href="{{ route('tasks.create') }}"
                                class="flex items-center gap-2.5 px-3 py-2 text-sm rounded-lg transition
                    {{ request()->routeIs('tasks.create') ? 'bg-teal-50 text-teal-700 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-800' }}">
                                <svg class="flex-shrink-0 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                Bikin Tugas KPI
                            </a>

                            <a href="{{ route('tasks.assigned') }}"
                                class="flex items-center gap-2.5 px-3 py-2 text-sm rounded-lg transition
                    {{ request()->routeIs('tasks.assigned') ? 'bg-teal-50 text-teal-700 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-800' }}">
                                <svg class="flex-shrink-0 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                                Tugas Yang diberikan
                            </a>
                        @endrole

                        @role('manager|korwil|kadiv|ketua|direktur_utama|direktur_bisnis|direktur_operasional|superadmin')
                            <a href="{{ route('task-check-queue.index') }}"
                                class="flex items-center gap-2.5 px-3 py-2 text-sm rounded-lg transition
                    {{ request()->routeIs('task-check-queue.*') ? 'bg-teal-50 text-teal-700 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-800' }}">
                                <svg class="flex-shrink-0 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Perlu Saya Cek
                            </a>

                            <a href="{{ route('task-approve-queue.index') }}"
                                class="flex items-center gap-2.5 px-3 py-2 text-sm rounded-lg transition
                    {{ request()->routeIs('task-approve-queue.*') ? 'bg-teal-50 text-teal-700 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-800' }}">
                                <svg class="flex-shrink-0 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Perlu Saya Approve
                            </a>
                        @endrole
                    </div>
                </div>
            @endrole

            {{-- ============================================================ --}}
            {{-- DROPDOWN: Laporan & Monitoring                                 --}}
            {{-- (KPI Tim, Set Hari Kerja)                                      --}}
            {{-- ============================================================ --}}
            @role('ketua|direktur_utama|direktur_bisnis|direktur_operasional|kadiv|korwil|superadmin')
                @php
                    $laporanActive =
                        request()->routeIs('tasks.team-drilldown') || request()->routeIs('period-working-days.*');
                @endphp

                <div x-data="{ laporanOpen: {{ $laporanActive ? 'true' : 'false' }} }">
                    <button type="button" @click="laporanOpen = !laporanOpen"
                        class="flex items-center w-full px-3 py-2 text-sm font-medium rounded-lg transition
            {{ $laporanActive ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}">

                        <svg class="flex-shrink-0 w-5 h-5" :class="{ 'mr-3': desktopOpen || isMobile }" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>

                        <span x-show="desktopOpen || isMobile" class="flex-1 text-left">Laporan & Monitoring</span>
                        <svg x-show="desktopOpen || isMobile"
                            class="flex-shrink-0 w-4 h-4 transition-transform duration-200"
                            :class="laporanOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="laporanOpen && (desktopOpen || isMobile)"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1"
                        class="mt-1 ml-4 pl-3 border-l-2 border-gray-100 space-y-0.5">

                        @role('ketua|direktur_utama|direktur_bisnis|direktur_operasional|kadiv|korwil|superadmin')
                            <a href="{{ route('tasks.team-drilldown') }}"
                                class="flex items-center gap-2.5 px-3 py-2 text-sm rounded-lg transition
                    {{ request()->routeIs('tasks.team-drilldown') ? 'bg-teal-50 text-teal-700 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-800' }}">
                                <svg class="flex-shrink-0 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                KPI Tim
                            </a>
                        @endrole

                        @role('superadmin|ketua|direktur_utama|direktur_bisnis|direktur_operasional')
                            <a href="{{ route('period-working-days.index') }}"
                                class="flex items-center gap-2.5 px-3 py-2 text-sm rounded-lg transition
                    {{ request()->routeIs('period-working-days.*') ? 'bg-teal-50 text-teal-700 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-800' }}">
                                <svg class="flex-shrink-0 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Set Hari Kerja
                            </a>
                        @endrole
                    </div>
                </div>
            @endrole

            {{-- ============================================================ --}}
            {{-- DROPDOWN: Administrasi (Superadmin only)                       --}}
            {{-- (Kelola Divisi, Panel Koreksi Data)                            --}}
            {{-- ============================================================ --}}
            @role('superadmin')
                @php
                    $administrasiActive =
                        request()->routeIs('divisions.*') || request()->routeIs('superadmin.koreksi-data.*');
                @endphp

                <div x-data="{ administrasiOpen: {{ $administrasiActive ? 'true' : 'false' }} }">
                    <button type="button" @click="administrasiOpen = !administrasiOpen"
                        class="flex items-center w-full px-3 py-2 text-sm font-medium rounded-lg transition
            {{ $administrasiActive ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}">

                        <svg class="flex-shrink-0 w-5 h-5" :class="{ 'mr-3': desktopOpen || isMobile }" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>

                        <span x-show="desktopOpen || isMobile" class="flex-1 text-left">Administrasi</span>
                        <svg x-show="desktopOpen || isMobile"
                            class="flex-shrink-0 w-4 h-4 transition-transform duration-200"
                            :class="administrasiOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="administrasiOpen && (desktopOpen || isMobile)"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1"
                        class="mt-1 ml-4 pl-3 border-l-2 border-gray-100 space-y-0.5">

                        <a href="{{ route('divisions.index') }}"
                            class="flex items-center gap-2.5 px-3 py-2 text-sm rounded-lg transition
                {{ request()->routeIs('divisions.*') ? 'bg-teal-50 text-teal-700 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-800' }}">
                            <svg class="flex-shrink-0 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            Kelola Divisi
                        </a>

                        <a href="{{ route('superadmin.koreksi-data.index') }}"
                            class="flex items-center gap-2.5 px-3 py-2 text-sm rounded-lg transition
                {{ request()->routeIs('superadmin.koreksi-data.*') ? 'bg-teal-50 text-teal-700 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-800' }}">
                            <svg class="flex-shrink-0 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Panel Koreksi Data
                        </a>
                    </div>
                </div>
            @endrole

            {{-- KPI & Data Resmi — dibiarkan flat seperti semula, tidak diubah --}}
            @role('superadmin|kadiv|owner|ketua|direktur_utama|direktur_bisnis|direktur_operasional|staff')
                <div class="pt-2 pb-2 border-t border-gray-200" x-show="desktopOpen || isMobile">
                    <p class="px-3 mb-2 text-xs font-semibold tracking-wider text-gray-500 uppercase">KPI & Data Resmi</p>
                </div>

                @role('kadiv|superadmin')
                    <a href="{{ route('data-resmi.index') }}"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-lg group {{ request()->routeIs('data-resmi.*') ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}">
                        <svg class="flex-shrink-0 w-5 h-5" :class="{ 'mr-3': desktopOpen || isMobile }" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        <span x-show="desktopOpen || isMobile">Upload Data Resmi</span>
                    </a>
                @endrole

                @role('superadmin')
                    <a href="{{ route('kpi-roles.index') }}"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-lg group {{ request()->routeIs('kpi-roles.*') ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}">
                        <svg class="flex-shrink-0 w-5 h-5" :class="{ 'mr-3': desktopOpen || isMobile }" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975M15 9.75a3 3 0 11-6 0 3 3 0 016 0zM21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span x-show="desktopOpen || isMobile">Set Role KPI</span>
                    </a>
                @endrole

                @role('superadmin|marketing|owner|ketua|direktur_utama|direktur_bisnis|direktur_operasional|kadiv|staff')
                    <a href="{{ route('rekonsiliasi.index') }}"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-lg group {{ request()->routeIs('rekonsiliasi.*') ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}">
                        <svg class="flex-shrink-0 w-5 h-5" :class="{ 'mr-3': desktopOpen || isMobile }" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span x-show="desktopOpen || isMobile">Rekonsiliasi</span>
                    </a>
                @endrole
            @endrole

            {{-- ============================================================ --}}
            {{-- Marketing                                                       --}}
            {{-- ============================================================ --}}
            @role('fo|marketing|superadmin')
                <div class="pt-2 pb-2 border-t border-gray-200" x-show="desktopOpen || isMobile">
                    <p class="px-3 mb-2 text-xs font-semibold tracking-wider text-gray-500 uppercase">Marketing</p>
                </div>

                <a href="{{ route('daily-contents.index') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-lg group {{ request()->routeIs('daily-contents.*') ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="flex-shrink-0 w-5 h-5" :class="{ 'mr-3': desktopOpen || isMobile }" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                    </svg>
                    <span x-show="desktopOpen || isMobile">Daily Content</span>
                </a>
            @endrole

            {{-- ============================================================ --}}
            {{-- Contact 90                                                      --}}
            {{-- ============================================================ --}}
            @role('marketing|superadmin')
                <div class="pt-2 pb-2 border-t border-gray-200" x-show="desktopOpen || isMobile">
                    <p class="px-3 mb-2 text-xs font-semibold tracking-wider text-gray-500 uppercase">Contact 90</p>
                </div>

                <a href="{{ route('contact90.index') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-lg group {{ request()->routeIs('contact90.index|contact90.create|contact90.edit') ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="flex-shrink-0 w-5 h-5" :class="{ 'mr-3': desktopOpen || isMobile }" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span x-show="desktopOpen || isMobile">My Contacts</span>
                </a>

                @role('superadmin')
                    <a href="{{ route('contact90.manager.dashboard') }}"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-lg group {{ request()->routeIs('contact90.manager.*') ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}">
                        <svg class="flex-shrink-0 w-5 h-5" :class="{ 'mr-3': desktopOpen || isMobile }" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span x-show="desktopOpen || isMobile">Manager - Contact 90</span>
                    </a>
                    {{-- <a href="{{ route('kpi-roles.index') }}"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-lg group {{ request()->routeIs('kpi-roles.*') ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}">
                        <svg class="flex-shrink-0 w-5 h-5" :class="{ 'mr-3': desktopOpen || isMobile }" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span x-show="desktopOpen || isMobile">KPI Roles</span>
                    </a> --}}
                @endrole
            @endrole

            {{-- ============================================================ --}}
            {{-- Daily Reports                                                   --}}
            {{-- ============================================================ --}}
            @role('fo|manager|superadmin')
                <div class="pt-2 pb-2 border-t border-gray-200" x-show="desktopOpen || isMobile">
                    <p class="px-3 mb-2 text-xs font-semibold tracking-wider text-gray-500 uppercase">Daily Reports</p>
                </div>

                <a href="{{ route('daily-reports.index') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-lg group {{ request()->routeIs('daily-reports.*') ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="flex-shrink-0 w-5 h-5" :class="{ 'mr-3': desktopOpen || isMobile }" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span x-show="desktopOpen || isMobile">Daily Reports</span>
                </a>

                @role('manager|superadmin')
                    <a href="{{ route('daily-reports.manager.dashboard') }}"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-lg group {{ request()->routeIs('daily-reports.manager.*') ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}">
                        <svg class="flex-shrink-0 w-5 h-5" :class="{ 'mr-3': desktopOpen || isMobile }" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span x-show="desktopOpen || isMobile">Manager - Daily Reports</span>
                    </a>
                @endrole
            @endrole

            {{-- ============================================================ --}}
            {{-- Daily Reports FO                                                --}}
            {{-- ============================================================ --}}
            @role('fo|manager|superadmin|marketing|owner|ketua|direktur_utama|direktur_bisnis|direktur_operasional|kadiv|staff')
                <div class="pt-2 pb-2 border-t border-gray-200" x-show="desktopOpen || isMobile">
                    <p class="px-3 mb-2 text-xs font-semibold tracking-wider text-gray-500 uppercase">Daily Reports FO</p>
                </div>

                {{-- FO: dashboard upload --}}
                @role('fo')
                    <a href="{{ route('daily-reports-fo.index') }}"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-lg group {{ request()->routeIs('daily-reports-fo.index|daily-reports-fo.slot.*|daily-reports-fo.history') ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}">
                        <svg class="flex-shrink-0 w-5 h-5" :class="{ 'mr-3': desktopOpen || isMobile }" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span x-show="desktopOpen || isMobile">FO Dashboard</span>
                    </a>
                @endrole

                {{-- Manager: FO Reports + Validasi --}}
                @role('manager|superadmin|marketing|owner|ketua|direktur_utama|direktur_bisnis|direktur_operasional|kadiv|staff')
                    {{-- <a href="{{ route('daily-reports-fo.manager.dashboard') }}"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-lg group {{ request()->routeIs('daily-reports-fo.manager.*') ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}">
                        <svg class="flex-shrink-0 w-5 h-5" :class="{ 'mr-3': desktopOpen || isMobile }" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span x-show="desktopOpen || isMobile">Manager - FO Reports</span>
                    </a> --}}

                    {{-- Validasi Laporan FO --}}
                    <a href="{{ route('validation.index') }}"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-lg group {{ request()->routeIs('validation.*') ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}">
                        <svg class="flex-shrink-0 w-5 h-5" :class="{ 'mr-3': desktopOpen || isMobile }" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span x-show="desktopOpen || isMobile">Validasi FO</span>
                    </a>
                @endrole

                {{-- Marketing: FO Reports (read-only) + Validasi (read-only) --}}
                {{-- @role('marketing')
                    <a href="{{ route('daily-reports-fo.marketing.dashboard') }}"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-lg group {{ request()->routeIs('daily-reports-fo.marketing.*') ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}">
                        <svg class="flex-shrink-0 w-5 h-5" :class="{ 'mr-3': desktopOpen || isMobile }" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span x-show="desktopOpen || isMobile">Marketing - FO Reports</span>
                    </a>

                    <a href="{{ route('validation.index') }}"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-lg group {{ request()->routeIs('validation.*') ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}">
                        <svg class="flex-shrink-0 w-5 h-5" :class="{ 'mr-3': desktopOpen || isMobile }" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <span x-show="desktopOpen || isMobile">Lihat Validasi FO</span>
                    </a>
                @endrole --}}

                {{-- Daily Reports Manager (3 jam) — manager, superadmin, marketing --}}
                {{-- @role('manager|superadmin|marketing')
                    <a href="{{ route('daily-reports.3hour-manager.index') }}"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-lg group {{ request()->routeIs('daily-reports-3hour-manager.*') ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}">
                        <svg class="flex-shrink-0 w-5 h-5" :class="{ 'mr-3': desktopOpen || isMobile }" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span x-show="desktopOpen || isMobile">Daily Reports Manager</span>
                    </a>
            @endrole --}}

            @endrole

            {{-- ============================================================ --}}
            {{-- Pengaturan — Master Data                                        --}}
            {{-- Hanya superadmin & marketing yang bisa edit                    --}}
            {{-- ============================================================ --}}
            @role('superadmin|marketing')
                <div class="pt-2 pb-2 border-t border-gray-200" x-show="desktopOpen || isMobile">
                    <p class="px-3 mb-2 text-xs font-semibold tracking-wider text-gray-500 uppercase">Pengaturan</p>
                </div>

                <a href="{{ route('master.categories.index') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-lg group {{ request()->routeIs('master.categories.*') ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="flex-shrink-0 w-5 h-5" :class="{ 'mr-3': desktopOpen || isMobile }" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                    <span x-show="desktopOpen || isMobile">Kategori Laporan</span>
                </a>

                <a href="{{ route('master.fields.index') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-lg group {{ request()->routeIs('master.fields.*') ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="flex-shrink-0 w-5 h-5" :class="{ 'mr-3': desktopOpen || isMobile }" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span x-show="desktopOpen || isMobile">Field Laporan</span>
                </a>

                <a href="{{ route('master.validation-actions.index') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-lg group {{ request()->routeIs('master.validation-actions.*') ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="flex-shrink-0 w-5 h-5" :class="{ 'mr-3': desktopOpen || isMobile }" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span x-show="desktopOpen || isMobile">Tindakan Validasi</span>
                </a>
            @endrole

        </nav>

        <!-- User Profile (Bottom) -->
        <div class="p-3 border-t border-gray-200 shrink-0">
            <div class="relative flex items-center" x-data="{ showDropdown: false }">
                <img src="{{ Auth::user()->profile_photo ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) }}"
                    alt="{{ Auth::user()->name }}" class="flex-shrink-0 w-8 h-8 rounded-full">

                <div x-show="desktopOpen || isMobile" class="flex-1 min-w-0 ml-3">
                    <p class="text-sm font-medium text-gray-700 truncate">{{ Auth::user()->name }}</p>
                    @php
                        $roles = Auth::user()->getRoleNames();
                    @endphp
                    <p class="text-xs text-gray-500 truncate">{{ $roles->get(1, $roles->first()) }}</p>
                </div>

                <button @click="showDropdown = !showDropdown" x-show="desktopOpen || isMobile"
                    class="flex-shrink-0 p-1 text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                    </svg>
                </button>

                <!-- Dropdown Menu -->
                <div x-show="showDropdown" @click.away="showDropdown = false"
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="transform opacity-0 scale-95"
                    x-transition:enter-end="transform opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="transform opacity-100 scale-100"
                    x-transition:leave-end="transform opacity-0 scale-95"
                    class="absolute z-50 w-48 mb-2 bg-white rounded-lg shadow-lg bottom-full left-4 ring-1 ring-black ring-opacity-5">
                    <a href="{{ route('profile.edit') }}"
                        class="block px-4 py-2 text-sm text-gray-700 rounded-t-lg hover:bg-gray-100">
                        Profile
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="block w-full px-4 py-2 text-sm text-left text-red-700 rounded-b-lg hover:bg-gray-100">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex flex-col flex-1 overflow-hidden">
        <!-- Mobile Top Bar -->
        <div class="flex items-center justify-between h-16 px-4 bg-white border-b border-gray-200 lg:hidden shrink-0">
            <button @click="sidebarOpen = true" class="p-2 text-gray-500 rounded-lg hover:bg-gray-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            <img src="{{ asset('assets/logo-intira.png') }}" alt="Logo" class="block w-auto h-8">
            <div class="w-10"></div>
        </div>

        <!-- Header (Mobile & Desktop) -->
        <header class="bg-white border-b border-gray-200 shrink-0">
            <div class="px-4 py-3 sm:px-6">
                @isset($header)
                    {{ $header }}
                @endisset
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
            {{ $slot }}
        </main>
    </div>
</div>
