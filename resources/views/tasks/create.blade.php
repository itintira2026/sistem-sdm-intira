<x-app-layout>

    <x-slot name="header">

        <div class="flex items-center justify-between gap-4">

            <h2 class="text-lg font-semibold tracking-tight sm:text-xl text-slate-800">

                ➕ Bikin Tugas KPI

            </h2>

            <a href="{{ route('dashboard') }}"
                class="px-4 py-2 text-sm font-medium text-white transition rounded-lg bg-slate-500 hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2">

                Batal

            </a>

        </div>

    </x-slot>

    <div class="min-h-screen py-6 sm:py-8 lg:py-10 bg-slate-50">

        <div class="max-w-6xl px-4 mx-auto sm:px-6 lg:px-8">

            @if (session('error'))
                <div class="p-4 mb-5 text-sm text-red-700 border border-red-100 bg-red-50 rounded-xl">
                    {{ session('error') }}</div>
            @endif

            @if ($errors->any())

                <div class="p-4 mb-5 text-sm text-red-700 border border-red-100 bg-red-50 rounded-xl">

                    <ul class="ml-4 list-disc">

                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach

                    </ul>

                </div>

            @endif

            <form action="{{ route('tasks.store') }}" method="POST" x-data="{
            
                title: @js(old('title', '')),
            
                description: @js(old('description', '')),
            
                trackingMode: @js(old('tracking_mode', 'numeric')),
            
                unit: @js(old('unit', '')),
            
                unitType: @js(old('unit_type', 'number')),
            
                targets: @js((object) old('individual_targets', [])),
            
                divisionScope: @js(old('division_scope', '')),
            
                repeatMode: @js(old('repeat_mode', 'once')),
            
                selected: @js(collect(old('assignee_ids', []))->map(fn($i) => (string) $i)->values()),
            
                search: '',
            
                templates: @js(
    $previousTasks->map(
        fn($t) => [
            'id' => $t->id,

            'title' => $t->title,

            'description' => $t->description,

            'tracking_mode' => $t->tracking_mode,

            'unit' => $t->unit,

            'unit_type' => $t->unit_type,

            'repeat_mode' => $t->repeat_mode,

            'division_scope' => $t->division_scope,
        ],
    ),
),
            
                applyTemplate(id) {
            
                    if (!id) return;
            
                    const t = this.templates.find(x => x.id == id);
            
                    if (!t) return;
            
                    this.title = t.title;
            
                    this.description = t.description || '';
            
                    this.trackingMode = t.tracking_mode;
            
                    this.unit = t.unit || '';
            
                    this.unitType = t.unit_type || 'number';
            
                    this.repeatMode = t.repeat_mode;
            
                    this.divisionScope = t.division_scope || '';
            
                },
            
                fmt(v) {
            
                    if (v === '' || v === null || v === undefined) return '';
            
                    const n = Number(v);
            
                    if (isNaN(n)) return '';
            
                    const s = new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 }).format(n);
            
                    if (this.unitType === 'currency') return 'Rp ' + s;
            
                    if (this.unitType === 'percent') return s + '%';
            
                    return s + (this.unit ? ' ' + this.unit : '');
            
                },
            
                toggleAll(ids, checked) {
            
                    this.selected = checked ? [...new Set([...this.selected, ...ids])] : this.selected.filter(id => !ids.includes(id));
            
                },
            
                get filledTargets() {
            
                    return this.selected.filter(id => this.targets[id] !== undefined && this.targets[id] !== '').length;
            
                }
            
            }"
                class="space-y-5 lg:grid lg:grid-cols-12 lg:gap-6 lg:space-y-0">

                @csrf

                {{-- Salin dari Tugas Sebelumnya --}}

                @if ($previousTasks->isNotEmpty())

                    <div
                        class="flex flex-col gap-3 p-4 bg-white border shadow-sm border-slate-200 rounded-2xl sm:p-5 sm:flex-row sm:items-center lg:col-span-12">

                        <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 shrink-0">

                            📋 Salin dari

                        </label>

                        <select @change="applyTemplate($event.target.value)"
                            class="w-full px-3 py-2.5 text-sm bg-white border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">

                            <option value="">— Mulai dari kosong —</option>

                            @foreach ($previousTasks as $prev)
                                <option value="{{ $prev->id }}">{{ $prev->title }}

                                    ({{ $prev->created_at->format('d M Y') }})
                                </option>
                            @endforeach

                        </select>

                    </div>

                @endif

                <div class="space-y-5 lg:col-span-7">
                    {{-- Info Dasar --}}

                    <section class="p-4 bg-white border shadow-sm border-slate-200 rounded-2xl sm:p-5">

                        <h3 class="mb-4 text-sm font-semibold tracking-tight text-slate-800">Info Dasar</h3>

                        <div class="space-y-4">

                            <div>

                                <label class="block mb-1.5 text-sm font-medium text-slate-700">

                                    Judul Tugas <span class="text-red-500">*</span>

                                </label>

                                <input type="text" name="title" x-model="title" required
                                    placeholder="misal: Contact 100 Nasabah Lama"
                                    class="w-full px-3 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">

                            </div>

                            <div>

                                <label class="block mb-1.5 text-sm font-medium text-slate-700">

                                    Deskripsi <span class="font-normal text-slate-400">(opsional)</span>

                                </label>

                                <textarea name="description" x-model="description" rows="2"
                                    class="w-full px-3 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition"></textarea>

                            </div>

                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">

                                <div>

                                    <label class="block mb-1.5 text-sm font-medium text-slate-700">

                                        Cara Tracking <span class="text-red-500">*</span>

                                    </label>

                                    <select name="tracking_mode" x-model="trackingMode"
                                        class="w-full px-3 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">

                                        <option value="numeric">Numeric (angka)</option>

                                        <option value="checklist">Checklist (ya/tidak)</option>

                                    </select>

                                </div>

                                <div x-show="trackingMode === 'numeric'">

                                    <label class="block mb-1.5 text-sm font-medium text-slate-700">Jenis Satuan</label>

                                    <select name="unit_type" x-model="unitType"
                                        class="w-full px-3 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">

                                        <option value="number">Angka biasa</option>

                                        <option value="currency">Rupiah</option>

                                        <option value="percent">Persen (%)</option>

                                    </select>

                                </div>

                                <div x-show="trackingMode === 'numeric' && unitType === 'number'">

                                    <label class="block mb-1.5 text-sm font-medium text-slate-700">

                                        Label Satuan <span class="font-normal text-slate-400">(bebas)</span>

                                    </label>

                                    <input type="text" name="unit" x-model="unit" placeholder="unit, toko, dst"
                                        class="w-full px-3 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">

                                </div>

                            </div>

                        </div>

                    </section>

                    {{-- Mode & Periode --}}

                    <section class="p-4 bg-white border shadow-sm border-slate-200 rounded-2xl sm:p-5">

                        <h3 class="mb-4 text-sm font-semibold tracking-tight text-slate-800">Mode & Periode</h3>

                        <div class="grid grid-cols-1 gap-2 mb-4 sm:grid-cols-3">

                            <label
                                class="flex items-center gap-2 px-3 py-2.5 border-2 rounded-lg cursor-pointer transition"
                                :class="repeatMode === 'once' ? 'border-teal-500 bg-teal-50' :
                                
                                    'border-gray-200 hover:border-gray-300'">

                                <input type="radio" name="repeat_mode" value="once" x-model="repeatMode"
                                    class="text-teal-600">

                                <span class="text-sm font-medium">Sekali</span>

                            </label>

                            <label
                                class="flex items-center gap-2 px-3 py-2.5 border-2 rounded-lg cursor-pointer transition"
                                :class="repeatMode === 'daily_split' ? 'border-teal-500 bg-teal-50' :
                                
                                    'border-gray-200 hover:border-gray-300'">

                                <input type="radio" name="repeat_mode" value="daily_split" x-model="repeatMode"
                                    class="text-teal-600">

                                <span class="text-sm font-medium">Harian</span>

                            </label>

                            <label
                                class="flex items-center gap-2 px-3 py-2.5 border-2 rounded-lg cursor-pointer transition"
                                :class="repeatMode === 'cumulative' ? 'border-teal-500 bg-teal-50' :
                                
                                    'border-gray-200 hover:border-gray-300'">

                                <input type="radio" name="repeat_mode" value="cumulative" x-model="repeatMode"
                                    class="text-teal-600">

                                <span class="text-sm font-medium">Kumulatif</span>

                            </label>

                        </div>

                        <p class="p-2.5 mb-4 text-xs text-slate-500 rounded-lg bg-gray-50"
                            x-show="repeatMode === 'daily_split'" x-cloak>

                            Entry akan digenerate otomatis untuk setiap tanggal yang ditandai sebagai hari kerja dalam

                            rentang periode di bawah.

                        </p>

                        <div class="grid grid-cols-2 gap-3">

                            <div>

                                <label class="block mb-1.5 text-sm font-medium text-slate-700">

                                    Periode Mulai <span class="text-red-500">*</span>

                                </label>

                                <input type="date" name="period_start" value="{{ old('period_start') }}" required
                                    class="w-full px-3 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">

                            </div>

                            <div>

                                <label class="block mb-1.5 text-sm font-medium text-slate-700">

                                    Periode Selesai <span class="text-red-500">*</span>

                                </label>

                                <input type="date" name="period_end" value="{{ old('period_end') }}" required
                                    class="w-full px-3 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">

                            </div>

                        </div>

                    </section>

                    {{-- Divisi & Referensi --}}

                    <section class="p-4 bg-white border shadow-sm border-slate-200 rounded-2xl sm:p-5">

                        <h3 class="mb-4 text-sm font-semibold tracking-tight text-slate-800">Divisi & Referensi <span
                                class="font-normal text-slate-400">(opsional)</span></h3>

                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">

                            <div>

                                <label class="block mb-1.5 text-sm font-medium text-slate-700">Ruang Lingkup
                                    Divisi</label>

                                <select name="division_scope" x-model="divisionScope"
                                    class="w-full px-3 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">

                                    <option value="">— Tidak spesifik —</option>

                                    @foreach ($divisions as $division)
                                        <option value="{{ $division->id }}">{{ $division->name }}</option>
                                    @endforeach

                                </select>

                            </div>

                            <div>

                                <label class="block mb-1.5 text-sm font-medium text-slate-700">Tugas Referensi</label>

                                <select name="reference_task_id"
                                    class="w-full px-3 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">

                                    <option value="">— Tidak ada —</option>

                                    @foreach ($previousTasks as $prev)
                                        <option value="{{ $prev->id }}"
                                            {{ old('reference_task_id') == $prev->id ? 'selected' : '' }}>

                                            {{ $prev->title }}

                                        </option>
                                    @endforeach

                                </select>

                            </div>

                        </div>

                    </section>

                </div>

                <div class="space-y-5 lg:col-span-5 lg:row-span-3">
                    {{-- Assignee --}}

                    <section
                        class="p-4 bg-white border shadow-sm border-slate-200 rounded-2xl sm:p-5 lg:sticky lg:top-6">

                        <div class="flex items-center justify-between mb-1">

                            <h3 class="text-sm font-semibold tracking-tight text-slate-800">

                                Assign Ke <span class="text-red-500">*</span>

                            </h3>

                            <span class="text-xs font-medium text-teal-600 bg-teal-50 px-2 py-0.5 rounded-full"
                                x-text="selected.length + ' dipilih'"></span>

                        </div>

                        <p class="mb-3 text-xs text-slate-400">

                            Centang orang, lalu isi target masing-masing di sebelah namanya (opsional, bisa beda-beda
                            per

                            orang).

                        </p>

                        <div class="flex flex-col gap-2 mb-2 sm:flex-row sm:items-center">

                            <input type="text" x-model="search" placeholder="Cari nama..."
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg sm:flex-1 focus:outline-none focus:ring-2 focus:ring-teal-500">

                            <label
                                class="flex items-center gap-2 px-3 py-2 text-sm border cursor-pointer border-slate-200 rounded-xl bg-gray-50 shrink-0">

                                <input type="checkbox"
                                    @change="toggleAll(@js($eligibleAssignees->pluck('id')->map(fn($i) => (string) $i)->values()), $event.target.checked)"
                                    class="w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500">

                                <span class="font-medium text-gray-600">Pilih Semua</span>

                            </label>

                        </div>

                        <div
                            class="overflow-y-auto border border-gray-200 divide-y divide-gray-100 rounded-lg max-h-80">

                            @forelse ($eligibleAssignees as $assignee)
                                <div x-show="@js(strtolower($assignee->name)).includes(search.toLowerCase())"
                                    class="px-3 py-2.5">

                                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">

                                        <label class="flex items-center flex-1 min-w-0 gap-3 cursor-pointer">

                                            <input type="checkbox" name="assignee_ids[]" value="{{ $assignee->id }}"
                                                x-model="selected"
                                                class="w-4 h-4 text-teal-600 border-gray-300 rounded shrink-0 focus:ring-teal-500">

                                            <div class="min-w-0">

                                                <p class="text-sm font-medium truncate text-slate-800">

                                                    {{ $assignee->name }}</p>

                                                @if ($assignee->getRoleNames()->count() > 1)
                                                    <p class="text-xs truncate text-slate-400">

                                                        {{ $assignee->division?->name }} ·

                                                        {{ $assignee->getRoleNames()[1] }}

                                                    </p>
                                                @endif

                                            </div>

                                        </label>

                                        <div x-show="selected.includes('{{ $assignee->id }}')" x-transition x-cloak
                                            class="flex items-center gap-2 sm:ml-3 sm:shrink-0">

                                            <div class="relative">

                                                <span x-show="unitType === 'currency'"
                                                    class="absolute -translate-y-1/2 left-2.5 top-1/2 text-xs text-slate-400">Rp</span>

                                                <input type="number" step="0.01" min="0"
                                                    name="individual_targets[{{ $assignee->id }}]"
                                                    x-model="targets[{{ $assignee->id }}]"
                                                    :max="unitType === 'percent' ? 100 : null"
                                                    :class="unitType === 'currency' ? 'pl-8' : 'pl-3'"
                                                    placeholder="Target"
                                                    class="w-28 sm:w-32 pr-2 py-1.5 text-sm border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition">

                                                <span x-show="unitType === 'percent'"
                                                    class="absolute -translate-y-1/2 right-2.5 top-1/2 text-xs text-slate-400">%</span>

                                            </div>

                                        </div>

                                    </div>

                                    <p class="mt-1 text-xs font-medium text-teal-600 sm:text-right"
                                        x-show="selected.includes('{{ $assignee->id }}') && targets[{{ $assignee->id }}]"
                                        x-cloak x-text="'Target: ' + fmt(targets[{{ $assignee->id }}])"></p>

                                </div>

                            @empty

                                <p class="py-6 text-sm text-center text-slate-400">Tidak ada orang yang bisa Anda
                                    assign.

                                </p>
                            @endforelse

                        </div>

                        <p class="mt-2 text-xs text-slate-400" x-show="selected.length > 0" x-cloak>

                            <span x-text="filledTargets"></span> dari <span x-text="selected.length"></span> orang
                            sudah

                            diberi target.

                            Tanpa target, nilai berapa pun otomatis dianggap tercapai.

                        </p>

                    </section>


                    <div class="flex items-start gap-2 p-3 text-xs text-blue-700 rounded-lg bg-blue-50">

                        <span>ℹ️</span>

                        <span>Bobot KPI (%) diatur di halaman terpisah setelah tugas ini dibuat, karena totalnya harus

                            mempertimbangkan semua tugas aktif orang tersebut supaya pas 100%.</span>

                    </div>

                </div>

                <div
                    class="sticky bottom-0 z-20 flex justify-end gap-3 px-4 py-3 -mx-4 border-t border-slate-200 bg-white/95 backdrop-blur sm:static sm:bg-transparent sm:border-0 sm:backdrop-blur-none sm:p-0 sm:-mx-0 lg:col-span-12">

                    <a href="{{ route('dashboard') }}"
                        class="px-5 py-2.5 text-sm font-medium text-slate-700 transition bg-slate-100 rounded-xl hover:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-300 focus:ring-offset-2">

                        Batal

                    </a>

                    <button type="submit"
                        class="px-5 py-2.5 text-sm font-semibold text-white transition bg-teal-600 rounded-xl hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 shadow-sm">

                        Simpan & Lanjut Atur Bobot

                    </button>

                </div>

            </form>

        </div>

    </div>

</x-app-layout>
