<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    🔍 Detail Laporan — Validasi
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $report->user->name }} | {{ $report->branch->name }} |
                    {{ $report->shift_label }} Slot {{ $report->slot }} ({{ $report->slot_time }})
                </p>
            </div>
            <a href="{{ route('validation.index') }}"
                class="flex items-center gap-2 px-4 py-2 text-white bg-gray-500 rounded-lg hover:bg-gray-600">
                ← Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Alerts --}}
            @if (session('success'))
                <div class="p-4 text-green-700 bg-green-100 rounded-lg">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="p-4 text-red-700 bg-red-100 rounded-lg">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="p-4 text-red-700 bg-red-100 rounded-lg">
                    <p class="font-semibold">Terjadi kesalahan:</p>
                    <ul class="mt-2 ml-4 list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- ============================================================ --}}
            {{-- INFO LAPORAN + STATUS                                         --}}
            {{-- ============================================================ --}}
            <div class="p-5 bg-white rounded-lg shadow-sm">
                <div class="flex items-start justify-between">
                    <div class="space-y-1">
                        <p class="text-sm text-gray-500">
                            Upload: <strong>{{ $report->uploaded_at->format('H:i:s') }}</strong>
                        </p>
                        <p class="text-sm text-gray-500">
                            Window FO:
                            <strong>{{ $report->fo_window_start->format('H:i') }} –
                                {{ $report->fo_window_end->format('H:i') }}</strong>
                        </p>
                        <p class="text-sm text-gray-500">
                            Window Validasi:
                            <strong>{{ $report->manager_window_start->format('H:i') }} –
                                {{ $report->manager_window_end->format('H:i') }}</strong>
                        </p>
                    </div>

                    <div class="text-right space-y-2">
                        @if ($report->validation_status === 'approved')
                            <span
                                class="px-3 py-1 text-sm font-semibold text-green-700 bg-green-100 rounded-full block">✅
                                Disetujui</span>
                        @elseif ($report->validation_status === 'rejected')
                            <span class="px-3 py-1 text-sm font-semibold text-red-700 bg-red-100 rounded-full block">❌
                                Ditolak</span>
                        @else
                            <span
                                class="px-3 py-1 text-sm font-semibold text-yellow-700 bg-yellow-100 rounded-full block">⏳
                                Menunggu Validasi</span>
                        @endif

                        @if ($managerWindowStatus === 'open')
                            <p class="text-xs text-orange-600 font-semibold">🟠 Window validasi sedang buka</p>
                        @elseif ($managerWindowStatus === 'waiting')
                            <p class="text-xs text-gray-400">⏳ Window validasi belum buka</p>
                        @else
                            <p class="text-xs text-gray-400">🔒 Window validasi sudah tutup</p>
                        @endif
                    </div>
                </div>

                {{-- Riwayat validasi --}}
                @if ($report->validation)
                    <div class="mt-4 p-3 bg-gray-50 rounded-lg text-sm border border-gray-200">
                        <p class="font-semibold text-gray-700 mb-1">Riwayat Validasi:</p>
                        <p class="text-gray-600">
                            Oleh: <strong>{{ $report->validation->manager->name }}</strong>
                            pada {{ $report->validation->validated_at->format('d M Y H:i') }}
                        </p>
                        <p class="text-gray-600">Tindakan: <strong>{{ $report->validation->action->name }}</strong></p>
                        @if ($report->validation->catatan)
                            <p class="text-gray-600">Catatan: {{ $report->validation->catatan }}</p>
                        @endif
                    </div>
                @endif
            </div>

            {{-- ============================================================ --}}
            {{-- METRIK BISNIS                                                  --}}
            {{-- ============================================================ --}}
            <div class="p-5 bg-blue-50 rounded-lg shadow-sm border border-blue-200">
                <h3 class="mb-4 text-base font-semibold text-blue-800">📊 Metrik Bisnis</h3>
                <div class="grid grid-cols-3 gap-4">
                    @php
                        $omset = $metrikDetails->get('mb_omset')?->value_number ?? 0;
                        $revenue = $metrikDetails->get('mb_revenue')?->value_number ?? 0;
                        $akad = $metrikDetails->get('mb_jumlah_akad')?->value_number ?? 0;
                    @endphp
                    <div class="p-4 bg-white rounded-lg text-center shadow-sm">
                        <p class="text-xs text-gray-500 mb-1">Omset</p>
                        <p class="text-xl font-bold text-gray-800">Rp {{ number_format($omset, 0, ',', '.') }}</p>
                    </div>
                    <div class="p-4 bg-white rounded-lg text-center shadow-sm">
                        <p class="text-xs text-gray-500 mb-1">Revenue</p>
                        <p class="text-xl font-bold text-gray-800">Rp {{ number_format($revenue, 0, ',', '.') }}</p>
                    </div>
                    <div class="p-4 bg-white rounded-lg text-center shadow-sm">
                        <p class="text-xs text-gray-500 mb-1">Jumlah Akad</p>
                        <p class="text-xl font-bold text-gray-800">{{ number_format($akad, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- FORM VALIDASI                                                  --}}
            {{-- ============================================================ --}}
            @if ($canValidate)
                <div class="p-5 bg-white rounded-lg shadow-sm">
                    <h3 class="mb-4 text-base font-semibold text-gray-800">
                        ✍️ {{ $report->validation ? 'Update' : 'Beri' }} Validasi
                    </h3>

                    <form method="POST" action="{{ route('validation.validate', $report->id) }}">
                        @csrf

                        {{-- Status --}}
                        <div class="mb-4">
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Status Validasi <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-3">
                                <label
                                    class="flex items-center gap-2 px-4 py-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-500 hover:bg-green-50 flex-1">
                                    <input type="radio" name="status" value="approved"
                                        {{ old('status', $report->validation?->status) === 'approved' ? 'checked' : '' }}
                                        class="w-4 h-4 text-green-600">
                                    <span class="font-semibold text-green-700">✅ Setujui</span>
                                </label>
                                <label
                                    class="flex items-center gap-2 px-4 py-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-red-500 hover:bg-red-50 flex-1">
                                    <input type="radio" name="status" value="rejected"
                                        {{ old('status', $report->validation?->status) === 'rejected' ? 'checked' : '' }}
                                        class="w-4 h-4 text-red-600">
                                    <span class="font-semibold text-red-700">❌ Tolak</span>
                                </label>
                            </div>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Opsi Tindakan --}}
                        <div class="mb-4">
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Tindakan yang Dilakukan <span class="text-red-500">*</span>
                            </label>
                            <div class="space-y-2">
                                @foreach ($validationActions as $action)
                                    <label
                                        class="flex items-center gap-3 px-4 py-3 border border-gray-200 rounded-lg cursor-pointer hover:border-teal-500 hover:bg-teal-50">
                                        <input type="radio" name="validation_action_id" value="{{ $action->id }}"
                                            {{ old('validation_action_id', $report->validation?->validation_action_id) == $action->id ? 'checked' : '' }}
                                            class="w-4 h-4 text-teal-600">
                                        <span class="text-sm text-gray-700">{{ $action->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('validation_action_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Catatan --}}
                        <div class="mb-6">
                            <label class="block mb-1 text-sm font-medium text-gray-700">
                                Catatan <span class="text-gray-400 font-normal">(opsional)</span>
                            </label>
                            <textarea name="catatan" rows="2" placeholder="Tambahkan catatan jika perlu..."
                                class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">{{ old('catatan', $report->validation?->catatan) }}</textarea>
                            @error('catatan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end gap-3">
                            <a href="{{ route('validation.index') }}"
                                class="px-5 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
                                Batal
                            </a>
                            <button type="submit"
                                class="px-6 py-2 text-white bg-teal-600 rounded-lg hover:bg-teal-700">
                                💾 Simpan Validasi
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            {{-- Reset (superadmin only) --}}
            @if ($isSuperadmin && $report->validation)
                <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-gray-700">Reset Validasi</p>
                            <p class="text-xs text-gray-500">Hapus validasi dan kembalikan status ke pending.</p>
                        </div>
                        <form method="POST" action="{{ route('validation.reset', $report->id) }}"
                            onsubmit="return confirm('Reset validasi laporan ini ke pending?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="px-4 py-2 text-sm text-white bg-red-600 rounded-lg hover:bg-red-700">
                                🔄 Reset ke Pending
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            {{-- ============================================================ --}}
            {{-- DETAIL LAPORAN LENGKAP + FOTO                                 --}}
            {{-- ============================================================ --}}
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <button onclick="toggleDetails()"
                    class="w-full flex items-center justify-between px-5 py-4 text-left border-b border-gray-200 hover:bg-gray-50">
                    <span class="font-semibold text-gray-700">📋 Detail Laporan Lengkap</span>
                    <span id="toggleIcon" class="text-gray-400 transition-transform">▼</span>
                </button>

                <div id="detailsPanel" class="hidden divide-y divide-gray-100">
                    @foreach ($detailsByCategory as $categoryName => $details)
                        <div class="px-5 py-4">
                            <h4 class="mb-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                {{ $categoryName }}
                            </h4>

                            @foreach ($details as $detail)
                                <div class="py-2 border-b border-gray-50 last:border-0">
                                    {{-- Baris atas: nama field + nilai --}}
                                    <div class="flex items-center justify-between gap-4">
                                        <p class="text-sm text-gray-700 flex-1">{{ $detail->field->name }}</p>
                                        <div class="text-right shrink-0">
                                            @if ($detail->field->input_type === 'checkbox')
                                                <span
                                                    class="{{ $detail->value_boolean ? 'text-green-600 font-semibold' : 'text-gray-400' }}">
                                                    {{ $detail->value_boolean ? '✅ Ya' : '—' }}
                                                </span>
                                            @elseif ($detail->field->input_type === 'number')
                                                <span class="text-sm font-semibold text-gray-800">
                                                    {{ number_format($detail->value_number, 0, ',', '.') }}
                                                </span>
                                            @elseif ($detail->field->input_type === 'photo_number')
                                                <span class="text-sm font-semibold text-gray-800">
                                                    {{ number_format($detail->value_number ?? 0, 0, ',', '.') }}
                                                </span>
                                                @if ($detail->photos->count() > 0)
                                                    <span class="ml-1 text-xs text-blue-500">
                                                        📷 {{ $detail->photos->count() }}
                                                    </span>
                                                @endif
                                            @elseif ($detail->field->input_type === 'text')
                                                <span
                                                    class="text-sm text-gray-700">{{ $detail->value_text ?? '—' }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Thumbnail grid foto (hanya photo_number yang punya foto) --}}
                                    @if ($detail->field->input_type === 'photo_number' && $detail->photos->count() > 0)
                                        <div class="flex flex-wrap gap-2 mt-2">
                                            @foreach ($detail->photos as $photo)
                                                <button type="button"
                                                    onclick="openLightbox('{{ $photo->url }}', '{{ $detail->field->name }}')"
                                                    class="relative group overflow-hidden rounded-lg w-16 h-16 shrink-0 focus:outline-none focus:ring-2 focus:ring-teal-500">
                                                    <img src="{{ $photo->url }}" alt="{{ $detail->field->name }}"
                                                        loading="lazy"
                                                        class="w-full h-full object-cover transition group-hover:scale-110 group-hover:brightness-75">
                                                    {{-- Overlay zoom icon --}}
                                                    <div
                                                        class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                                                        <svg class="w-6 h-6 text-white drop-shadow" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0zm0 0l4 4" />
                                                        </svg>
                                                    </div>
                                                </button>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- LIGHTBOX                                                       --}}
    {{-- ============================================================ --}}
    <div id="lightbox" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-90"
        onclick="closeLightboxOnBackdrop(event)">

        <div class="relative max-w-5xl max-h-screen w-full mx-4 flex flex-col items-center">

            {{-- Caption --}}
            <p id="lightboxCaption"
                class="mb-3 text-sm font-semibold text-white text-center px-4 py-1 bg-black bg-opacity-50 rounded-full">
            </p>

            {{-- Gambar --}}
            <img id="lightboxImg" src="" alt=""
                class="max-h-[80vh] max-w-full rounded-lg shadow-2xl object-contain">

            {{-- Tombol tutup --}}
            <button onclick="closeLightbox()"
                class="absolute top-0 right-0 -translate-y-10 translate-x-0 text-white text-3xl font-bold leading-none hover:text-gray-300 focus:outline-none"
                aria-label="Tutup">
                ✕
            </button>

            {{-- Open in new tab --}}
            <a id="lightboxLink" href="#" target="_blank"
                class="mt-4 px-4 py-2 text-sm text-white bg-teal-600 rounded-lg hover:bg-teal-700">
                🔗 Buka di tab baru
            </a>
        </div>
    </div>

    <script>
        // ============================================================
        // Toggle detail panel
        // ============================================================
        function toggleDetails() {
            const panel = document.getElementById('detailsPanel');
            const icon = document.getElementById('toggleIcon');
            panel.classList.toggle('hidden');
            icon.textContent = panel.classList.contains('hidden') ? '▼' : '▲';
        }

        // ============================================================
        // Lightbox
        // ============================================================
        const lightbox = document.getElementById('lightbox');
        const lightboxImg = document.getElementById('lightboxImg');
        const lightboxCap = document.getElementById('lightboxCaption');
        const lightboxLnk = document.getElementById('lightboxLink');

        function openLightbox(url, caption) {
            lightboxImg.src = url;
            lightboxImg.alt = caption;
            lightboxCap.textContent = caption;
            lightboxLnk.href = url;
            lightbox.classList.remove('hidden');
            lightbox.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeLightbox() {
            lightbox.classList.add('hidden');
            lightbox.classList.remove('flex');
            lightboxImg.src = '';
            document.body.style.overflow = '';
        }

        // Klik backdrop (bukan gambar) untuk tutup
        function closeLightboxOnBackdrop(event) {
            if (event.target === lightbox) {
                closeLightbox();
            }
        }

        // Tekan Escape untuk tutup
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeLightbox();
        });
    </script>
</x-app-layout>
