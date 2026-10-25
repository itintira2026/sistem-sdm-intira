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
            {{-- INFO LAPORAN + STATUS (2 tahap)                               --}}
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
                            Window Cek:
                            <strong>{{ $report->manager_window_start->format('H:i') }} –
                                {{ $report->manager_window_end->format('H:i') }}</strong>
                        </p>
                    </div>

                    <div class="space-y-2 text-center sm:text-left">
                        {{-- Badge status — DITAMBAH 'checked' --}}
                        @if ($report->validation_status === 'approved')
                            <span
                                class="block px-3 py-1 text-sm font-semibold text-green-700 bg-green-100 rounded-full">✅
                                Disetujui</span>
                        @elseif ($report->validation_status === 'rejected')
                            <span class="block px-3 py-1 text-sm font-semibold text-red-700 bg-red-100 rounded-full">❌
                                Ditolak</span>
                        @elseif ($report->validation_status === 'checked')
                            <span
                                class="block px-3 py-1 text-sm font-semibold text-blue-700 bg-blue-100 rounded-full">🔍
                                Dicek — Menunggu Approve Korwil</span>
                        @else
                            <span
                                class="block px-3 py-1 text-sm font-semibold text-yellow-700 bg-yellow-100 rounded-full">⏳
                                Menunggu Cek</span>
                        @endif

                        @if ($managerWindowStatus === 'open')
                            <p class="text-xs font-semibold text-orange-600">🟠 Window cek sedang buka</p>
                        @elseif ($managerWindowStatus === 'waiting')
                            <p class="text-xs text-gray-400">⏳ Window cek belum buka</p>
                        @else
                            <p class="text-xs text-gray-400">🔒 Window cek sudah tutup</p>
                        @endif
                    </div>
                </div>

                {{-- Riwayat validasi — DIPISAH jadi 2 baris: Cek & Approve --}}
                @if ($report->validation)
                    <div class="p-3 mt-4 space-y-3 text-sm border border-gray-200 rounded-lg bg-gray-50">

                        {{-- Riwayat Cek --}}
                        @if ($report->validation->checked_at)
                            <div>
                                <p class="mb-1 font-semibold text-gray-700">🔍 Tahap Cek:</p>
                                <p class="text-gray-600">
                                    Oleh: <strong>{{ $report->validation->checker->name ?? '—' }}</strong>
                                    pada {{ $report->validation->checked_at->format('d M Y H:i') }}
                                </p>
                            </div>
                        @endif

                        {{-- Riwayat Approve Final --}}
                        @if ($report->validation->validated_at)
                            <div class="pt-2 border-t border-gray-200">
                                <p class="mb-1 font-semibold text-gray-700">
                                    {{ $report->validation->status === 'approved' ? '✅' : '❌' }} Approve Final:
                                </p>
                                <p class="text-gray-600">
                                    Oleh: <strong>{{ $report->validation->manager->name ?? '—' }}</strong>
                                    pada {{ $report->validation->validated_at->format('d M Y H:i') }}
                                </p>
                            </div>
                        @endif

                        {{-- Tindakan & Catatan (berlaku untuk tahap manapun yang terakhir diisi) --}}
                        <div class="pt-2 border-t border-gray-200">
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
                            @if ($report->validation->catatan)
                                <p class="mt-2 text-gray-600">Catatan: {{ $report->validation->catatan }}</p>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            {{-- ============================================================ --}}
            {{-- METRIK BISNIS (tidak berubah)                                  --}}
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
            {{-- FORM TAHAP 1 — CEK (AM / manager)                             --}}
            {{-- ============================================================ --}}
            @if ($canCheck)
                <div class="p-5 bg-white border-l-4 border-blue-400 rounded-lg shadow-sm">
                    <h3 class="mb-1 text-base font-semibold text-gray-800">🔍 Form Cek (Tahap 1)</h3>
                    <p class="mb-4 text-xs text-gray-500">Setelah dicek, laporan akan diteruskan ke Korwil untuk
                        approve final.</p>

                    <form method="POST" action="{{ route('validation.check', $report->id) }}">
                        @csrf

                        <div class="mb-4">
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Hasil Cek <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-3">
                                <label
                                    class="flex items-center flex-1 gap-2 px-4 py-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 hover:bg-blue-50">
                                    <input type="radio" name="check_status" value="checked"
                                        {{ old('check_status') === 'checked' ? 'checked' : '' }}
                                        class="w-4 h-4 text-blue-600">
                                    <span class="font-semibold text-blue-700">🔍 Sudah Dicek, Lanjutkan</span>
                                </label>
                                <label
                                    class="flex items-center flex-1 gap-2 px-4 py-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-red-500 hover:bg-red-50">
                                    <input type="radio" name="check_status" value="rejected"
                                        {{ old('check_status') === 'rejected' ? 'checked' : '' }}
                                        class="w-4 h-4 text-red-600">
                                    <span class="font-semibold text-red-700">❌ Tolak Langsung</span>
                                </label>
                            </div>
                            @error('check_status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4" x-data="{
                            open: false,
                            selected: [],
                            allActions: @js($validationActions->map(fn($a) => ['id' => $a->id, 'name' => $a->name])->toArray()),
                            get availableActions() { return this.allActions.filter(a => !this.selected.includes(a.id)); },
                            get selectedActions() { return this.allActions.filter(a => this.selected.includes(a.id)); },
                            add(id) { if (!this.selected.includes(id)) this.selected.push(id); },
                            remove(id) { this.selected = this.selected.filter(i => i !== id); }
                        }">
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Tindakan yang Dilakukan <span class="text-red-500">*</span>
                            </label>

                            <div class="p-4 mb-3 border-2 border-gray-200 rounded-lg bg-gray-50"
                                :class="{ 'border-dashed': selected.length === 0 }">
                                <div class="flex flex-wrap gap-2" x-show="selected.length > 0">
                                    <template x-for="action in selectedActions" :key="action.id">
                                        <div
                                            class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg shadow-sm">
                                            <span x-text="action.name"></span>
                                            <button type="button" @click="remove(action.id)"
                                                class="flex items-center justify-center w-4 h-4 text-white rounded-full hover:bg-blue-700">✕</button>
                                            <input type="hidden" name="validation_action_ids[]" :value="action.id">
                                        </div>
                                    </template>
                                </div>
                                <div x-show="selected.length === 0" class="py-4 text-xs text-center text-gray-400">
                                    Belum ada tindakan dipilih
                                </div>
                            </div>

                            <div class="relative">
                                <button type="button" @click="open = !open"
                                    class="flex items-center justify-between w-full px-4 py-3 text-left transition bg-white border-2 border-gray-300 rounded-lg hover:border-blue-500">
                                    <span class="text-sm font-medium text-gray-700">+ Tambah Tindakan</span>
                                    <span class="text-xs text-gray-500"
                                        x-text="`${availableActions.length} tersedia`"></span>
                                </button>
                                <div x-show="open" @click.away="open = false"
                                    class="absolute z-10 w-full mt-2 overflow-hidden bg-white border-2 border-gray-200 rounded-lg shadow-xl">
                                    <div class="overflow-y-auto divide-y divide-gray-100 max-h-64">
                                        <template x-for="action in availableActions" :key="action.id">
                                            <button type="button" @click="add(action.id)"
                                                class="w-full px-4 py-3 text-sm text-left text-gray-700 hover:bg-blue-50"
                                                x-text="action.name"></button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            @error('validation_action_ids')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="block mb-1 text-sm font-medium text-gray-700">
                                Catatan <span class="font-normal text-gray-400">(opsional)</span>
                            </label>
                            <textarea name="catatan" rows="2" placeholder="Tambahkan catatan jika perlu..."
                                class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('catatan') }}</textarea>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                class="px-6 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                                💾 Simpan Hasil Cek
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            {{-- ============================================================ --}}
            {{-- FORM TAHAP 2 — APPROVE FINAL (Korwil)                         --}}
            {{-- ============================================================ --}}
            @if ($canApprove)
                <div class="p-5 bg-white border-l-4 border-teal-500 rounded-lg shadow-sm">
                    <h3 class="mb-1 text-base font-semibold text-gray-800">✍️ Form Approve Final (Tahap 2 — Korwil)
                    </h3>
                    <p class="mb-4 text-xs text-gray-500">Korwil bisa approve/tolak kapan saja, termasuk override
                        keputusan sebelumnya.</p>

                    <form method="POST" action="{{ route('validation.validate', $report->id) }}">
                        @csrf

                        <div class="mb-4">
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Status Final <span class="text-red-500">*</span>
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

                        <div class="mb-4" x-data="{
                            open: false,
                            selected: @js(old('validation_action_ids', $report->validation?->actions->pluck('id')->toArray() ?? [])),
                            allActions: @js($validationActions->map(fn($a) => ['id' => $a->id, 'name' => $a->name])->toArray()),
                            get availableActions() { return this.allActions.filter(a => !this.selected.includes(a.id)); },
                            get selectedActions() { return this.allActions.filter(a => this.selected.includes(a.id)); },
                            add(id) { if (!this.selected.includes(id)) this.selected.push(id); },
                            remove(id) { this.selected = this.selected.filter(i => i !== id); }
                        }">
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Tindakan yang Dilakukan <span class="text-red-500">*</span>
                            </label>

                            <div class="p-4 mb-3 border-2 border-gray-200 rounded-lg bg-gray-50"
                                :class="{ 'border-dashed': selected.length === 0 }">
                                <div class="flex flex-wrap gap-2" x-show="selected.length > 0">
                                    <template x-for="action in selectedActions" :key="action.id">
                                        <div
                                            class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-white bg-teal-600 rounded-lg shadow-sm">
                                            <span x-text="action.name"></span>
                                            <button type="button" @click="remove(action.id)"
                                                class="flex items-center justify-center w-4 h-4 text-white rounded-full hover:bg-teal-700">✕</button>
                                            <input type="hidden" name="validation_action_ids[]"
                                                :value="action.id">
                                        </div>
                                    </template>
                                </div>
                                <div x-show="selected.length === 0" class="py-4 text-xs text-center text-gray-400">
                                    Belum ada tindakan dipilih
                                </div>
                            </div>

                            <div class="relative">
                                <button type="button" @click="open = !open"
                                    class="flex items-center justify-between w-full px-4 py-3 text-left transition bg-white border-2 border-gray-300 rounded-lg hover:border-teal-500">
                                    <span class="text-sm font-medium text-gray-700">+ Tambah Tindakan</span>
                                    <span class="text-xs text-gray-500"
                                        x-text="`${availableActions.length} tersedia`"></span>
                                </button>
                                <div x-show="open" @click.away="open = false"
                                    class="absolute z-10 w-full mt-2 overflow-hidden bg-white border-2 border-gray-200 rounded-lg shadow-xl">
                                    <div class="overflow-y-auto divide-y divide-gray-100 max-h-64">
                                        <template x-for="action in availableActions" :key="action.id">
                                            <button type="button" @click="add(action.id)"
                                                class="w-full px-4 py-3 text-sm text-left text-gray-700 hover:bg-teal-50"
                                                x-text="action.name"></button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            @error('validation_action_ids')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="block mb-1 text-sm font-medium text-gray-700">
                                Catatan <span class="font-normal text-gray-400">(opsional)</span>
                            </label>
                            <textarea name="catatan" rows="2" placeholder="Tambahkan catatan jika perlu..."
                                class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">{{ old('catatan', $report->validation?->catatan) }}</textarea>
                        </div>

                        <div class="flex justify-end gap-3">
                            <a href="{{ route('validation.index') }}"
                                class="px-5 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
                                Batal
                            </a>
                            <button type="submit"
                                class="px-6 py-2 text-white bg-teal-600 rounded-lg hover:bg-teal-700">
                                💾 Simpan Approve Final
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            {{-- Reset (superadmin only) — tidak berubah --}}
            @if ($isSuperadmin && $report->validation)
                <div class="p-4 border border-gray-200 rounded-lg bg-gray-50">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold text-gray-700">Reset Validasi</p>
                            <p class="text-xs text-gray-500">Hapus seluruh riwayat cek & approve, kembalikan status ke
                                pending.</p>
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
            {{-- DETAIL LAPORAN LENGKAP + FOTO (tidak berubah)                 --}}
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
                                                    <span class="ml-1 text-xs text-blue-500">📷
                                                        {{ $detail->photos->count() }}</span>
                                                @endif
                                            @elseif ($detail->field->input_type === 'text')
                                                <span
                                                    class="text-sm text-gray-700">{{ $detail->value_text ?? '—' }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    @if ($detail->field->input_type === 'photo_number' && $detail->photos->count() > 0)
                                        <div class="flex flex-wrap gap-2 mt-2">
                                            @foreach ($detail->photos as $photo)
                                                <button type="button"
                                                    onclick="openLightbox('{{ $photo->url }}', '{{ $detail->field->name }}')"
                                                    class="relative w-16 h-16 overflow-hidden rounded-lg group shrink-0 focus:outline-none focus:ring-2 focus:ring-teal-500">
                                                    <img src="{{ $photo->url }}" alt="{{ $detail->field->name }}"
                                                        loading="lazy"
                                                        class="object-cover w-full h-full transition group-hover:scale-110 group-hover:brightness-75">
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

    {{-- LIGHTBOX (tidak berubah) --}}
    <div id="lightbox" class="fixed inset-0 z-50 items-center justify-center hidden bg-black bg-opacity-90"
        onclick="closeLightboxOnBackdrop(event)">
        <div class="relative flex flex-col items-center w-full max-w-5xl max-h-screen mx-4">
            <p id="lightboxCaption"
                class="px-4 py-1 mb-3 text-sm font-semibold text-center text-white bg-black bg-opacity-50 rounded-full">
            </p>
            <img id="lightboxImg" src="" alt=""
                class="max-h-[80vh] max-w-full rounded-lg shadow-2xl object-contain">
            <button onclick="closeLightbox()"
                class="absolute top-0 right-0 text-3xl font-bold leading-none text-white translate-x-0 -translate-y-10 hover:text-gray-300 focus:outline-none"
                aria-label="Tutup">✕</button>
            <a id="lightboxLink" href="#" target="_blank"
                class="px-4 py-2 mt-4 text-sm text-white bg-teal-600 rounded-lg hover:bg-teal-700">🔗 Buka di tab
                baru</a>
        </div>
    </div>

    <script>
        function toggleDetails() {
            const panel = document.getElementById('detailsPanel');
            const icon = document.getElementById('toggleIcon');
            panel.classList.toggle('hidden');
            icon.textContent = panel.classList.contains('hidden') ? '▼' : '▲';
        }

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

        function closeLightboxOnBackdrop(event) {
            if (event.target === lightbox) closeLightbox();
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeLightbox();
        });
    </script>
</x-app-layout>
