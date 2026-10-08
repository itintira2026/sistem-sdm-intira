<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">📋 Tugas Saya</h2>
        <p class="mt-1 text-sm text-gray-500">{{ now()->translatedFormat('l, d F Y') }}</p>
    </x-slot>

    <div class="py-8 sm:py-12">
        <div class="max-w-5xl px-4 mx-auto sm:px-6 lg:px-8">

            {{-- Navigasi bulan — cuma berlaku buat Tugas Selesai --}}
            <div
                class="flex flex-col items-center justify-between gap-2 p-4 mb-4 bg-white border border-gray-200 rounded-xl sm:flex-row">
                <a href="{{ route('tasks.my-tasks', ['month' => $prevMonth->month, 'year' => $prevMonth->year]) }}"
                    class="w-full px-3 py-2 text-sm text-center text-gray-700 bg-gray-100 rounded-lg sm:w-auto hover:bg-gray-200">
                    ← Sebelumnya
                </a>
                <div class="text-center">
                    <p class="text-sm font-semibold text-gray-800">{{ $startOfMonth->translatedFormat('F Y') }}</p>
                    <p class="text-[11px] text-gray-400">berlaku untuk riwayat Tugas Selesai</p>
                </div>
                <a href="{{ route('tasks.my-tasks', ['month' => $nextMonth->month, 'year' => $nextMonth->year]) }}"
                    class="w-full px-3 py-2 text-sm text-center text-gray-700 bg-gray-100 rounded-lg sm:w-auto hover:bg-gray-200">
                    Berikutnya →
                </a>
            </div>

            <div class="grid items-start grid-cols-1 gap-4 mb-4 lg:grid-cols-2">

                {{-- PERLU DIKERJAKAN HARI INI --}}
                <div class="p-4 border border-teal-100 bg-teal-50 rounded-xl sm:p-5">
                    <h3 class="mb-3 text-sm font-semibold text-teal-800">🔥 Perlu Dikerjakan Hari Ini</h3>

                    @forelse ($activeAssignees as $row)
                        @php
                            $assignee = $row['assignee'];
                            $task = $row['task'];
                            $e = $row['actionableEntry'];
                            $status = $e?->validation_status;
                            $filled = $e?->hasSubmittedValue();
                            $canEdit =
                                $e &&
                                !$assignee->isFinalized() &&
                                in_array($status, ['pending', 'rejected']) &&
                                ($status !== 'pending' || $e->entry_date === null || $e->entry_date->isToday());
                            $pct = $row['percentage'];
                            $pctColor =
                                $pct >= 80 ? 'text-green-600' : ($pct >= 50 ? 'text-yellow-600' : 'text-red-600');
                        @endphp
                        <div class="p-3 mb-2 bg-white rounded-lg last:mb-0">
                            <div class="flex items-center justify-between gap-2">
                                <a href="{{ route('tasks.show', $task->id) }}"
                                    class="text-sm font-medium text-gray-800 truncate hover:text-teal-600">
                                    {{ $task->title }}
                                </a>
                                <span class="text-xs font-semibold shrink-0 {{ $pctColor }}">
                                    {{ number_format($pct, 1) }}%
                                </span>
                            </div>

                            @if ($assignee->isFinalized())
                                <p class="mt-1 text-xs font-semibold text-blue-600">
                                    🧊 {{ $assignee->isRemoved() ? 'Non-aktif' : 'Dibekukan' }}
                                </p>
                            @elseif ($e)
                                <p class="mt-0.5 text-xs text-gray-400">
                                    @if ($status === 'rejected')
                                        ❌ Ditolak, perlu diperbaiki
                                    @elseif ($status === 'checked')
                                        🔍 Dicek AM, menunggu approve korwil
                                    @elseif ($status === 'approved')
                                        ✅ Sudah disetujui
                                    @elseif (!$filled)
                                        {{ $e->entry_date ? 'Entry ' . $e->entry_date->format('d M') . ' belum diisi' : 'Belum diisi' }}
                                    @else
                                        ⏳ Sudah diisi, menunggu validasi
                                    @endif
                                </p>

                                @if ($canEdit)
                                    <x-modal-sheet
                                        title="{{ $status === 'rejected' ? 'Perbaiki' : (!$filled ? 'Isi' : 'Ubah') }} Entry — {{ $task->title }}">
                                        <x-slot:trigger>
                                            <button type="button" @click="open = true"
                                                class="w-full px-3 py-1.5 mt-2 text-xs text-white transition bg-teal-500 rounded-lg hover:bg-teal-600 sm:w-auto">
                                                {{ $status === 'rejected' ? 'Perbaiki' : (!$filled ? 'Isi Sekarang' : 'Ubah') }}
                                            </button>
                                        </x-slot:trigger>
                                        <x-entry-fill-form :entry="$e" />
                                    </x-modal-sheet>
                                @endif
                            @else
                                <p class="mt-0.5 text-xs text-gray-300">Tidak ada entry untuk hari ini.</p>
                            @endif
                        </div>
                    @empty
                        <p class="py-8 text-sm text-center text-teal-700">🎉 Semua sudah beres hari ini.</p>
                    @endforelse
                </div>

                {{-- TUGAS TAMBAHAN --}}
                <div class="p-4 bg-white border border-gray-200 rounded-xl sm:p-5">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-semibold text-gray-800">📝 Tugas Tambahan</h3>

                        <x-modal-sheet title="Catat Tugas Tambahan">
                            <x-slot:trigger>
                                <button type="button" @click="open = true"
                                    class="px-3 py-1.5 text-xs text-white transition bg-gray-700 rounded-lg hover:bg-gray-800">
                                    + Tambah
                                </button>
                            </x-slot:trigger>
                            <x-self-record-form />
                        </x-modal-sheet>
                    </div>

                    <div class="divide-y divide-gray-100">
                        @forelse ($selfRecordAssignees as $assignee)
                            @php $entry = $assignee->entries->first(); @endphp
                            <div class="flex items-center justify-between gap-2 py-2.5">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-700 truncate">{{ $assignee->task->title }}
                                    </p>
                                    <p class="text-xs text-gray-400">{{ $assignee->task->created_at->format('d M Y') }}
                                    </p>
                                </div>
                                <span class="text-xs text-gray-600 shrink-0">
                                    @if ($entry)
                                        @if ($assignee->task->tracking_mode === 'checklist')
                                            {{ $entry->value_checklist ? '✅ Selesai' : '⚪ Belum' }}
                                        @else
                                            {{ $assignee->task->formatValue($entry->value_number) }}
                                        @endif
                                    @endif
                                </span>
                            </div>
                        @empty
                            <p class="py-6 text-sm text-center text-gray-400">Belum ada catatan.</p>
                        @endforelse
                    </div>

                    @if ($selfRecordAssignees->hasPages())
                        <div class="mt-3 text-xs">
                            {{ $selfRecordAssignees->links() }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- TUGAS SELESAI --}}
            <div class="p-4 bg-white border border-gray-200 rounded-xl sm:p-5" x-data="{ open: {{ $finishedOpenDefault ? 'true' : 'false' }} }">
                <button type="button" @click="open = !open" class="flex items-center justify-between w-full text-left">
                    <div>
                        <p class="text-sm font-semibold text-gray-800">
                            Tugas Selesai · {{ $startOfMonth->translatedFormat('F Y') }}
                        </p>
                        <p class="text-xs text-gray-400">
                            {{ $finishedStats['total'] }} tugas · rata-rata
                            {{ number_format($finishedStats['average'], 1) }}%
                        </p>
                    </div>
                    <span class="text-xs font-medium text-teal-600 shrink-0"
                        x-text="open ? 'Tutup ▲' : 'Lihat Semua ▾'"></span>
                </button>

                <div x-show="open" x-cloak x-transition class="pt-3 mt-3 border-t border-gray-100">
                    @forelse ($finishedPaginated as $row)
                        @php
                            $assignee = $row['assignee'];
                            $task = $row['task'];
                            $pct = $row['percentage'];
                            $pctColor =
                                $pct >= 80 ? 'text-green-600' : ($pct >= 50 ? 'text-yellow-600' : 'text-red-600');
                        @endphp
                        <div class="py-3 border-b border-gray-50 last:border-0">
                            <div class="flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <a href="{{ route('tasks.show', $task->id) }}"
                                        class="text-sm font-medium text-gray-800 truncate hover:text-teal-600">
                                        {{ $task->title }}
                                    </a>
                                    <p class="text-xs text-gray-400">
                                        {{ $task->period_start?->format('d M') }} -
                                        {{ $task->period_end?->format('d M Y') }}
                                    </p>
                                </div>
                                <span class="text-sm font-semibold shrink-0 {{ $pctColor }}">
                                    {{ number_format($pct, 1) }}%
                                </span>
                            </div>
                            <div class="mt-1">
                                <x-task-daily-detail :assignee="$assignee" :show-fill-link="false" />
                            </div>
                        </div>
                    @empty
                        <p class="py-4 text-sm text-center text-gray-400">Tidak ada tugas selesai bulan ini.</p>
                    @endforelse

                    @if ($finishedPaginated->hasPages())
                        <div class="mt-3 text-xs">
                            {{ $finishedPaginated->links() }}
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
