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

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST"
                        action="{{ $isEdit ? route('daily-reports-fo.slot.update', $slotNumber) : route('daily-reports-fo.slot.store', $slotNumber) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @if ($isEdit)
                            @method('PUT')
                        @endif

                        {{-- Upload Foto Per Kategori --}}
                        <h3 class="mb-6 text-lg font-semibold text-gray-800">
                            📷 Upload Foto Bukti Aktivitas
                        </h3>

                        @foreach ($categories as $key => $label)
                            <div class="p-4 mb-6 border border-gray-200 rounded-lg">
                                <label class="block mb-3 text-sm font-semibold text-gray-700">
                                    {{ $label }} <span class="text-red-600">*</span>
                                </label>

                                @if ($isEdit && $existingReport)
                                    {{-- Existing Photos --}}
                                    @php
                                        $existingPhotos = $existingReport->getPhotosByCategory($key);
                                    @endphp

                                    @if ($existingPhotos->count() > 0)
                                        <div class="mb-4">
                                            <p class="mb-2 text-xs text-gray-600">Foto yang sudah ada:</p>
                                            <div class="grid grid-cols-3 gap-3 md:grid-cols-4">
                                                @foreach ($existingPhotos as $photo)
                                                    <div class="relative group">
                                                        <img src="{{ $photo->url }}" alt="Photo"
                                                            class="object-cover w-full h-24 rounded-lg">
                                                        <label
                                                            class="absolute flex items-center gap-1 px-2 py-1 text-xs text-white bg-red-600 rounded cursor-pointer top-1 right-1 hover:bg-red-700">
                                                            <input type="checkbox"
                                                                name="delete_photos_{{ $key }}[]"
                                                                value="{{ $photo->id }}" class="w-3 h-3">
                                                            Hapus
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                @endif

                                {{-- Upload New Photos --}}
                                <input type="file" name="photos_{{ $key }}[]"
                                    id="photos_{{ $key }}" accept="image/jpeg,image/jpg,image/png" multiple
                                    {{ !$isEdit ? 'required' : '' }} onchange="previewPhotos('{{ $key }}')"
                                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 @error('photos_' . $key) border-red-500 @enderror">

                                <p class="mt-1 text-xs text-gray-500">
                                    Minimal 1 foto {{ $isEdit ? '(opsional jika sudah ada foto)' : '' }}, maksimal
                                    unlimited. Format: JPG, PNG. Max 5MB per foto.
                                </p>

                                @error('photos_' . $key)
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror

                                {{-- Preview --}}
                                <div id="preview_{{ $key }}"
                                    class="grid grid-cols-3 gap-3 mt-3 md:grid-cols-4"></div>
                            </div>
                        @endforeach

                        {{-- Divider --}}
                        <div class="my-6 border-t border-gray-200"></div>

                        {{-- Keterangan --}}
                        <div class="mb-6">
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Keterangan (Opsional)
                            </label>
                            <textarea name="keterangan" rows="3" placeholder="Catatan tambahan tentang laporan slot ini..."
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 @error('keterangan') border-red-500 @enderror">{{ old('keterangan', $existingReport->keterangan ?? '') }}</textarea>
                            @error('keterangan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Maksimal 1000 karakter</p>
                        </div>

                        {{-- Info Box --}}
                        <div class="p-4 mb-6 rounded-lg bg-teal-50">
                            <p class="text-sm text-teal-800">
                                💡 <strong>Tips:</strong> Pastikan semua 6 kategori foto sudah diupload minimal 1 foto
                                masing-masing. Upload dalam window waktu yang tersedia.
                            </p>
                        </div>

                        {{-- Buttons --}}
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

        </div>
    </div>

    {{-- JavaScript --}}
    <script>
        // Photo Preview
        function previewPhotos(category) {
            const input = document.getElementById(`photos_${category}`);
            const preview = document.getElementById(`preview_${category}`);
            preview.innerHTML = '';

            if (input.files) {
                Array.from(input.files).forEach((file, index) => {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        const div = document.createElement('div');
                        div.className = 'relative';
                        div.innerHTML = `
                            <img src="${e.target.result}" class="object-cover w-full h-24 rounded-lg">
                            <div class="absolute top-1 right-1">
                                <span class="px-2 py-1 text-xs text-white bg-black bg-opacity-50 rounded">
                                    ${index + 1}
                                </span>
                            </div>
                        `;
                        preview.appendChild(div);
                    }

                    reader.readAsDataURL(file);
                });
            }
        }

        // Countdown timer
        function updateCountdown() {
            const timer = document.querySelector('.countdown-timer');
            if (!timer) return;

            let seconds = parseInt(timer.dataset.seconds);

            if (seconds <= 0) {
                timer.textContent = '00:00:00';
                // Refresh page when time runs out
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
    </script>
</x-app-layout>
