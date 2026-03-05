<x-app-layout>
    <div class="flex items-center justify-center min-h-screen px-4 py-12 bg-gray-50">
        <div class="w-full max-w-2xl text-center">

            <!-- Icon -->
            <div class="flex justify-center mb-8">
                <div class="relative">
                    <div class="flex items-center justify-center w-24 h-24 rounded-2xl bg-blue-100">
                        <svg class="w-12 h-12 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <!-- Decorative dots -->
                    <div class="absolute w-2 h-2 bg-blue-400 rounded-full -top-1 -right-1 animate-ping"></div>
                    <div class="absolute w-2 h-2 bg-blue-500 rounded-full -top-1 -right-1"></div>
                </div>
            </div>

            <!-- Title -->
            <h1 class="mb-4 text-3xl font-bold text-gray-900 sm:text-4xl">
                Belum Ada Data Gaji
            </h1>

            <!-- Description -->
            <p class="max-w-lg mx-auto mb-8 text-base text-gray-600 sm:text-lg">
                Anda belum memiliki data gaji untuk periode ini.
                Silakan hubungi HRD atau administrator jika ada pertanyaan.
            </p>

            <!-- Status Badge -->
            <div
                class="inline-flex items-center px-4 py-2 mb-10 space-x-2 text-sm font-medium text-blue-700 rounded-full bg-blue-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Data gaji belum tersedia</span>
            </div>

            <!-- Info Cards -->
            <div class="grid max-w-md gap-4 mx-auto mb-10 sm:grid-cols-3">
                <div class="p-4 transition bg-white border border-gray-200 rounded-lg hover:shadow-md">
                    <div class="mb-2 text-2xl">📋</div>
                    <div class="text-xs font-medium text-gray-500 uppercase">Status</div>
                    <div class="text-sm font-semibold text-gray-900">Belum Diproses</div>
                </div>
                <div class="p-4 transition bg-white border border-gray-200 rounded-lg hover:shadow-md">
                    <div class="mb-2 text-2xl">🗓️</div>
                    <div class="text-xs font-medium text-gray-500 uppercase">Periode</div>
                    <div class="text-sm font-semibold text-gray-900">{{ now()->translatedFormat('F Y') }}</div>
                </div>
                <div class="p-4 transition bg-white border border-gray-200 rounded-lg hover:shadow-md">
                    <div class="mb-2 text-2xl">💼</div>
                    <div class="text-xs font-medium text-gray-500 uppercase">Informasi</div>
                    <div class="text-sm font-semibold text-gray-900">Hubungi HRD</div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col items-center justify-center gap-3 sm:flex-row">
                <a href="{{ route('dashboard') }}"
                    class="inline-flex items-center justify-center w-full gap-2 px-6 py-3 text-sm font-medium text-white transition bg-blue-600 rounded-lg sm:w-auto hover:bg-blue-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Dashboard
                </a>

                <a href="mailto:support@example.com"
                    class="inline-flex items-center justify-center w-full gap-2 px-6 py-3 text-sm font-medium text-gray-700 transition bg-white border border-gray-300 rounded-lg sm:w-auto hover:bg-gray-50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Hubungi Support
                </a>
            </div>

            <!-- Footer Note -->
            <p class="mt-10 text-xs text-gray-400">
                Terima kasih atas kesabaran Anda 🙏
            </p>
        </div>
    </div>
</x-app-layout>