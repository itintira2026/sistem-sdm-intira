<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            🔍 Perlu Saya Cek
        </h2>
        <p class="mt-1 text-sm text-gray-500">Entry Tugas KPI yang menunggu dicek oleh Anda.</p>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="p-6 overflow-hidden bg-white shadow-sm sm:rounded-lg" x-data="{
                selected: [],
                result: 'checked',
                reason: '',
                toggleAll(ids, checked) {
                    this.selected = checked ? [...ids] : [];
                }
            }">

                @if (session('success'))
                    <div class="p-4 mb-6 text-green-700 bg-green-100 rounded-lg">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="p-4 mb-6 text-red-700 bg-red-100 rounded-lg">{{ session('error') }}</div>
                @endif

                @if ($entries->isEmpty())
                    <p class="py-8 text-sm text-center text-gray-400">Tidak ada entry yang perlu dicek saat ini. 🎉</p>
                @else
                    <form action="{{ route('task-check-queue.bulk-check') }}" method="POST">
                        @csrf

                        <div
                            class="flex items-center gap-2 px-3 py-2 mb-2 border border-gray-200 rounded-lg bg-gray-50">
                            <input type="checkbox"
                                @change="toggleAll(@js($entries->pluck('id')->map(fn($i) => (string) $i)->values()), $event.target.checked)"
                                class="w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                            <label class="text-sm font-semibold text-gray-600">Pilih Semua</label>
                            <span class="ml-auto text-xs text-teal-600 font-medium bg-teal-50 px-2 py-0.5 rounded-full"
                                x-text="selected.length + ' dipilih'"></span>
                        </div>

                        <div
                            class="mb-4 overflow-y-auto border border-gray-200 divide-y divide-gray-100 rounded-lg max-h-96">
                            @foreach ($entries as $entry)
                                @php
                                    $task = $entry->taskAssignee->task;
                                    $worker = $entry->taskAssignee->user;
                                    $roles = implode(', ', $worker->getRoleNames()->toArray());
                                @endphp
                                <label class="flex items-center gap-3 px-4 py-3 cursor-pointer hover:bg-gray-50">
                                    <input type="checkbox" name="entry_ids[]" value="{{ $entry->id }}"
                                        x-model="selected"
                                        class="w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-800">{{ $task->title }}</p>
                                        <p class="text-xs text-gray-400">
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
                                                <span class="text-gray-300">(target:
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
                                            <p class="mt-1 text-xs font-semibold {{ $color }}">
                                                {{ $icon }} Data dari {{ $worker->name }}
                                                ({{ $roles }})
                                                {{ $cmp['label'] }}
                                            </p>
                                        @endif

                                        <div class="flex flex-wrap items-center gap-2 mt-1" @click.stop>
                                            @forelse ($entry->attachments as $att)
                                                <a href="{{ Storage::url($att->file_path) }}" target="_blank"
                                                    class="px-2 py-0.5 text-xs text-teal-700 rounded-full bg-teal-50 hover:bg-teal-100">
                                                    📎 {{ $att->file_name }}
                                                </a>
                                            @empty
                                                <span class="text-xs text-gray-300">Tanpa lampiran</span>
                                            @endforelse
                                        </div>
                                    </div>
                                    <a href="{{ route('tasks.show', $entry->taskAssignee->task_id) }}" target="_blank"
                                        class="text-xs text-teal-600 hover:underline shrink-0" @click.stop>
                                        Detail
                                    </a>
                                </label>
                            @endforeach
                        </div>

                        <div class="flex flex-wrap items-end gap-3 p-4 rounded-lg bg-gray-50">
                            <div>
                                <label class="block mb-1 text-xs font-medium text-gray-500">Hasil</label>
                                <div class="flex gap-2">
                                    <label class="flex items-center gap-1 px-3 py-2 border-2 rounded-lg cursor-pointer"
                                        :class="result === 'checked' ? 'border-blue-500 bg-blue-50' : 'border-gray-200'">
                                        <input type="radio" name="result" value="checked" x-model="result"
                                            class="text-blue-600">
                                        <span class="text-sm">🔍 Lolos Cek</span>
                                    </label>
                                    <label class="flex items-center gap-1 px-3 py-2 border-2 rounded-lg cursor-pointer"
                                        :class="result === 'rejected' ? 'border-red-500 bg-red-50' : 'border-gray-200'">
                                        <input type="radio" name="result" value="rejected" x-model="result"
                                            class="text-red-600">
                                        <span class="text-sm">❌ Tolak</span>
                                    </label>
                                </div>
                            </div>
                            <div class="flex-1 min-w-[200px]">
                                <label class="block mb-1 text-xs font-medium text-gray-500">
                                    Alasan <span x-show="result === 'rejected'" class="text-red-500">*</span>
                                </label>
                                <input type="text" name="reason" x-model="reason" :required="result === 'rejected'"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                            </div>
                            <button type="submit" :disabled="selected.length === 0"
                                :class="selected.length === 0 ? 'opacity-50 cursor-not-allowed' : ''"
                                class="px-6 py-2 text-white transition bg-teal-500 rounded-lg hover:bg-teal-600">
                                Proses (<span x-text="selected.length"></span>)
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
