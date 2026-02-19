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

            @if (session('success'))
                <div class="p-4 mb-6 text-green-700 bg-green-100 rounded-lg">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="p-4 mb-6 text-red-700 bg-red-100 rounded-lg">{{ session('error') }}</div>
            @endif

            @if ($needShiftSelection)
                {{-- ============================================================ --}}
                {{-- MODAL PILIH SHIFT                                             --}}
                {{-- ============================================================ --}}
                <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                    <div class="w-full max-w-md p-6 mx-4 bg-white rounded-lg shadow-xl">
                        <h3 class="mb-4 text-xl font-semibold text-gray-800">🕐 Pilih Shift Hari Ini</h3>
                        <p class="mb-6 text-sm text-gray-600">
                            Pilih shift yang akan Anda kerjakan hari ini. Setelah memilih dan membuat laporan pertama,
                            shift tidak dapat diubah.
                        </p>

                        <form method="POST" action="{{ route('daily-reports-fo.select-shift') }}">
                            @csrf
                            <div class="space-y-3">
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
                {{-- ============================================================ --}}
                {{-- DASHBOARD SLOT                                                --}}
                {{-- ============================================================ --}}

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
                            style="width: {{ $stats['progress_percentage'] }}%"></div>
                    </div>
                    <div class="flex items-center justify-between mt-2">
                        <p class="text-sm text-gray-600">{{ number_format($stats['progress_percentage'], 0) }}% selesai
                        </p>
                        {{-- Ringkasan status validasi --}}
                        @if ($stats['completed_slots'] > 0)
                            <div class="flex items-center gap-3 text-xs">
                                @if ($stats['approved'] > 0)
                                    <span class="font-semibold text-green-600">✅ {{ $stats['approved'] }}
                                        disetujui</span>
                                @endif
                                @if ($stats['rejected'] > 0)
                                    <span class="font-semibold text-red-600">❌ {{ $stats['rejected'] }} ditolak</span>
                                @endif
                                @if ($stats['pending'] > 0)
                                    <span class="font-semibold text-yellow-600">⏳ {{ $stats['pending'] }}
                                        pending</span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Slot Cards --}}
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    @foreach ($slotData as $slot)
                        @php
                            $report = $slot['existing_report'];
                            $validationStatus = $report?->validation_status ?? null;
                            // Border color: prioritaskan validation status jika laporan ada
                            $borderColor = 'border-gray-300'; // default: belum ada laporan / waiting / closed
                            if ($slot['has_report']) {
                                $borderColor = match ($validationStatus) {
                                    'approved' => 'border-green-500',
                                    'rejected' => 'border-red-500',
                                    default => 'border-yellow-400', // pending
                                };
                            } elseif ($slot['status'] === 'open') {
                                $borderColor = 'border-orange-500';
                            }
                        @endphp

                        <div class="p-6 bg-white rounded-lg shadow-sm border-l-4 {{ $borderColor }}">

                            {{-- Slot Header --}}
                            <div class="flex flex-wrap items-start justify-between gap-4 mb-4 md:gap-0">
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-800">
                                        📍 Slot {{ $slot['slot_number'] }} — {{ $slot['slot_time'] }}
                                    </h4>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        Window FO: {{ $slot['window']['start']->format('H:i') }} –
                                        {{ $slot['window']['end']->format('H:i') }}
                                        &nbsp;|&nbsp;
                                        Validasi: {{ $slot['window']['end']->format('H:i') }} –
                                        {{ $slot['window']['end']->copy()->addMinutes(config('daily_report_fo.validation_window_minutes', 15))->format('H:i') }}
                                    </p>
                                </div>

                                {{-- Badge Upload Status --}}
                                <div class="flex flex-row items-end gap-1 md:flex-col">
                                    @if ($slot['has_report'])
                                        <span
                                            class="px-2 py-1 text-xs font-semibold text-gray-700 bg-gray-100 rounded-full">
                                            📤 Terkirim
                                        </span>
                                    @elseif ($slot['status'] === 'open')
                                        <span
                                            class="px-2 py-1 text-xs font-semibold text-orange-700 bg-orange-100 rounded-full">
                                            ⏰ Bisa Lapor
                                        </span>
                                    @elseif ($slot['status'] === 'closed')
                                        <span
                                            class="px-2 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded-full">
                                            🔒 Tutup
                                        </span>
                                    @else
                                        <span
                                            class="px-2 py-1 text-xs font-semibold text-gray-500 bg-gray-100 rounded-full">
                                            ⏳ Menunggu
                                        </span>
                                    @endif

                                    {{-- Badge Validasi (hanya jika ada laporan) --}}
                                    @if ($slot['has_report'])
                                        @if ($validationStatus === 'approved')
                                            <span
                                                class="px-2 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full">
                                                ✅ Disetujui
                                            </span>
                                        @elseif ($validationStatus === 'rejected')
                                            <span
                                                class="px-2 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded-full">
                                                ❌ Ditolak
                                            </span>
                                        @else
                                            <span
                                                class="px-2 py-1 text-xs font-semibold text-yellow-700 bg-yellow-100 rounded-full">
                                                ⏳ Belum Divalidasi
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            {{-- Slot Content --}}
                            @if ($slot['has_report'])
                                @php
                                    $metrikDetails = $report->details
                                        ->filter(function ($detail) {
                                            return in_array($detail->field->code, [
                                                'mb_omset',
                                                'mb_revenue',
                                                'mb_jumlah_akad',
                                            ]);
                                        })
                                        ->keyBy(fn($d) => $d->field->code);
                                @endphp

                                {{-- Background sesuai status validasi --}}
                                <div
                                    class="p-4 rounded-lg
                                    {{ $validationStatus === 'approved'
                                        ? 'bg-green-50'
                                        : ($validationStatus === 'rejected'
                                            ? 'bg-red-50'
                                            : 'bg-yellow-50') }}">

                                    {{-- Upload time & total foto --}}
                                    <div class="flex items-center justify-between mb-3">
                                        <p class="text-sm text-gray-600">
                                            <strong>Upload:</strong> {{ $report->uploaded_at->format('H:i:s') }}
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            <strong>📷</strong> {{ $report->total_photos }} foto
                                        </p>
                                    </div>

                                    {{-- Metrik Bisnis --}}
                                    @if ($metrikDetails->count() > 0)
                                        <div class="pt-3 mt-2 border-t border-gray-200">
                                            <p class="mb-2 text-xs font-semibold tracking-wide text-gray-500 uppercase">
                                                📊 Metrik Bisnis
                                            </p>
                                            <div class="grid grid-cols-3 gap-2">
                                                @if ($metrikDetails->has('mb_omset'))
                                                    <div class="p-2 text-center bg-white rounded-lg shadow-sm">
                                                        <p class="text-xs text-gray-400">Omset</p>
                                                        <p class="text-sm font-bold text-gray-800">
                                                            Rp
                                                            {{ number_format($metrikDetails['mb_omset']->value_number, 0, ',', '.') }}
                                                        </p>
                                                    </div>
                                                @endif
                                                @if ($metrikDetails->has('mb_revenue'))
                                                    <div class="p-2 text-center bg-white rounded-lg shadow-sm">
                                                        <p class="text-xs text-gray-400">Revenue</p>
                                                        <p class="text-sm font-bold text-gray-800">
                                                            Rp
                                                            {{ number_format($metrikDetails['mb_revenue']->value_number, 0, ',', '.') }}
                                                        </p>
                                                    </div>
                                                @endif
                                                @if ($metrikDetails->has('mb_jumlah_akad'))
                                                    <div class="p-2 text-center bg-white rounded-lg shadow-sm">
                                                        <p class="text-xs text-gray-400">Akad</p>
                                                        <p class="text-sm font-bold text-gray-800">
                                                            {{ number_format($metrikDetails['mb_jumlah_akad']->value_number, 0, ',', '.') }}
                                                        </p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Info validasi jika sudah divalidasi --}}
                                    @if ($report->validation)
                                        <div class="pt-3 mt-3 border-t border-gray-200">
                                            <p class="text-xs text-gray-500">
                                                <strong>Tindakan:</strong> {{ $report->validation->action->name }}
                                                &nbsp;·&nbsp;
                                                {{ $report->validation->validated_at->format('H:i') }}
                                            </p>
                                            @if ($report->validation->catatan)
                                                <p class="mt-1 text-xs text-gray-500">
                                                    <strong>Catatan:</strong> {{ $report->validation->catatan }}
                                                </p>
                                            @endif
                                        </div>
                                    @endif

                                    {{-- Action Button --}}
                                    <div class="mt-3">
                                        @if ($slot['can_edit'])
                                            <a href="{{ route('daily-reports-fo.slot.show', $slot['slot_number']) }}"
                                                class="flex items-center gap-2 px-4 py-2 text-sm text-white transition bg-blue-600 rounded-lg hover:bg-blue-700 w-fit">
                                                ✏️ Edit Laporan
                                            </a>
                                        @else
                                            <span class="text-xs text-gray-400">Window edit sudah tutup</span>
                                        @endif
                                    </div>
                                </div>
                            @elseif ($slot['status'] === 'open')
                                <div class="p-4 rounded-lg bg-orange-50">
                                    <p class="mb-2 text-sm font-semibold text-orange-800">⏱️ Waktu Tersisa:</p>
                                    <p class="mb-3 text-2xl font-bold text-orange-600 countdown-timer"
                                        data-seconds="{{ $slot['time_remaining'] }}">
                                        {{ \App\Helpers\TimeHelper::formatCountdown($slot['time_remaining']) }}
                                    </p>
                                    <a href="{{ route('daily-reports-fo.slot.show', $slot['slot_number']) }}"
                                        class="flex items-center justify-center gap-2 px-6 py-3 text-white transition bg-teal-600 rounded-lg hover:bg-teal-700">
                                        📝 Upload Laporan Sekarang
                                    </a>
                                </div>
                            @elseif ($slot['status'] === 'waiting')
                                <div class="p-4 rounded-lg bg-gray-50">
                                    <p class="mb-2 text-sm text-gray-600">Slot akan dibuka dalam:</p>
                                    <p class="text-xl font-semibold text-gray-800 countdown-timer"
                                        data-seconds="{{ $slot['time_until_open'] }}">
                                        {{ \App\Helpers\TimeHelper::formatCountdown($slot['time_until_open']) }}
                                    </p>
                                </div>
                            @else
                                <div class="p-4 rounded-lg bg-red-50">
                                    <p class="text-sm text-red-600">❌ Window upload sudah ditutup tanpa laporan.</p>
                                </div>
                            @endif

                        </div>
                    @endforeach
                </div>

                {{-- Info Box --}}
                <div class="p-4 mt-6 rounded-lg bg-blue-50">
                    <p class="text-sm text-blue-800">
                        💡 <strong>Info:</strong>
                        Setiap slot punya window <strong>15 menit</strong> untuk upload laporan.
                        Setelah itu manager punya <strong>15 menit</strong> untuk memvalidasi.
                        Laporan yang disetujui ditandai hijau, ditolak merah.
                    </p>
                </div>

            @endif
        </div>
    </div>

    <script>
        @if (!$needShiftSelection)
            let serverTime = {{ $serverTimestamp }} * 1000;
            const branchTimezone = '{{ \App\Helpers\TimeHelper::getTimezoneStringForView($branchTimezone) }}';

            setInterval(() => {
                serverTime += 1000;
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

        function updateCountdowns() {
            document.querySelectorAll('.countdown-timer').forEach(el => {
                let seconds = parseInt(el.dataset.seconds);
                if (seconds <= 0) {
                    el.textContent = '00:00:00';
                    return;
                }
                seconds--;
                el.dataset.seconds = seconds;
                const h = Math.floor(seconds / 3600);
                const m = Math.floor((seconds % 3600) / 60);
                const s = seconds % 60;
                el.textContent =
                    `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
            });
        }

        setInterval(updateCountdowns, 1000);
        setTimeout(() => location.reload(), 5 * 60 * 1000);
    </script>
</x-app-layout>
