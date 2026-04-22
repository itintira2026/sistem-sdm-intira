<x-app-layout>
    <div class="flex items-center justify-center min-h-screen px-4 py-12 bg-gray-50">
        <div class="w-full max-w-2xl text-center">

            <!-- Icon -->
            <div class="flex justify-center mb-8">
                <div class="relative">
                    <div class="flex items-center justify-center w-24 h-24 bg-red-100 rounded-2xl">
                        <svg class="w-12 h-12 text-red-600 animate-pulse" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                       
                    </div>
                    <!-- Decorative dots -->
                    <div class="absolute w-2 h-2 bg-red-400 rounded-full -top-1 -right-1 animate-ping"></div>
                    <div class="absolute w-2 h-2 bg-red-500 rounded-full -top-1 -right-1"></div>
                </div>
            </div>

            <!-- Title -->
            <h1 class="mb-4 text-3xl font-bold text-gray-900 sm:text-4xl">
                Sedang Dalam Pengembangan
            </h1>

            <!-- Description -->
            <p class="max-w-lg mx-auto mb-8 text-base text-gray-600 sm:text-lg">
                Fitur ini sedang kami kembangkan untuk memberikan pengalaman terbaik bagi Anda.
                Mohon bersabar, kami akan segera hadir!
            </p>

            <!-- Status Badge -->
            <div
                class="inline-flex items-center px-4 py-2 mb-10 space-x-2 text-sm font-medium text-red-700 rounded-full bg-red-50">
                <span class="relative flex w-3 h-3">
                    <span
                        class="absolute inline-flex w-full h-full bg-red-400 rounded-full opacity-75 animate-ping"></span>
                    <span class="relative inline-flex w-3 h-3 bg-red-500 rounded-full"></span>
                </span>
                <span>Tim kami sedang bekerja</span>
            </div>

            <!-- Info Cards -->
            <div class="grid max-w-md gap-4 mx-auto mb-10 sm:grid-cols-3">
                <div class="p-4 transition bg-white border border-gray-200 rounded-lg hover:shadow-md">
                    <div class="mb-2 text-2xl">🚀</div>
                    <div class="text-xs font-medium text-gray-500 uppercase">Status</div>
                    <div class="text-sm font-semibold text-gray-900">In Progress</div>
                </div>
                <div class="p-4 transition bg-white border border-gray-200 rounded-lg hover:shadow-md">
                    <div class="mb-2 text-2xl">⚡</div>
                    <div class="text-xs font-medium text-gray-500 uppercase">Priority</div>
                    <div class="text-sm font-semibold text-gray-900">High</div>
                </div>
                <div class="p-4 transition bg-white border border-gray-200 rounded-lg hover:shadow-md">
                    <div class="mb-2 text-2xl">🎯</div>
                    <div class="text-xs font-medium text-gray-500 uppercase">Progress</div>
                    <div class="text-sm font-semibold text-gray-900">Active</div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col items-center justify-center gap-3 sm:flex-row">


                <a href="https://wa.me/6289530203497"
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