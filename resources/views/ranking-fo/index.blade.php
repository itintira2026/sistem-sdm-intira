<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    🏆 Ranking FO
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ \Carbon\Carbon::parse($dateFrom)->translatedFormat('d F Y') }}
                    @if ($dateFrom !== $dateTo)
                        — {{ \Carbon\Carbon::parse($dateTo)->translatedFormat('d F Y') }}
                    @endif
                </p>
            </div>

            {{-- Cache timestamp + tombol perbarui --}}
            <div class="flex items-center gap-3">
                @if ($lastUpdated)
                    <p class="text-xs text-gray-400">
                        🕐 Data per <span class="font-medium">{{ $lastUpdated }}</span>
                    </p>
                @endif

                <form method="POST" action="{{ route('ranking-fo.refresh') }}">
                    @csrf
                    @foreach (request()->except('_token') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <button type="submit"
                        class="flex items-center gap-1.5 px-3 py-1.5 text-xs text-teal-700 border border-teal-300 rounded-lg hover:bg-teal-50 transition">
                        🔄 Perbarui
                    </button>
                </form>

                <button onclick="openExportModal()"
                    class="flex items-center gap-2 px-4 py-2 text-sm text-white transition bg-teal-600 rounded-lg hover:bg-teal-700">
                    📥 Export
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">

            {{-- Alert --}}
            @if (session('success'))
                <div class="p-4 text-green-700 bg-green-100 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            {{-- ============================================================ --}}
            {{-- FILTER                                                         --}}
            {{-- ============================================================ --}}
            <div class="p-4 bg-white rounded-lg shadow-sm">
                <form id="filterForm" method="GET" action="{{ route('ranking-fo.index') }}">
                    <div class="flex flex-wrap items-end gap-3">

                        <div>
                            <label class="block mb-1 text-xs font-medium text-gray-500">Dari</label>
                            <input type="date" name="date_from" value="{{ $dateFrom }}" onchange="submitFilter()"
                                class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
                        </div>

                        <div>
                            <label class="block mb-1 text-xs font-medium text-gray-500">Sampai</label>
                            <input type="date" name="date_to" value="{{ $dateTo }}" onchange="submitFilter()"
                                class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
                        </div>

                        <div>
                            <label class="block mb-1 text-xs font-medium text-gray-500">Cabang</label>
                            <select name="branch_id" onchange="submitFilter()"
                                class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
                                <option value="all" {{ $branchId === 'all' ? 'selected' : '' }}>
                                    🌐 Semua
                                </option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}"
                                        {{ $branchId == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block mb-1 text-xs font-medium text-gray-500">Urutkan</label>
                            <select name="sort_by" onchange="submitFilter()"
                                class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
                                <option value="omset" {{ $sortBy === 'omset' ? 'selected' : '' }}>💰 Omset</option>
                                <option value="revenue" {{ $sortBy === 'revenue' ? 'selected' : '' }}>📈 Revenue
                                </option>
                                <option value="akad" {{ $sortBy === 'akad' ? 'selected' : '' }}>🤝 Akad</option>
                            </select>
                        </div>

                        <div>
                            <label class="block mb-1 text-xs font-medium text-gray-500">Mode</label>
                            <select name="mode" onchange="submitFilter()"
                                class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
                                <option value="validated" {{ $mode === 'validated' ? 'selected' : '' }}>
                                    ✅ Validated
                                </option>
                                <option value="all" {{ $mode === 'all' ? 'selected' : '' }}>
                                    📊 Termasuk Pending
                                </option>
                            </select>
                        </div>

                        {{-- Mode info --}}
                        @if ($mode === 'all')
                            <div
                                class="px-3 py-2 text-xs text-orange-700 border border-orange-200 rounded-lg bg-orange-50">
                                ⚠️ Mode ini termasuk laporan pending yang belum divalidasi
                            </div>
                        @endif

                        {{-- Loading indicator --}}
                        <div id="filterLoading" class="items-center hidden gap-2 pb-1 text-sm text-gray-400">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                            </svg>
                            Memuat...
                        </div>

                    </div>
                </form>
            </div>

            {{-- ============================================================ --}}
            {{-- SKELETON LOADING (hidden by default)                           --}}
            {{-- ============================================================ --}}
            <div id="skeletonSection" class="hidden space-y-4">
                {{-- Skeleton podium --}}
                <div class="flex items-end justify-center gap-4">
                    @for ($i = 0; $i < 3; $i++)
                        <div class="flex flex-col items-center flex-1 max-w-[200px] animate-pulse">
                            <div class="w-12 h-12 mb-2 bg-gray-200 rounded-full"></div>
                            <div class="w-24 h-3 mb-1 bg-gray-200 rounded"></div>
                            <div class="w-16 h-3 mb-2 bg-gray-200 rounded"></div>
                            <div class="w-full h-20 bg-gray-200 rounded-t-lg"></div>
                        </div>
                    @endfor
                </div>
                {{-- Skeleton tabel --}}
                <div class="p-4 space-y-3 bg-white rounded-lg shadow-sm">
                    @for ($i = 0; $i < 8; $i++)
                        <div class="flex gap-3 animate-pulse">
                            <div class="w-8 h-8 bg-gray-200 rounded"></div>
                            <div class="w-8 h-8 bg-gray-200 rounded-full"></div>
                            <div class="h-8 bg-gray-200 rounded w-36"></div>
                            <div class="h-8 bg-gray-200 rounded w-28"></div>
                            <div class="h-8 bg-gray-200 rounded w-28"></div>
                            <div class="w-16 h-8 bg-gray-200 rounded"></div>
                            <div class="flex-1 h-8 bg-gray-200 rounded"></div>
                        </div>
                    @endfor
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- CONTENT                                                        --}}
            {{-- ============================================================ --}}
            <div id="mainContent">

                {{-- -------------------------------------------------------- --}}
                {{-- TOP 3 PODIUM                                              --}}
                {{-- -------------------------------------------------------- --}}
                @if ($top3->isNotEmpty())
                    <div>
                        <p class="mb-3 text-xs font-semibold tracking-wider text-gray-400 uppercase">
                            🏆 Top 3 —
                            {{ $sortBy === 'omset' ? 'Omset' : ($sortBy === 'revenue' ? 'Revenue' : 'Akad') }}
                            <span class="font-normal text-gray-300 normal-case">
                                (berdasarkan laporan disetujui)
                            </span>
                        </p>

                        @php
                            // Urutan podium: 2nd (kiri), 1st (tengah), 3rd (kanan)
                            $podiumDisplay = [
                                $top3->get(1), // index 1 = rank 2
                                $top3->get(0), // index 0 = rank 1
                                $top3->get(2), // index 2 = rank 3
                            ];
                            $podiumConfig = [
                                0 => [
                                    // kiri = rank 2
                                    'emoji' => '🥈',
                                    'height' => 'h-20',
                                    'bg' => 'bg-gray-50',
                                    'border' => 'border-gray-300',
                                    'text' => 'text-gray-600',
                                    'avatar' => 'bg-gray-100 text-gray-600',
                                ],
                                1 => [
                                    // tengah = rank 1
                                    'emoji' => '🥇',
                                    'height' => 'h-28',
                                    'bg' => 'bg-amber-50',
                                    'border' => 'border-amber-300',
                                    'text' => 'text-amber-700',
                                    'avatar' => 'bg-amber-100 text-amber-700',
                                ],
                                2 => [
                                    // kanan = rank 3
                                    'emoji' => '🥉',
                                    'height' => 'h-16',
                                    'bg' => 'bg-orange-50',
                                    'border' => 'border-orange-200',
                                    'text' => 'text-orange-600',
                                    'avatar' => 'bg-orange-100 text-orange-600',
                                ],
                            ];
                        @endphp

                        <div class="flex items-end justify-center gap-3 md:gap-6">
                            @foreach ($podiumDisplay as $podiumIdx => $r)
                                @php
                                    $cfg = $podiumConfig[$podiumIdx];
                                    $val = match ($sortBy) {
                                        'revenue' => $r?->total_revenue,
                                        'akad' => $r?->total_akad,
                                        default => $r?->total_omset,
                                    };
                                @endphp
                                <div class="flex flex-col items-center flex-1 max-w-[180px]">
                                    @if ($r)
                                        <div
                                            class="flex items-center justify-center w-12 h-12 mb-2 text-sm font-semibold rounded-full border-2
                                        {{ $cfg['avatar'] }} {{ $cfg['border'] }}">
                                            {{ strtoupper(substr($r->user?->name ?? '?', 0, 2)) }}
                                        </div>

                                        <p
                                            class="mb-0.5 text-sm font-semibold text-center text-gray-800 line-clamp-1 w-full">
                                            {{ $r->user?->name ?? '-' }}
                                        </p>

                                        <p class="mb-2 text-[10px] text-center text-gray-400 line-clamp-1 w-full">
                                            {{ $r->user?->branches->first()?->name ?? '-' }}
                                        </p>
                                    @endif

                                    <div
                                        class="flex flex-col items-center justify-start w-full pt-3 border-t-4 rounded-t-lg
                                    {{ $cfg['height'] }} {{ $cfg['bg'] }} {{ $cfg['border'] }}">
                                        <span class="text-xl">{{ $cfg['emoji'] }}</span>
                                        @if ($r && $val !== null)
                                            <p class="mt-1 text-xs font-semibold text-center px-1 {{ $cfg['text'] }}">
                                                @if ($sortBy === 'akad')
                                                    {{ number_format($val, 0, ',', '.') }}
                                                @else
                                                    {{ \App\Helpers\FormatHelper::rupiah($val) }}
                                                @endif
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($mode === 'all')
                    <div
                        class="mt-3 px-4 py-2.5 bg-blue-50 border border-blue-200 rounded-lg text-xs text-blue-700 flex items-start gap-2">
                        <span class="shrink-0">ℹ️</span>
                        <span>
                            Podium berdasarkan laporan <strong>disetujui</strong> saja.
                            Tabel ranking di bawah sudah termasuk laporan pending.
                        </span>
                    </div>
                @endif

                {{-- -------------------------------------------------------- --}}
                {{-- PINNED TOP 3 (halaman 2+)                                 --}}
                {{-- -------------------------------------------------------- --}}
                @if ($rows->currentPage() > 1 && $top3->isNotEmpty())
                    <div class="px-4 py-3 border rounded-lg bg-amber-50 border-amber-200">
                        <p class="mb-2 text-xs font-semibold text-amber-700">📌 Top 3 (pinned)</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($top3 as $i => $r)
                                @php
                                    $val = match ($sortBy) {
                                        'revenue' => $r->total_revenue,
                                        'akad' => $r->total_akad,
                                        default => $r->total_omset,
                                    };
                                    $medals = ['🥇', '🥈', '🥉'];
                                @endphp
                                <div
                                    class="flex items-center gap-2 px-3 py-1.5 bg-white border border-amber-200 rounded-lg text-sm">
                                    <span>{{ $medals[$i] }}</span>
                                    <span class="font-semibold text-gray-800">{{ $r->user?->name }}</span>
                                    <span class="text-xs text-gray-400">
                                        {{ $r->user?->branches->first()?->name ?? '-' }}
                                    </span>
                                    <span class="font-semibold text-amber-700">
                                        @if ($sortBy === 'akad')
                                            {{ number_format($val, 0, ',', '.') }}
                                        @else
                                            {{ \App\Helpers\FormatHelper::rupiah($val) }}
                                        @endif
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- -------------------------------------------------------- --}}
                {{-- TABEL RANKING                                             --}}
                {{-- -------------------------------------------------------- --}}
                <div class="bg-white rounded-lg shadow-sm">
                    @if ($rows->isEmpty())
                        <div class="p-12 text-center text-gray-400">
                            <p class="mb-3 text-4xl">📊</p>
                            <p>Tidak ada data untuk filter ini.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm min-w-max whitespace-nowrap">
                                <thead class="border-b border-gray-200 bg-gray-50">
                                    <tr>
                                        <th
                                            class="w-12 px-4 py-3 text-xs font-semibold text-left text-gray-500 uppercase">
                                            #</th>
                                        <th class="px-4 py-3 text-xs font-semibold text-left text-gray-500 uppercase">
                                            FO</th>
                                        <th class="px-4 py-3 text-xs font-semibold text-left text-gray-500 uppercase">
                                            Cabang</th>
                                        <th class="px-4 py-3 text-xs font-semibold text-right text-gray-500 uppercase">
                                            Omset
                                            @if ($sortBy === 'omset')
                                                <span class="text-teal-500">↓</span>
                                            @endif
                                        </th>
                                        <th class="px-4 py-3 text-xs font-semibold text-right text-gray-500 uppercase">
                                            Revenue
                                            @if ($sortBy === 'revenue')
                                                <span class="text-teal-500">↓</span>
                                            @endif
                                        </th>
                                        <th
                                            class="px-4 py-3 text-xs font-semibold text-center text-gray-500 uppercase">
                                            Akad
                                            @if ($sortBy === 'akad')
                                                <span class="text-teal-500">↓</span>
                                            @endif
                                        </th>
                                        <th
                                            class="px-4 py-3 text-xs font-semibold text-center text-gray-500 uppercase">
                                            Laporan</th>
                                        <th
                                            class="px-4 py-3 text-xs font-semibold text-center text-gray-500 uppercase">
                                            Hari Aktif
                                        </th>
                                        <th
                                            class="px-4 py-3 text-xs font-semibold text-center text-gray-500 uppercase">
                                            Pending</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($rows as $i => $row)
                                        @php
                                            $rank = ($rows->currentPage() - 1) * $rows->perPage() + $i + 1;
                                            $user = $users->get($row->user_id);
                                            $rowBg = $i % 2 === 0 ? 'bg-white' : 'bg-gray-50/50';

                                            // Highlight top 3 di halaman pertama
                                            if ($rows->currentPage() === 1) {
                                                $rowBg = match (true) {
                                                    $rank === 1 => 'bg-amber-50',
                                                    $rank === 2 => 'bg-gray-100',
                                                    $rank === 3 => 'bg-orange-50',
                                                    default => $rowBg,
                                                };
                                            }

                                            $hasPending = $row->laporan_pending > 0;
                                        @endphp
                                        <tr
                                            class="{{ $rowBg }} border-t border-gray-100 hover:brightness-95 transition-colors">

                                            {{-- Rank --}}
                                            <td class="px-4 py-3 text-center">
                                                @if ($rows->currentPage() === 1 && $rank <= 3)
                                                    <span class="text-lg">
                                                        {{ ['🥇', '🥈', '🥉'][$rank - 1] }}
                                                    </span>
                                                @else
                                                    <span class="text-sm font-medium text-gray-400">
                                                        {{ $rank }}
                                                    </span>
                                                @endif
                                            </td>

                                            {{-- FO --}}
                                            <td class="px-4 py-3">
                                                <div class="flex items-center gap-2">
                                                    <div
                                                        class="flex items-center justify-center w-8 h-8 text-xs font-semibold text-teal-700 bg-teal-100 rounded-full shrink-0">
                                                        {{ strtoupper(substr($user?->name ?? '?', 0, 2)) }}
                                                    </div>
                                                    <span class="font-medium text-gray-800">
                                                        {{ $user?->name ?? '-' }}
                                                    </span>
                                                </div>
                                            </td>

                                            {{-- Cabang --}}
                                            <td class="px-4 py-3 text-xs text-gray-500">
                                                {{ $user?->branches->first()?->name ?? '-' }}
                                            </td>

                                            {{-- Omset --}}
                                            <td class="px-4 py-3 text-right">
                                                <span class="font-medium text-gray-800">
                                                    {{ \App\Helpers\FormatHelper::rupiah($row->total_omset) }}
                                                </span>
                                                <br>
                                                <span class="text-[10px] text-gray-400">
                                                    {{ \App\Helpers\FormatHelper::rupiahFull($row->total_omset) }}
                                                </span>
                                            </td>

                                            {{-- Revenue --}}
                                            <td class="px-4 py-3 text-right">
                                                <span class="font-medium text-gray-800">
                                                    {{ \App\Helpers\FormatHelper::rupiah($row->total_revenue) }}
                                                </span>
                                                <br>
                                                <span class="text-[10px] text-gray-400">
                                                    {{ \App\Helpers\FormatHelper::rupiahFull($row->total_revenue) }}
                                                </span>
                                            </td>

                                            {{-- Akad --}}
                                            <td class="px-4 py-3 font-medium text-center text-gray-800">
                                                {{ number_format($row->total_akad, 0, ',', '.') }}
                                            </td>

                                            {{-- Total Laporan --}}
                                            <td class="px-4 py-3 text-center text-gray-600">
                                                {{ $row->total_laporan }}
                                                <span class="text-[10px] text-gray-400 block">laporan</span>
                                            </td>
                                            {{-- Hari Aktif --}}
                                            <td class="px-4 py-3 text-center text-gray-600">
                                                {{ $row->hari_aktif }}
                                                <span class="text-[10px] text-gray-400 block">hari</span>
                                            </td>

                                            {{-- Pending --}}
                                            <td class="px-4 py-3 text-center">
                                                @if ($hasPending)
                                                    <span
                                                        class="inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold text-orange-700 bg-orange-100 rounded-full">
                                                        ⚠️ {{ $row->laporan_pending }}
                                                    </span>
                                                    <span class="text-[10px] text-orange-500 block mt-0.5">
                                                        belum divalidasi
                                                    </span>
                                                @else
                                                    <span class="text-xs text-gray-300">—</span>
                                                @endif
                                            </td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <div class="flex items-center justify-between px-4 py-3 border-t border-gray-100">
                            <p class="text-xs text-gray-400">
                                Menampilkan {{ $rows->firstItem() }}–{{ $rows->lastItem() }}
                                dari {{ $rows->total() }} FO
                            </p>
                            {{ $rows->links() }}
                        </div>
                    @endif
                </div>

            </div>{{-- end #mainContent --}}

        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- MODAL EXPORT                                                   --}}
    {{-- ============================================================ --}}
    <div id="exportModal" class="fixed inset-0 z-50 items-center justify-center hidden bg-black bg-opacity-50">
        <div class="w-full max-w-md mx-4 bg-white rounded-lg shadow-xl">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">📥 Export Ranking FO</h3>
                <button onclick="closeExportModal()" class="text-gray-400 hover:text-gray-600">✕</button>
            </div>
            <form method="GET" action="{{ route('ranking-fo.export') }}">
                <input type="hidden" name="date_from" value="{{ $dateFrom }}">
                <input type="hidden" name="date_to" value="{{ $dateTo }}">
                <input type="hidden" name="branch_id" value="{{ $branchId }}">
                <input type="hidden" name="sort_by" value="{{ $sortBy }}">
                <div class="px-6 py-4 space-y-3">
                    <p class="text-sm text-gray-600">
                        Export akan menghasilkan <strong>2 sheet</strong>:
                    </p>
                    <div class="p-3 space-y-2 text-sm text-gray-500 rounded-lg bg-gray-50">
                        <p>✅ <strong>Sheet 1</strong> — Validated only (approved)</p>
                        <p>📊 <strong>Sheet 2</strong> — All (approved + pending)</p>
                    </div>
                    <div class="p-3 text-xs text-blue-700 rounded-lg bg-blue-50">
                        ℹ️ Range: {{ \Carbon\Carbon::parse($dateFrom)->translatedFormat('d F Y') }}
                        — {{ \Carbon\Carbon::parse($dateTo)->translatedFormat('d F Y') }}
                    </div>
                </div>
                <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-100">
                    <button type="button" onclick="closeExportModal()"
                        class="px-4 py-2 text-sm text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-5 py-2 text-sm text-white bg-teal-600 rounded-lg hover:bg-teal-700">
                        📥 Download Excel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function submitFilter() {
            const loading = document.getElementById('filterLoading');
            const main = document.getElementById('mainContent');
            const skeleton = document.getElementById('skeletonSection');

            if (loading) {
                loading.classList.remove('hidden');
                loading.classList.add('flex');
            }
            if (main) {
                main.classList.add('hidden');
            }
            if (skeleton) {
                skeleton.classList.remove('hidden');
            }

            document.getElementById('filterForm').submit();
        }

        function openExportModal() {
            const m = document.getElementById('exportModal');
            m.classList.remove('hidden');
            m.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeExportModal() {
            const m = document.getElementById('exportModal');
            m.classList.add('hidden');
            m.classList.remove('flex');
            document.body.style.overflow = '';
        }

        document.getElementById('exportModal').addEventListener('click', function(e) {
            if (e.target === this) closeExportModal();
        });

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') closeExportModal();
        });
    </script>

</x-app-layout>
