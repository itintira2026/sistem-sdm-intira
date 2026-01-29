<x-app-layout>
    <x-slot name="header">
        <div class="grid items-center justify-between grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Dashboard Manager - Contact 90
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Monitoring performa tim Front Office
                </p>
            </div>

            <div class="flex justify-start gap-3 md:justify-end">
                <a href="{{ route('contact90.manager.folist', ['tanggal' => $tanggal]) }}"
                    class="flex items-center gap-2 px-4 py-2 text-white transition bg-teal-600 rounded-lg hover:bg-teal-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Daftar FO
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            {{-- 🔥 TEMPORARY: SUPERADMIN ONLY NOTICE --}}
            {{-- 🔥 TODO: Nanti ganti middleware ke 'manager' juga bisa akses --}}
            <div class="p-4 mb-6 text-purple-700 bg-purple-100 rounded-lg">
                <p class="font-semibold">🔐 Temporary Access: Superadmin Only</p>
                <p class="mt-1 text-sm">
                    Saat ini halaman ini hanya bisa diakses oleh Superadmin.
                    <strong>Nanti akan dibuka untuk role "manager"</strong> dengan menambahkan middleware di route.
                </p>
            </div>
            {{-- 🔥 INFO CABANG YANG DI-MANAGE --}}
            @if (!Auth::user()->hasRole('superadmin'))
                <div class="p-4 mb-6 bg-blue-100 rounded-lg">
                    <p class="font-semibold text-blue-800">📍 Cabang yang Anda Kelola:</p>
                    <div class="flex flex-wrap gap-2 mt-2">
                        @foreach ($managedBranches as $branch)
                            <span class="px-3 py-1 text-sm text-blue-700 bg-white rounded-full">
                                {{ $branch->name }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="p-4 mb-6 bg-gray-100 rounded-lg">
                    <p class="text-sm text-gray-700">
                        🔓 <strong>Superadmin Mode:</strong> Anda bisa melihat semua cabang dan semua FO.
                    </p>
                </div>
            @endif

            {{-- FILTER TANGGAL --}}
            <div class="p-6 mb-6 bg-white rounded-lg shadow-sm">
                <form method="GET" class="flex items-center gap-4">
                    <label class="text-sm font-medium text-gray-700">Pilih Tanggal:</label>
                    <input type="date" name="tanggal" value="{{ $tanggal }}" onchange="this.form.submit()"
                        class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500">
                </form>
            </div>

            {{-- 📊 STATISTIK DASHBOARD --}}
            <div class="grid grid-cols-1 gap-6 mb-6 md:grid-cols-2 lg:grid-cols-4">
                {{-- Total FO --}}
                <div class="p-6 bg-white rounded-lg shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Total FO Aktif</p>
                            <p class="mt-2 text-3xl font-bold text-gray-800">{{ $totalFo }}</p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-full">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Total Kontak vs Target --}}
                <div class="p-6 bg-white rounded-lg shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Total Kontak</p>
                            <p class="mt-2 text-3xl font-bold text-gray-800">
                                {{ $totalKontak }}<span class="text-lg text-gray-400">/{{ $target }}</span>
                            </p>
                            <div class="w-full h-2 mt-3 bg-gray-200 rounded-full">
                                <div class="h-2 bg-teal-600 rounded-full"
                                    style="width: {{ $target > 0 ? min(($totalKontak / $target) * 100, 100) : 0 }}%">
                                </div>
                            </div>
                        </div>
                        <div class="p-3 bg-teal-100 rounded-full">
                            <svg class="w-8 h-8 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
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
                                ✅ Sudah: <span class="font-bold text-green-600">{{ $sudahValidasi }}</span>
                            </p>
                            <p class="text-sm text-gray-600">
                                ⏳ Belum: <span class="font-bold text-orange-600">{{ $belumValidasi }}</span>
                            </p>
                            <div class="w-full h-2 mt-3 bg-gray-200 rounded-full">
                                <div class="h-2 bg-green-600 rounded-full"
                                    style="width: {{ $totalKontak > 0 ? ($sudahValidasi / $totalKontak) * 100 : 0 }}%">
                                </div>
                            </div>
                        </div>
                        <div class="p-3 bg-orange-100 rounded-full">
                            <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Conversion Rate --}}
                <div class="p-6 bg-white rounded-lg shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Conversion Rate</p>
                            <p class="mt-2 text-3xl font-bold text-green-600">
                                {{ $totalKontak > 0 ? number_format(($closing / $totalKontak) * 100, 1) : 0 }}%
                            </p>
                            <p class="mt-1 text-xs text-gray-500">Closing / Total Kontak</p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-full">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- BREAKDOWN SITUASI --}}
            <div class="grid grid-cols-1 gap-6 mb-6 md:grid-cols-2 lg:grid-cols-4">
                {{-- Closing --}}
                <div class="p-6 bg-white rounded-lg shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-sm font-medium text-gray-700">✅ Closing</h4>
                        <span class="px-2 py-1 text-xs text-green-700 bg-green-100 rounded">
                            {{ $totalKontak > 0 ? number_format(($closing / $totalKontak) * 100, 1) : 0 }}%
                        </span>
                    </div>
                    <p class="text-3xl font-bold text-green-600">{{ $closing }}</p>
                    <div class="w-full h-2 mt-3 bg-gray-200 rounded-full">
                        <div class="h-2 bg-green-600 rounded-full"
                            style="width: {{ $totalKontak > 0 ? ($closing / $totalKontak) * 100 : 0 }}%"></div>
                    </div>
                </div>

                {{-- Tertarik --}}
                <div class="p-6 bg-white rounded-lg shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-sm font-medium text-gray-700">⭐ Tertarik</h4>
                        <span class="px-2 py-1 text-xs text-yellow-700 bg-yellow-100 rounded">
                            {{ $totalKontak > 0 ? number_format(($tertarik / $totalKontak) * 100, 1) : 0 }}%
                        </span>
                    </div>
                    <p class="text-3xl font-bold text-yellow-600">{{ $tertarik }}</p>
                    <div class="w-full h-2 mt-3 bg-gray-200 rounded-full">
                        <div class="h-2 bg-yellow-600 rounded-full"
                            style="width: {{ $totalKontak > 0 ? ($tertarik / $totalKontak) * 100 : 0 }}%"></div>
                    </div>
                </div>

                {{-- Merespon --}}
                <div class="p-6 bg-white rounded-lg shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-sm font-medium text-gray-700">💬 Merespon</h4>
                        <span class="px-2 py-1 text-xs text-blue-700 bg-blue-100 rounded">
                            {{ $totalKontak > 0 ? number_format(($merespon / $totalKontak) * 100, 1) : 0 }}%
                        </span>
                    </div>
                    <p class="text-3xl font-bold text-blue-600">{{ $merespon }}</p>
                    <div class="w-full h-2 mt-3 bg-gray-200 rounded-full">
                        <div class="h-2 bg-blue-600 rounded-full"
                            style="width: {{ $totalKontak > 0 ? ($merespon / $totalKontak) * 100 : 0 }}%"></div>
                    </div>
                </div>

                {{-- Tidak Merespon --}}
                <div class="p-6 bg-white rounded-lg shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-sm font-medium text-gray-700">❌ Tidak Merespon</h4>
                        <span class="px-2 py-1 text-xs text-gray-700 bg-gray-100 rounded">
                            {{ $totalKontak > 0 ? number_format(($tdkMerespon / $totalKontak) * 100, 1) : 0 }}%
                        </span>
                    </div>
                    <p class="text-3xl font-bold text-gray-600">{{ $tdkMerespon }}</p>
                    <div class="w-full h-2 mt-3 bg-gray-200 rounded-full">
                        <div class="h-2 bg-gray-600 rounded-full"
                            style="width: {{ $totalKontak > 0 ? ($tdkMerespon / $totalKontak) * 100 : 0 }}%"></div>
                    </div>
                </div>
            </div>

            {{-- QUICK ACTIONS --}}
            <div class="p-6 bg-white rounded-lg shadow-sm">
                <h3 class="mb-4 text-lg font-semibold text-gray-800">Quick Actions</h3>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <a href="{{ route('contact90.manager.folist', ['tanggal' => $tanggal]) }}"
                        class="flex items-center gap-3 p-4 transition border rounded-lg hover:bg-gray-50">
                        <div class="p-3 bg-teal-100 rounded-full">
                            <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">Lihat Daftar FO</p>
                            <p class="text-sm text-gray-500">Monitoring per FO</p>
                        </div>
                    </a>

                    <a href="{{ route('contact90.manager.folist', ['tanggal' => $tanggal, 'validasi' => '0']) }}"
                        class="flex items-center gap-3 p-4 transition border rounded-lg hover:bg-gray-50">
                        <div class="p-3 bg-orange-100 rounded-full">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">Kontak Belum Validasi</p>
                            <p class="text-sm text-orange-600">{{ $belumValidasi }} kontak pending</p>
                        </div>
                    </a>

                    <a href="{{ route('contact90.index') }}"
                        class="flex items-center gap-3 p-4 transition border rounded-lg hover:bg-gray-50">
                        <div class="p-3 bg-blue-100 rounded-full">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">Kembali ke Dashboard FO</p>
                            <p class="text-sm text-gray-500">View sebagai FO</p>
                        </div>
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
