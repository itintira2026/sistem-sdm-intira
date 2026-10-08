@props(['entry'])

@php
    $task = $entry->taskAssignee->task;
@endphp

<form @submit.prevent="submit($event)" enctype="multipart/form-data" x-data="{
    val: @js($entry->value_number),
    checklist: @js($entry->value_checklist),
    submitting: false,
    error: '',
    target: @js($entry->taskAssignee->individual_target),
    unitType: @js($task->unit_type),
    unit: @js($task->unit),
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
        if (r >= 150) return '= ' + this.fmt(n) + ' — ⚠️ ' + r + '% dari target, cek lagi';
        return '= ' + this.fmt(n) + ' — melampaui target (' + r + '%)';
    },
    async submit(e) {
        this.submitting = true;
        this.error = '';
        const fd = new FormData(e.target);
        fd.append('_method', 'PUT');
        try {
            const res = await fetch('{{ route('task-entries.update', $entry->id) }}', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                },
                body: fd,
            });
            const data = await res.json().catch(() => ({}));
            if (!res.ok) {
                this.error = data.message || Object.values(data.errors || {}).flat().join(' ') || 'Gagal menyimpan.';
                this.submitting = false;
                return;
            }
            window.location.reload();
        } catch (err) {
            this.error = 'Terjadi kesalahan jaringan.';
            this.submitting = false;
        }
    }
}">
    @if ($entry->validation_status === 'rejected' && $entry->rejected_reason)
        <div class="p-3 mb-3 text-xs text-red-700 border border-red-200 rounded-lg bg-red-50">
            <p class="font-semibold">❌ Ditolak</p>
            <p class="mt-1">{{ $entry->rejected_reason }}</p>
        </div>
    @endif

    @if ($task->tracking_mode === 'numeric')
        <div class="mb-4">
            <label class="block mb-2 text-sm font-medium text-gray-700">
                Nilai
                @if ($task->unit_type === 'number' && $task->unit)
                    ({{ $task->unit }})
                @endif
                <span class="text-red-500">*</span>
            </label>
            <input type="number" step="0.01" min="0" name="value_number" x-model="val" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
            <p class="mt-1 text-xs font-medium text-teal-600" x-show="hint" x-text="hint"></p>
            @if ($entry->taskAssignee->individual_target)
                <p class="mt-1 text-xs text-gray-400">
                    Target: {{ $task->formatValue($entry->taskAssignee->individual_target) }}
                </p>
            @endif
        </div>
    @else
        <div class="mb-4">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="value_checklist" value="1" x-model="checklist"
                    class="w-5 h-5 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                <span class="text-sm font-medium text-gray-700">Sudah selesai dikerjakan</span>
            </label>
        </div>
    @endif

    <div class="mb-4">
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
                        📎 {{ $att->file_name }} (sebelumnya)
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    <p class="mb-3 text-xs text-red-600" x-show="error" x-text="error" x-cloak></p>

    <div class="flex justify-end gap-2">
        <button type="button" @click="open = false"
            class="px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100">
            Batal
        </button>
        <button type="submit" :disabled="submitting"
            class="px-4 py-2 text-sm text-white transition bg-teal-500 rounded-lg hover:bg-teal-600 disabled:opacity-50">
            <span x-show="!submitting">💾 Simpan</span>
            <span x-show="submitting" x-cloak>Menyimpan...</span>
        </button>
    </div>
</form>
