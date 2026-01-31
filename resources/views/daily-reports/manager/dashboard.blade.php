<x-app-layout>
    <x-slot name="header">
        <div class="grid items-center justify-between grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Dashboard Manager - Laporan Harian
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Monitoring laporan harian cabang
                </p>
            </div>

            <div class="flex justify-start gap-3 md:justify-end">
                <a href="{{ route('daily-reports.manager.reports', ['tanggal' => $tanggal]) }}"
                    class="flex items-center gap-2 px-4 py-2 text-white transition bg-teal-600 rounded-lg hover:bg-teal-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Validasi Laporan
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

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
                        🔓 <strong>Superadmin Mode:</strong> Anda bisa melihat semua cabang.
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
                {{-- Total Cabang --}}
                <div class="p-6 bg-white rounded-lg shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Total Cabang</p>
                            <p class="mt-2 text-3xl font-bold text-gray-800">{{ $totalCabang }}</p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-full">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Total Laporan vs Target --}}
                <div class="p-6 bg-white rounded-lg shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Total Laporan</p>
                            <p class="mt-2 text-3xl font-bold text-gray-800">
                                {{ $totalLaporan }}<span class="text-lg text-gray-400">/{{ $targetLaporan }}</span>
                            </p>
                            <div class="w-full h-2 mt-3 bg-gray-200 rounded-full">
                                <div class="h-2 bg-teal-600 rounded-full"
                                    style="width: {{ $targetLaporan > 0 ? min(($totalLaporan / $targetLaporan) * 100, 100) : 0 }}%">
                                </div>
                            </div>
                        </div>
                        <div class="p-3 bg-teal-100 rounded-full">
                            <svg class="w-8 h-8 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Cabang Lengkap --}}
                <div class="p-6 bg-white rounded-lg shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Cabang Lengkap</p>
                            <p class="mt-2 text-sm text-gray-600">
                                ✅ Lengkap: <span class="font-bold text-green-600">{{ $cabangLengkap }}</span>
                            </p>
                            <p class="text-sm text-gray-600">
                                ⏳ Belum: <span class="font-bold text-orange-600">{{ $cabangBelumLengkap }}</span>
                            </p>
                            <div class="w-full h-2 mt-3 bg-gray-200 rounded-full">
                                <div class="h-2 bg-green-600 rounded-full"
                                    style="width: {{ $totalCabang > 0 ? ($cabangLengkap / $totalCabang) * 100 : 0 }}%">
                                </div>
                            </div>
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
                                    style="width: {{ $totalLaporan > 0 ? ($sudahValidasi / $totalLaporan) * 100 : 0 }}%">
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
            </div>

            {{-- TOTAL PENCAIRAN & PELUNASAN --}}
            {{-- <div class="grid grid-cols-1 gap-6 mb-6 md:grid-cols-2">
                <div class="p-6 bg-white rounded-lg shadow-sm">
                    <h3 class="mb-4 text-lg font-semibold text-gray-800">📥 Total Pencairan (Barang Masuk)</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 rounded-lg bg-green-50">
                            <span class="text-sm text-green-700">Jumlah Barang:</span>
                            <span
                                class="text-xl font-bold text-green-800">{{ number_format($totals->total_pencairan_barang ?? 0) }}
                                unit</span>
                        </div>
                        <div class="flex items-center justify-between p-3 rounded-lg bg-green-50">
                            <span class="text-sm text-green-700">Total Nominal:</span>
                            <span class="text-xl font-bold text-green-800">Rp
                                {{ number_format($totals->total_pencairan_nominal ?? 0, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <div class="p-6 bg-white rounded-lg shadow-sm">
                    <h3 class="mb-4 text-lg font-semibold text-gray-800">📤 Total Pelunasan (Barang Keluar)</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 rounded-lg bg-blue-50">
                            <span class="text-sm text-blue-700">Jumlah Barang:</span>
                            <span
                                class="text-xl font-bold text-blue-800">{{ number_format($totals->total_pelunasan_barang ?? 0) }}
                                unit</span>
                        </div>
                        <div class="flex items-center justify-between p-3 rounded-lg bg-blue-50">
                            <span class="text-sm text-blue-700">Total Nominal:</span>
                            <span class="text-xl font-bold text-blue-800">Rp
                                {{ number_format($totals->total_pelunasan_nominal ?? 0, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div> --}}
            {{-- TOTAL PENCAIRAN & PELUNASAN --}}
            <div class="grid grid-cols-1 gap-6 mb-6 md:grid-cols-2">
                {{-- Pencairan --}}
                <div class="p-6 bg-white rounded-lg shadow-sm">
                    <h3 class="mb-4 text-lg font-semibold text-gray-800">📥 Total Pencairan (Barang Masuk)</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 rounded-lg bg-green-50">
                            <span class="text-sm text-green-700">Jumlah Barang:</span>
                            <span class="text-xl font-bold text-green-800">
                                {{ number_format($totals->total_pencairan_barang ?? 0) }} unit
                            </span>
                        </div>
                        <div class="flex items-center justify-between p-3 rounded-lg bg-green-50">
                            <span class="text-sm text-green-700">Total Nominal:</span>
                            <span class="text-xl font-bold text-green-800">
                                Rp {{ number_format($totals->total_pencairan_nominal ?? 0, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Pelunasan --}}
                <div class="p-6 bg-white rounded-lg shadow-sm">
                    <h3 class="mb-4 text-lg font-semibold text-gray-800">📤 Total Pelunasan (Barang Keluar)</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 rounded-lg bg-blue-50">
                            <span class="text-sm text-blue-700">Jumlah Barang:</span>
                            <span class="text-xl font-bold text-blue-800">
                                {{ number_format($totals->total_pelunasan_barang ?? 0) }} unit
                            </span>
                        </div>
                        <div class="flex items-center justify-between p-3 rounded-lg bg-blue-50">
                            <span class="text-sm text-blue-700">Total Nominal:</span>
                            <span class="text-xl font-bold text-blue-800">
                                Rp {{ number_format($totals->total_pelunasan_nominal ?? 0, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 🔥 STOK AKHIR PER CABANG --}}
            <div class="p-6 mb-6 bg-white rounded-lg shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">📦 Stok Akhir Per Cabang</h3>
                    <div class="p-3 bg-purple-100 rounded-full">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                </div>

                {{-- Table Stok Akhir --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 bg-gray-50">
                                <th class="px-3 py-3 text-xs font-semibold text-left text-gray-600 uppercase">#</th>
                                <th class="px-3 py-3 text-xs font-semibold text-left text-gray-600 uppercase">Cabang
                                </th>
                                <th class="px-3 py-3 text-xs font-semibold text-left text-gray-600 uppercase">Jumlah
                                    Barang</th>
                                <th class="px-3 py-3 text-xs font-semibold text-left text-gray-600 uppercase">Total
                                    Nominal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @php
                                $totalStokBarang = 0;
                                $totalStokNominal = 0;
                            @endphp
                            @foreach ($managedBranches as $index => $branch)
                                @php
                                    $stok = $stokAkhirPerCabang[$branch->id] ?? ['jumlah_barang' => 0, 'nominal' => 0];
                                    $totalStokBarang += $stok['jumlah_barang'];
                                    $totalStokNominal += $stok['nominal'];
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-3 text-sm text-gray-900">{{ $index + 1 }}</td>
                                    <td class="px-3 py-3">
                                        <p class="text-sm font-medium text-gray-900">{{ $branch->name }}</p>
                                    </td>
                                    <td class="px-3 py-3">
                                        <span
                                            class="inline-flex items-center px-2 py-1 text-sm font-medium text-purple-700 bg-purple-100 rounded">
                                            {{ number_format($stok['jumlah_barang']) }} unit
                                        </span>
                                    </td>
                                    <td class="px-3 py-3">
                                        <span class="text-sm font-medium text-gray-900">
                                            Rp {{ number_format($stok['nominal'], 0, ',', '.') }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach

                            {{-- TOTAL ROW --}}
                            <tr class="font-bold bg-purple-50">
                                <td colspan="2" class="px-3 py-3 text-sm text-purple-800 uppercase">
                                    Total Keseluruhan
                                </td>
                                <td class="px-3 py-3">
                                    <span
                                        class="inline-flex items-center px-2 py-1 text-sm font-bold text-purple-800 bg-purple-200 rounded">
                                        {{ number_format($totalStokBarang) }} unit
                                    </span>
                                </td>
                                <td class="px-3 py-3">
                                    <span class="text-sm font-bold text-purple-800">
                                        Rp {{ number_format($totalStokNominal, 0, ',', '.') }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Info Box --}}
                <div class="p-3 mt-4 rounded-lg bg-blue-50">
                    <p class="text-xs text-blue-800">
                        💡 <strong>Info:</strong> Stok Akhir diambil dari laporan terakhir masing-masing cabang (update
                        otomatis)
                    </p>
                </div>
            </div>

            {{-- QUICK ACTIONS --}}
            <div class="p-6 bg-white rounded-lg shadow-sm">
                <h3 class="mb-4 text-lg font-semibold text-gray-800">Quick Actions</h3>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <a href="{{ route('daily-reports.manager.reports', ['tanggal' => $tanggal]) }}"
                        class="flex items-center gap-3 p-4 transition border rounded-lg hover:bg-gray-50">
                        <div class="p-3 bg-teal-100 rounded-full">
                            <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">Lihat Semua Laporan</p>
                            <p class="text-sm text-gray-500">Validasi & monitoring</p>
                        </div>
                    </a>

                    <a href="{{ route('daily-reports.manager.reports', ['tanggal' => $tanggal, 'validasi' => '0']) }}"
                        class="flex items-center gap-3 p-4 transition border rounded-lg hover:bg-gray-50">
                        <div class="p-3 bg-orange-100 rounded-full">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">Laporan Belum Validasi</p>
                            <p class="text-sm text-orange-600">{{ $belumValidasi }} laporan pending</p>
                        </div>
                    </a>

                    <a href="{{ route('daily-reports.index') }}"
                        class="flex items-center gap-3 p-4 transition border rounded-lg hover:bg-gray-50">
                        <div class="p-3 bg-blue-100 rounded-full">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">Kembali ke Dashboard</p>
                            <p class="text-sm text-gray-500">View sebagai FO/Staff</p>
                        </div>
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
