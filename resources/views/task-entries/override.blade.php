<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            🛠️ Koreksi Entry (Superadmin) — {{ $entry->taskAssignee->task->title }}
        </h2>
        <p class="mt-1 text-sm text-gray-500">
            {{ $entry->taskAssignee->user->name }}
            @if ($entry->entry_date)
                · {{ $entry->entry_date->format('d M Y') }}
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

                <div class="p-3 mb-6 text-xs text-purple-700 border border-purple-200 rounded-lg bg-purple-50">
                    ⚠️ Ini akses koreksi langsung — perubahan dicatat sebagai "Dikoreksi oleh Superadmin" di
                    audit trail (`task_status_logs`). Kalau data ini sudah pernah difinalisasi (snapshot KPI),
                    sistem otomatis hitung ulang setelah disimpan.
                </div>

                <form action="{{ route('task-entries.override.update', $entry->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    @if ($entry->taskAssignee->task->tracking_mode === 'numeric')
                        <div class="mb-4">
                            <label class="block mb-2 text-sm font-medium text-gray-700">Nilai</label>
                            <input type="number" step="0.01" min="0" name="value_number"
                                value="{{ old('value_number', $entry->value_number) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>
                    @else
                        <div class="mb-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="value_checklist" value="1"
                                    {{ old('value_checklist', $entry->value_checklist) ? 'checked' : '' }}
                                    class="w-5 h-5 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                                <span class="text-sm font-medium text-gray-700">Selesai dikerjakan</span>
                            </label>
                        </div>
                    @endif

                    @if ($entry->validation_status !== null)
                        <div class="mb-4">
                            <label class="block mb-2 text-sm font-medium text-gray-700">Status Validasi</label>
                            <select name="validation_status"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                                @foreach (['pending', 'checked', 'approved', 'rejected'] as $status)
                                    <option value="{{ $status }}"
                                        {{ old('validation_status', $entry->validation_status) === $status ? 'selected' : '' }}>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <div class="p-3 mb-4 text-xs text-gray-500 rounded-lg bg-gray-50">
                            Entry ini Tugas Tambahan (self-record) — status validasi tetap "tidak berlaku",
                            koreksi cuma berlaku ke nilainya.
                        </div>
                    @endif

                    <div class="mb-6">
                        <label class="block mb-2 text-sm font-medium text-gray-700">
                            Alasan Koreksi <span class="text-red-500">*</span>
                        </label>
                        <textarea name="reason" rows="3" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">{{ old('reason') }}</textarea>
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('tasks.show', $entry->taskAssignee->task_id) }}"
                            class="px-6 py-2 text-gray-700 transition bg-gray-200 rounded-lg hover:bg-gray-300">
                            Batal
                        </a>
                        <button type="submit"
                            class="px-6 py-2 text-white transition bg-purple-600 rounded-lg hover:bg-purple-700">
                            🛠️ Simpan Koreksi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
