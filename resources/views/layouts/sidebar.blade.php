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
                <x-application-logo class="block w-auto text-gray-800 fill-current h-9" />
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

            <!-- HR Section -->
            @role('hr|superadmin')
                <div class="pt-2 pb-2 border-t border-gray-200" x-show="desktopOpen || isMobile">
                    <p class="px-3 mb-2 text-xs font-semibold tracking-wider text-gray-500 uppercase">
                        HR Management
                    </p>
                </div>

                <!-- Users -->
                <a href="{{ route('users.index') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-lg group {{ request()->routeIs('users.*') ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="flex-shrink-0 w-5 h-5" :class="{ 'mr-3': desktopOpen || isMobile }" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span x-show="desktopOpen || isMobile">User</span>
                </a>

                <!-- Branches -->
                <a href="{{ route('branches.index') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-lg group {{ request()->routeIs('branches.*') ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="flex-shrink-0 w-5 h-5" :class="{ 'mr-3': desktopOpen || isMobile }" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span x-show="desktopOpen || isMobile">Branch</span>
                </a>

                <!-- Gaji -->
                <a href="{{ route('gaji-pokok.index') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-lg group {{ request()->routeIs('gaji-pokok.*') ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="flex-shrink-0 w-5 h-5" :class="{ 'mr-3': desktopOpen || isMobile }" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span x-show="desktopOpen || isMobile">Gaji</span>
                </a>

                <!-- Presensi -->
                <a href="{{ route('presensi.index') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-lg group {{ request()->routeIs('presensi.*') ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="flex-shrink-0 w-5 h-5" :class="{ 'mr-3': desktopOpen || isMobile }" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    <span x-show="desktopOpen || isMobile">Presensi</span>
                </a>
            @endrole

            <!-- Marketing Section -->
            @role('marketing|superadmin')
                <div class="pt-2 pb-2 border-t border-gray-200" x-show="desktopOpen || isMobile">
                    <p class="px-3 mb-2 text-xs font-semibold tracking-wider text-gray-500 uppercase">
                        Marketing
                    </p>
                </div>

                <!-- Daily Content -->
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

            <!-- Contact 90 Section -->
            @role('fo|manager|superadmin')
                <div class="pt-2 pb-2 border-t border-gray-200" x-show="desktopOpen || isMobile">
                    <p class="px-3 mb-2 text-xs font-semibold tracking-wider text-gray-500 uppercase">
                        Contact 90
                    </p>
                </div>

                <!-- My Contacts -->
                <a href="{{ route('contact90.index') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-lg group {{ request()->routeIs('contact90.index') || request()->routeIs('contact90.create') || request()->routeIs('contact90.edit') ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="flex-shrink-0 w-5 h-5" :class="{ 'mr-3': desktopOpen || isMobile }" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span x-show="desktopOpen || isMobile">My Contacts</span>
                </a>

                @role('manager|superadmin')
                    <!-- Manager Dashboard -->
                    <a href="{{ route('contact90.manager.dashboard') }}"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-lg group {{ request()->routeIs('contact90.manager.*') ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}">
                        <svg class="flex-shrink-0 w-5 h-5" :class="{ 'mr-3': desktopOpen || isMobile }" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span x-show="desktopOpen || isMobile">Manager- Contact 90</span>
                    </a>
                @endrole
                <div class="pt-2 pb-2 border-t border-gray-200" x-show="desktopOpen || isMobile">
                    <p class="px-3 mb-2 text-xs font-semibold tracking-wider text-gray-500 uppercase">
                        Daily Reports
                    </p>
                </div>

                <!-- Daily Reports -->
                <a href="{{ route('daily-reports.index') }}"
                    class="flex items-center px-3 py-2 text-sm font-medium rounded-lg group {{ request()->routeIs('daily-reports.index') || request()->routeIs('daily-reports.create') || request()->routeIs('daily-reports.edit') ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="flex-shrink-0 w-5 h-5" :class="{ 'mr-3': desktopOpen || isMobile }" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span x-show="desktopOpen || isMobile">Daily Reports</span>
                </a>

                @role('manager|superadmin')
                    <!-- Manager Dashboard -->
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

        </nav>

        <!-- User Profile (Bottom) -->
        <div class="p-3 border-t border-gray-200 shrink-0">
            <div class="relative flex items-center" x-data="{ showDropdown: false }">
                <img src="{{ Auth::user()->profile_photo ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) }}"
                    alt="{{ Auth::user()->name }}" class="flex-shrink-0 w-8 h-8 rounded-full">

                <div x-show="desktopOpen || isMobile" class="flex-1 min-w-0 ml-3">
                    <p class="text-sm font-medium text-gray-700 truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ Auth::user()->getRoleNames()->first() }}</p>
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
            <x-application-logo class="block w-auto h-8 text-gray-800 fill-current" />
            <div class="w-10"></div> <!-- Spacer for centering logo -->
        </div>

        <!-- Desktop Header -->
        <header class="hidden bg-white border-b border-gray-200 lg:block shrink-0">
            <div class="px-6 py-3">
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
{{-- ```

---

## ✅ **Perbaikan yang Dilakukan:**

### **1. Responsive Design:**
- ✅ **Mobile (<1024px):** Sidebar slide dari kiri dengan overlay
- ✅ **Desktop (≥1024px):** Sidebar collapsible (64px ↔ 256px)
- ✅ **Hamburger Menu** di mobile
- ✅ **Smooth transitions** semua state

### **2. Mobile Behavior:**
```
Mobile:
- Sidebar hidden by default
- Click hamburger → sidebar slides in
- Click overlay → sidebar slides out
- Full width sidebar (256px)

Desktop:
- Sidebar visible by default
- Click toggle → minimize (64px) / expand (256px)
- Icons only mode saat minimize
```

### **3. Fixed Issues:**
| Issue | Before | After |
|-------|--------|-------|
| **Mobile sidebar** | Selalu visible, menutupi konten | Hidden, slide in saat klik hamburger |
| **Header di mobile** | Tidak ada | Ada top bar dengan hamburger menu |
| **Overflow** | Sidebar gede banget | Proper z-index & transitions |
| **Icon spacing** | Tidak konsisten | Flex-shrink-0 + conditional margin |
| **Profile dropdown** | Positioning error | Fixed with absolute positioning |

### **4. UX Improvements:**
- ✅ **Smooth transitions** (300ms ease)
- ✅ **Click outside to close** (dropdown & mobile sidebar)
- ✅ **Overlay backdrop** di mobile
- ✅ **Truncate text** untuk nama panjang
- ✅ **Responsive logo** di mobile top bar

---

## 📱 **Preview Behavior:**

### **Desktop (≥1024px):**
```
┌─────────┬────────────────────────────┐
│         │ Header                     │
│ SIDEBAR ├────────────────────────────┤
│  [<-]   │                            │
│ (Full)  │     MAIN CONTENT           │
│         │                            │
└─────────┴────────────────────────────┘

Click [<-] →

┌──┬─────────────────────────────────┐
│  │ Header                          │
│[]├─────────────────────────────────┤
│  │                                 │
│  │     MAIN CONTENT (Wider)        │
│  │                                 │
└──┴─────────────────────────────────┘
```

### **Mobile (<1024px):**
```
Default:
┌─────────────────────┐
│ [☰] LOGO        [ ] │
├─────────────────────┤
│                     │
│   MAIN CONTENT      │
│   (Full Width)      │
│                     │
└─────────────────────┘

Click [☰] →

┌────────┬────────────┐
│SIDEBAR │ [Overlay]  │
│        │            │
│  Full  │   Dimmed   │
│  Menu  │   Content  │
│        │            │
└────────┴────────────┘ --}}
