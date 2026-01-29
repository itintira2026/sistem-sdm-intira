<x-app-layout>
    <x-slot name="header">
        <div class="grid items-center justify-between grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Contact 90 - Dashboard FO
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Target harian: 90 kontak yang berhasil dihubungi
                </p>
            </div>

            <div class="flex justify-start gap-3 md:justify-end">
                <a href="{{ route('contact90.create') }}"
                    class="flex items-center gap-2 px-4 py-2 text-white transition bg-teal-600 rounded-lg hover:bg-teal-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Input Kontak Baru
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            {{-- Alert Success --}}
            @if (session('success'))
                <div class="p-4 mb-6 text-green-700 bg-green-100 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            {{-- 📊 STATISTIK DASHBOARD --}}
            <div class="grid grid-cols-1 gap-6 mb-6 md:grid-cols-2 lg:grid-cols-4">
                {{-- Total Kontak Hari Ini --}}
                <div class="p-6 bg-white rounded-lg shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Kontak Hari Ini</p>
                            <p class="mt-2 text-3xl font-bold text-gray-800">
                                {{ $stats['total'] }}<span class="text-lg text-gray-400">/{{ $stats['target'] }}</span>
                            </p>
                            <div class="w-full h-2 mt-3 bg-gray-200 rounded-full">
                                <div class="h-2 bg-teal-600 rounded-full"
                                    style="width: {{ $stats['total'] > 0 ? min(($stats['total'] / $stats['target']) * 100, 100) : 0 }}%">
                                </div>
                            </div>
                        </div>
                        <div class="p-3 bg-teal-100 rounded-full">
                            <svg class="w-8 h-8 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Closing --}}
                <div class="p-6 bg-white rounded-lg shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Closing</p>
                            <p class="mt-2 text-3xl font-bold text-green-600">{{ $stats['closing'] }}</p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-full">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Tertarik --}}
                <div class="p-6 bg-white rounded-lg shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Tertarik</p>
                            <p class="mt-2 text-3xl font-bold text-yellow-600">{{ $stats['tertarik'] }}</p>
                        </div>
                        <div class="p-3 bg-yellow-100 rounded-full">
                            <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Status Validasi --}}
                <div class="p-6 bg-white rounded-lg shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Status Validasi</p>
                            <p class="mt-2 text-sm text-gray-600">
                                ✅ Sudah: <span class="font-bold text-green-600">{{ $stats['validated'] }}</span>
                            </p>
                            <p class="text-sm text-gray-600">
                                ⏳ Belum: <span class="font-bold text-orange-600">{{ $stats['pending'] }}</span>
                            </p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-full">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 📋 DAFTAR KONTAK --}}
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="mb-6 text-lg font-semibold text-gray-800">
                        Daftar Kontak Hari Ini
                    </h3>

                    {{-- 🔥 FILTER GABUNGAN (Satu Form) --}}
                    <form method="GET" class="mb-6">

                        {{-- 🔥 SUPERADMIN & MANAGER: PILIH FO (di dalam form yang sama) --}}
                        @if ((Auth::user()->hasRole('superadmin') || Auth::user()->hasRole('manager')) && isset($foList))
                            <div
                                class="p-4 mb-4 rounded-lg {{ Auth::user()->hasRole('superadmin') ? 'bg-yellow-50' : 'bg-blue-50' }}">
                                <label class="block mb-2 text-sm font-medium text-gray-700">
                                    @if (Auth::user()->hasRole('superadmin'))
                                        🔐 Superadmin Mode: Lihat Kontak FO
                                    @else
                                        👨‍💼 Manager Mode: Lihat Kontak FO di Cabang Anda
                                    @endif
                                </label>
                                <select name="user_id" onchange="this.form.submit()"
                                    class="w-full px-4 py-2 border rounded-lg">
                                    <option value="{{ Auth::id() }}">-- Kontak Saya --</option>
                                    @foreach ($foList as $fo)
                                        <option value="{{ $fo->id }}"
                                            {{ request('user_id') == $fo->id ? 'selected' : '' }}>
                                            {{ $fo->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        {{-- FILTER ROW --}}
                        <div class="flex flex-wrap gap-4">
                            {{-- PER PAGE --}}
                            <div>
                                <select name="per_page" onchange="this.form.submit()"
                                    class="px-4 py-2 pr-10 border rounded-lg">
                                    @foreach ([10, 25, 50, 100] as $size)
                                        <option value="{{ $size }}"
                                            {{ request('per_page', 10) == $size ? 'selected' : '' }}>
                                            {{ $size }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- SOSMED --}}
                            <div>
                                <select name="sosmed" onchange="this.form.submit()"
                                    class="px-4 py-2 pr-10 border rounded-lg">
                                    <option value="">Semua Platform</option>
                                    <option value="DM_IG" {{ request('sosmed') == 'DM_IG' ? 'selected' : '' }}>
                                        DM Instagram
                                    </option>
                                    <option value="CHAT_WA" {{ request('sosmed') == 'CHAT_WA' ? 'selected' : '' }}>
                                        Chat WhatsApp
                                    </option>
                                    <option value="INBOX_FB" {{ request('sosmed') == 'INBOX_FB' ? 'selected' : '' }}>
                                        Inbox Facebook
                                    </option>
                                    <option value="MRKT_PLACE_FB"
                                        {{ request('sosmed') == 'MRKT_PLACE_FB' ? 'selected' : '' }}>
                                        Marketplace Facebook
                                    </option>
                                    <option value="TIKTOK" {{ request('sosmed') == 'TIKTOK' ? 'selected' : '' }}>
                                        TikTok
                                    </option>
                                </select>
                            </div>

                            {{-- SITUASI --}}
                            <div>
                                <select name="situasi" onchange="this.form.submit()"
                                    class="px-4 py-2 pr-10 border rounded-lg">
                                    <option value="">Semua Situasi</option>
                                    <option value="tdk_merespon"
                                        {{ request('situasi') == 'tdk_merespon' ? 'selected' : '' }}>
                                        Tidak Merespon
                                    </option>
                                    <option value="merespon" {{ request('situasi') == 'merespon' ? 'selected' : '' }}>
                                        Merespon
                                    </option>
                                    <option value="tertarik" {{ request('situasi') == 'tertarik' ? 'selected' : '' }}>
                                        Tertarik
                                    </option>
                                    <option value="closing" {{ request('situasi') == 'closing' ? 'selected' : '' }}>
                                        Closing
                                    </option>
                                </select>
                            </div>

                            {{-- STATUS VALIDASI --}}
                            <div>
                                <select name="validasi" onchange="this.form.submit()"
                                    class="px-4 py-2 pr-10 border rounded-lg">
                                    <option value="">Semua Status</option>
                                    <option value="1" {{ request('validasi') === '1' ? 'selected' : '' }}>
                                        Sudah Validasi
                                    </option>
                                    <option value="0" {{ request('validasi') === '0' ? 'selected' : '' }}>
                                        Belum Validasi
                                    </option>
                                </select>
                            </div>

                            {{-- TANGGAL --}}
                            <div>
                                <input type="date" name="tanggal" value="{{ request('tanggal', $tanggal) }}"
                                    onchange="this.form.submit()" class="px-4 py-2 border rounded-lg">
                            </div>

                            {{-- SEARCH --}}
                            <div class="relative flex-1 min-w-[250px]">
                                <input type="text" name="search" value="{{ request('search') }}"
                                    placeholder="Cari nama nasabah atau akun..."
                                    class="w-full px-4 py-2 border rounded-lg">
                            </div>

                            <button type="submit"
                                class="px-4 py-2 text-white bg-teal-600 rounded-lg hover:bg-teal-700">
                                Cari
                            </button>
                        </div>
                    </form>

                    {{-- FILTER --}}
                    {{-- <form method="GET" class="flex flex-wrap gap-4 mb-6">
                        <div>
                            <select name="per_page" onchange="this.form.submit()"
                                class="px-4 py-2 pr-10 border rounded-lg">
                                @foreach ([10, 25, 50, 100] as $size)
                                    <option value="{{ $size }}"
                                        {{ request('per_page', 10) == $size ? 'selected' : '' }}>
                                        {{ $size }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <select name="sosmed" onchange="this.form.submit()"
                                class="px-4 py-2 pr-10 border rounded-lg">
                                <option value="">Semua Platform</option>
                                <option value="DM_IG" {{ request('sosmed') == 'DM_IG' ? 'selected' : '' }}>
                                    DM Instagram
                                </option>
                                <option value="CHAT_WA" {{ request('sosmed') == 'CHAT_WA' ? 'selected' : '' }}>
                                    Chat WhatsApp
                                </option>
                                <option value="INBOX_FB" {{ request('sosmed') == 'INBOX_FB' ? 'selected' : '' }}>
                                    Inbox Facebook
                                </option>
                                <option value="MRKT_PLACE_FB"
                                    {{ request('sosmed') == 'MRKT_PLACE_FB' ? 'selected' : '' }}>
                                    Marketplace Facebook
                                </option>
                                <option value="TIKTOK" {{ request('sosmed') == 'TIKTOK' ? 'selected' : '' }}>
                                    TikTok
                                </option>
                            </select>
                        </div>

                        <div>
                            <select name="situasi" onchange="this.form.submit()"
                                class="px-4 py-2 pr-10 border rounded-lg">
                                <option value="">Semua Situasi</option>
                                <option value="tdk_merespon"
                                    {{ request('situasi') == 'tdk_merespon' ? 'selected' : '' }}>
                                    Tidak Merespon
                                </option>
                                <option value="merespon" {{ request('situasi') == 'merespon' ? 'selected' : '' }}>
                                    Merespon
                                </option>
                                <option value="tertarik" {{ request('situasi') == 'tertarik' ? 'selected' : '' }}>
                                    Tertarik
                                </option>
                                <option value="closing" {{ request('situasi') == 'closing' ? 'selected' : '' }}>
                                    Closing
                                </option>
                            </select>
                        </div>

                        <div>
                            <select name="validasi" onchange="this.form.submit()"
                                class="px-4 py-2 pr-10 border rounded-lg">
                                <option value="">Semua Status</option>
                                <option value="1" {{ request('validasi') === '1' ? 'selected' : '' }}>
                                    Sudah Validasi
                                </option>
                                <option value="0" {{ request('validasi') === '0' ? 'selected' : '' }}>
                                    Belum Validasi
                                </option>
                            </select>
                        </div>

                        <div>
                            <input type="date" name="tanggal" value="{{ request('tanggal', $tanggal) }}"
                                onchange="this.form.submit()" class="px-4 py-2 border rounded-lg">
                        </div>

                        <div class="relative flex-1 min-w-[250px]">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari nama nasabah atau akun..."
                                class="w-full px-4 py-2 border rounded-lg">
                        </div>

                        <button type="submit" class="px-4 py-2 text-white bg-teal-600 rounded-lg hover:bg-teal-700">
                            Cari
                        </button>
                    </form> --}}

                    {{-- 🔥 SUPERADMIN: PILIH FO --}}
                    {{-- 🔥 SUPERADMIN & MANAGER: PILIH FO --}}
                    {{-- @if ((Auth::user()->hasRole('superadmin') || Auth::user()->hasRole('manager')) && isset($foList))
                        <form method="GET"
                            class="p-4 mb-6 rounded-lg {{ Auth::user()->hasRole('superadmin') ? 'bg-yellow-50' : 'bg-blue-50' }}">
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                @if (Auth::user()->hasRole('superadmin'))
                                    🔐 Superadmin Mode: Lihat Kontak FO
                                @else
                                    👨‍💼 Manager Mode: Lihat Kontak FO di Cabang Anda
                                @endif
                            </label>
                            <div class="flex gap-3">
                                <select name="user_id" class="flex-1 px-4 py-2 border rounded-lg">
                                    <option value="{{ Auth::id() }}">-- Kontak Saya --</option>
                                    @foreach ($foList as $fo)
                                        <option value="{{ $fo->id }}"
                                            {{ request('user_id') == $fo->id ? 'selected' : '' }}>
                                            {{ $fo->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit"
                                    class="px-4 py-2 text-white bg-teal-600 rounded-lg hover:bg-teal-700">
                                    Lihat
                                </button>
                            </div>
                        </form>
                    @endif --}}
                    {{-- @if (Auth::user()->hasRole('superadmin') && isset($foList))
                        <form method="GET" class="p-4 mb-6 rounded-lg bg-yellow-50">
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                🔐 Superadmin Mode: Lihat Kontak FO
                            </label>
                            <div class="flex gap-3">
                                <select name="user_id" class="flex-1 px-4 py-2 border rounded-lg">
                                    <option value="{{ Auth::id() }}">-- Kontak Saya --</option>
                                    @foreach ($foList as $fo)
                                        <option value="{{ $fo->id }}"
                                            {{ request('user_id') == $fo->id ? 'selected' : '' }}>
                                            {{ $fo->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit"
                                    class="px-4 py-2 text-white bg-teal-600 rounded-lg hover:bg-teal-700">
                                    Lihat
                                </button>
                            </div>
                        </form>
                    @endif --}}

                    {{-- TABLE --}}
                    <div class="relative w-full overflow-x-auto md:overflow-x-visible">
                        <table class="w-full text-sm min-w-max whitespace-nowrap">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="px-4 py-4 min-w-[50px] text-left text-gray-600 uppercase">#</th>
                                    <th class="px-4 py-4 min-w-[180px] text-left text-gray-600 uppercase">Nama Nasabah
                                    </th>
                                    <th class="px-4 py-4 min-w-[140px] text-left text-gray-600 uppercase">Akun/No Telp
                                    </th>
                                    <th class="px-4 py-4 min-w-[120px] text-left text-gray-600 uppercase">Platform</th>
                                    <th class="px-4 py-4 min-w-[120px] text-left text-gray-600 uppercase">Situasi</th>
                                    <th class="px-4 py-4 min-w-[100px] text-left text-gray-600 uppercase">Validasi</th>
                                    <th class="px-4 py-4 min-w-[180px] text-left text-gray-600 uppercase">Keterangan
                                    </th>
                                    <th class="px-4 py-4 min-w-[100px] text-left text-gray-600 uppercase">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($contacts as $index => $contact)
                                    <tr class="border-t">
                                        <td class="px-4 py-3">
                                            {{ ($contacts->currentPage() - 1) * $contacts->perPage() + $index + 1 }}
                                        </td>
                                        <td class="px-4 py-3 font-medium">{{ $contact->nama_nasabah }}</td>
                                        <td class="px-4 py-3">{{ $contact->akun_or_notelp }}</td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 text-xs text-blue-700 bg-blue-100 rounded">
                                                {{ $contact->sosmed_label }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            @php
                                                $badge = $contact->situasi_badge;
                                            @endphp
                                            <span
                                                class="px-2 py-1 text-{{ $badge['color'] }}-700 bg-{{ $badge['color'] }}-100 rounded">
                                                {{ $badge['text'] }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            @if ($contact->validasi_manager)
                                                <span class="px-2 py-1 text-green-700 bg-green-100 rounded">
                                                    ✅ Sudah
                                                </span>
                                            @else
                                                <span class="px-2 py-1 text-orange-700 bg-orange-100 rounded">
                                                    ⏳ Belum
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-xs text-gray-600">
                                            {{ $contact->keterangan ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="relative inline-block text-left">
                                                <button type="button" onclick="toggleDropdown({{ $contact->id }})"
                                                    class="text-gray-400 hover:text-gray-600">
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path
                                                            d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                                    </svg>
                                                </button>

                                                <div id="dropdown-{{ $contact->id }}"
                                                    class="fixed z-50 hidden w-48 bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5">
                                                    <div class="py-1">
                                                        {{-- Edit --}}
                                                        @if (!$contact->validasi_manager || Auth::user()->hasRole('superadmin'))
                                                            <a href="{{ route('contact90.edit', $contact->id) }}"
                                                                class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                                <svg class="w-4 h-4" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                                </svg>
                                                                Edit
                                                            </a>
                                                        @endif

                                                        {{-- Delete --}}
                                                        @if (!$contact->validasi_manager || Auth::user()->hasRole('superadmin'))
                                                            <form
                                                                action="{{ route('contact90.destroy', $contact->id) }}"
                                                                method="POST" class="inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    onclick="return confirm('Yakin hapus kontak {{ $contact->nama_nasabah }}?')"
                                                                    class="flex items-center w-full gap-2 px-4 py-2 text-sm text-left text-red-700 hover:bg-gray-100">
                                                                    <svg class="w-4 h-4" fill="none"
                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                    </svg>
                                                                    Hapus
                                                                </button>
                                                            </form>
                                                        @endif

                                                        {{-- Locked --}}
                                                        @if ($contact->validasi_manager && !Auth::user()->hasRole('superadmin'))
                                                            <div class="px-4 py-2 text-sm text-gray-500">
                                                                🔒 Sudah Divalidasi
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                            Belum ada kontak yang diinput hari ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <div class="mt-6">
                            {{ $contacts->withQueryString()->links() }}
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    {{-- Dropdown Script --}}
    <script>
        function toggleDropdown(id) {
            event.stopPropagation();

            const button = event.currentTarget;
            const dropdown = document.getElementById(`dropdown-${id}`);

            // Tutup dropdown lain
            document.querySelectorAll('[id^="dropdown-"]').forEach(el => {
                if (el !== dropdown) el.classList.add('hidden');
            });

            // Toggle
            dropdown.classList.toggle('hidden');

            if (!dropdown.classList.contains('hidden')) {
                const rect = button.getBoundingClientRect();
                dropdown.style.top = `${rect.bottom + 8}px`;
                dropdown.style.left = `${rect.right - dropdown.offsetWidth}px`;
            }
        }

        document.addEventListener('click', () => {
            document.querySelectorAll('[id^="dropdown-"]').forEach(el => {
                el.classList.add('hidden');
            });
        });
    </script>

</x-app-layout>
