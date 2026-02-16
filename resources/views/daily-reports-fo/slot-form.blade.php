<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ $isEdit ? '✏️ Edit' : '📝 Upload' }} Laporan Slot {{ $slotNumber }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $branch->name }} | {{ $slotConfig['shift_label'] }} | {{ $slotTime }}
                </p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('daily-reports-fo.index') }}"
                    class="flex items-center gap-2 px-4 py-2 text-white transition bg-gray-500 rounded-lg hover:bg-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            {{-- Alert Error --}}
            @if ($errors->any())
                <div class="p-4 mb-6 text-red-700 bg-red-100 rounded-lg">
                    <p class="font-semibold">Terjadi kesalahan:</p>
                    <ul class="mt-2 ml-4 list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Window Info --}}
            <div class="p-4 mb-6 rounded-lg bg-orange-50">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-orange-800">⏰ Window Upload</p>
                        <p class="text-lg font-bold text-orange-600">
                            {{ $window['start']->format('H:i') }} - {{ $window['end']->format('H:i') }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-orange-700">Waktu Tersisa:</p>
                        <p class="text-2xl font-bold text-orange-600 countdown-timer"
                            data-seconds="{{ \App\Helpers\TimeHelper::getRemainingTimeInSlot($slotTime, $branch->id) }}">
                            Loading...
                        </p>
                    </div>
                </div>
            </div>

            <form method="POST"
                action="{{ $isEdit ? route('daily-reports-fo.slot.update', $slotNumber) : route('daily-reports-fo.slot.store', $slotNumber) }}"
                enctype="multipart/form-data">
                @csrf
                @if ($isEdit)
                    @method('PUT')
                @endif

                {{-- ============================================================ --}}
                {{-- LOOP KATEGORI DARI MASTER DATA                               --}}
                {{-- ============================================================ --}}
                @foreach ($categories as $category)
                    <div class="mb-6 bg-white shadow-sm sm:rounded-lg">
                        {{-- Category Header --}}
                        <div
                            class="px-6 py-4 border-b border-gray-200 rounded-t-lg
                            @if ($category->code === 'metrik_bisnis') bg-blue-50
                            @elseif($category->code === 'operasional') bg-green-50
                            @elseif($category->code === 'keuangan') bg-yellow-50
                            @elseif($category->code === 'sdm') bg-purple-50
                            @elseif($category->code === 'marketing') bg-orange-50
                            @else bg-gray-50 @endif">
                            <h3 class="text-base font-semibold text-gray-800">
                                @if ($category->code === 'metrik_bisnis')
                                    📊
                                @elseif($category->code === 'operasional')
                                    🏢
                                @elseif($category->code === 'keuangan')
                                    💰
                                @elseif($category->code === 'sdm')
                                    👥
                                @elseif($category->code === 'marketing')
                                    📱
                                @else
                                    📋
                                @endif
                                {{ $category->name }}
                            </h3>
                            @if ($category->description)
                                <p class="mt-1 text-xs text-gray-500">{{ $category->description }}</p>
                            @endif
                        </div>

                        <div class="p-6">
                            @foreach ($category->fields as $field)
                                @php
                                    $inputName = "field_{$field->id}";
                                    $photoName = "photo_{$field->id}";
                                    $deletePhotoName = "delete_photos_{$field->id}";
                                    $existingDetail = $existingDetails->get($field->id);
                                @endphp

                                {{-- ============================================ --}}
                                {{-- CHECKBOX                                     --}}
                                {{-- ============================================ --}}
                                @if ($field->input_type === 'checkbox')
                                    <div class="flex items-start gap-3 py-3 border-b border-gray-100 last:border-0">
                                        <div class="flex items-center h-6">
                                            <input type="checkbox" name="{{ $inputName }}" id="{{ $inputName }}"
                                                value="1"
                                                {{ old($inputName, $existingDetail?->value_boolean) ? 'checked' : '' }}
                                                class="w-5 h-5 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                                        </div>
                                        <div class="flex-1">
                                            <label for="{{ $inputName }}"
                                                class="text-sm font-medium text-gray-700 cursor-pointer">
                                                {{ $field->name }}
                                                @if ($field->is_required)
                                                    <span class="text-red-500">*</span>
                                                @endif
                                            </label>
                                            @if ($field->help_text)
                                                <p class="mt-0.5 text-xs text-gray-400">{{ $field->help_text }}</p>
                                            @endif
                                        </div>
                                        @error($inputName)
                                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- ============================================ --}}
                                    {{-- NUMBER                                       --}}
                                    {{-- ============================================ --}}
                                @elseif ($field->input_type === 'number')
                                    <div class="mb-4">
                                        <label for="{{ $inputName }}"
                                            class="block mb-1 text-sm font-medium text-gray-700">
                                            {{ $field->name }}
                                            @if ($field->is_required)
                                                <span class="text-red-500">*</span>
                                            @endif
                                        </label>
                                        <input type="number" name="{{ $inputName }}" id="{{ $inputName }}"
                                            value="{{ old($inputName, $existingDetail?->value_number ?? '') }}"
                                            min="0" step="1"
                                            placeholder="{{ $field->placeholder ?? 'Masukkan angka...' }}"
                                            {{ $field->is_required ? 'required' : '' }}
                                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 @error($inputName) border-red-500 @else border-gray-300 @enderror">
                                        @if ($field->help_text)
                                            <p class="mt-1 text-xs text-gray-400">{{ $field->help_text }}</p>
                                        @endif
                                        @error($inputName)
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- ============================================ --}}
                                    {{-- PHOTO + NUMBER                               --}}
                                    {{-- ============================================ --}}
                                @elseif ($field->input_type === 'photo_number')
                                    <div class="p-4 mb-4 border border-gray-200 rounded-lg">
                                        <p class="mb-3 text-sm font-semibold text-gray-700">
                                            {{ $field->name }}
                                            @if ($field->is_required)
                                                <span class="text-red-500">*</span>
                                            @endif
                                        </p>

                                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                            {{-- Input Angka --}}
                                            <div>
                                                <label class="block mb-1 text-xs font-medium text-gray-600">
                                                    Jumlah
                                                </label>
                                                <input type="number" name="{{ $inputName }}"
                                                    id="{{ $inputName }}"
                                                    value="{{ old($inputName, $existingDetail?->value_number ?? '') }}"
                                                    min="0" step="1"
                                                    placeholder="{{ $field->placeholder ?? '0' }}"
                                                    class="w-full px-3 py-2 text-sm border rounded-lg focus:ring-2 focus:ring-teal-500 @error($inputName) border-red-500 @else border-gray-300 @enderror">
                                                @error($inputName)
                                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            {{-- Upload Foto --}}
                                            <div>
                                                <label class="block mb-1 text-xs font-medium text-gray-600">
                                                    Foto Bukti
                                                    @if ($field->is_required)
                                                        <span class="text-red-500">*</span>
                                                    @endif
                                                </label>
                                                <input type="file" name="{{ $photoName }}[]"
                                                    id="{{ $photoName }}" accept="image/jpeg,image/jpg,image/png"
                                                    multiple onchange="previewPhotos({{ $field->id }})"
                                                    class="w-full px-3 py-2 text-sm border rounded-lg focus:ring-2 focus:ring-teal-500 @error($photoName) border-red-500 @else border-gray-300 @enderror">
                                                <p class="mt-1 text-xs text-gray-400">
                                                    JPG/PNG, maks. 5MB per foto
                                                </p>
                                                @error($photoName)
                                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Foto existing (saat edit) --}}
                                        @if ($isEdit && $existingDetail && $existingDetail->photos->count() > 0)
                                            <div class="mt-3">
                                                <p class="mb-2 text-xs font-medium text-gray-500">
                                                    Foto yang sudah ada ({{ $existingDetail->photos->count() }}):
                                                </p>
                                                <div class="grid grid-cols-4 gap-2 sm:grid-cols-6">
                                                    @foreach ($existingDetail->photos as $photo)
                                                        <div class="relative group">
                                                            <img src="{{ $photo->url }}" alt="Foto bukti"
                                                                class="object-cover w-full h-16 rounded-lg">
                                                            <label
                                                                class="absolute inset-0 flex items-center justify-center transition rounded-lg opacity-0 cursor-pointer bg-red-600/70 group-hover:opacity-100">
                                                                <input type="checkbox" name="{{ $deletePhotoName }}[]"
                                                                    value="{{ $photo->id }}"
                                                                    class="hidden photo-delete-checkbox"
                                                                    data-field="{{ $field->id }}"
                                                                    onchange="toggleDeleteMark(this)">
                                                                <span class="text-xs font-semibold text-white">
                                                                    🗑 Hapus
                                                                </span>
                                                            </label>
                                                            {{-- Tanda merah jika ditandai hapus --}}
                                                            <div
                                                                class="absolute top-0 left-0 hidden w-full h-full border-2 border-red-500 rounded-lg delete-mark">
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <p class="mt-1 text-xs text-gray-400">
                                                    Hover foto → klik untuk tandai hapus
                                                </p>
                                            </div>
                                        @endif

                                        {{-- Preview foto baru --}}
                                        <div id="preview_{{ $field->id }}"
                                            class="grid grid-cols-4 gap-2 mt-3 sm:grid-cols-6"></div>
                                    </div>

                                    {{-- ============================================ --}}
                                    {{-- TEXT                                         --}}
                                    {{-- ============================================ --}}
                                @elseif ($field->input_type === 'text')
                                    <div class="mb-4">
                                        <label for="{{ $inputName }}"
                                            class="block mb-1 text-sm font-medium text-gray-700">
                                            {{ $field->name }}
                                            @if ($field->is_required)
                                                <span class="text-red-500">*</span>
                                            @endif
                                        </label>
                                        <input type="text" name="{{ $inputName }}" id="{{ $inputName }}"
                                            value="{{ old($inputName, $existingDetail?->value_text ?? '') }}"
                                            placeholder="{{ $field->placeholder ?? '' }}"
                                            {{ $field->is_required ? 'required' : '' }}
                                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 @error($inputName) border-red-500 @else border-gray-300 @enderror">
                                        @if ($field->help_text)
                                            <p class="mt-1 text-xs text-gray-400">{{ $field->help_text }}</p>
                                        @endif
                                        @error($inputName)
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @endif
                            @endforeach {{-- end fields loop --}}
                        </div>
                    </div>
                @endforeach {{-- end categories loop --}}

                {{-- ============================================================ --}}
                {{-- KETERANGAN                                                    --}}
                {{-- ============================================================ --}}
                <div class="p-6 mb-6 bg-white shadow-sm sm:rounded-lg">
                    <label class="block mb-2 text-sm font-medium text-gray-700">
                        Keterangan (Opsional)
                    </label>
                    <textarea name="keterangan" rows="3" placeholder="Catatan tambahan tentang laporan slot ini..."
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 @error('keterangan') border-red-500 @else border-gray-300 @enderror">{{ old('keterangan', $existingReport?->keterangan ?? '') }}</textarea>
                    @error('keterangan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Maksimal 1000 karakter</p>
                </div>

                {{-- ============================================================ --}}
                {{-- INFO BOX + BUTTONS                                            --}}
                {{-- ============================================================ --}}
                <div class="p-4 mb-6 rounded-lg bg-teal-50">
                    <p class="text-sm text-teal-800">
                        💡 <strong>Tips:</strong>
                        Isi semua field yang wajib (<span class="text-red-500">*</span>).
                        Untuk Metrik Bisnis, isi angka 0 jika memang tidak ada.
                        Upload foto bukti untuk setiap aktivitas marketing.
                    </p>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('daily-reports-fo.index') }}"
                        class="px-6 py-2 text-gray-700 transition border border-gray-300 rounded-lg hover:bg-gray-50">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-6 py-2 text-white transition bg-teal-600 rounded-lg hover:bg-teal-700">
                        💾 {{ $isEdit ? 'Update' : 'Upload' }} Laporan
                    </button>
                </div>

            </form>

        </div>
    </div>

    {{-- JavaScript --}}
    <script>
        // ============================================================
        // Countdown timer
        // ============================================================
        function updateCountdown() {
            const timer = document.querySelector('.countdown-timer');
            if (!timer) return;

            let seconds = parseInt(timer.dataset.seconds);

            if (seconds <= 0) {
                timer.textContent = '00:00:00';
                setTimeout(() => location.reload(), 2000);
                return;
            }

            seconds--;
            timer.dataset.seconds = seconds;

            const hours = Math.floor(seconds / 3600);
            const minutes = Math.floor((seconds % 3600) / 60);
            const secs = seconds % 60;

            timer.textContent =
                `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
        }

        setInterval(updateCountdown, 1000);

        // ============================================================
        // Preview foto baru sebelum upload
        // ============================================================
        function previewPhotos(fieldId) {
            const input = document.getElementById(`photo_${fieldId}`);
            const preview = document.getElementById(`preview_${fieldId}`);
            preview.innerHTML = '';

            if (!input || !input.files) return;

            Array.from(input.files).forEach((file, index) => {
                const reader = new FileReader();

                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative';
                    div.innerHTML = `
                        <img src="${e.target.result}"
                            class="object-cover w-full h-16 rounded-lg border border-teal-300">
                        <div class="absolute top-0.5 right-0.5 bg-teal-600 text-white text-xs rounded px-1">
                            ${index + 1}
                        </div>
                    `;
                    preview.appendChild(div);
                };

                reader.readAsDataURL(file);
            });
        }

        // ============================================================
        // Toggle tanda hapus pada foto existing
        // ============================================================
        function toggleDeleteMark(checkbox) {
            const container = checkbox.closest('.relative.group');
            const deleteMark = container.querySelector('.delete-mark');

            if (checkbox.checked) {
                deleteMark.classList.remove('hidden');
                container.classList.add('opacity-60');
            } else {
                deleteMark.classList.add('hidden');
                container.classList.remove('opacity-60');
            }
        }
    </script>
</x-app-layout>
