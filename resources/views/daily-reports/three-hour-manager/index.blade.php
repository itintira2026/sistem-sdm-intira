<x-app-layout>
    <x-slot name="header">
        <div class="grid items-center justify-between grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Laporan Per 3 Jam Area Manager
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Wajib 3 laporan per hari (Pada Jam tertera di toleransi lambat mengisi laporan selama 1 jam di jam terakhir)
                </p>
            </div>
             <div class="flex justify-start gap-3 md:justify-end">
             
                <a href="{{ route('daily-reports.3hour-manager.create') }}"
                    class="flex items-center gap-2 px-4 py-2 text-white transition bg-teal-500 rounded-lg hover:bg-teal-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Data
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

        {{-- Alert Error --}}
        @if (session('error'))
            <div class="p-4 mb-6 text-red-700 bg-red-100 rounded-lg">
                {{ session('error') }}
            </div>
        @endif


        {{-- CARDS --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

            {{-- ========================= --}}
            {{-- OMZET --}}
            {{-- ========================= --}}
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <h3 class="mb-4 text-lg font-semibold text-gray-800">
                        OMZET
                    </h3>

                    <div class="space-y-3">

                        <div class="p-3 border border-gray-300 rounded-lg">
                            <p class="text-xs text-gray-500">
                                Hari Ini
                            </p>

                            <p class="mt-1 text-lg font-semibold text-blue-600">
                                Rp {{ number_format($omzetHariIni ?? 0, 0, ',', '.') }}
                            </p>
                        </div>

                        <div class="p-3 border border-gray-300 rounded-lg">
                            <p class="text-xs text-gray-500">
                                Bulan Ini
                            </p>

                            <p class="mt-1 text-lg font-semibold text-blue-600">
                                Rp {{ number_format($omzetBulanIni ?? 0, 0, ',', '.') }}
                            </p>
                        </div>

                    </div>

                </div>
            </div>



            {{-- ========================= --}}
            {{-- NASABAH --}}
            {{-- ========================= --}}
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">

                <div class="p-6">

                    <h3 class="mb-4 text-lg font-semibold text-gray-800">
                        NASABAH
                    </h3>

                    <div class="space-y-3">

                        <div class="p-3 border border-gray-300 rounded-lg">

                            <p class="text-xs text-gray-500">
                                Hari Ini
                            </p>

                            <p class="mt-1 text-lg font-semibold text-green-600">
                                {{ number_format($nasabahHariIni ?? 0) }}
                            </p>

                        </div>

                        <div class="p-3 border border-gray-300 rounded-lg">

                            <p class="text-xs text-gray-500">
                                Bulan Ini
                            </p>

                            <p class="mt-1 text-lg font-semibold text-green-600">
                                {{ number_format($nasabahBulanIni ?? 0) }}
                            </p>

                        </div>

                    </div>

                </div>

            </div>



            {{-- ========================= --}}
            {{-- REVENUE --}}
            {{-- ========================= --}}
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">

                <div class="p-6">

                    <h3 class="mb-4 text-lg font-semibold text-gray-800">
                        REVENUE
                    </h3>

                    <div class="space-y-3">

                        <div class="p-3 border border-gray-300 rounded-lg">

                            <p class="text-xs text-gray-500">
                                Hari Ini
                            </p>

                            <p class="mt-1 text-lg font-semibold text-teal-600">
                                Rp {{ number_format($revenueHariIni ?? 0, 0, ',', '.') }}
                            </p>

                        </div>

                        <div class="p-3 border border-gray-300 rounded-lg">

                            <p class="text-xs text-gray-500">
                                Bulan Ini
                            </p>

                            <p class="mt-1 text-lg font-semibold text-teal-600">
                                Rp {{ number_format($revenueBulanIni ?? 0, 0, ',', '.') }}
                            </p>

                        </div>

                    </div>

                </div>

            </div>


        </div>

    </div>
</div>



</x-app-layout>