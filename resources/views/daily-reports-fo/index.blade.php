<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    📋 Laporan Harian FO
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $branch->name }} | {{ $branchTime->format('d M Y') }}
                </p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('daily-reports-fo.history') }}"
                    class="flex items-center gap-2 px-4 py-2 text-white transition bg-gray-600 rounded-lg hover:bg-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    History
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

            @if ($needShiftSelection)
                {{-- MODAL PILIH SHIFT --}}
                <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" id="shiftModal">
                    <div class="w-full max-w-md p-6 mx-4 bg-white rounded-lg shadow-xl">
                        <h3 class="mb-4 text-xl font-semibold text-gray-800">
                            🕐 Pilih Shift Hari Ini
                        </h3>
                        <p class="mb-6 text-sm text-gray-600">
                            Pilih shift yang akan Anda kerjakan hari ini. Setelah memilih dan membuat laporan pertama,
                            shift tidak dapat diubah.
                        </p>

                        <form method="POST" action="{{ route('daily-reports-fo.select-shift') }}">
                            @csrf

                            <div class="space-y-3">
                                {{-- Shift Pagi --}}
                                <label
                                    class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 hover:bg-blue-50">
                                    <input type="radio" name="shift" value="pagi" required
                                        class="w-5 h-5 text-blue-600">
                                    <div class="ml-3">
                                        <p class="font-semibold text-gray-800">🌅 Shift Pagi</p>
                                        <p class="text-sm text-gray-600">08:00 - 16:00</p>
                                        <p class="text-xs text-gray-500">Laporan: 10:00, 12:00, 14:00, 16:00</p>
                                    </div>
                                </label>

                                {{-- Shift Siang --}}
                                <label
                                    class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-purple-500 hover:bg-purple-50">
                                    <input type="radio" name="shift" value="siang" required
                                        class="w-5 h-5 text-purple-600">
                                    <div class="ml-3">
                                        <p class="font-semibold text-gray-800">🌆 Shift Siang</p>
                                        <p class="text-sm text-gray-600">14:00 - 22:00</p>
                                        <p class="text-xs text-gray-500">Laporan: 15:00, 17:00, 19:00, 21:00</p>
                                    </div>
                                </label>
                            </div>

                            @error('shift')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror

                            <button type="submit"
                                class="w-full px-6 py-3 mt-6 text-white transition bg-teal-600 rounded-lg hover:bg-teal-700">
                                ✅ Konfirmasi Shift
                            </button>
                        </form>
                    </div>
                </div>
            @else
                {{-- DASHBOARD SLOT --}}

                {{-- Shift Info Banner --}}
                <div class="p-4 mb-6 rounded-lg {{ $selectedShift === 'pagi' ? 'bg-blue-50' : 'bg-purple-50' }}">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex items-center justify-center w-12 h-12 rounded-full {{ $selectedShift === 'pagi' ? 'bg-blue-200' : 'bg-purple-200' }}">
                                <span class="text-2xl">{{ $selectedShift === 'pagi' ? '🌅' : '🌆' }}</span>
                            </div>
                            <div>
                                <p
                                    class="font-semibold {{ $selectedShift === 'pagi' ? 'text-blue-800' : 'text-purple-800' }}">
                                    {{ $selectedShift === 'pagi' ? 'Shift Pagi' : 'Shift Siang' }}
                                </p>
                                <p
                                    class="text-sm {{ $selectedShift === 'pagi' ? 'text-blue-600' : 'text-purple-600' }}">
                                    {{ config('daily_report_fo.shifts')[$selectedShift]['start_time'] }} -
                                    {{ config('daily_report_fo.shifts')[$selectedShift]['end_time'] }}
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-600">Waktu Cabang ({{ $branch->timezone }})</p>
                            <p class="text-lg font-semibold text-gray-800" id="currentTime">
                                {{ $branchTime->format('H:i:s') }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Progress Bar --}}
                <div class="p-6 mb-6 bg-white rounded-lg shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-lg font-semibold text-gray-800">Progress Hari Ini</h3>
                        <span class="text-2xl font-bold text-teal-600">
                            {{ $stats['completed_slots'] }}/{{ $stats['total_slots'] }}
                        </span>
                    </div>
                    <div class="w-full h-4 bg-gray-200 rounded-full">
                        <div class="h-4 transition-all bg-teal-600 rounded-full"
                            style="width: {{ $stats['progress_percentage'] }}%">
                        </div>
                    </div>
                    <p class="mt-2 text-sm text-gray-600">
                        {{ number_format($stats['progress_percentage'], 0) }}% selesai
                    </p>
                </div>

                {{-- Slot Cards --}}
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    @foreach ($slotData as $slot)
                        <div
                            class="p-6 bg-white rounded-lg shadow-sm border-l-4
                            {{ $slot['has_report'] ? 'border-green-500' : ($slot['status'] === 'open' ? 'border-orange-500' : 'border-gray-300') }}">

                            {{-- Slot Header --}}
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-800">
                                        📍 Slot {{ $slot['slot_number'] }} - {{ $slot['slot_time'] }}
                                    </h4>
                                    <p class="text-sm text-gray-600">
                                        Window: {{ $slot['window']['start']->format('H:i') }} -
                                        {{ $slot['window']['end']->format('H:i') }}
                                    </p>
                                </div>

                                {{-- Status Badge --}}
                                @if ($slot['has_report'])
                                    <span
                                        class="px-3 py-1 text-sm font-semibold text-green-700 bg-green-100 rounded-full">
                                        ✅ Sudah Lapor
                                    </span>
                                @elseif ($slot['status'] === 'open')
                                    <span
                                        class="px-3 py-1 text-sm font-semibold text-orange-700 bg-orange-100 rounded-full">
                                        ⏰ Bisa Lapor
                                    </span>
                                @elseif ($slot['status'] === 'closed')
                                    <span class="px-3 py-1 text-sm font-semibold text-red-700 bg-red-100 rounded-full">
                                        🔒 Tutup
                                    </span>
                                @else
                                    <span
                                        class="px-3 py-1 text-sm font-semibold text-gray-700 bg-gray-100 rounded-full">
                                        ⏳ Menunggu
                                    </span>
                                @endif
                            </div>

                            {{-- Slot Content --}}
                            @if ($slot['has_report'])
                                {{-- Sudah Ada Laporan --}}
                                <div class="p-4 rounded-lg bg-green-50">
                                    <p class="mb-2 text-sm text-green-800">
                                        <strong>Upload:</strong>
                                        {{ $slot['existing_report']->uploaded_at->format('H:i:s') }}
                                    </p>
                                    <p class="mb-3 text-sm text-green-800">
                                        <strong>Total Foto:</strong> {{ $slot['existing_report']->photos->count() }}
                                    </p>

                                    <div class="flex gap-2">
                                        @if ($slot['can_edit'])
                                            <a href="{{ route('daily-reports-fo.slot.show', $slot['slot_number']) }}"
                                                class="flex items-center gap-2 px-4 py-2 text-white transition bg-blue-600 rounded-lg hover:bg-blue-700">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                Edit Laporan
                                            </a>
                                        @else
                                            <button disabled
                                                class="flex items-center gap-2 px-4 py-2 text-gray-500 bg-gray-100 rounded-lg cursor-not-allowed">
                                                🔒 Sudah Ditutup
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @elseif ($slot['status'] === 'open')
                                {{-- Slot Terbuka --}}
                                <div class="p-4 rounded-lg bg-orange-50">
                                    <p class="mb-2 text-sm font-semibold text-orange-800">
                                        ⏱️ Waktu Tersisa:
                                    </p>
                                    <p class="mb-3 text-2xl font-bold text-orange-600 countdown-timer"
                                        data-seconds="{{ $slot['time_remaining'] }}">
                                        {{ \App\Helpers\TimeHelper::formatCountdown($slot['time_remaining']) }}
                                    </p>

                                    <a href="{{ route('daily-reports-fo.slot.show', $slot['slot_number']) }}"
                                        class="flex items-center justify-center gap-2 px-6 py-3 text-white transition bg-teal-600 rounded-lg hover:bg-teal-700">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4" />
                                        </svg>
                                        📝 Upload Laporan Sekarang
                                    </a>
                                </div>
                            @elseif ($slot['status'] === 'waiting')
                                {{-- Slot Menunggu --}}
                                <div class="p-4 rounded-lg bg-gray-50">
                                    <p class="mb-2 text-sm text-gray-700">
                                        Slot akan dibuka dalam:
                                    </p>
                                    <p class="text-xl font-semibold text-gray-800 countdown-timer"
                                        data-seconds="{{ $slot['time_until_open'] }}">
                                        {{ \App\Helpers\TimeHelper::formatCountdown($slot['time_until_open']) }}
                                    </p>
                                </div>
                            @else
                                {{-- Slot Tutup --}}
                                <div class="p-4 rounded-lg bg-red-50">
                                    <p class="text-sm text-red-700">
                                        ❌ Window upload sudah ditutup.
                                    </p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                {{-- Info Box --}}
                <div class="p-4 mt-6 rounded-lg bg-blue-50">
                    <p class="text-sm text-blue-800">
                        💡 <strong>Petunjuk:</strong> Upload laporan saat slot berwarna orange. Setiap slot wajib upload
                        6 kategori foto (Like & Comment: FB, IG, TikTok).
                    </p>
                </div>
            @endif

        </div>
    </div>

    {{-- JavaScript --}}
    <script>
        // Update current time setiap detik
        // setInterval(() => {
        //     const timeEl = document.getElementById('currentTime');
        //     if (timeEl) {
        //         const now = new Date();
        //         timeEl.textContent = now.toTimeString().split(' ')[0];
        //     }
        // }, 1000);
        // Server time sync
        @if (!$needShiftSelection)
        let serverTime = {{ $serverTimestamp }} * 1000; // Convert to milliseconds
        const branchTimezone = '{{ \App\Helpers\TimeHelper::getTimezoneStringForView($branchTimezone) }}';

        // Update current time setiap detik (synced dengan server)
        setInterval(() => {
            serverTime += 1000; // Increment 1 detik
            
            const timeEl = document.getElementById('currentTime');
            if (timeEl) {
                const date = new Date(serverTime);
                timeEl.textContent = date.toLocaleTimeString('id-ID', {
                    timeZone: branchTimezone,
                    hour12: false,
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });
            }
        }, 1000);
        @endif

        // Countdown timers
        function updateCountdowns() {
            document.querySelectorAll('.countdown-timer').forEach(el => {
                let seconds = parseInt(el.dataset.seconds);

                if (seconds <= 0) {
                    el.textContent = '00:00:00';
                    return;
                }

                seconds--;
                el.dataset.seconds = seconds;

                const hours = Math.floor(seconds / 3600);
                const minutes = Math.floor((seconds % 3600) / 60);
                const secs = seconds % 60;

                el.textContent =
                    `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
            });
        }

        setInterval(updateCountdowns, 1000);

        // Auto refresh page setiap 5 menit (untuk update slot status)
        setTimeout(() => {
            location.reload();
        }, 5 * 60 * 1000); // 5 minutes
    </script>
</x-app-layout>
