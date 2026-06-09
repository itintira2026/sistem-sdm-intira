<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">Galeri Foto Presensi</h2>
                <p class="mt-0.5 text-sm text-gray-500">
                    Foto presensi karyawan —
                    {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}
                </p>
            </div>
            <div class="flex items-center gap-2 px-3 py-1.5 bg-teal-50 border border-teal-200 rounded-lg">
                <div class="w-2 h-2 bg-teal-500 rounded-full"></div>
                <span class="text-xs font-medium text-teal-700">
                    {{ $fotos->count() }} foto ditemukan
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
                    <div class="flex flex-col gap-1 min-w-[180px]">
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

                    {{-- Search --}}
                    <div class="flex flex-col gap-1 flex-1 min-w-[200px]">
                        <label class="text-xs font-medium tracking-wide text-gray-500 uppercase">Cari Karyawan</label>
                        <input type="text" name="search" id="filterSearch" value="{{ $search }}"
                            placeholder="Nama atau email..."
                            class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                    </div>

                    {{-- Hidden tipe (dihandle tombol toggle di bawah) --}}
                    <input type="hidden" name="tipe" id="filterTipe" value="{{ $tipeFilter }}">

                </form>

                {{-- Tipe Filter Toggle --}}
                <div class="flex flex-wrap gap-2 mt-3">
                    @php
                        $tipes = [
                            'CHECK_IN' => ['label' => 'Check In', 'color' => 'green'],
                            'ISTIRAHAT_OUT' => ['label' => 'Istirahat Out', 'color' => 'yellow'],
                            'ISTIRAHAT_IN' => ['label' => 'Istirahat In', 'color' => 'blue'],
                            'CHECK_OUT' => ['label' => 'Check Out', 'color' => 'red'],
                            'OUTFIT' => ['label' => 'Outfit', 'color' => 'purple'],
                        ];
                    @endphp

                    @foreach ($tipes as $key => $tipe)
                        @php
                            $isActive = $tipeFilter === $key;
                            $colorMap = [
                                'green' => $isActive
                                    ? 'bg-green-600 text-white border-green-600'
                                    : 'bg-white text-green-700 border-green-300 hover:bg-green-50',
                                'yellow' => $isActive
                                    ? 'bg-yellow-500 text-white border-yellow-500'
                                    : 'bg-white text-yellow-700 border-yellow-300 hover:bg-yellow-50',
                                'blue' => $isActive
                                    ? 'bg-blue-600 text-white border-blue-600'
                                    : 'bg-white text-blue-700 border-blue-300 hover:bg-blue-50',
                                'red' => $isActive
                                    ? 'bg-red-600 text-white border-red-600'
                                    : 'bg-white text-red-700 border-red-300 hover:bg-red-50',
                                'purple' => $isActive
                                    ? 'bg-purple-600 text-white border-purple-600'
                                    : 'bg-white text-purple-700 border-purple-300 hover:bg-purple-50',
                            ];
                        @endphp
                        <button type="button" onclick="setTipe('{{ $key }}')"
                            class="px-3 py-1.5 text-xs font-medium border rounded-full transition-all duration-150 {{ $colorMap[$tipe['color']] }}">
                            {{ $tipe['label'] }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- ===================== GRID FOTO ===================== --}}
            <div id="fotoGrid">

                {{-- SKELETON (ditampilkan saat loading) --}}
                <div id="skeletonGrid"
                    class="grid hidden grid-cols-3 gap-3 px-4 md:px-0 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6">
                    @for ($i = 0; $i < 18; $i++)
                        <div class="overflow-hidden bg-white border border-gray-100 shadow-sm rounded-xl animate-pulse">
                            <div class="bg-gray-200 aspect-square"></div>
                            <div class="p-2 space-y-1.5">
                                <div class="w-3/4 h-3 bg-gray-200 rounded"></div>
                                <div class="h-2.5 bg-gray-100 rounded w-1/2"></div>
                                <div class="h-2.5 bg-gray-100 rounded w-2/3"></div>
                            </div>
                        </div>
                    @endfor
                </div>

                {{-- HASIL FOTO --}}
                <div id="fotoContent">
                    @if ($fotos->count() > 0)
                        <div class="grid grid-cols-3 gap-3 px-4 md:px-0 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6">
                            @foreach ($fotos as $foto)
                                <div class="bg-white rounded-xl overflow-hidden shadow-sm border border-gray-100 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 cursor-pointer group"
                                    onclick="openFotoModal({{ json_encode($foto) }})">

                                    {{-- Foto --}}
                                    <div class="relative overflow-hidden bg-gray-100 aspect-square">
                                        <img src="{{ $foto['foto_url'] }}" alt="{{ $foto['nama'] }}"
                                            class="object-cover w-full h-full transition-transform duration-300 group-hover:scale-105"
                                            loading="lazy"
                                            onerror="this.closest('.aspect-square').innerHTML='<div class=\'flex items-center justify-center h-full text-gray-300\'><svg class=\'w-8 h-8\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\'/></svg></div>'">
                                    </div>

                                    {{-- Info --}}
                                    <div class="p-2">
                                        <p class="text-xs font-semibold leading-tight text-gray-800 truncate">
                                            {{ $foto['nama'] }}
                                        </p>
                                        <p class="text-xs text-gray-400 truncate mt-0.5">
                                            {{ $foto['cabang'] }}
                                        </p>
                                        <p class="text-xs font-medium text-teal-600 mt-0.5">
                                            {{ $foto['jam'] }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        {{-- Empty State --}}
                        <div
                            class="flex flex-col items-center justify-center py-20 bg-white border border-gray-100 rounded-xl">
                            <div class="flex items-center justify-center w-16 h-16 mb-4 bg-gray-100 rounded-full">
                                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <p class="text-sm font-medium text-gray-400">Tidak ada foto</p>
                            <p class="mt-1 text-xs text-gray-300">
                                Belum ada foto
                                {{ collect($tipes)->get($tipeFilter)['label'] ?? $tipeFilter }}
                                pada tanggal ini
                            </p>
                        </div>
                    @endif
                </div>

            </div>

        </div>
    </div>

    {{-- ===================== MODAL PREVIEW ===================== --}}
    <div id="fotoModal" class="fixed inset-0 z-50 items-center justify-center hidden p-4 bg-black/80 backdrop-blur-sm"
        onclick="if(event.target===this) closeFotoModal()">
        <div class="relative w-full max-w-sm overflow-hidden bg-white shadow-2xl rounded-2xl"
            onclick="event.stopPropagation()">

            {{-- Header modal --}}
            <div class="flex items-center justify-between px-4 py-3 bg-white border-b border-gray-100">
                <div class="flex items-center min-w-0 gap-3">
                    {{-- Avatar inisial --}}
                    <div id="modalAvatar"
                        class="flex items-center justify-center flex-shrink-0 w-8 h-8 bg-teal-100 rounded-full">
                        <span id="modalInisial" class="text-xs font-bold text-teal-600"></span>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-gray-800 truncate" id="modalNama"></p>
                        <p class="text-xs text-gray-400 truncate" id="modalCabang"></p>
                    </div>
                </div>
                <div class="flex items-center flex-shrink-0 gap-1 ml-2">
                    {{-- Tombol Download --}}
                    <button id="btnDownload" onclick="downloadFoto()" title="Download foto"
                        class="p-1.5 text-gray-400 hover:text-teal-600 hover:bg-teal-50 rounded-lg transition group">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                    </button>
                    {{-- Tombol Tutup --}}
                    <button onclick="closeFotoModal()"
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
                {{-- Loading spinner saat foto belum load --}}
                <div id="modalFotoLoader" class="absolute inset-0 flex items-center justify-center bg-gray-950">
                    <svg class="w-8 h-8 text-gray-600 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4" />
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                    </svg>
                </div>
                <img id="modalFoto" src="" alt=""
                    class="w-full object-contain max-h-[65vh] opacity-0 transition-opacity duration-300"
                    onload="this.classList.remove('opacity-0'); document.getElementById('modalFotoLoader').classList.add('hidden')"
                    onerror="document.getElementById('modalFotoLoader').innerHTML='<p class=\'text-xs text-gray-500\'>Foto tidak tersedia</p>'">
            </div>

            {{-- Footer info --}}
            <div class="flex items-center justify-between px-4 py-3 bg-white">
                <div class="flex flex-wrap items-center gap-2">
                    <span id="modalBadge" class="px-2.5 py-0.5 text-xs font-semibold rounded-full"></span>
                    <span class="text-xs text-gray-400" id="modalTanggal"></span>
                </div>
                <div class="flex items-center gap-1.5 flex-shrink-0">
                    <svg class="w-3.5 h-3.5 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm font-bold text-teal-600" id="modalJam"></span>
                </div>
            </div>
        </div>
    </div>

    <script>
        // =====================================================
        // AUTO-SUBMIT FILTER
        // =====================================================
        let searchDebounce;

        document.getElementById('filterTanggal').addEventListener('change', () => submitFilter());
        document.getElementById('filterBranch').addEventListener('change', () => submitFilter());
        document.getElementById('filterSearch').addEventListener('input', function() {
            clearTimeout(searchDebounce);
            searchDebounce = setTimeout(() => submitFilter(), 400);
        });

        function setTipe(tipe) {
            document.getElementById('filterTipe').value = tipe;
            submitFilter();
        }

        function submitFilter() {
            showSkeleton();
            document.getElementById('filterForm').submit();
        }

        function showSkeleton() {
            document.getElementById('fotoContent').style.opacity = '0.3';
            document.getElementById('skeletonGrid').classList.remove('hidden');
        }

        // =====================================================
        // MODAL PREVIEW
        // =====================================================
        const badgeColorMap = {
            'CHECK_IN': 'bg-green-100 text-green-700',
            'ISTIRAHAT_OUT': 'bg-yellow-100 text-yellow-700',
            'ISTIRAHAT_IN': 'bg-blue-100 text-blue-700',
            'CHECK_OUT': 'bg-red-100 text-red-700',
            'OUTFIT': 'bg-purple-100 text-purple-700',
        };

        let currentFotoUrl = '';
        let currentFotoNama = '';

        function openFotoModal(foto) {
            currentFotoUrl = foto.foto_url;
            currentFotoNama = foto.nama;

            // Reset loader
            const loader = document.getElementById('modalFotoLoader');
            loader.classList.remove('hidden');
            loader.innerHTML = `
                <svg class="w-8 h-8 text-gray-600 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>`;

            // Set foto (opacity 0 dulu, onload akan munculkan)
            const imgEl = document.getElementById('modalFoto');
            imgEl.classList.add('opacity-0');
            imgEl.src = foto.foto_url;

            // Set inisial avatar
            const inisial = foto.nama
                .split(' ')
                .slice(0, 2)
                .map(w => w[0] ?? '')
                .join('')
                .toUpperCase();
            document.getElementById('modalInisial').textContent = inisial;

            // Set teks
            document.getElementById('modalNama').textContent = foto.nama;
            document.getElementById('modalCabang').textContent = foto.cabang;
            document.getElementById('modalTanggal').textContent = foto.tanggal;
            document.getElementById('modalJam').textContent = foto.jam;

            // Badge warna berdasarkan status
            const badge = document.getElementById('modalBadge');
            badge.textContent = foto.tipe_label;
            badge.className = 'px-2.5 py-0.5 text-xs font-semibold rounded-full ' +
                (badgeColorMap[foto.status] ?? 'bg-gray-100 text-gray-600');

            const modal = document.getElementById('fotoModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeFotoModal() {
            const modal = document.getElementById('fotoModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.getElementById('modalFoto').src = '';
            currentFotoUrl = '';
            currentFotoNama = '';
        }

        // =====================================================
        // DOWNLOAD FOTO
        // =====================================================
        async function downloadFoto() {
            if (!currentFotoUrl) return;

            const btn = document.getElementById('btnDownload');
            btn.classList.add('opacity-50', 'pointer-events-none');

            try {
                const response = await fetch(currentFotoUrl);
                const blob = await response.blob();
                const url = URL.createObjectURL(blob);

                // Ambil ekstensi dari URL atau default ke jpg
                const ext = currentFotoUrl.split('.').pop().split('?')[0] || 'jpg';
                const nama = currentFotoNama.replace(/\s+/g, '_').toLowerCase();
                const filename = `presensi_${nama}_${Date.now()}.${ext}`;

                const a = document.createElement('a');
                a.href = url;
                a.download = filename;
                a.click();
                URL.revokeObjectURL(url);
            } catch (e) {
                // Fallback: buka di tab baru
                window.open(currentFotoUrl, '_blank');
            } finally {
                btn.classList.remove('opacity-50', 'pointer-events-none');
            }
        }

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') closeFotoModal();
        });
    </script>

</x-app-layout>
