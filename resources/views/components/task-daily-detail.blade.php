@props(['assignee', 'showFillLink' => true])

@php
    $task = $assignee->task;
@endphp

@if ($task->repeat_mode === 'daily_split')
    <div x-data="{ open: false }">
        <button type="button" @click="open = !open" class="text-xs text-gray-400 hover:text-gray-600">
            <span x-text="open ? 'Tutup ▲' : 'Detail Harian ▼'"></span>
        </button>

        <div x-show="open" x-cloak x-transition
            class="mt-3 overflow-y-auto border border-gray-100 divide-y rounded-lg divide-gray-50 max-h-64">
            @forelse ($assignee->entries as $entry)
                @php
                    $entryDateLabel = $entry->entry_date?->format('d M Y');

                    $value = $entry->value_number;
                    $target = $assignee->individual_target;

                    $rowClass = '';
                    if (
                        $task->tracking_mode !== 'checklist' &&
                        $value !== null &&
                        $target !== null &&
                        (float) $target > 0
                    ) {
                        $rowClass = (float) $value >= (float) $target ? 'bg-green-50' : 'bg-red-50';
                    }

                    $badge = match ($entry->validation_status) {
                        'approved' => ['✅ Disetujui', 'text-green-700 bg-green-100'],
                        'rejected' => ['❌ Ditolak', 'text-red-700 bg-red-100'],
                        'checked' => ['🔍 Dicek', 'text-blue-700 bg-blue-100'],
                        'pending' => ['⏳ Pending', 'text-yellow-700 bg-yellow-100'],
                        default => ['—', 'text-gray-400 bg-gray-100'],
                    };
                @endphp
                <div class="flex flex-col gap-1 px-3 py-2 text-xs rounded {{ $rowClass ?? '' }}">

                    {{-- Baris utama: tanggal, nilai, badge --}}
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <span class="text-gray-500 shrink-0">{{ $entryDateLabel }}</span>

                        <span class="text-gray-700">
                            @if ($task->tracking_mode === 'checklist')
                                {{ $entry->value_checklist ? '✅ Selesai' : ($entry->value_checklist === false ? '❌ Belum' : '⚪ Kosong') }}
                            @else
                                {{ $value !== null ? $task->formatValue($value) : '⚪ Kosong' }}
                            @endif
                        </span>

                        <span class="px-2 py-0.5 rounded-full {{ $badge[1] }} shrink-0">{{ $badge[0] }}</span>
                    </div>

                    {{-- Baris kedua: lampiran / isi, full width, kecil --}}
                    @php
                        $canFill =
                            $showFillLink &&
                            $assignee->user_id === auth()->id() &&
                            !$assignee->isFinalized() &&
                            in_array($entry->validation_status, ['pending', 'rejected', null]) &&
                            ($entry->validation_status !== 'pending' ||
                                $entry->entry_date === null ||
                                $entry->entry_date->isToday());
                    @endphp
                    <div class="w-full pt-1 mt-1 border-t border-gray-100 text-center">
                        @if ($canFill)
                            <a href="{{ route('task-entries.edit', $entry->id) }}"
                                class="text-teal-600 hover:underline">Isi sekarang</a>
                        @elseif ($entry->attachments->isNotEmpty())
                            <span class="flex flex-wrap gap-2">
                                @foreach ($entry->attachments as $att)
                                    <a href="{{ Storage::url($att->file_path) }}" target="_blank"
                                        class="text-teal-600 hover:underline" title="{{ $att->file_name }}">
                                        📎 {{ $att->file_name }}
                                    </a>
                                @endforeach
                            </span>
                        @else
                            <span class="text-gray-400">Tidak ada lampiran</span>
                        @endif
                    </div>
                </div>
            @empty
                <p class="px-3 py-4 text-xs text-center text-gray-400">Belum ada entry.</p>
            @endforelse
        </div>
    </div>
@endif
