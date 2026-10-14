@props(['assignee' => null])

@php
    $isEdit = $assignee !== null;
    $task = $assignee?->task;
    $entry = $assignee?->entries->first();
    $mode = $task?->tracking_mode ?? 'checklist';
    $action = $isEdit ? route('tasks.self-record.update', $assignee) : route('tasks.self-record.store');
@endphp

<form @submit.prevent="submit($event)" x-data="{
    trackingMode: '{{ $mode }}',
    submitting: false,
    error: '',
    async submit(e) {
        this.submitting = true;
        this.error = '';
        const fd = new FormData(e.target);
        try {
            const res = await fetch('{{ $action }}', {
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
    @if ($isEdit)
        {{-- FormData cuma bisa POST, Laravel baca _method buat route PUT --}}
        <input type="hidden" name="_method" value="PUT">
    @endif

    <div class="mb-4">
        <label class="block mb-2 text-sm font-medium text-gray-700">
            Judul <span class="text-red-500">*</span>
        </label>
        <input type="text" name="title" required value="{{ $task?->title }}"
            placeholder="misal: Bantu training FO baru"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
    </div>

    <div class="mb-4">
        <label class="block mb-2 text-sm font-medium text-gray-700">Deskripsi</label>
        <textarea name="description" rows="2"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">{{ $task?->description }}</textarea>
    </div>

    @if ($isEdit)
        {{-- Mode catat dikunci saat edit --}}
        <div class="mb-4">
            <label class="block mb-2 text-sm font-medium text-gray-700">Cara Catat</label>
            <p class="px-3 py-2 text-sm text-gray-600 rounded-lg bg-gray-50">
                {{ $mode === 'numeric' ? 'Angka' : 'Selesai / Tidak' }}
                <span class="text-xs text-gray-400">· tidak bisa diubah, hapus lalu buat ulang kalau salah mode</span>
            </p>
        </div>
    @else
        <div class="mb-4">
            <label class="block mb-2 text-sm font-medium text-gray-700">
                Cara Catat <span class="text-red-500">*</span>
            </label>
            <div class="flex gap-3">
                <label class="flex items-center flex-1 gap-2 px-3 py-2 border-2 rounded-lg cursor-pointer"
                    :class="trackingMode === 'checklist' ? 'border-teal-500 bg-teal-50' : 'border-gray-200'">
                    <input type="radio" name="tracking_mode" value="checklist" x-model="trackingMode"
                        class="text-teal-600">
                    <span class="text-sm font-medium">Selesai / Tidak</span>
                </label>
                <label class="flex items-center flex-1 gap-2 px-3 py-2 border-2 rounded-lg cursor-pointer"
                    :class="trackingMode === 'numeric' ? 'border-teal-500 bg-teal-50' : 'border-gray-200'">
                    <input type="radio" name="tracking_mode" value="numeric" x-model="trackingMode"
                        class="text-teal-600">
                    <span class="text-sm font-medium">Angka</span>
                </label>
            </div>
        </div>
    @endif

    <div class="mb-4" x-show="trackingMode === 'checklist'" @if ($mode !== 'checklist') x-cloak @endif>
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="value_checklist" value="1" @checked(!$isEdit || $entry?->value_checklist)
                class="w-5 h-5 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
            <span class="text-sm font-medium text-gray-700">Sudah selesai dikerjakan</span>
        </label>
    </div>

    <div class="mb-4" x-show="trackingMode === 'numeric'" x-cloak>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-700">Nilai</label>
                <input type="number" step="0.01" min="0" name="value_number"
                    value="{{ $entry?->value_number }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
            </div>
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-700">
                    Satuan <span class="font-normal text-gray-400">(opsional)</span>
                </label>
                <input type="text" name="unit" value="{{ $task?->unit }}" placeholder="misal: orang, jam"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
            </div>
        </div>
    </div>

    @if ($isEdit)
        <p class="p-2.5 mb-3 text-xs text-amber-700 rounded-lg bg-amber-50">
            ⏳ Bisa diubah sampai {{ $task->created_at->copy()->addDays(3)->translatedFormat('d M Y, H:i') }}.
            Setelah itu catatan terkunci.
        </p>
    @else
        <p class="p-2.5 mb-3 text-xs text-gray-500 rounded-lg bg-gray-50">
            ℹ️ Bukan penugasan atasan, gak masuk hitungan KPI, gak perlu divalidasi.
            Bisa diedit 3 hari, bisa dihapus 1 hari.
        </p>
    @endif

    <p class="mb-3 text-xs text-red-600" x-show="error" x-text="error" x-cloak></p>

    <div class="flex justify-end gap-2">
        <button type="button" @click="open = false"
            class="px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100">
            Batal
        </button>
        <button type="submit" :disabled="submitting"
            class="px-4 py-2 text-sm text-white transition bg-teal-500 rounded-lg hover:bg-teal-600 disabled:opacity-50">
            <span x-show="!submitting">💾 {{ $isEdit ? 'Simpan Perubahan' : 'Simpan' }}</span>
            <span x-show="submitting" x-cloak>Menyimpan...</span>
        </button>
    </div>
</form>
