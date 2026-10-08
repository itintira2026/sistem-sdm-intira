<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            👥 KPI Tim — {{ $monthLabel }}
        </h2>
    </x-slot>

    <div class="py-8 sm:py-12">
        <div class="max-w-5xl px-4 mx-auto sm:px-6 lg:px-8" x-data="{ search: '' }">

            <div class="flex items-center justify-between p-4 mb-4 bg-white border border-gray-200 rounded-xl">
                <a href="{{ route('tasks.team-drilldown', ['month' => $prevMonth->month, 'year' => $prevMonth->year]) }}"
                    class="px-3 py-2 text-sm text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                    ← Sebelumnya
                </a>
                <span class="font-semibold text-gray-800">{{ $monthLabel }}</span>
                <a href="{{ route('tasks.team-drilldown', ['month' => $nextMonth->month, 'year' => $nextMonth->year]) }}"
                    class="px-3 py-2 text-sm text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                    Berikutnya →
                </a>
            </div>

            @if ($groups->isEmpty())
                <div class="p-8 text-sm text-center text-gray-400 bg-white border border-gray-200 rounded-xl">
                    Tidak ada anggota tim di bawah lingkup Anda.
                </div>
            @else
                @php $big = $groups->count() === 1; @endphp

                <div class="mb-4">
                    <input type="text" x-model="search" placeholder="Cari nama..."
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg sm:w-64 focus:outline-none focus:ring-2 focus:ring-teal-500">
                </div>

                <div class="grid grid-cols-1 gap-4 {{ $big ? '' : 'lg:grid-cols-2 items-start' }}">
                    @foreach ($groups as $key => $group)
                        @php $stats = $group['stats']; @endphp

                        @if ($big)
                            {{-- Satu grup aja -> daftar di kiri, panel ringkasan besar di kanan --}}
                            <div class="grid grid-cols-1 gap-4 lg:grid-cols-[1.1fr_1.4fr] items-start">
                                <div class="p-4 bg-white border border-gray-200 rounded-xl sm:p-5">
                                    <h3 class="mb-3 text-sm font-semibold text-gray-700">{{ $group['label'] }}</h3>
                                    @include('tasks.partials.team-drilldown-rows', [
                                        'group' => $group,
                                        'month' => $month,
                                        'year' => $year,
                                    ])
                                </div>

                                <div class="p-5 border border-gray-200 rounded-xl bg-gray-50">
                                    <p class="mb-4 text-xs font-medium text-gray-500">Ringkasan {{ $group['label'] }}
                                    </p>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-xs text-gray-400">Rata-rata</p>
                                            <p class="text-2xl font-bold text-gray-700">
                                                {{ number_format($stats['average'], 1) }}%</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-400">Total orang</p>
                                            <p class="text-2xl font-bold text-gray-700">{{ $stats['total'] }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-green-500">&ge; {{ 80 }}%</p>
                                            <p class="text-2xl font-bold text-green-600">{{ $stats['high'] }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-red-500">&lt; {{ 40 }}%</p>
                                            <p class="text-2xl font-bold text-red-600">{{ $stats['low'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- Dua grup sekaligus -> tiap grup 1 kartu, ringkasan mini di atas listnya sendiri --}}
                            <div class="p-4 bg-white border border-gray-200 rounded-xl sm:p-5">
                                <div class="flex items-center justify-between mb-3">
                                    <h3 class="text-sm font-semibold text-gray-700">{{ $group['label'] }}</h3>
                                    <span class="text-xs text-gray-400">{{ $stats['total'] }} orang</span>
                                </div>

                                <div class="grid grid-cols-3 gap-2 mb-3">
                                    <div class="p-2 text-center rounded-lg bg-gray-50">
                                        <p class="text-[10px] text-gray-400">Rata-rata</p>
                                        <p class="text-sm font-bold text-gray-700">
                                            {{ number_format($stats['average'], 1) }}%</p>
                                    </div>
                                    <div class="p-2 text-center rounded-lg bg-green-50">
                                        <p class="text-[10px] text-green-500">&ge;80%</p>
                                        <p class="text-sm font-bold text-green-600">{{ $stats['high'] }}</p>
                                    </div>
                                    <div class="p-2 text-center rounded-lg bg-red-50">
                                        <p class="text-[10px] text-red-500">&lt;40%</p>
                                        <p class="text-sm font-bold text-red-600">{{ $stats['low'] }}</p>
                                    </div>
                                </div>

                                @include('tasks.partials.team-drilldown-rows', [
                                    'group' => $group,
                                    'month' => $month,
                                    'year' => $year,
                                ])
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
