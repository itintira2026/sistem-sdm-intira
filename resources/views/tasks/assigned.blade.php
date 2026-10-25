<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">👥 Tim & Tugas yang Saya Berikan</h2>
            <a href="{{ route('tasks.create') }}"
                class="px-4 py-2 text-sm text-white bg-teal-500 rounded-lg hover:bg-teal-600">+ Bikin Tugas KPI</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto space-y-8 sm:px-6 lg:px-8">
            {{-- A. Bobot per orang --}}
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">Bobot KPI Tim</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse ($team as $member)
                        @php
                            $row = $weightTotals->get($member->id);
                            $total = (float) ($row->total ?? 0);
                            $belum = (int) ($row->belum_diatur ?? 0);
                            $ok = $row && $belum === 0 && abs($total - 100) <= 0.01;
                        @endphp
                        <div class="flex items-center justify-between px-6 py-3">
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $member->name }}</p>
                                <p class="text-xs text-gray-400">
                                    {{ $row->jumlah ?? 0 }} tugas aktif · {{ $member->division?->name }}
                                </p>
                            </div>
                            <div class="flex items-center gap-3">
                                @if (!$row)
                                    <span class="text-xs text-gray-400">Belum ada tugas</span>
                                @else
                                    @if ($ok)
                                        <span
                                            class="px-2 py-0.5 text-xs font-semibold text-green-700 bg-green-100 rounded-full">✅
                                            100%</span>
                                    @elseif ($belum > 0)
                                        <span
                                            class="px-2 py-0.5 text-xs font-semibold text-red-700 bg-red-100 rounded-full">
                                            ⚠️ {{ $belum }} tugas belum diberi bobot
                                        </span>
                                    @else
                                        <span
                                            class="px-2 py-0.5 text-xs font-semibold text-yellow-700 bg-yellow-100 rounded-full">
                                            ⚠️ {{ rtrim(rtrim(number_format($total, 2), '0'), '.') }}%
                                        </span>
                                    @endif
                                    <a href="{{ route('tasks.weight-reinput', $member->id) }}"
                                        class="px-3 py-1 text-xs text-white bg-teal-500 rounded-lg hover:bg-teal-600">Atur
                                        Bobot</a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="py-8 text-sm text-center text-gray-400">Tidak ada bawahan.</p>
                    @endforelse
                </div>
            </div>

            {{-- Navigasi bulan — cuma berlaku buat Tugas Selesai --}}
            <div
                class="flex flex-col items-center justify-between gap-2 p-4 mb-4 bg-white border border-gray-200 rounded-xl sm:flex-row">
                <a href="{{ route('tasks.assigned', ['month' => $prevMonth->month, 'year' => $prevMonth->year]) }}"
                    class="w-full px-3 py-2 text-sm text-center text-gray-700 bg-gray-100 rounded-lg sm:w-auto hover:bg-gray-200">
                    ← Sebelumnya
                </a>
                <div class="text-center">
                    <p class="text-sm font-semibold text-gray-800">{{ $startOfMonth->translatedFormat('F Y') }}</p>
                    <p class="text-[11px] text-gray-400">berlaku untuk riwayat Tugas Selesai</p>
                </div>
                <a href="{{ route('tasks.assigned', ['month' => $nextMonth->month, 'year' => $nextMonth->year]) }}"
                    class="w-full px-3 py-2 text-sm text-center text-gray-700 bg-gray-100 rounded-lg sm:w-auto hover:bg-gray-200">
                    Berikutnya →
                </a>
            </div>

            {{-- B. Riwayat tugas --}}
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">Tugas yang Saya Buat</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse ($tasks as $task)
                        <div class="flex items-center justify-between px-6 py-3">
                            <div class="min-w-0">
                                <a href="{{ route('tasks.show', $task->id) }}"
                                    class="text-sm font-medium text-gray-800 hover:text-teal-600">{{ $task->title }}</a>
                                <p class="text-xs text-gray-400">
                                    {{ ucfirst(str_replace('_', ' ', $task->repeat_mode)) }}
                                    · {{ $task->period_start?->format('d M') }} -
                                    {{ $task->period_end?->format('d M Y') }}
                                    · {{ $task->assignees_count }} orang
                                    @if ($task->unweighted_count > 0)
                                        · <span class="text-yellow-600">⚠️ {{ $task->unweighted_count }} belum diberi
                                            bobot</span>
                                    @endif
                                </p>
                                @if ($task->assignees->isNotEmpty())
                                    <div class="flex flex-wrap items-center gap-2 mt-1">
                                        <span class="text-xs text-yellow-600">⚠️ Belum diberi bobot:</span>
                                        @foreach ($task->assignees as $a)
                                            <a href="{{ route('tasks.weight-reinput', $a->user_id) }}"
                                                class="px-2 py-0.5 text-xs text-teal-700 bg-teal-50 rounded-full hover:bg-teal-100">
                                                {{ $a->user->name }} →
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            @if ($task->assignees_count > 1)
                                <a href="{{ route('tasks.assignees-weight-status', $task->id) }}"
                                    class="text-xs text-teal-600 hover:underline shrink-0">Status bobot</a>
                            @endif
                        </div>
                    @empty
                        <p class="py-8 text-sm text-center text-gray-400">Belum ada tugas yang Anda buat.</p>
                    @endforelse
                </div>
                <div class="px-6 py-3">{{ $tasks->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
