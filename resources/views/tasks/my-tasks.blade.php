<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold leading-tight tracking-tight text-gray-900">📋 Tugas Saya</h2>
        <p class="mt-2 text-sm text-gray-500">{{ now()->translatedFormat('l, d F Y') }}</p>
    </x-slot>

    <div class="py-6 sm:py-10">
        <div class="max-w-5xl px-4 mx-auto sm:px-6 lg:px-8">

            {{-- Navigasi bulan — cuma berlaku buat Tugas Selesai --}}
            <div
                class="flex flex-col items-stretch justify-between gap-4 p-4 mb-6 bg-white border border-gray-200 shadow-sm rounded-2xl sm:flex-row sm:items-center sm:p-5">
                <a href="{{ route('tasks.my-tasks', ['month' => $prevMonth->month, 'year' => $prevMonth->year]) }}"
                    class="w-full px-4 py-2.5 text-sm font-medium text-center text-gray-700 transition-colors border border-gray-200 bg-gray-50 rounded-xl sm:w-auto hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2">
                    ← Sebelumnya
                </a>
                <div class="text-center">
                    <p class="text-base font-semibold leading-6 text-gray-900">{{ $startOfMonth->translatedFormat('F Y') }}</p>
                    <p class="mt-1 text-xs leading-5 text-gray-500">berlaku untuk riwayat Tugas Selesai</p>
                </div>
                <a href="{{ route('tasks.my-tasks', ['month' => $nextMonth->month, 'year' => $nextMonth->year]) }}"
                    class="w-full px-4 py-2.5 text-sm font-medium text-center text-gray-700 transition-colors border border-gray-200 bg-gray-50 rounded-xl sm:w-auto hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2">
                    Berikutnya →
                </a>
            </div>

            @if ($rejectedAssignees->isNotEmpty())
                <div class="p-4 mb-6 border border-amber-200 bg-amber-50 rounded-2xl sm:p-6">
                    <h3 class="mb-2 text-base font-bold text-amber-900">Perlu Diperbaiki</h3>
                    <p class="mb-5 text-sm leading-relaxed text-amber-800">
                        Entry yang baru ditolak tetap bisa diperbaiki meski periode tugasnya sudah berakhir.
                    </p>

                    <div class="space-y-3">
                        @foreach ($rejectedAssignees as $assignee)
                            @php
                                $task = $assignee->task;
                                $rejectedEntries = $assignee->entries
                                    ->where('validation_status', 'rejected')
                                    ->sortByDesc('entry_date')
                                    ->values();
                            @endphp
                            <div class="p-4 bg-white border border-amber-200 shadow-sm rounded-xl sm:p-5">
                                <div class="flex flex-wrap items-start justify-between gap-2 mb-3">
                                    <a href="{{ route('tasks.show', $task->id) }}"
                                        class="text-sm font-semibold leading-6 text-gray-900 break-words transition-colors hover:text-teal-600">
                                        {{ $task->title }}
                                    </a>
                                    <span class="text-xs leading-5 text-gray-500">
                                        Periode {{ $task->period_start?->format('d M Y') }} -
                                        {{ $task->period_end?->format('d M Y') }}
                                    </span>
                                </div>

                                @foreach ($rejectedEntries as $entry)
                                    <div
                                        class="flex flex-wrap items-center justify-between gap-3 py-3 border-t border-gray-100 first:border-0">
                                        <div class="min-w-0 break-words">
                                            <p class="text-xs font-semibold leading-5 text-red-700">
                                                Ditolak · {{ $entry->entry_date?->format('d M Y') ?? 'Tanpa tanggal' }}
                                            </p>
                                            @if ($entry->rejected_reason)
                                                <p class="mt-1 text-sm leading-relaxed text-gray-600 break-words">Alasan:
                                                    {{ $entry->rejected_reason }}</p>
                                            @endif
                                        </div>
                                        <x-modal-sheet title="Perbaiki Entry — {{ $task->title }}">
                                            <x-slot:trigger>
                                                <button type="button" @click="open = true"
                                                    class="px-4 py-2 text-xs font-semibold text-white transition-colors bg-teal-600 rounded-lg hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2">
                                                    Perbaiki
                                                </button>
                                            </x-slot:trigger>
                                            <x-entry-fill-form :entry="$entry" />
                                        </x-modal-sheet>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>

                    @if ($rejectedAssignees->hasPages())
                        <div class="pt-4 mt-4 overflow-x-auto text-xs border-t border-gray-100">{{ $rejectedAssignees->links() }}</div>
                    @endif
                </div>
            @endif

            <div class="grid items-start grid-cols-1 gap-6 mb-6 lg:grid-cols-2">

                {{-- PERLU DIKERJAKAN HARI INI --}}
                <div class="p-4 border border-teal-200 bg-teal-50 rounded-2xl sm:p-6" x-data="{ limit: 5 }">
                    <h3 class="mb-5 text-base font-bold leading-6 text-teal-900">🔥 Perlu Dikerjakan Hari Ini</h3>

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
                        <div x-show="{{ $loop->index }} < limit" class="p-4 mb-3 bg-white border border-teal-100 shadow-sm rounded-xl last:mb-0">
                            <div class="flex items-start justify-between gap-3">
                                <a href="{{ route('tasks.show', $task->id) }}"
                                    class="block min-w-0 text-sm font-semibold leading-6 text-gray-900 break-words transition-colors hover:text-teal-600">
                                    {{ $task->title }}
                                </a>
                                <span class="px-2.5 py-1 text-xs font-bold border border-gray-100 rounded-lg bg-gray-50 shrink-0 {{ $pctColor }}">
                                    {{ number_format($pct, 1) }}%
                                </span>
                            </div>

                            @if ($assignee->isFinalized())
                                <p class="mt-2 text-xs font-semibold leading-5 text-blue-600">
                                    🧊 {{ $assignee->isRemoved() ? 'Non-aktif' : 'Dibekukan' }}
                                </p>
                            @elseif ($e)
                                <p class="mt-2 text-xs leading-relaxed text-gray-500">
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
                                                class="w-full px-4 py-2 mt-4 text-xs font-semibold text-white transition-colors bg-teal-600 rounded-lg hover:bg-teal-700 sm:w-auto focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2">
                                                {{ $status === 'rejected' ? 'Perbaiki' : (!$filled ? 'Isi Sekarang' : 'Ubah') }}
                                            </button>
                                        </x-slot:trigger>
                                        <x-entry-fill-form :entry="$e" />
                                    </x-modal-sheet>
                                @endif
                            @else
                                <p class="mt-2 text-xs leading-relaxed text-gray-500">Tidak ada entry untuk hari ini.</p>
                            @endif
                        </div>
                    @empty
                        <p class="px-4 py-12 text-sm leading-relaxed text-center text-teal-700 border border-dashed border-teal-200 rounded-xl bg-white/60">🎉 Semua sudah beres hari ini.</p>
                    @endforelse
                    @if ($activeAssignees->count() > 5)
                        <button type="button" x-show="limit < {{ $activeAssignees->count() }}" @click="limit += 5"
                            class="w-full px-4 py-3 mt-2 text-xs font-semibold text-teal-700 transition-colors border border-teal-200 rounded-xl hover:bg-teal-100 focus:outline-none focus:ring-2 focus:ring-teal-500">
                            Tampilkan lainnya (<span x-text="{{ $activeAssignees->count() }} - limit"></span>)
                        </button>
                    @endif
                </div>

                {{-- TUGAS TAMBAHAN --}}
                <div class="p-4 bg-white border border-gray-200 shadow-sm rounded-2xl sm:p-6">
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
                        <h3 class="text-base font-semibold leading-6 text-gray-900">📝 Tugas Tambahan</h3>

                        <x-modal-sheet title="Catat Tugas Tambahan">
                            <x-slot:trigger>
                                <button type="button" @click="open = true"
                                    class="px-4 py-2 text-xs font-semibold text-white transition-colors bg-gray-800 rounded-lg hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                                    + Tambah
                                </button>
                            </x-slot:trigger>
                            <x-self-record-form />
                        </x-modal-sheet>
                    </div>

                    <div class="divide-y divide-gray-100">
                        @forelse ($selfRecordAssignees as $assignee)
                            @php $entry = $assignee->entries->first(); @endphp
                            <div class="py-4 first:pt-0 last:pb-0">
                                <p class="text-sm font-semibold leading-6 text-gray-800 break-words">{{ $assignee->task->title }}</p>
                                <p class="mt-1 text-xs leading-5 text-gray-500">
                                    {{ $assignee->task->created_at->format('d M Y') }} ·
                                    @if ($entry)
                                        @if ($assignee->task->tracking_mode === 'checklist')
                                            {{ $entry->value_checklist ? '✅ Selesai' : '⚪ Belum' }}
                                        @else
                                            {{ $assignee->task->formatValue($entry->value_number) }}
                                        @endif
                                    @endif
                                </p>

                                @if ($assignee->canEditSelfRecord() || $assignee->canDeleteSelfRecord())
                                    <div class="flex flex-wrap items-center gap-2 mt-3">
                                        @if ($assignee->canEditSelfRecord())
                                            <x-modal-sheet title="Ubah Tugas Tambahan">
                                                <x-slot:trigger>
                                                    <button type="button" @click="open = true"
                                                        class="px-3 py-2 text-xs font-semibold text-white transition-colors bg-teal-600 rounded-lg hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2">
                                                        Edit
                                                    </button>
                                                </x-slot:trigger>
                                                <x-self-record-form :assignee="$assignee" />
                                            </x-modal-sheet>
                                        @endif

                                        @if ($assignee->canDeleteSelfRecord())
                                            <form method="POST"
                                                action="{{ route('tasks.self-record.destroy', $assignee) }}"
                                                onsubmit="return confirm('Hapus tugas tambahan ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="px-3 py-2 text-xs font-semibold text-red-700 transition-colors border border-red-100 rounded-lg bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                                    Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @empty
                            ...
                            <p class="px-4 py-12 text-sm text-center text-gray-500 border border-dashed border-gray-200 bg-gray-50 rounded-xl">Belum ada catatan.</p>
                        @endforelse
                    </div>

                    @if ($selfRecordAssignees->hasPages())
                        <div class="pt-4 mt-4 overflow-x-auto text-xs border-t border-gray-100">
                            {{ $selfRecordAssignees->links() }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- TUGAS SELESAI --}}
            <div class="p-4 bg-white border border-gray-200 shadow-sm rounded-2xl sm:p-6" x-data="{ open: {{ $finishedOpenDefault ? 'true' : 'false' }} }">
                <button type="button" @click="open = !open" class="flex items-start justify-between w-full gap-4 text-left rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-4">
                    <div>
                        <p class="text-base font-semibold leading-6 text-gray-900">
                            Tugas Selesai · {{ $startOfMonth->translatedFormat('F Y') }}
                        </p>
                        <p class="mt-1 text-xs leading-5 text-gray-500">
                            {{ $finishedStats['total'] }} tugas · rata-rata
                            {{ number_format($finishedStats['average'], 1) }}%
                        </p>
                    </div>
                    <span class="px-3 py-2 text-xs font-semibold text-teal-700 rounded-lg bg-teal-50 shrink-0"
                        x-text="open ? 'Tutup ▲' : 'Lihat Semua ▾'"></span>
                </button>

                <div x-show="open" x-cloak x-transition class="pt-5 mt-5 border-t border-gray-100">
                    @forelse ($finishedPaginated as $row)
                        @php
                            $assignee = $row['assignee'];
                            $task = $row['task'];
                            $pct = $row['percentage'];
                            $pctColor =
                                $pct >= 80 ? 'text-green-600' : ($pct >= 50 ? 'text-yellow-600' : 'text-red-600');
                        @endphp
                        <div class="p-4 mb-3 border border-gray-100 rounded-xl bg-gray-50/50 last:mb-0">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0 break-words">
                                    <a href="{{ route('tasks.show', $task->id) }}"
                                        class="block min-w-0 text-sm font-semibold leading-6 text-gray-900 break-words transition-colors hover:text-teal-600">
                                        {{ $task->title }}
                                    </a>
                                    <p class="mt-1 text-xs leading-5 text-gray-500">
                                        {{ $task->period_start?->format('d M') }} -
                                        {{ $task->period_end?->format('d M Y') }}
                                    </p>
                                </div>
                                <span class="px-3 py-1.5 text-sm font-bold bg-white border border-gray-200 rounded-lg shrink-0 {{ $pctColor }}">
                                    {{ number_format($pct, 1) }}%
                                </span>
                            </div>
                            <div class="mt-4">
                                <x-task-daily-detail :assignee="$assignee" :show-fill-link="false" />
                            </div>
                        </div>
                    @empty
                        <p class="px-4 py-10 text-sm text-center text-gray-500 border border-dashed border-gray-200 rounded-xl bg-gray-50">Tidak ada tugas selesai bulan ini.</p>
                    @endforelse

                    @if ($finishedPaginated->hasPages())
                        <div class="pt-4 mt-4 overflow-x-auto text-xs border-t border-gray-100">
                            {{ $finishedPaginated->links() }}
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
