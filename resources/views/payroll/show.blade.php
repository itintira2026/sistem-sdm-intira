<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Detail Gaji - Budi Santoso
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Cabang Jakarta Pusat • Januari 2025
                </p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('gaji.index') }}"
                    class="flex items-center gap-2 px-4 py-2 text-white transition bg-gray-500 rounded-lg hover:bg-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>

                <a href="{{ route('potongan.create', ['branch' => $branch->id, 'bulan' => $bulan, 'tahun' => $tahun]) }}"
                    class="flex items-center gap-2 px-4 py-2 text-white transition bg-teal-500 rounded-lg hover:bg-teal-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Main Content - Left Side -->
                <div class="space-y-6 lg:col-span-2">
                    <!-- User Information Card -->
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="mb-4 text-lg font-semibold text-gray-900">Informasi Karyawan</h3>
                            <div class="flex items-start gap-4">
                                <div
                                    class="flex items-center justify-center w-20 h-20 text-2xl font-bold text-white rounded-full shadow-lg bg-gradient-to-br from-blue-500 to-blue-600">
                                    BS
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-xl font-bold text-gray-900">Budi Santoso</h4>
                                    <p class="mt-1 text-gray-600">budi.santoso@example.com</p>
                                    <div class="flex items-center gap-2 mt-3">
                                        <span
                                            class="px-3 py-1 text-sm font-medium text-purple-700 bg-purple-100 rounded-full">
                                            Kasir
                                        </span>
                                        <span
                                            class="px-3 py-1 text-sm font-medium rounded-full bg-amber-100 text-amber-700">
                                            Manager
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-6">
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Cabang</label>
                                    <p class="mt-1 font-semibold text-gray-900">Jakarta Pusat</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Status</label>
                                    <p class="mt-1 font-semibold text-gray-900">Manager</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Salary Details Card -->
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Detail Gaji Pokok</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                    <span class="font-medium text-gray-600">Periode</span>
                                    <span class="font-semibold text-gray-900">Januari 2025</span>
                                </div>
                                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                    <span class="font-medium text-gray-600">Bulan</span>
                                    <span class="font-semibold text-gray-900">Januari</span>
                                </div>
                                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                    <span class="font-medium text-gray-600">Tahun</span>
                                    <span class="font-semibold text-gray-900">2025</span>
                                </div>
                                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                    <span class="font-medium text-gray-600">Tanggal Input</span>
                                    <span class="font-semibold text-gray-900">05 Januari 2025, 09:30</span>
                                </div>
                                <div class="flex items-center justify-between py-3">
                                    <span class="font-medium text-gray-600">Terakhir Update</span>
                                    <span class="font-semibold text-gray-900">10 Januari 2025, 14:15</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notes Card -->
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Keterangan</h3>
                        </div>
                        <div class="p-6">
                            <div class="p-4 rounded-lg bg-gray-50">
                                <p class="text-gray-700">Gaji pokok periode Januari 2025. Sudah termasuk penyesuaian
                                    kenaikan tahunan sebesar 10% dari periode sebelumnya.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Riwayat Gaji Pokok -->
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Riwayat Gaji Pokok</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3">
                                <div
                                    class="flex items-center justify-between p-4 border border-blue-200 rounded-lg bg-blue-50">
                                    <div>
                                        <p class="font-semibold text-gray-900">Januari 2025</p>
                                        <p class="text-sm text-gray-600">Periode Aktif</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-blue-600">Rp 5.500.000</p>
                                        <p class="text-xs text-gray-500">+10%</p>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between p-4 rounded-lg bg-gray-50">
                                    <div>
                                        <p class="font-semibold text-gray-900">Desember 2024</p>
                                        <p class="text-sm text-gray-600">Periode Sebelumnya</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-gray-700">Rp 5.000.000</p>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between p-4 rounded-lg bg-gray-50">
                                    <div>
                                        <p class="font-semibold text-gray-900">November 2024</p>
                                        <p class="text-sm text-gray-600">2 bulan lalu</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-gray-700">Rp 5.000.000</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar - Right Side -->
                <div class="space-y-6">
                    <!-- Amount Card -->
                    <div
                        class="overflow-hidden text-white shadow-lg bg-gradient-to-br from-blue-500 to-blue-600 sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-sm font-medium opacity-90">Gaji Pokok</span>
                            </div>
                            <div class="mb-1 text-4xl font-bold">
                                Rp 5.500.000
                            </div>
                            <p class="text-sm opacity-75">Per Bulan</p>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Statistik</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center justify-center w-10 h-10 bg-green-100 rounded-lg">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Status</p>
                                        <p class="font-semibold text-gray-900">Aktif</p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center justify-center w-10 h-10 bg-blue-100 rounded-lg">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Cabang</p>
                                        <p class="font-semibold text-gray-900">Jakarta Pusat</p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center justify-center w-10 h-10 bg-purple-100 rounded-lg">
                                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Periode</p>
                                        <p class="font-semibold text-gray-900">Januari 2025</p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center justify-center w-10 h-10 bg-orange-100 rounded-lg">
                                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Kenaikan</p>
                                        <p class="font-semibold text-green-600">+10%</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions Card -->
                    {{-- <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Aksi</h3>
                        </div>
                        <div class="p-6 space-y-3">
                            <button class="flex items-center justify-center w-full gap-2 px-4 py-2 text-white transition bg-blue-500 rounded-lg hover:bg-blue-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Edit Gaji Pokok
                            </button>

                            <button onclick="alert('Data akan dihapus!')" class="flex items-center justify-center w-full gap-2 px-4 py-2 text-white transition bg-red-500 rounded-lg hover:bg-red-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Hapus Data
                            </button>

                            <button class="flex items-center justify-center w-full gap-2 px-4 py-2 text-white transition bg-green-500 rounded-lg hover:bg-green-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                Export PDF
                            </button>

                            <button class="flex items-center justify-center w-full gap-2 px-4 py-2 text-gray-700 transition bg-gray-100 rounded-lg hover:bg-gray-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                                Print
                            </button>
                        </div>
                    </div> --}}

                    <!-- Info Card -->
                    <div
                        class="overflow-hidden text-white shadow-lg bg-gradient-to-br from-purple-500 to-purple-600 sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-start gap-3">
                                <svg class="flex-shrink-0 w-6 h-6 mt-1" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <h4 class="mb-1 font-semibold">Informasi</h4>
                                    <p class="text-sm opacity-90">Data gaji pokok ini akan digunakan untuk perhitungan
                                        payroll bulan berjalan. Pastikan data sudah benar sebelum periode penggajian.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
