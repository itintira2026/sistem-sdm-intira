<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
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
        <div class="max-w-4xl mx-auto space-y-6 sm:px-6 lg:px-8">

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
                <div class="flex flex-wrap items-start justify-center gap-4 md:justify-between">
                    <div class="space-y-1 text-center sm:text-start">
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

                    <div class="space-y-2 text-center sm:text-left">
                        @if ($report->validation_status === 'approved')
                            <span
                                class="block px-3 py-1 text-sm font-semibold text-green-700 bg-green-100 rounded-full">✅
                                Disetujui</span>
                        @elseif ($report->validation_status === 'rejected')
                            <span class="block px-3 py-1 text-sm font-semibold text-red-700 bg-red-100 rounded-full">❌
                                Ditolak</span>
                        @else
                            <span
                                class="block px-3 py-1 text-sm font-semibold text-yellow-700 bg-yellow-100 rounded-full">⏳
                                Menunggu Validasi</span>
                        @endif

                        @if ($managerWindowStatus === 'open')
                            <p class="text-xs font-semibold text-orange-600">🟠 Window validasi sedang buka</p>
                        @elseif ($managerWindowStatus === 'waiting')
                            <p class="text-xs text-gray-400">⏳ Window validasi belum buka</p>
                        @else
                            <p class="text-xs text-gray-400">🔒 Window validasi sudah tutup</p>
                        @endif
                    </div>
                </div>

                {{-- Riwayat validasi --}}
                @if ($report->validation)
                    <div class="p-3 mt-4 text-sm border border-gray-200 rounded-lg bg-gray-50">
                        <p class="mb-1 font-semibold text-gray-700">Riwayat Validasi:</p>
                        <p class="text-gray-600">
                            Oleh: <strong>{{ $report->validation->manager->name }}</strong>
                            pada {{ $report->validation->validated_at->format('d M Y H:i') }}
                        </p>
                        <div class="mt-1">
                            <span class="text-gray-600">Tindakan: </span>
                            <div class="inline-flex flex-wrap gap-1 mt-1">
                                @forelse ($report->validation->actions as $action)
                                    <span
                                        class="inline-block px-2 py-0.5 text-xs font-semibold bg-teal-100 text-teal-700 rounded-full">
                                        {{ $action->name }}
                                    </span>
                                @empty
                                    <span class="text-xs text-gray-400">Tidak ada tindakan</span>
                                @endforelse
                            </div>
                        </div>
                        @if ($report->validation->catatan)
                            <p class="mt-1 text-gray-600">Catatan: {{ $report->validation->catatan }}</p>
                        @endif
                    </div>
                @endif
            </div>

            {{-- ============================================================ --}}
            {{-- METRIK BISNIS                                                  --}}
            {{-- ============================================================ --}}
            <div class="p-5 border border-blue-200 rounded-lg shadow-sm bg-blue-50">
                <h3 class="mb-4 text-base font-semibold text-blue-800">📊 Metrik Bisnis</h3>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    @php
                        $omset = $metrikDetails->get('mb_omset')?->value_number ?? 0;
                        $revenue = $metrikDetails->get('mb_revenue')?->value_number ?? 0;
                        $akad = $metrikDetails->get('mb_jumlah_akad')?->value_number ?? 0;
                        $nasabahBaru = $metrikDetails->get('mb_nasabah_baru')?->value_number ?? 0;
                    @endphp
                    <div class="p-4 text-center bg-white rounded-lg shadow-sm">
                        <p class="mb-1 text-xs text-gray-500">Omset</p>
                        <p class="text-xl font-bold text-gray-800">Rp {{ number_format($omset, 0, ',', '.') }}</p>
                    </div>
                    <div class="p-4 text-center bg-white rounded-lg shadow-sm">
                        <p class="mb-1 text-xs text-gray-500">Revenue</p>
                        <p class="text-xl font-bold text-gray-800">Rp {{ number_format($revenue, 0, ',', '.') }}</p>
                    </div>
                    <div class="p-4 text-center bg-white rounded-lg shadow-sm">
                        <p class="mb-1 text-xs text-gray-500">Jumlah Akad</p>
                        <p class="text-xl font-bold text-gray-800">{{ number_format($akad, 0, ',', '.') }}</p>
                    </div>
                    <div class="p-4 text-center bg-white rounded-lg shadow-sm">
                        <p class="mb-1 text-xs text-gray-500">Nasabah Baru</p>
                        <p class="text-xl font-bold text-gray-800">{{ number_format($nasabahBaru, 0, ',', '.') }}</p>
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
                                    class="flex items-center flex-1 gap-2 px-4 py-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-500 hover:bg-green-50">
                                    <input type="radio" name="status" value="approved"
                                        {{ old('status', $report->validation?->status) === 'approved' ? 'checked' : '' }}
                                        class="w-4 h-4 text-green-600">
                                    <span class="font-semibold text-green-700">✅ Setujui</span>
                                </label>
                                <label
                                    class="flex items-center flex-1 gap-2 px-4 py-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-red-500 hover:bg-red-50">
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
                        <div class="mb-4" x-data="{
                            open: false,
                            selected: @js(old('validation_action_ids', $report->validation?->actions->pluck('id')->toArray() ?? [])),
                            allActions: @js($validationActions->map(fn($a) => ['id' => $a->id, 'name' => $a->name])->toArray()),
                        
                            get availableActions() {
                                return this.allActions.filter(a => !this.selected.includes(a.id));
                            },
                        
                            get selectedActions() {
                                return this.allActions.filter(a => this.selected.includes(a.id));
                            },
                        
                            add(id) {
                                if (!this.selected.includes(id)) {
                                    this.selected.push(id);
                                }
                            },
                        
                            remove(id) {
                                this.selected = this.selected.filter(i => i !== id);
                            }
                        }">
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Tindakan yang Dilakukan <span class="text-red-500">*</span>
                            </label>

                            {{-- ============================================================ --}}
                            {{-- AREA 1: TINDAKAN TERPILIH (Selected Tags di Atas)             --}}
                            {{-- ============================================================ --}}
                            <div class="p-4 mb-3 border-2 border-gray-200 rounded-lg bg-gray-50"
                                :class="{ 'border-dashed': selected.length === 0 }">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-semibold tracking-wider text-gray-500 uppercase">
                                        Tindakan Terpilih
                                    </span>
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full"
                                        :class="selected.length > 0 ? 'bg-teal-100 text-teal-700' : 'bg-gray-200 text-gray-500'"
                                        x-text="selected.length">
                                        0
                                    </span>
                                </div>

                                {{-- Badges --}}
                                <div x-show="selected.length > 0" class="flex flex-wrap gap-2">
                                    <template x-for="action in selectedActions" :key="action.id">
                                        <div
                                            class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-white bg-teal-600 rounded-lg shadow-sm">
                                            <span x-text="action.name"></span>
                                            <button type="button" @click="remove(action.id)"
                                                class="flex items-center justify-center w-4 h-4 text-white transition rounded-full hover:bg-teal-700">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="3" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                            {{-- Hidden input untuk submit --}}
                                            <input type="hidden" name="validation_action_ids[]" :value="action.id">
                                        </div>
                                    </template>
                                </div>

                                {{-- Empty state --}}
                                <div x-show="selected.length === 0" class="py-6 text-center text-gray-400">
                                    <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <p class="text-xs">Belum ada tindakan dipilih</p>
                                    <p class="text-xs text-gray-400">Pilih dari dropdown di bawah</p>
                                </div>
                            </div>

                            {{-- ============================================================ --}}
                            {{-- AREA 2: DROPDOWN TINDAKAN TERSEDIA (Available Actions)       --}}
                            {{-- ============================================================ --}}
                            <div class="relative">
                                <button type="button" @click="open = !open"
                                    class="flex items-center justify-between w-full px-4 py-3 text-left transition bg-white border-2 border-gray-300 rounded-lg hover:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500"
                                    :class="{ 'border-teal-500 bg-teal-50': open }">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4" />
                                        </svg>
                                        <span class="text-sm font-medium text-gray-700">Tambah Tindakan</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs text-gray-500"
                                            x-text="`${availableActions.length} tersedia`"></span>
                                        <svg class="w-5 h-5 text-gray-400 transition-transform"
                                            :class="{ 'rotate-180': open }" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </button>

                                {{-- Dropdown Panel --}}
                                <div x-show="open" @click.away="open = false"
                                    x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="opacity-0 -translate-y-1"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="opacity-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 -translate-y-1"
                                    class="absolute z-10 w-full mt-2 overflow-hidden bg-white border-2 border-gray-200 rounded-lg shadow-xl">

                                    {{-- Header --}}
                                    <div
                                        class="px-4 py-2 text-xs font-semibold tracking-wider text-gray-500 uppercase border-b bg-gray-50">
                                        Pilih Tindakan
                                    </div>

                                    {{-- Available Actions List --}}
                                    <div class="overflow-y-auto divide-y divide-gray-100 max-h-64">
                                        <template x-for="action in availableActions" :key="action.id">
                                            <button type="button" @click="add(action.id)"
                                                class="flex items-center justify-between w-full px-4 py-3 text-left transition group hover:bg-teal-50">
                                                <span
                                                    class="text-sm font-medium text-gray-700 group-hover:text-teal-700"
                                                    x-text="action.name"></span>
                                                <div class="flex items-center gap-2">
                                                    <span
                                                        class="text-xs font-semibold text-teal-600 transition opacity-0 group-hover:opacity-100">
                                                        Tambah
                                                    </span>
                                                    <svg class="w-5 h-5 text-teal-600 transition opacity-0 group-hover:opacity-100"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                    </svg>
                                                </div>
                                            </button>
                                        </template>

                                        {{-- Empty State --}}
                                        <div x-show="availableActions.length === 0" class="px-4 py-8 text-center">
                                            <div
                                                class="inline-flex items-center justify-center w-16 h-16 mb-3 bg-teal-100 rounded-full">
                                                <svg class="w-8 h-8 text-teal-600" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                            <p class="text-sm font-medium text-gray-700">Semua tindakan sudah dipilih
                                            </p>
                                            <p class="text-xs text-gray-500">Hapus tindakan di atas untuk memilih ulang
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @error('validation_action_ids')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Catatan --}}
                        <div class="mb-6">
                            <label class="block mb-1 text-sm font-medium text-gray-700">
                                Catatan <span class="font-normal text-gray-400">(opsional)</span>
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
                <div class="p-4 border border-gray-200 rounded-lg bg-gray-50">
                    <div class="flex flex-wrap items-center justify-between gap-4">
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
            <div class="overflow-hidden bg-white rounded-lg shadow-sm">
                <button onclick="toggleDetails()"
                    class="flex items-center justify-between w-full px-5 py-4 text-left border-b border-gray-200 hover:bg-gray-50">
                    <span class="font-semibold text-gray-700">📋 Detail Laporan Lengkap</span>
                    <span id="toggleIcon" class="text-gray-400 transition-transform">▼</span>
                </button>

                <div id="detailsPanel" class="hidden divide-y divide-gray-100">
                    @foreach ($detailsByCategory as $categoryName => $details)
                        <div class="px-5 py-4">
                            <h4 class="mb-3 text-xs font-semibold tracking-wider text-gray-500 uppercase">
                                {{ $categoryName }}
                            </h4>

                            @foreach ($details as $detail)
                                <div class="py-2 border-b border-gray-50 last:border-0">
                                    {{-- Baris atas: nama field + nilai --}}
                                    <div class="flex items-center justify-between gap-4">
                                        <p class="flex-1 text-sm text-gray-700">{{ $detail->field->name }}</p>
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
                                                    class="relative w-16 h-16 overflow-hidden rounded-lg group shrink-0 focus:outline-none focus:ring-2 focus:ring-teal-500">
                                                    <img src="{{ $photo->url }}" alt="{{ $detail->field->name }}"
                                                        loading="lazy"
                                                        class="object-cover w-full h-full transition group-hover:scale-110 group-hover:brightness-75">
                                                    {{-- Overlay zoom icon --}}
                                                    <div
                                                        class="absolute inset-0 flex items-center justify-center transition opacity-0 group-hover:opacity-100">
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
    <div id="lightbox" class="fixed inset-0 z-50 items-center justify-center hidden bg-black bg-opacity-90"
        onclick="closeLightboxOnBackdrop(event)">

        <div class="relative flex flex-col items-center w-full max-w-5xl max-h-screen mx-4">

            {{-- Caption --}}
            <p id="lightboxCaption"
                class="px-4 py-1 mb-3 text-sm font-semibold text-center text-white bg-black bg-opacity-50 rounded-full">
            </p>

            {{-- Gambar --}}
            <img id="lightboxImg" src="" alt=""
                class="max-h-[80vh] max-w-full rounded-lg shadow-2xl object-contain">

            {{-- Tombol tutup --}}
            <button onclick="closeLightbox()"
                class="absolute top-0 right-0 text-3xl font-bold leading-none text-white translate-x-0 -translate-y-10 hover:text-gray-300 focus:outline-none"
                aria-label="Tutup">
                ✕
            </button>

            {{-- Open in new tab --}}
            <a id="lightboxLink" href="#" target="_blank"
                class="px-4 py-2 mt-4 text-sm text-white bg-teal-600 rounded-lg hover:bg-teal-700">
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
