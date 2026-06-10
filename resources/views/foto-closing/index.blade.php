<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">Foto Closing Cabang</h2>
                <p class="mt-0.5 text-sm text-gray-500">
                    Dokumentasi closing karyawan —
                    {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}
                </p>
            </div>
            <div class="flex items-center gap-2 px-3 py-1.5 bg-teal-50 border border-teal-200 rounded-lg">
                <div class="w-2 h-2 bg-teal-500 rounded-full"></div>
                <span class="text-xs font-medium text-teal-700">
                    {{ collect($grouped)->sum(fn($b) => count($b['users'])) }} karyawan ·
                    {{ count($grouped) }} cabang
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="mx-auto space-y-5 max-w-7xl sm:px-6 lg:px-8">

            {{-- ===================== FILTER BAR ===================== --}}
            <div class="p-4 bg-white border border-gray-100 shadow-sm rounded-xl">
                <form method="GET" id="filterForm" class="flex flex-wrap items-end gap-3">

                    {{-- Tanggal --}}
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-medium tracking-wide text-gray-500 uppercase">Tanggal</label>
                        <input type="date" name="tanggal" id="filterTanggal" value="{{ $tanggal }}"
                            max="{{ now()->toDateString() }}"
                            class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                    </div>

                    {{-- Cabang --}}
                    <div class="flex flex-col gap-1 min-w-[200px]">
                        <label class="text-xs font-medium tracking-wide text-gray-500 uppercase">Cabang</label>
                        <select name="branch_id" id="filterBranch"
                            class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                            <option value="">Semua Cabang</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}" {{ $branchId == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </form>
            </div>

            {{-- ===================== SKELETON ===================== --}}
            <div id="skeletonArea" class="hidden space-y-4">
                @for ($i = 0; $i < 3; $i++)
                    <div class="overflow-hidden bg-white border border-gray-100 shadow-sm rounded-xl animate-pulse">
                        {{-- Branch header skeleton --}}
                        <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-100">
                            <div class="w-8 h-8 bg-gray-200 rounded-lg"></div>
                            <div class="w-40 h-4 bg-gray-200 rounded"></div>
                        </div>
                        {{-- User row skeleton --}}
                        <div class="p-5 space-y-5">
                            <div class="flex gap-4">
                                <div class="w-24 h-24 bg-gray-200 rounded-xl"></div>
                                <div class="w-24 h-24 bg-gray-200 rounded-xl"></div>
                                <div class="w-24 h-24 bg-gray-200 rounded-xl"></div>
                                <div class="w-24 h-24 bg-gray-200 rounded-xl"></div>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>

            {{-- ===================== KONTEN UTAMA ===================== --}}
            <div id="mainContent">
                @if (count($grouped) > 0)
                    <div class="space-y-4">
                        @foreach ($grouped as $branchName => $branchData)
                            <div class="overflow-hidden bg-white border border-gray-100 shadow-sm rounded-xl">

                                {{-- Branch Header --}}
                                <div
                                    class="px-5 py-3.5 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex items-center justify-center flex-shrink-0 w-8 h-8 bg-teal-100 rounded-lg">
                                            <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-800">{{ $branchName }}</p>
                                            <p class="text-xs text-gray-400">
                                                {{ count($branchData['users']) }}
                                                {{ count($branchData['users']) > 1 ? 'karyawan' : 'karyawan' }}
                                                melakukan closing
                                            </p>
                                        </div>
                                    </div>
                                    {{-- Badge jumlah foto --}}
                                    @php
                                        $totalFoto = collect($branchData['users'])->sum(
                                            fn($u) => collect($u['fotos'])->filter()->count(),
                                        );
                                    @endphp
                                    <span
                                        class="px-2.5 py-1 text-xs font-medium bg-teal-50 text-teal-600 rounded-full border border-teal-100">
                                        {{ $totalFoto }} foto
                                    </span>
                                </div>

                                {{-- Users dalam cabang ini --}}
                                <div class="divide-y divide-gray-50">
                                    @foreach ($branchData['users'] as $userId => $userData)
                                        <div class="p-5">

                                            {{-- Info karyawan --}}
                                            <div class="flex items-center gap-2.5 mb-4">
                                                {{-- Avatar inisial --}}
                                                <div
                                                    class="flex items-center justify-center flex-shrink-0 bg-indigo-100 rounded-full w-7 h-7">
                                                    <span class="text-xs font-bold text-indigo-600">
                                                        {{ collect(explode(' ', $userData['user']->name))->take(2)->map(fn($w) => strtoupper($w[0] ?? ''))->join('') }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-semibold leading-tight text-gray-700">
                                                        {{ $userData['user']->name }}
                                                    </p>
                                                    <p class="text-xs text-gray-400">
                                                        Check Out {{ $userData['jam'] }}
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Grid 4 foto kategori --}}
                                            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                                                @foreach ($kategoriOrder as $kat)
                                                    @php
                                                        $fotoData = $userData['fotos'][$kat] ?? null;
                                                        $label = $kategoriLabel[$kat] ?? $kat;
                                                        $color = $kategoriColor[$kat] ?? 'bg-gray-100 text-gray-600';
                                                    @endphp

                                                    <div class="relative group">
                                                        {{-- Badge kategori --}}
                                                        <div class="absolute z-10 top-2 left-2">
                                                            <span
                                                                class="px-2 py-0.5 text-xs font-semibold rounded-full shadow-sm {{ $color }}">
                                                                {{ $label }}
                                                            </span>
                                                        </div>

                                                        @if ($fotoData)
                                                            {{-- Ada foto --}}
                                                            <div class="overflow-hidden transition-all duration-200 bg-gray-100 border-2 border-transparent cursor-pointer aspect-square rounded-xl group-hover:border-teal-400"
                                                                onclick="openClosingModal({{ json_encode([
                                                                    'foto_url' => $fotoData['foto_url'],
                                                                    'nama' => $userData['user']->name,
                                                                    'cabang' => $branchName,
                                                                    'jam' => $userData['jam'],
                                                                    'label' => $fotoData['label'],
                                                                    'color' => $fotoData['color'],
                                                                    'tanggal' => \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y'),
                                                                ]) }})">
                                                                <img src="{{ $fotoData['foto_url'] }}"
                                                                    alt="{{ $label }}"
                                                                    class="object-cover w-full h-full transition-transform duration-300 group-hover:scale-105"
                                                                    loading="lazy"
                                                                    onerror="this.closest('.aspect-square').innerHTML='<div class=\'flex flex-col items-center justify-center h-full text-gray-300 gap-1\'><svg class=\'w-6 h-6\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\'/></svg><p class=\'text-xs\'>Error</p></div>'">
                                                            </div>
                                                        @else
                                                            {{-- Tidak ada foto --}}
                                                            <div
                                                                class="aspect-square rounded-xl bg-gray-50 border-2 border-dashed border-gray-200 flex flex-col items-center justify-center gap-1.5">
                                                                <svg class="w-6 h-6 text-gray-300" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="1.5"
                                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                                </svg>
                                                                <p class="text-xs font-medium text-gray-300">Tidak ada
                                                                    foto</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>

                                        </div>
                                    @endforeach
                                </div>

                            </div>
                        @endforeach
                    </div>
                @else
                    {{-- Empty State --}}
                    <div
                        class="flex flex-col items-center justify-center py-20 bg-white border border-gray-100 rounded-xl">
                        <div class="flex items-center justify-center w-16 h-16 mb-4 bg-gray-100 rounded-full">
                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-gray-400">Tidak ada data closing</p>
                        <p class="mt-1 text-xs text-gray-300">
                            Belum ada foto closing pada
                            {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}
                        </p>
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- ===================== MODAL PREVIEW ===================== --}}
    <div id="closingModal"
        class="fixed inset-0 z-50 items-center justify-center hidden p-4 bg-black/80 backdrop-blur-sm"
        onclick="if(event.target===this) closeClosingModal()">
        <div class="relative w-full max-w-sm overflow-hidden bg-white shadow-2xl rounded-2xl"
            onclick="event.stopPropagation()">

            {{-- Header --}}
            <div class="flex items-center justify-between px-4 py-3 bg-white border-b border-gray-100">
                <div class="flex items-center min-w-0 gap-3">
                    <div class="flex items-center justify-center flex-shrink-0 w-8 h-8 bg-indigo-100 rounded-full">
                        <span id="closingModalInisial" class="text-xs font-bold text-indigo-600"></span>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-gray-800 truncate" id="closingModalNama"></p>
                        <p class="text-xs text-gray-400 truncate" id="closingModalCabang"></p>
                    </div>
                </div>
                <div class="flex items-center flex-shrink-0 gap-1 ml-2">
                    {{-- Tombol Download --}}
                    <button id="closingBtnDownload" onclick="downloadClosingFoto()" title="Download foto"
                        class="p-1.5 text-gray-400 hover:text-teal-600 hover:bg-teal-50 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                    </button>
                    {{-- Tombol Tutup --}}
                    <button onclick="closeClosingModal()"
                        class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Foto --}}
            <div class="relative bg-gray-950">
                <div id="closingModalLoader" class="absolute inset-0 flex items-center justify-center bg-gray-950">
                    <svg class="w-8 h-8 text-gray-600 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4" />
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                    </svg>
                </div>
                <img id="closingModalFoto" src="" alt=""
                    class="w-full object-contain max-h-[65vh] opacity-0 transition-opacity duration-300"
                    onload="this.classList.remove('opacity-0'); document.getElementById('closingModalLoader').classList.add('hidden')"
                    onerror="document.getElementById('closingModalLoader').innerHTML='<p class=\'text-xs text-gray-500\'>Foto tidak tersedia</p>'">
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-between px-4 py-3 bg-white">
                <div class="flex items-center gap-2">
                    <span id="closingModalBadge" class="px-2.5 py-0.5 text-xs font-semibold rounded-full"></span>
                    <span class="text-xs text-gray-400" id="closingModalTanggal"></span>
                </div>
                <div class="flex items-center gap-1.5 flex-shrink-0">
                    <svg class="w-3.5 h-3.5 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm font-bold text-teal-600" id="closingModalJam"></span>
                </div>
            </div>

        </div>
    </div>

    <script>
        // =====================================================
        // AUTO-SUBMIT FILTER
        // =====================================================
        document.getElementById('filterTanggal').addEventListener('change', () => submitFilter());
        document.getElementById('filterBranch').addEventListener('change', () => submitFilter());

        function submitFilter() {
            showSkeleton();
            document.getElementById('filterForm').submit();
        }

        function showSkeleton() {
            document.getElementById('mainContent').style.opacity = '0.3';
            document.getElementById('skeletonArea').classList.remove('hidden');
        }

        // =====================================================
        // MODAL PREVIEW
        // =====================================================
        let currentClosingUrl = '';
        let currentClosingNama = '';

        function openClosingModal(data) {
            currentClosingUrl = data.foto_url;
            currentClosingNama = data.nama;

            // Reset loader
            const loader = document.getElementById('closingModalLoader');
            loader.classList.remove('hidden');
            loader.innerHTML = `
                <svg class="w-8 h-8 text-gray-600 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>`;

            // Set foto
            const imgEl = document.getElementById('closingModalFoto');
            imgEl.classList.add('opacity-0');
            imgEl.src = data.foto_url;

            // Set inisial avatar
            const inisial = data.nama
                .split(' ')
                .slice(0, 2)
                .map(w => w[0] ?? '')
                .join('')
                .toUpperCase();
            document.getElementById('closingModalInisial').textContent = inisial;

            // Set teks
            document.getElementById('closingModalNama').textContent = data.nama;
            document.getElementById('closingModalCabang').textContent = data.cabang;
            document.getElementById('closingModalTanggal').textContent = data.tanggal;
            document.getElementById('closingModalJam').textContent = data.jam;

            // Badge
            const badge = document.getElementById('closingModalBadge');
            badge.textContent = data.label;
            badge.className = 'px-2.5 py-0.5 text-xs font-semibold rounded-full ' + data.color;

            const modal = document.getElementById('closingModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeClosingModal() {
            const modal = document.getElementById('closingModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.getElementById('closingModalFoto').src = '';
            currentClosingUrl = '';
            currentClosingNama = '';
        }

        // =====================================================
        // DOWNLOAD FOTO
        // =====================================================
        async function downloadClosingFoto() {
            if (!currentClosingUrl) return;

            const btn = document.getElementById('closingBtnDownload');
            btn.classList.add('opacity-50', 'pointer-events-none');

            try {
                const response = await fetch(currentClosingUrl);
                const blob = await response.blob();
                const url = URL.createObjectURL(blob);

                const ext = currentClosingUrl.split('.').pop().split('?')[0] || 'jpg';
                const nama = currentClosingNama.replace(/\s+/g, '_').toLowerCase();
                const filename = `closing_${nama}_${Date.now()}.${ext}`;

                const a = document.createElement('a');
                a.href = url;
                a.download = filename;
                a.click();
                URL.revokeObjectURL(url);
            } catch (e) {
                window.open(currentClosingUrl, '_blank');
            } finally {
                btn.classList.remove('opacity-50', 'pointer-events-none');
            }
        }

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') closeClosingModal();
        });
    </script>

</x-app-layout>
