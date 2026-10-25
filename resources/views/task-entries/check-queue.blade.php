<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold leading-tight tracking-tight text-gray-900">
            🔍 Perlu Saya Cek
        </h2>
        <p class="mt-2 text-sm leading-relaxed text-gray-500">Entry Tugas KPI yang menunggu dicek oleh Anda.</p>
    </x-slot>

    <div class="py-6 sm:py-10">
        <div class="max-w-5xl px-4 mx-auto sm:px-6 lg:px-8">
            <div class="p-4 overflow-hidden bg-white border border-gray-200 shadow-sm rounded-2xl sm:p-6 lg:p-8" x-data="{
                selected: [],
                result: 'checked',
                reason: '',
                toggleAll(ids, checked) {
                    this.selected = checked ? [...ids] : [];
                }
            }">

                @if (session('success'))
                    <div class="p-4 mb-6 text-sm leading-relaxed text-green-800 border border-green-200 bg-green-50 rounded-xl">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="p-4 mb-6 text-sm leading-relaxed text-red-800 border border-red-200 bg-red-50 rounded-xl">{{ session('error') }}</div>
                @endif

                @if ($entries->isEmpty())
                    <p class="px-4 py-16 text-sm font-medium leading-relaxed text-center text-gray-500 border border-dashed border-gray-200 bg-gray-50 rounded-xl">Tidak ada entry yang perlu dicek saat ini. 🎉</p>
                @else
                    <form action="{{ route('task-check-queue.bulk-check') }}" method="POST">
                        @csrf

                        <div
                            class="flex items-center gap-3 px-4 py-3 mb-4 border border-gray-200 rounded-xl bg-gray-50">
                            <input type="checkbox"
                                @change="toggleAll(@js($entries->pluck('id')->map(fn($i) => (string) $i)->values()), $event.target.checked)"
                                class="w-4 h-4 shrink-0 text-teal-600 border-gray-300 rounded focus:ring-teal-500 focus:ring-offset-2">
                            <label class="text-sm font-semibold text-gray-700">Pilih Semua</label>
                            <span class="ml-auto shrink-0 px-3 py-1 text-xs font-semibold text-teal-700 bg-teal-100 rounded-full"
                                x-text="selected.length + ' dipilih'"></span>
                        </div>

                        <div
                            class="mb-6 overflow-y-auto border border-gray-200 divide-y divide-gray-100 rounded-xl max-h-[32rem]">
                            @foreach ($entries as $entry)
                                @php
                                    $task = $entry->taskAssignee->task;
                                    $worker = $entry->taskAssignee->user;
                                    $roles = implode(', ', $worker->getRoleNames()->toArray());
                                @endphp
                                <label class="flex flex-wrap items-start gap-3 p-4 transition-colors cursor-pointer sm:flex-nowrap sm:gap-4 sm:p-5 hover:bg-teal-50/40">
                                    <input type="checkbox" name="entry_ids[]" value="{{ $entry->id }}"
                                        x-model="selected"
                                        class="w-4 h-4 shrink-0 text-teal-600 border-gray-300 rounded focus:ring-teal-500 focus:ring-offset-2">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold leading-6 text-gray-900 break-words sm:text-base">{{ $task->title }}</p>
                                        <p class="mt-1 text-xs leading-6 text-gray-500 break-words sm:text-sm">
                                            {{ $worker->name }} ({{ $roles }})
                                            @if ($entry->entry_date)
                                                · {{ $entry->entry_date->format('d M Y') }}
                                            @endif
                                            · Nilai:
                                            {{ $task->tracking_mode === 'checklist'
                                                ? ($entry->value_checklist
                                                    ? 'Selesai'
                                                    : 'Belum')
                                                : $task->formatValue($entry->value_number) }}
                                            @if ($entry->taskAssignee->individual_target)
                                                <span class="text-gray-500">(target:
                                                    {{ $task->formatValue($entry->taskAssignee->individual_target) }})</span>
                                            @endif
                                        </p>

                                        @if ($entry->comparison)
                                            @php
                                                $cmp = $entry->comparison;
                                                $color = match ($cmp['status']) {
                                                    'kurang' => 'text-yellow-600',
                                                    'janggal' => 'text-red-600',
                                                    default => 'text-green-600',
                                                };
                                                $icon = match ($cmp['status']) {
                                                    'kurang' => '⬇️',
                                                    'lebih' => '⬆️',
                                                    'janggal' => '⚠️',
                                                    default => '✅',
                                                };
                                            @endphp
                                            <p class="mt-3 text-xs font-semibold leading-relaxed {{ $color }}">
                                                {{ $icon }} Data dari {{ $worker->name }}
                                                ({{ $roles }})
                                                {{ $cmp['label'] }}
                                            </p>
                                        @endif

                                        <div class="flex flex-wrap items-center gap-2 mt-3" @click.stop>
                                            @forelse ($entry->attachments as $att)
                                                <a href="{{ Storage::url($att->file_path) }}" target="_blank"
                                                    class="max-w-full px-3 py-1.5 text-xs font-medium leading-5 text-teal-700 break-all transition-colors border border-teal-100 rounded-lg bg-teal-50 hover:bg-teal-100 focus:outline-none focus:ring-2 focus:ring-teal-500">
                                                    📎 {{ $att->file_name }}
                                                </a>
                                            @empty
                                                <span class="text-xs text-gray-400">Tanpa lampiran</span>
                                            @endforelse
                                        </div>
                                    </div>
                                    <a href="{{ route('tasks.show', $entry->taskAssignee->task_id) }}" target="_blank"
                                        class="ml-7 px-3 py-1.5 text-xs font-semibold text-teal-700 transition-colors bg-white border border-gray-200 rounded-lg shrink-0 sm:ml-0 hover:bg-teal-50 hover:border-teal-200 focus:outline-none focus:ring-2 focus:ring-teal-500" @click.stop>
                                        Detail
                                    </a>
                                </label>
                            @endforeach
                        </div>

                        <div class="flex flex-col items-stretch gap-5 p-4 border border-gray-200 rounded-xl bg-gray-50 sm:flex-row sm:flex-wrap sm:items-end sm:p-5">
                            <div>
                                <label class="block mb-2 text-xs font-semibold tracking-wide text-gray-600">Hasil</label>
                                <div class="flex flex-wrap gap-2">
                                    <label class="flex items-center justify-center gap-2 px-4 py-2.5 text-gray-700 transition-colors border-2 rounded-xl cursor-pointer"
                                        :class="result === 'checked' ? 'border-blue-500 bg-blue-50' : 'border-gray-200'">
                                        <input type="radio" name="result" value="checked" x-model="result"
                                            class="text-blue-600">
                                        <span class="text-sm">🔍 Lolos Cek</span>
                                    </label>
                                    <label class="flex items-center justify-center gap-2 px-4 py-2.5 text-gray-700 transition-colors border-2 rounded-xl cursor-pointer"
                                        :class="result === 'rejected' ? 'border-red-500 bg-red-50' : 'border-gray-200'">
                                        <input type="radio" name="result" value="rejected" x-model="result"
                                            class="text-red-600">
                                        <span class="text-sm">❌ Tolak</span>
                                    </label>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0 sm:min-w-[200px]">
                                <label class="block mb-2 text-xs font-semibold tracking-wide text-gray-600">
                                    Alasan <span x-show="result === 'rejected'" class="text-red-500">*</span>
                                </label>
                                <input type="text" name="reason" x-model="reason" :required="result === 'rejected'"
                                    class="w-full px-4 py-2.5 text-sm bg-white border border-gray-300 rounded-xl focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500">
                            </div>
                            <button type="submit" :disabled="selected.length === 0"
                                :class="selected.length === 0 ? 'opacity-50 cursor-not-allowed' : ''"
                                class="w-full px-6 py-3 text-sm font-semibold text-white transition bg-teal-600 shadow-sm rounded-xl sm:w-auto hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2">
                                Proses (<span x-text="selected.length"></span>)
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
