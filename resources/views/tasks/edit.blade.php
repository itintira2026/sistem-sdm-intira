<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                ✏️ Edit Tugas — {{ $task->title }}
            </h2>
            <a href="{{ route('tasks.show', $task->id) }}"
                class="px-4 py-2 text-white transition bg-gray-500 rounded-lg hover:bg-gray-600">
                Batal
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
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

                <div class="p-3 mb-6 text-xs text-yellow-700 border border-yellow-200 rounded-lg bg-yellow-50">
                    ⚠️ Cara tracking, mode tugas, dan periode TIDAK BISA diubah setelah tugas dibuat — sudah ada
                    entry/data yang bergantung ke nilai-nilai itu. Kalau memang perlu diubah, hapus tugas ini
                    dan buat baru.
                </div>

                <form action="{{ route('tasks.update', $task->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-medium text-gray-700">
                            Judul Tugas <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title" value="{{ old('title', $task->title) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>

                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-medium text-gray-700">Deskripsi</label>
                        <textarea name="description" rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">{{ old('description', $task->description) }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">Satuan</label>
                            <input type="text" name="unit" value="{{ old('unit', $task->unit) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">Divisi</label>
                            <select name="division_scope"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                                <option value="">— Tidak spesifik —</option>
                                @foreach ($divisions as $division)
                                    <option value="{{ $division->id }}"
                                        {{ old('division_scope', $task->division_scope) == $division->id ? 'selected' : '' }}>
                                        {{ $division->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block mb-2 text-sm font-medium text-gray-700">Tugas Referensi</label>
                        <select name="reference_task_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                            <option value="">— Tidak ada —</option>
                            @foreach ($previousTasks as $prev)
                                <option value="{{ $prev->id }}"
                                    {{ old('reference_task_id', $task->reference_task_id) == $prev->id ? 'selected' : '' }}>
                                    {{ $prev->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Field non-editable, ditampilkan read-only sebagai konteks --}}
                    <div class="grid grid-cols-3 gap-4 p-4 mb-6 text-sm rounded-lg bg-gray-50">
                        <div>
                            <p class="text-xs text-gray-400">Cara Tracking</p>
                            <p class="text-gray-600">{{ ucfirst($task->tracking_mode) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Mode</p>
                            <p class="text-gray-600">{{ ucfirst(str_replace('_', ' ', $task->repeat_mode)) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Periode</p>
                            <p class="text-gray-600">
                                @if ($task->period_start && $task->period_end)
                                    {{ $task->period_start->format('d/m/Y') }} -
                                    {{ $task->period_end->format('d/m/Y') }}
                                @else
                                    —
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('tasks.show', $task->id) }}"
                            class="px-6 py-2 text-gray-700 transition bg-gray-200 rounded-lg hover:bg-gray-300">
                            Batal
                        </a>
                        <button type="submit"
                            class="px-6 py-2 text-white transition bg-teal-500 rounded-lg hover:bg-teal-600">
                            💾 Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
