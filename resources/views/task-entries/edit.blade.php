<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            📝 Isi Entry — {{ $entry->taskAssignee->task->title }}
        </h2>
        <p class="mt-1 text-sm text-gray-500">
            @if ($entry->entry_date)
                Tanggal: {{ $entry->entry_date->translatedFormat('d F Y') }}
            @else
                Entry tunggal (mode {{ $entry->taskAssignee->task->repeat_mode }})
            @endif
        </p>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="p-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">

                @if ($errors->any())
                    <div class="p-4 mb-6 text-red-700 bg-red-100 rounded-lg">
                        <ul class="ml-4 list-disc">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Banner kalau ini reopened dari rejected --}}
                @if ($entry->validation_status === 'rejected')
                    <div class="p-4 mb-6 text-red-700 border border-red-200 rounded-lg bg-red-50">
                        <p class="font-semibold">❌ Entry ini ditolak</p>
                        @if ($entry->rejected_reason)
                            <p class="mt-1 text-sm">Alasan: {{ $entry->rejected_reason }}</p>
                        @endif
                        <p class="mt-1 text-sm">Perbaiki dan kirim ulang di bawah — otomatis masuk antrian cek lagi.</p>
                    </div>
                @endif

                <form action="{{ route('task-entries.update', $entry->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    @if ($entry->taskAssignee->task->tracking_mode === 'numeric')
                        <div class="mb-4" x-data="{
                            val: @js(old('value_number', $entry->value_number)),
                            target: @js($entry->taskAssignee->individual_target),
                            unitType: @js($entry->taskAssignee->task->unit_type),
                            unit: @js($entry->taskAssignee->task->unit),
                            fmt(v) {
                                const s = new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 }).format(Number(v));
                                if (this.unitType === 'currency') return 'Rp ' + s;
                                if (this.unitType === 'percent') return s + '%';
                                return s + (this.unit ? ' ' + this.unit : '');
                            },
                            get hint() {
                                if (this.val === '' || this.val === null) return '';
                                const n = Number(this.val),
                                    t = Number(this.target);
                                if (!t) return '= ' + this.fmt(n);
                                const r = Math.round(n / t * 100);
                                if (n < t) return '= ' + this.fmt(n) + ' — kurang ' + this.fmt(t - n) + ' dari target (' + r + '%)';
                                if (n === t) return '= ' + this.fmt(n) + ' — sesuai target';
                                if (r >= 150) return '= ' + this.fmt(n) + ' — ⚠️ ' + r + '% dari target, pastikan tidak salah ketik';
                                return '= ' + this.fmt(n) + ' — melampaui target (' + r + '%)';
                            }
                        }">
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Nilai
                                @if ($entry->taskAssignee->task->unit_type === 'number' && $entry->taskAssignee->task->unit)
                                    ({{ $entry->taskAssignee->task->unit }})
                                @endif
                                <span class="text-red-500">*</span>
                            </label>
                            <input type="number" step="0.01" min="0" name="value_number" x-model="val"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                            <p class="mt-1 text-xs font-medium text-teal-600" x-show="hint" x-text="hint"></p>
                            @if ($entry->taskAssignee->individual_target)
                                <p class="mt-1 text-xs text-gray-400">
                                    Target:
                                    {{ $entry->taskAssignee->task->formatValue($entry->taskAssignee->individual_target) }}
                                </p>
                            @endif
                        </div>
                    @else
                        <div class="mb-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="value_checklist" value="1"
                                    {{ old('value_checklist', $entry->value_checklist) ? 'checked' : '' }}
                                    class="w-5 h-5 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                                <span class="text-sm font-medium text-gray-700">Sudah selesai dikerjakan</span>
                            </label>
                        </div>
                    @endif

                    <div class="mb-6">
                        <label class="block mb-2 text-sm font-medium text-gray-700">
                            Lampiran <span class="font-normal text-gray-400">(opsional)</span>
                        </label>
                        <input type="file" name="attachment"
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100">

                        <p class="mt-1 text-xs text-gray-400">
                            Gambar/dokumen maks. 3MB, video maks. 20MB.
                        </p>

                        @if ($entry->attachments->isNotEmpty())
                            <div class="mt-2 space-y-1">
                                @foreach ($entry->attachments as $att)
                                    <a href="{{ Storage::url($att->file_path) }}" target="_blank"
                                        class="block text-xs text-teal-600 hover:underline">
                                        📎 {{ $att->file_name }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('tasks.show', $entry->taskAssignee->task_id) }}"
                            class="px-6 py-2 text-gray-700 transition bg-gray-200 rounded-lg hover:bg-gray-300">
                            Batal
                        </a>
                        <button type="submit"
                            class="px-6 py-2 text-white transition bg-teal-500 rounded-lg hover:bg-teal-600">
                            💾 Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
