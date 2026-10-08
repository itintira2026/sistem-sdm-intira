<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                🗓️ Set Hari Kerja
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Klik tanggal untuk tandai sebagai hari kerja. Klik lagi untuk batalkan — misalnya kalau ternyata
                hari itu jadi libur mendadak.
            </p>
        </div>
    </x-slot>

    <div class="py-12" x-data="{
        selected: @js($workingDays),
        csrf: '{{ csrf_token() }}',
        toggle(tanggal) {
            fetch('{{ route('period-working-days.toggle') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrf,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ tanggal: tanggal }),
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'added') {
                        this.selected.push(tanggal);
                    } else {
                        this.selected = this.selected.filter(d => d !== tanggal);
                    }
                })
                .catch(() => alert('Gagal menyimpan, coba lagi.'));
        }
    }">
        <div class="max-w-4xl mx-auto space-y-6 sm:px-6 lg:px-8">

            {{-- Navigasi bulan --}}
            <div class="flex items-center justify-between p-4 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <a href="{{ route('period-working-days.index', ['month' => $prevMonth]) }}"
                    class="px-3 py-2 text-sm text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                    ← Bulan Sebelumnya
                </a>
                <span class="font-semibold text-gray-800">
                    {{ $startOfMonth->translatedFormat('F Y') }}
                </span>
                <a href="{{ route('period-working-days.index', ['month' => $nextMonth]) }}"
                    class="px-3 py-2 text-sm text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                    Bulan Berikutnya →
                </a>
            </div>

            {{-- Counter --}}
            <div class="flex items-center justify-between p-4 border border-teal-200 rounded-lg bg-teal-50">
                <span class="text-sm font-medium text-teal-800">Total Hari Kerja Bulan Ini</span>
                <span class="text-2xl font-bold text-teal-700" x-text="selected.length"></span>
            </div>

            {{-- Kalender --}}
            <div class="p-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="grid grid-cols-7 gap-2 mb-2">
                    @foreach (['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $hari)
                        <span class="text-xs font-semibold text-center text-gray-400">{{ $hari }}</span>
                    @endforeach
                </div>

                <div class="grid grid-cols-7 gap-2">
                    {{-- Blank cells sebelum tanggal 1 (minggu mulai Senin) --}}
                    @for ($i = 0; $i < $startOfMonth->dayOfWeekIso - 1; $i++)
                        <div></div>
                    @endfor

                    @for ($day = 1; $day <= $endOfMonth->day; $day++)
                        @php
                            $currentDate = $startOfMonth->copy()->day($day)->toDateString();
                            $dayOfWeekIso = $startOfMonth->copy()->day($day)->dayOfWeekIso;
                            $isWeekend = $dayOfWeekIso >= 6;
                        @endphp
                        <button type="button" @click="toggle('{{ $currentDate }}')"
                            :class="selected.includes('{{ $currentDate }}') ?
                                'bg-teal-500 text-white border-teal-500' :
                                '{{ $isWeekend ? 'bg-gray-50 text-gray-400' : 'bg-white text-gray-700' }} border-gray-200 hover:border-teal-400'"
                            class="text-sm font-medium transition border rounded-lg h-11">
                            {{ $day }}
                        </button>
                    @endfor
                </div>

                <p class="mt-4 text-xs text-gray-400">
                    Sabtu/Minggu ditampilkan abu-abu sebagai penanda visual doang — tetap bisa ditandai hari kerja
                    kalau memang perlu (misal event khusus).
                </p>
            </div>

        </div>
    </div>
</x-app-layout>
