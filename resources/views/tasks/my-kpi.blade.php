<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">📊 KPI Saya</h2>
    </x-slot>

    <div class="py-8 sm:py-12">
        <div class="max-w-5xl px-4 mx-auto sm:px-6 lg:px-8">

            <div class="flex items-center justify-between p-4 mb-4 bg-white border border-gray-200 rounded-xl">
                <a href="{{ route('tasks.my-kpi', ['month' => $prevMonth->month, 'year' => $prevMonth->year]) }}"
                    class="px-3 py-2 text-sm text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                    ← Sebelumnya
                </a>
                <span class="font-semibold text-gray-800">{{ $startOfMonth->translatedFormat('F Y') }}</span>
                <a href="{{ route('tasks.my-kpi', ['month' => $nextMonth->month, 'year' => $nextMonth->year]) }}"
                    class="px-3 py-2 text-sm text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                    Berikutnya →
                </a>
            </div>

            <div class="grid grid-cols-1 gap-4 lg:grid-cols-[minmax(0,1fr)_minmax(0,1.6fr)] items-start">

                {{-- Kolom kiri: total --}}
                <div class="p-6 text-center border border-teal-200 rounded-xl bg-teal-50">
                    <p class="mb-1 text-sm font-medium text-teal-800">Total KPI Bulan Ini</p>
                    <p class="text-4xl font-bold text-teal-700">
                        {{ number_format($monthlyKpi->total_percentage ?? 0, 1) }}%
                    </p>
                    <p class="mt-1 text-xs text-teal-600">Dihitung live dari data terbaru</p>
                </div>

                {{-- Kolom kanan: breakdown --}}
                <div class="overflow-hidden bg-white border border-gray-200 rounded-xl">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="font-semibold text-gray-800">Breakdown Bobot</h3>
                    </div>

                    @if ($breakdown->isEmpty())
                        <p class="py-8 text-sm text-center text-gray-400">Tidak ada Tugas KPI di bulan ini.</p>
                    @else
                        <div class="divide-y divide-gray-100">
                            @foreach ($breakdown as $row)
                                @php $task = $row['task']; @endphp
                                <div class="px-6 py-4 {{ $row['assignee']->finalized_at ? 'bg-blue-100' : '' }}">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="flex-1 min-w-0">
                                            <a href="{{ route('tasks.show', $task->id) }}" target="_blank"
                                                class="text-sm font-medium text-gray-800 hover:text-teal-600">
                                                {{ $row['assignee']->finalized_at ? '🔒 ' : '' }}{{ $task->title }}
                                            </a>
                                            <p class="text-xs text-gray-400">
                                                {{ $task->period_start?->format('d M') }} -
                                                {{ $task->period_end?->format('d M Y') }}
                                                · Bobot {{ number_format($row['weight'], 2) }}%
                                            </p>
                                            <p class="text-xs text-gray-400">
                                                Target KPI:
                                                {{ $task->formatValue($row['assignee']->individual_target) }}
                                            </p>
                                        </div>
                                        <div class="text-right shrink-0">
                                            <p class="text-sm font-semibold text-gray-700">
                                                {{ number_format($row['percentage'], 1) }}%
                                            </p>
                                            <p class="text-xs text-gray-400">
                                                kontribusi: {{ number_format($row['contribution'], 2) }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="mt-2">
                                        <x-task-daily-detail :assignee="$row['assignee']" :show-fill-link="true" />
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
