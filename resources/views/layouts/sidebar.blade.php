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

            {{-- ============================================================ --}}
            {{-- KARYAWAN (flat — dipakai semua orang)                          --}}
            {{-- ============================================================ --}}
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
            {{-- SISTEM TUGAS & KPI                                             --}}
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

            @role('superadmin|owner|ketua|direktur_utama|direktur_bisnis|direktur_operasional|kadiv|korwil|staff|manager|fo')
                <a href="{{ route('panduan-sistem.index') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-lg group {{ request()->routeIs('panduan-sistem.*') ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="flex-shrink-0 w-5 h-5" :class="{ 'mr-3': desktopOpen || isMobile }" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <span x-show="desktopOpen || isMobile">Panduan Sistem</span>
                </a>
            @endrole

            {{-- ============================================================ --}}
            {{-- DROPDOWN: Tugas Tim                                            --}}
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

                        <span x-show="desktopOpen || isMobile" class="flex-1 text-left">Tugas Tim</span>
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
            {{-- DROPDOWN: Laporan & Pengaturan KPI                             --}}
            {{-- (KPI Tim, Set Hari Kerja, Upload Data Resmi, Set Role KPI,     --}}
            {{--  Kelola Divisi, Panel Koreksi Data)                            --}}
            {{-- ============================================================ --}}
            @role('ketua|direktur_utama|direktur_bisnis|direktur_operasional|kadiv|korwil|superadmin|staff|owner')
                @php
                    $laporanActive =
                        request()->routeIs('tasks.team-drilldown') ||
                        request()->routeIs('period-working-days.*') ||
                        request()->routeIs('kpi-roles.*') ||
                        request()->routeIs('divisions.*') ||
                        request()->routeIs('superadmin.koreksi-data.*');
                @endphp

                <div x-data="{ laporanOpen: {{ $laporanActive ? 'true' : 'false' }} }">
                    <button type="button" @click="laporanOpen = !laporanOpen"
                        class="flex items-center w-full px-3 py-2 text-sm font-medium rounded-lg transition
            {{ $laporanActive ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}">

                        <svg class="flex-shrink-0 w-5 h-5" :class="{ 'mr-3': desktopOpen || isMobile }" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>

                        <span x-show="desktopOpen || isMobile" class="flex-1 text-left">Laporan & Pengaturan KPI</span>
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

                        {{-- KPI Tim (drill-down) --}}
                        @role('ketua|direktur_utama|direktur_bisnis|direktur_operasional|kadiv|korwil|superadmin|staff|owner')
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

                        {{-- Set Hari Kerja --}}
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

                        {{-- Set Role KPI --}}
                        @role('superadmin')
                            <a href="{{ route('kpi-roles.index') }}"
                                class="flex items-center gap-2.5 px-3 py-2 text-sm rounded-lg transition
                    {{ request()->routeIs('kpi-roles.*') ? 'bg-teal-50 text-teal-700 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-800' }}">
                                <svg class="flex-shrink-0 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975M15 9.75a3 3 0 11-6 0 3 3 0 016 0zM21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Set Role KPI
                            </a>

                            {{-- Kelola Divisi --}}
                            <a href="{{ route('divisions.index') }}"
                                class="flex items-center gap-2.5 px-3 py-2 text-sm rounded-lg transition
                    {{ request()->routeIs('divisions.*') ? 'bg-teal-50 text-teal-700 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-800' }}">
                                <svg class="flex-shrink-0 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                Kelola Divisi
                            </a>

                            {{-- Panel Koreksi Data --}}
                            <a href="{{ route('superadmin.koreksi-data.index') }}"
                                class="flex items-center gap-2.5 px-3 py-2 text-sm rounded-lg transition
                    {{ request()->routeIs('superadmin.koreksi-data.*') ? 'bg-teal-50 text-teal-700 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-800' }}">
                                <svg class="flex-shrink-0 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Panel Koreksi Data
                            </a>
                        @endrole
                    </div>
                </div>
            @endrole

            {{-- ============================================================ --}}
            {{-- DROPDOWN: HR Management                                        --}}
            {{-- (User, Branch, Gaji, Manajemen Presensi, Foto Presensi, Foto Closing) --}}
            {{-- ============================================================ --}}
            @role('hr|superadmin')
                @php
                    $hrActive =
                        request()->routeIs('users.*') ||
                        request()->routeIs('branches.*') ||
                        request()->routeIs('gaji-pokok.*') ||
                        request()->routeIs('presensi.*') ||
                        request()->routeIs('foto-presensi.*') ||
                        request()->routeIs('foto-closing.*');
                @endphp

                <div x-data="{ hrOpen: {{ $hrActive ? 'true' : 'false' }} }">
                    <button type="button" @click="hrOpen = !hrOpen"
                        class="flex items-center w-full px-3 py-2 text-sm font-medium rounded-lg transition
            {{ $hrActive ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}">

                        <svg class="flex-shrink-0 w-5 h-5" :class="{ 'mr-3': desktopOpen || isMobile }" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>

                        <span x-show="desktopOpen || isMobile" class="flex-1 text-left">HR Management</span>
                        <svg x-show="desktopOpen || isMobile"
                            class="flex-shrink-0 w-4 h-4 transition-transform duration-200"
                            :class="hrOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="hrOpen && (desktopOpen || isMobile)"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1"
                        class="mt-1 ml-4 pl-3 border-l-2 border-gray-100 space-y-0.5">

                        {{-- User --}}
                        <a href="{{ route('users.index') }}"
                            class="flex items-center gap-2.5 px-3 py-2 text-sm rounded-lg transition
                {{ request()->routeIs('users.*') ? 'bg-teal-50 text-teal-700 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-800' }}">
                            <svg class="flex-shrink-0 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            User
                        </a>

                        {{-- Branch --}}
                        <a href="{{ route('branches.index') }}"
                            class="flex items-center gap-2.5 px-3 py-2 text-sm rounded-lg transition
                {{ request()->routeIs('branches.*') ? 'bg-teal-50 text-teal-700 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-800' }}">
                            <svg class="flex-shrink-0 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            Branch
                        </a>

                        {{-- Gaji --}}
                        <a href="{{ route('gaji-pokok.index') }}"
                            class="flex items-center gap-2.5 px-3 py-2 text-sm rounded-lg transition
                {{ request()->routeIs('gaji-pokok.*') ? 'bg-teal-50 text-teal-700 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-800' }}">
                            <svg class="flex-shrink-0 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Gaji
                        </a>

                        {{-- Manajemen Presensi --}}
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

            {{-- ============================================================ --}}
            {{-- LAPORAN FO & BISNIS (flat)                                     --}}
            {{-- (FO Dashboard, Validasi FO, Data Resmi & Rekonsiliasi)         --}}
            {{-- ============================================================ --}}
            @role('fo|manager|korwil|superadmin|marketing|owner|ketua|direktur_utama|direktur_bisnis|direktur_operasional|kadiv|staff')
                <div class="pt-2 pb-2 border-t border-gray-200" x-show="desktopOpen || isMobile">
                    <p class="px-3 mb-2 text-xs font-semibold tracking-wider text-gray-500 uppercase">Laporan FO & Bisnis
                    </p>
                </div>

                {{-- FO: dashboard upload --}}
                @role('fo')
                    <a href="{{ route('daily-reports-fo.index') }}"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-lg group {{ request()->routeIs('daily-reports-fo.index', 'daily-reports-fo.slot.*', 'daily-reports-fo.history') ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}">
                        <svg class="flex-shrink-0 w-5 h-5" :class="{ 'mr-3': desktopOpen || isMobile }" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span x-show="desktopOpen || isMobile">FO Dashboard</span>
                    </a>
                @endrole

                {{-- Manager: FO Reports + Validasi --}}
                @role('manager|korwil|superadmin|marketing|owner|ketua|direktur_utama|direktur_bisnis|direktur_operasional|kadiv|staff')
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

                {{-- ======================================================== --}}
                {{-- DROPDOWN: Data Resmi & Rekonsiliasi                        --}}
                {{-- (Upload Data Resmi -> hasilnya masuk ke Rekonsiliasi)      --}}
                {{-- ======================================================== --}}
                @role('superadmin|kadiv|owner|ketua|direktur_utama|direktur_bisnis|direktur_operasional|staff')
                    @php
                        $dataResmiActive = request()->routeIs('data-resmi.*') || request()->routeIs('rekonsiliasi.*');
                    @endphp

                    <div x-data="{ dataResmiOpen: {{ $dataResmiActive ? 'true' : 'false' }} }">
                        <button type="button" @click="dataResmiOpen = !dataResmiOpen"
                            class="flex items-center w-full px-3 py-2 text-sm font-medium rounded-lg transition
                {{ $dataResmiActive ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}">

                            <svg class="flex-shrink-0 w-5 h-5" :class="{ 'mr-3': desktopOpen || isMobile }" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>

                            <span x-show="desktopOpen || isMobile" class="flex-1 text-left">Data Resmi & Rekonsiliasi</span>
                            <svg x-show="desktopOpen || isMobile"
                                class="flex-shrink-0 w-4 h-4 transition-transform duration-200"
                                :class="dataResmiOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="dataResmiOpen && (desktopOpen || isMobile)"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-1"
                            class="mt-1 ml-4 pl-3 border-l-2 border-gray-100 space-y-0.5">

                            {{-- Upload Data Resmi --}}
                            @role('kadiv|superadmin')
                                <a href="{{ route('data-resmi.index') }}"
                                    class="flex items-center gap-2.5 px-3 py-2 text-sm rounded-lg transition
                        {{ request()->routeIs('data-resmi.*') ? 'bg-teal-50 text-teal-700 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-800' }}">
                                    <svg class="flex-shrink-0 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    Upload Data Resmi
                                </a>
                            @endrole

                            {{-- Rekonsiliasi --}}
                            @role('superadmin|owner|ketua|direktur_utama|direktur_bisnis|direktur_operasional|kadiv|staff')
                                <a href="{{ route('rekonsiliasi.index') }}"
                                    class="flex items-center gap-2.5 px-3 py-2 text-sm rounded-lg transition
                        {{ request()->routeIs('rekonsiliasi.*') ? 'bg-teal-50 text-teal-700 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-800' }}">
                                    <svg class="flex-shrink-0 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Rekonsiliasi
                                </a>
                            @endrole
                        </div>
                    </div>
                @endrole
            @endrole

            {{-- ============================================================ --}}
            {{-- DROPDOWN: Pengaturan — Master Data                             --}}
            {{-- Hanya superadmin & marketing yang bisa edit                    --}}
            {{-- ============================================================ --}}
            @role('superadmin|marketing')
                @php
                    $pengaturanActive =
                        request()->routeIs('master.categories.*') ||
                        request()->routeIs('master.fields.*') ||
                        request()->routeIs('master.validation-actions.*');
                @endphp

                <div x-data="{ pengaturanOpen: {{ $pengaturanActive ? 'true' : 'false' }} }">
                    <button type="button" @click="pengaturanOpen = !pengaturanOpen"
                        class="flex items-center w-full px-3 py-2 text-sm font-medium rounded-lg transition
            {{ $pengaturanActive ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}">

                        <svg class="flex-shrink-0 w-5 h-5" :class="{ 'mr-3': desktopOpen || isMobile }" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>

                        <span x-show="desktopOpen || isMobile" class="flex-1 text-left">Pengaturan</span>
                        <svg x-show="desktopOpen || isMobile"
                            class="flex-shrink-0 w-4 h-4 transition-transform duration-200"
                            :class="pengaturanOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="pengaturanOpen && (desktopOpen || isMobile)"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1"
                        class="mt-1 ml-4 pl-3 border-l-2 border-gray-100 space-y-0.5">

                        <a href="{{ route('master.categories.index') }}"
                            class="flex items-center gap-2.5 px-3 py-2 text-sm rounded-lg transition
                {{ request()->routeIs('master.categories.*') ? 'bg-teal-50 text-teal-700 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-800' }}">
                            <svg class="flex-shrink-0 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                            </svg>
                            Kategori Laporan
                        </a>

                        <a href="{{ route('master.fields.index') }}"
                            class="flex items-center gap-2.5 px-3 py-2 text-sm rounded-lg transition
                {{ request()->routeIs('master.fields.*') ? 'bg-teal-50 text-teal-700 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-800' }}">
                            <svg class="flex-shrink-0 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Field Laporan
                        </a>

                        <a href="{{ route('master.validation-actions.index') }}"
                            class="flex items-center gap-2.5 px-3 py-2 text-sm rounded-lg transition
                {{ request()->routeIs('master.validation-actions.*') ? 'bg-teal-50 text-teal-700 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-800' }}">
                            <svg class="flex-shrink-0 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Tindakan Validasi
                        </a>
                    </div>
                </div>
            @endrole

            {{-- ============================================================ --}}
            {{-- DROPDOWN: Pembersihan Data (Superadmin only)                   --}}
            {{-- (Foto Presensi, Foto Daily Report FO, Lampiran)                --}}
            {{-- ============================================================ --}}
            @role('superadmin')
                @php
                    $cleanupActive =
                        request()->routeIs('superadmin.absensi.photo-cleanup.*') ||
                        request()->routeIs('superadmin.daily-report-fo.photo-cleanup.*') ||
                        request()->routeIs('superadmin.lampiran.*');
                @endphp

                <div x-data="{ cleanupOpen: {{ $cleanupActive ? 'true' : 'false' }} }">
                    <button type="button" @click="cleanupOpen = !cleanupOpen"
                        class="flex items-center w-full px-3 py-2 text-sm font-medium rounded-lg transition
            {{ $cleanupActive ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}">

                        {{-- Icon: trash --}}
                        <svg class="flex-shrink-0 w-5 h-5" :class="{ 'mr-3': desktopOpen || isMobile }" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>

                        <span x-show="desktopOpen || isMobile" class="flex-1 text-left">Pembersihan Data</span>
                        <svg x-show="desktopOpen || isMobile"
                            class="flex-shrink-0 w-4 h-4 transition-transform duration-200"
                            :class="cleanupOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="cleanupOpen && (desktopOpen || isMobile)"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1"
                        class="mt-1 ml-4 pl-3 border-l-2 border-gray-100 space-y-0.5">

                        {{-- Foto Presensi --}}
                        <a href="{{ route('superadmin.absensi.photo-cleanup.index') }}"
                            class="flex items-center gap-2.5 px-3 py-2 text-sm rounded-lg transition
                {{ request()->routeIs('superadmin.absensi.photo-cleanup.*') ? 'bg-teal-50 text-teal-700 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-800' }}">
                            <svg class="flex-shrink-0 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Foto Presensi
                        </a>

                        {{-- Foto Daily Report FO --}}
                        <a href="{{ route('superadmin.daily-report-fo.photo-cleanup.index') }}"
                            class="flex items-center gap-2.5 px-3 py-2 text-sm rounded-lg transition
                {{ request()->routeIs('superadmin.daily-report-fo.photo-cleanup.*') ? 'bg-teal-50 text-teal-700 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-800' }}">
                            <svg class="flex-shrink-0 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Foto Daily Report FO
                        </a>

                        {{-- Lampiran --}}
                        <a href="{{ route('superadmin.lampiran.index') }}"
                            class="flex items-center gap-2.5 px-3 py-2 text-sm rounded-lg transition
                {{ request()->routeIs('superadmin.lampiran.*') ? 'bg-teal-50 text-teal-700 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-800' }}">
                            <svg class="flex-shrink-0 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                            </svg>
                            Lampiran
                        </a>
                    </div>
                </div>
            @endrole


            {{-- ============================================================ --}}
            {{-- DROPDOWN: Arsip (Fitur Lama) — Marketing & Superadmin only     --}}
            {{-- Fitur sudah tidak dipakai; FO & AM tidak melihatnya lagi.      --}}
            {{-- ============================================================ --}}
            @role('marketing|superadmin')
                @php
                    $arsipActive =
                        request()->routeIs('daily-reports.*') ||
                        request()->routeIs('contact90.*') ||
                        request()->routeIs('daily-contents.*');
                @endphp

                <div x-data="{ arsipOpen: {{ $arsipActive ? 'true' : 'false' }} }">
                    <button type="button" @click="arsipOpen = !arsipOpen"
                        class="flex items-center w-full px-3 py-2 text-sm font-medium rounded-lg transition
            {{ $arsipActive ? 'bg-teal-50 text-teal-700' : 'text-gray-500 hover:bg-gray-50' }}">

                        {{-- Icon: archive --}}
                        <svg class="flex-shrink-0 w-5 h-5" :class="{ 'mr-3': desktopOpen || isMobile }" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                        </svg>

                        <span x-show="desktopOpen || isMobile" class="flex-1 text-left">Arsip (Fitur Lama)</span>
                        <svg x-show="desktopOpen || isMobile"
                            class="flex-shrink-0 w-4 h-4 transition-transform duration-200"
                            :class="arsipOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="arsipOpen && (desktopOpen || isMobile)"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1"
                        class="mt-1 ml-4 pl-3 border-l-2 border-gray-100 space-y-0.5">

                        {{-- Daily Reports (lama) — dulu fo|manager|superadmin, sekarang FO & AM dikeluarkan --}}
                        @role('superadmin')
                            <a href="{{ route('daily-reports.index') }}"
                                class="flex items-center gap-2.5 px-3 py-2 text-sm rounded-lg transition
                    {{ request()->routeIs('daily-reports.*') ? 'bg-teal-50 text-teal-700 font-medium' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700' }}">
                                <svg class="flex-shrink-0 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                Daily Reports
                            </a>

                            <a href="{{ route('daily-reports.manager.dashboard') }}"
                                class="flex items-center gap-2.5 px-3 py-2 text-sm rounded-lg transition
                    {{ request()->routeIs('daily-reports.manager.*') ? 'bg-teal-50 text-teal-700 font-medium' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700' }}">
                                <svg class="flex-shrink-0 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                Manager - Daily Reports
                            </a>
                        @endrole

                        {{-- Daily Content (lama) — dulu fo|marketing|superadmin, FO dikeluarkan --}}
                        @role('marketing|superadmin')
                            <a href="{{ route('daily-contents.index') }}"
                                class="flex items-center gap-2.5 px-3 py-2 text-sm rounded-lg transition
                    {{ request()->routeIs('daily-contents.*') ? 'bg-teal-50 text-teal-700 font-medium' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700' }}">
                                <svg class="flex-shrink-0 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                </svg>
                                Daily Content
                            </a>

                            {{-- Contact 90 (lama) --}}
                            <a href="{{ route('contact90.index') }}"
                                class="flex items-center gap-2.5 px-3 py-2 text-sm rounded-lg transition
                    {{ request()->routeIs('contact90.index', 'contact90.create', 'contact90.edit') ? 'bg-teal-50 text-teal-700 font-medium' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700' }}">
                                <svg class="flex-shrink-0 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                My Contacts
                            </a>

                            @role('superadmin')
                                <a href="{{ route('contact90.manager.dashboard') }}"
                                    class="flex items-center gap-2.5 px-3 py-2 text-sm rounded-lg transition
                    {{ request()->routeIs('contact90.manager.*') ? 'bg-teal-50 text-teal-700 font-medium' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700' }}">
                                    <svg class="flex-shrink-0 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                    Manager - Contact 90
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
                    </div>
                </div>
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
