<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            ⚖️ Atur Bobot & Target — {{ $targetUser->name }}
        </h2>
        <p class="mt-1 text-sm text-gray-500">
            Semua Tugas KPI aktif milik {{ $targetUser->name }}. Bobot wajib total pas 100%, target boleh
            dikoreksi kapan saja.
        </p>
    </x-slot>

    <div class="py-8 sm:py-12">
        <div class="max-w-2xl px-4 mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">{{ session('error') }}</div>
            @endif

            @if ($assignments->isEmpty())
                <div class="p-8 text-sm text-center text-gray-400 bg-white border border-gray-200 rounded-xl">
                    Tidak ada Tugas KPI aktif untuk {{ $targetUser->name }}.
                </div>
            @else
                <form action="{{ route('tasks.weight-reinput.update', $targetUser->id) }}" method="POST"
                    x-data="{
                        weights: @js($assignments->mapWithKeys(fn($a) => [$a->id => old('weights.' . $a->id, (float) $a->weight_percent)])),
                        get total() {
                            return Object.values(this.weights).reduce((sum, v) => sum + (Number(v) || 0), 0);
                        },
                        get isOk() {
                            return Math.abs(this.total - 100) < 0.01;
                        }
                    }">
                    @csrf
                    @method('PUT')

                    {{-- Ringkasan total bobot, live --}}
                    <div class="sticky top-0 z-10 flex items-center justify-between p-4 mb-4 bg-white border shadow-sm rounded-xl"
                        :class="isOk ? 'border-green-300' : 'border-yellow-300'">
                        <div>
                            <p class="text-xs text-gray-400">Total Bobot</p>
                            <p class="text-2xl font-bold" :class="isOk ? 'text-green-600' : 'text-yellow-600'"
                                x-text="total.toFixed(2) + '%'"></p>
                        </div>
                        <span class="text-xs font-medium" :class="isOk ? 'text-green-600' : 'text-yellow-600'"
                            x-text="isOk ? '✅ Pas 100%' : 'Harus pas 100% untuk disimpan'"></span>
                    </div>

                    <div class="space-y-3">
                        @foreach ($assignments as $assignment)
                            @php $task = $assignment->task; @endphp
                            <div class="p-4 bg-white border border-gray-200 rounded-xl" x-data="{
                                showRemove: false,
                                removing: false,
                                reason: '',
                                error: '',
                                async remove() {
                                    if (!this.reason.trim()) { this.error = 'Alasan wajib diisi.'; return; }
                                    this.removing = true;
                                    this.error = '';
                                    try {
                                        const res = await fetch('{{ route('task-assignees.remove', $assignment->id) }}', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'Accept': 'application/json',
                                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                            },
                                            body: JSON.stringify({ reason: this.reason }),
                                        });
                                        const data = await res.json();
                                        if (!res.ok) {
                                            this.error = data.message || 'Gagal menonaktifkan.';
                                            this.removing = false;
                                            return;
                                        }
                                        window.location.reload();
                                    } catch (e) {
                                        this.error = 'Terjadi kesalahan jaringan.';
                                        this.removing = false;
                                    }
                                }
                            }">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <a href="{{ route('tasks.show', $task->id) }}" target="_blank"
                                            class="text-sm font-medium text-gray-800 truncate hover:text-teal-600">
                                            {{ $task->title }}
                                        </a>
                                        <p class="text-xs text-gray-400">
                                            {{ ucfirst(str_replace('_', ' ', $task->repeat_mode)) }}
                                            @if ($task->period_start && $task->period_end)
                                                · {{ $task->period_start->format('d M') }} -
                                                {{ $task->period_end->format('d M Y') }}
                                            @endif
                                        </p>
                                    </div>
                                    <button type="button" @click="showRemove = !showRemove"
                                        class="text-xs text-red-500 shrink-0 hover:text-red-700 hover:underline">
                                        Nonaktifkan
                                    </button>
                                </div>

                                <div class="grid grid-cols-2 gap-3 mt-3">
                                    <div>
                                        <label class="block mb-1 text-xs font-medium text-gray-500">Bobot (%)</label>
                                        <div class="relative">
                                            <input type="number" step="0.01" min="0" max="100"
                                                name="weights[{{ $assignment->id }}]"
                                                x-model="weights[{{ $assignment->id }}]"
                                                class="w-full py-2 pl-3 pr-8 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                                            <span
                                                class="absolute text-xs text-gray-400 -translate-y-1/2 right-3 top-1/2">%</span>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block mb-1 text-xs font-medium text-gray-500">
                                            Target
                                            @if ($task->tracking_mode !== 'numeric')
                                                <span class="text-gray-300">(checklist, tanpa target)</span>
                                            @endif
                                        </label>
                                        @if ($task->tracking_mode === 'numeric')
                                            <div class="relative">
                                                @if ($task->unit_type === 'currency')
                                                    <span
                                                        class="absolute text-xs text-gray-400 -translate-y-1/2 left-3 top-1/2">Rp</span>
                                                @endif
                                                <input type="number" step="0.01" min="0"
                                                    @if ($task->unit_type === 'percent') max="100" @endif
                                                    name="targets[{{ $assignment->id }}]"
                                                    value="{{ old('targets.' . $assignment->id, $assignment->individual_target) }}"
                                                    placeholder="Opsional"
                                                    class="w-full py-2 {{ $task->unit_type === 'currency' ? 'pl-8' : 'pl-3' }} {{ $task->unit_type === 'percent' ? 'pr-7' : 'pr-3' }} text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                                                @if ($task->unit_type === 'percent')
                                                    <span
                                                        class="absolute text-xs text-gray-400 -translate-y-1/2 right-3 top-1/2">%</span>
                                                @endif
                                            </div>
                                        @else
                                            <div
                                                class="flex items-center h-[38px] px-3 text-sm text-gray-300 border border-gray-100 rounded-lg bg-gray-50">
                                                —
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Konfirmasi nonaktifkan --}}
                                <div x-show="showRemove" x-cloak x-transition
                                    class="p-3 mt-3 border border-red-200 rounded-lg bg-red-50">
                                    <p class="mb-2 text-xs text-red-700">
                                        {{ $targetUser->name }} akan dikeluarkan dari tugas ini. Progress dikunci di
                                        angka sekarang dan tidak bisa isi entry baru lagi. Riwayat entry lama tetap
                                        tersimpan.
                                    </p>
                                    <textarea x-model="reason" rows="2" placeholder="Alasan menonaktifkan (wajib)"
                                        class="w-full px-3 py-2 text-sm border border-red-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-400"></textarea>
                                    <p class="mt-1 text-xs text-red-600" x-show="error" x-text="error" x-cloak></p>
                                    <div class="flex justify-end gap-2 mt-2">
                                        <button type="button" @click="showRemove = false"
                                            class="px-3 py-1.5 text-xs text-gray-600 hover:underline">
                                            Batal
                                        </button>
                                        <button type="button" @click="remove()" :disabled="removing"
                                            class="px-3 py-1.5 text-xs text-white bg-red-600 rounded-lg hover:bg-red-700 disabled:opacity-50">
                                            <span x-show="!removing">Ya, Nonaktifkan</span>
                                            <span x-show="removing" x-cloak>Memproses...</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex justify-end gap-3 mt-6">
                        <a href="{{ route('tasks.assigned') }}"
                            class="px-5 py-2.5 text-sm text-gray-700 transition bg-gray-200 rounded-lg hover:bg-gray-300">
                            Nanti Dulu
                        </a>
                        <button type="submit" :disabled="!isOk"
                            :class="!isOk ? 'opacity-50 cursor-not-allowed' : ''"
                            class="px-5 py-2.5 text-sm text-white transition bg-teal-500 rounded-lg hover:bg-teal-600">
                            💾 Simpan Bobot & Target
                        </button>
                    </div>
                </form>
            @endif

            {{-- Riwayat yang sudah dikunci --}}
            @if ($lockedAssignments->isNotEmpty())
                <div class="mt-8">
                    <h3 class="mb-3 text-sm font-semibold text-gray-500">Riwayat Non-aktif / Dibekukan</h3>
                    <div class="overflow-hidden bg-white border border-gray-200 divide-y divide-gray-100 rounded-xl">
                        @foreach ($lockedAssignments as $locked)
                            <div class="px-4 py-3">
                                <div class="flex items-center justify-between gap-3">
                                    <a href="{{ route('tasks.show', $locked->task_id) }}" target="_blank"
                                        class="text-sm font-medium text-gray-700 truncate hover:text-teal-600">
                                        {{ $locked->task->title }}
                                    </a>
                                    <span class="text-sm font-semibold text-gray-500 shrink-0">
                                        {{ number_format((float) $locked->final_percentage, 1) }}%
                                    </span>
                                </div>
                                <p class="mt-1 text-xs text-gray-400">
                                    @if ($locked->isRemoved())
                                        🚫 Dinonaktifkan oleh {{ $locked->remover?->name ?? '—' }}
                                        pada {{ $locked->finalized_at->format('d M Y') }}
                                        @if ($locked->removed_reason)
                                            — "{{ $locked->removed_reason }}"
                                        @endif
                                    @else
                                        🧊 Dibekukan otomatis karena mutasi jabatan
                                        pada {{ $locked->finalized_at->format('d M Y') }}
                                    @endif
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
