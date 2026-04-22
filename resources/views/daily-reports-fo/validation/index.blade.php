{{-- resources/views/daily-reports-fo/validation/index.blade.php --}}
{{-- GANTI SELURUH FILE dengan ini --}}

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    ✅ Validasi Laporan FO
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    @if ($isAllBranches)
                        Semua Cabang ({{ $accessibleBranches->count() }} cabang)
                    @else
                        {{ $selectedBranch->name }}
                    @endif
                    | {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                {{-- for superadmin route into superadmin.daily-report-fo.photo-cleanup.index --}}
                @if (auth()->user()->hasRole('superadmin'))
                    <a href="{{ route('superadmin.daily-report-fo.photo-cleanup.index') }}"
                        class="flex items-center gap-2 px-4 py-2 text-sm text-white transition bg-purple-600 rounded-lg hover:bg-purple-700">
                        📸 Photo Cleanup
                    </a>
                @endif

                {{-- show only for superadmin and marketing --}}
                @if (auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('marketing'))
                    <a href="{{ route('ranking-fo.index') }}"
                        class="flex items-center gap-2 px-4 py-2 text-sm text-white transition bg-blue-600 rounded-lg hover:bg-blue-700">
                        🏆 Ranking FO
                    </a>
                @endif

                {{-- Tombol Export --}}
                <button onclick="openExportModal()"
                    class="flex items-center gap-2 px-4 py-2 text-sm text-white transition bg-teal-600 rounded-lg hover:bg-teal-700">
                    📥 Export Excel
                </button>
            </div>
            {{-- tombol link ke ranking FO --}}
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            {{-- ============================================================ --}}
            {{-- ALERTS                                                         --}}
            {{-- ============================================================ --}}
            @if (session('success'))
                <div class="p-4 mb-6 text-green-700 bg-green-100 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="p-4 mb-6 text-red-700 bg-red-100 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            {{-- ============================================================ --}}
            {{-- FILTER                                                         --}}
            {{-- ============================================================ --}}
            <div class="p-4 mb-6 bg-white rounded-lg shadow-sm">
                <form id="filterForm" method="GET" action="{{ route('validation.index') }}">
                    <div class="flex flex-wrap items-end gap-3">

                        {{-- Cabang --}}
                        <div>
                            <label class="block mb-1 text-xs font-medium text-gray-500">Cabang</label>
                            <select name="branch_id" onchange="submitFilter()"
                                class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
                                <option value="all" {{ $branchIdParam === 'all' ? 'selected' : '' }}>
                                    🌐 Semua Cabang
                                </option>
                                @foreach ($accessibleBranches as $branch)
                                    <option value="{{ $branch->id }}"
                                        {{ $branchIdParam == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Tanggal --}}
                        <div>
                            <label class="block mb-1 text-xs font-medium text-gray-500">Tanggal</label>
                            <input type="date" name="tanggal" value="{{ $tanggal }}" onchange="submitFilter()"
                                class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
                        </div>

                        {{-- Shift --}}
                        <div>
                            <label class="block mb-1 text-xs font-medium text-gray-500">Shift</label>
                            <select name="shift" onchange="submitFilter()"
                                class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
                                <option value="">Semua Shift</option>
                                <option value="pagi" {{ request('shift') === 'pagi' ? 'selected' : '' }}>
                                    🌅 Shift Pagi
                                </option>
                                <option value="siang" {{ request('siang') === 'siang' ? 'selected' : '' }}>
                                    🌆 Shift Siang
                                </option>
                            </select>
                        </div>

                        {{-- Status Validasi --}}
                        <div>
                            <label class="block mb-1 text-xs font-medium text-gray-500">Status Validasi</label>
                            <select name="validation_status" onchange="submitFilter()"
                                class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
                                <option value="">Semua Status</option>
                                <option value="pending"
                                    {{ request('validation_status') === 'pending' ? 'selected' : '' }}>
                                    ⏳ Pending
                                </option>
                                <option value="approved"
                                    {{ request('validation_status') === 'approved' ? 'selected' : '' }}>
                                    ✅ Disetujui
                                </option>
                                <option value="rejected"
                                    {{ request('validation_status') === 'rejected' ? 'selected' : '' }}>
                                    ❌ Ditolak
                                </option>
                            </select>
                        </div>

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
            {{-- STATS                                                          --}}
            {{-- ============================================================ --}}

            {{-- Baris 1: Status Validasi --}}
            <div class="mx-4 mb-3 md:mx-0">
                <p class="mb-2 text-[10px] font-semibold tracking-widest text-gray-400 uppercase">
                    Status Validasi
                </p>
                <div class="grid grid-cols-2 gap-3 md:grid-cols-4">

                    {{-- Total --}}
                    <div
                        class="relative overflow-hidden p-4 bg-gray-50 border border-gray-200 rounded-xl
                    before:absolute before:inset-y-0 before:left-0 before:w-[3px]
                    before:bg-gray-300 before:rounded-full">
                        <p class="mb-1 text-[15px]">📋</p>
                        <p class="text-[10px] font-semibold tracking-wider text-gray-500 uppercase">Total</p>
                        <p
                            class="font-medium text-gray-800 @if ($stats['total'] >= 1000) text-base @elseif($stats['total'] >= 100) text-xl @else text-2xl @endif">
                            {{ number_format($stats['total'], 0, ',', '.') }}
                        </p>
                        <p class="mt-1 text-[11px] text-gray-400">laporan hari ini</p>
                    </div>

                    {{-- Pending --}}
                    <div
                        class="relative overflow-hidden p-4 bg-[#FAEEDA] border border-[#FAC775] rounded-xl
                    before:absolute before:inset-y-0 before:left-0 before:w-[3px]
                    before:bg-[#EF9F27] before:rounded-full">
                        <p class="mb-1 text-[15px]">⏳</p>
                        <p class="text-[10px] font-semibold tracking-wider text-[#854F0B] uppercase">Pending</p>
                        <p
                            class="font-medium text-[#854F0B] @if ($stats['pending'] >= 1000) text-base @elseif($stats['pending'] >= 100) text-xl @else text-2xl @endif">
                            {{ number_format($stats['pending'], 0, ',', '.') }}
                        </p>
                        <p class="mt-1 text-[11px] text-[#BA7517]">menunggu validasi</p>
                    </div>

                    {{-- Disetujui --}}
                    <div
                        class="relative overflow-hidden p-4 bg-[#EAF3DE] border border-[#C0DD97] rounded-xl
                    before:absolute before:inset-y-0 before:left-0 before:w-[3px]
                    before:bg-[#639922] before:rounded-full">
                        <p class="mb-1 text-[15px]">✅</p>
                        <p class="text-[10px] font-semibold tracking-wider text-[#27500A] uppercase">Disetujui</p>
                        <p
                            class="font-medium text-[#27500A] @if ($stats['approved'] >= 1000) text-base @elseif($stats['approved'] >= 100) text-xl @else text-2xl @endif">
                            {{ number_format($stats['approved'], 0, ',', '.') }}
                        </p>
                        <p class="mt-1 text-[11px] text-[#3B6D11]">laporan valid</p>
                    </div>

                    {{-- Ditolak --}}
                    <div
                        class="relative overflow-hidden p-4 bg-[#FCEBEB] border border-[#F7C1C1] rounded-xl
                    before:absolute before:inset-y-0 before:left-0 before:w-[3px]
                    before:bg-[#E24B4A] before:rounded-full">
                        <p class="mb-1 text-[15px]">✕</p>
                        <p class="text-[10px] font-semibold tracking-wider text-[#791F1F] uppercase">Ditolak</p>
                        <p
                            class="font-medium text-[#791F1F] @if ($stats['rejected'] >= 1000) text-base @elseif($stats['rejected'] >= 100) text-xl @else text-2xl @endif">
                            {{ number_format($stats['rejected'], 0, ',', '.') }}
                        </p>
                        <p class="mt-1 text-[11px] text-[#A32D2D]">perlu perbaikan</p>
                    </div>

                </div>
            </div>

            {{-- Baris 2: Metrik Bisnis --}}
            <div class="mx-4 mb-6 md:mx-0">
                <p class="mb-2 text-[10px] font-semibold tracking-widest text-gray-400 uppercase">
                    Metrik Bisnis
                </p>
                <div class="grid grid-cols-1 gap-3 md:grid-cols-3">

                    {{-- Total Omset --}}
                    <div x-data="{ open: false }" @click="open = !open"
                        class="relative overflow-hidden border rounded-xl
        bg-[#E6F1FB] border-[#B5D4F4]
        before:absolute before:inset-y-0 before:left-0 before:w-[3px]
        before:bg-[#378ADD] before:rounded-full
        flex flex-row items-center gap-0 p-3
        md:flex-col md:items-start md:p-4
        md:cursor-pointer md:hover:ring-1 md:hover:ring-[#378ADD]/40 md:transition-shadow">
                        <div class="flex flex-col flex-1 min-w-0">
                            <p class="mb-1 text-[15px]">💰</p>
                            <p class="text-[10px] font-semibold tracking-wider text-[#0C447C] uppercase">Total Omset</p>
                            <p class="mt-1 text-[11px] text-[#185FA5] md:hidden">dari laporan disetujui</p>
                        </div>
                        <div class="flex flex-col items-end text-right md:items-start md:text-left shrink-0">
                            <p class="font-medium leading-tight text-[#0C447C] text-lg md:text-lg">
                                {{ \App\Helpers\FormatHelper::rupiah($stats['total_omset']) }}
                                <span class="hidden md:inline text-[11px] font-normal opacity-50 ml-0.5"
                                    x-text="open ? 'Hide' : 'Detail'"></span>
                            </p>
                            <p class="text-[11px] text-[#185FA5] md:hidden">
                                {{ \App\Helpers\FormatHelper::rupiahFull($stats['total_omset']) }}
                            </p>
                            <p x-show="!open" class="hidden mt-1 text-[11px] text-[#185FA5] md:block">dari laporan
                                disetujui</p>
                            <p x-show="open" class="hidden mt-1 text-[11px] text-[#185FA5] md:block break-all">
                                {{ \App\Helpers\FormatHelper::rupiahFull($stats['total_omset']) }}
                            </p>
                        </div>
                    </div>

                    {{-- Total Revenue --}}
                    <div x-data="{ open: false }" @click="open = !open"
                        class="relative overflow-hidden border rounded-xl
        bg-[#EEEDFE] border-[#CECBF6]
        before:absolute before:inset-y-0 before:left-0 before:w-[3px]
        before:bg-[#7F77DD] before:rounded-full
        flex flex-row items-center gap-0 p-3
        md:flex-col md:items-start md:p-4
        md:cursor-pointer md:hover:ring-1 md:hover:ring-[#7F77DD]/40 md:transition-shadow">
                        <div class="flex flex-col flex-1 min-w-0">
                            <p class="mb-1 text-[15px]">📈</p>
                            <p class="text-[10px] font-semibold tracking-wider text-[#3C3489] uppercase">Total Revenue
                            </p>
                            <p class="mt-1 text-[11px] text-[#534AB7] md:hidden">dari laporan disetujui</p>
                        </div>
                        <div class="flex flex-col items-end text-right md:items-start md:text-left shrink-0">
                            <p class="font-medium leading-tight text-[#3C3489] text-lg md:text-lg">
                                {{ \App\Helpers\FormatHelper::rupiah($stats['total_revenue']) }}
                                <span class="hidden md:inline text-[11px] font-normal opacity-50 ml-0.5"
                                    x-text="open ? 'Hide' : 'Detail'"></span>
                            </p>
                            <p class="text-[11px] text-[#534AB7] md:hidden">
                                {{ \App\Helpers\FormatHelper::rupiahFull($stats['total_revenue']) }}
                            </p>
                            <p x-show="!open" class="hidden mt-1 text-[11px] text-[#534AB7] md:block">dari laporan
                                disetujui</p>
                            <p x-show="open" class="hidden mt-1 text-[11px] text-[#534AB7] md:block break-all">
                                {{ \App\Helpers\FormatHelper::rupiahFull($stats['total_revenue']) }}
                            </p>
                        </div>
                    </div>

                    {{-- Total Akad — tidak ada toggle, angka sudah integer biasa --}}
                    <div
                        class="relative overflow-hidden border rounded-xl
        bg-[#EEEDFE] border-[#AFA9EC]
        before:absolute before:inset-y-0 before:left-0 before:w-[3px]
        before:bg-[#534AB7] before:rounded-full
        flex flex-row items-center gap-0 p-3 md:flex-col md:items-start md:p-4">
                        <div class="flex flex-col flex-1 min-w-0">
                            <p class="mb-1 text-[15px]">🤝</p>
                            <p class="text-[10px] font-semibold tracking-wider text-[#3C3489] uppercase">Total Akad</p>
                            <p class="mt-1 text-[11px] text-[#534AB7] md:hidden">transaksi disetujui</p>
                        </div>
                        <div class="flex flex-col items-end text-right md:items-start md:text-left shrink-0">
                            <p
                                class="font-medium text-[#3C3489]
                @if ($stats['total_akad'] >= 1000) text-base @elseif($stats['total_akad'] >= 100) text-xl @else text-2xl @endif">
                                {{ number_format($stats['total_akad'], 0, ',', '.') }}
                            </p>
                            <p class="hidden mt-1 text-[11px] text-[#534AB7] md:block">transaksi disetujui</p>
                        </div>
                    </div>

                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- REPORT LIST                                                    --}}
            {{-- ============================================================ --}}
            <div class="bg-white rounded-lg shadow-sm">
                @if ($reports->isEmpty())
                    <div class="p-12 text-center text-gray-400">
                        <p class="mb-3 text-4xl">📋</p>
                        <p>Tidak ada laporan untuk filter ini.</p>
                    </div>
                @else
                    <div class="overflow-x-auto" id="tableWrapper">
                        <table class="w-full text-sm min-w-max whitespace-nowrap">
                            <thead class="border-b border-gray-200 bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-xs font-semibold text-left text-gray-500 uppercase">
                                        FO
                                    </th>
                                    @if ($isAllBranches)
                                        <th class="px-4 py-3 text-xs font-semibold text-left text-gray-500 uppercase">
                                            Cabang
                                        </th>
                                    @endif
                                    <th class="px-4 py-3 text-xs font-semibold text-left text-gray-500 uppercase">
                                        Shift / Slot
                                    </th>
                                    <th class="px-4 py-3 text-xs font-semibold text-left text-gray-500 uppercase">
                                        Upload
                                    </th>
                                    <th class="px-4 py-3 text-xs font-semibold text-right text-gray-500 uppercase">
                                        Omset
                                    </th>
                                    <th class="px-4 py-3 text-xs font-semibold text-right text-gray-500 uppercase">
                                        Revenue
                                    </th>
                                    <th class="px-4 py-3 text-xs font-semibold text-center text-gray-500 uppercase">
                                        Akad
                                    </th>
                                    <th class="px-4 py-3 text-xs font-semibold text-center text-gray-500 uppercase">
                                        Status
                                    </th>
                                    <th class="px-4 py-3 text-xs font-semibold text-center text-gray-500 uppercase">
                                        Window
                                    </th>
                                    <th class="px-4 py-3 text-xs font-semibold text-center text-gray-500 uppercase">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $seenUserIds = []; @endphp

                                @foreach ($reports as $index => $report)
                                    @php
                                        $metrikDetails = $report->details->keyBy(fn($d) => $d->field->code);
                                        $omset = $metrikDetails->get('mb_omset')?->value_number ?? 0;
                                        $revenue = $metrikDetails->get('mb_revenue')?->value_number ?? 0;
                                        $akad = $metrikDetails->get('mb_jumlah_akad')?->value_number ?? 0;
                                        $windowStatus = $report->manager_window_status;

                                        // Color coding per status validasi
                                        $rowBg = match ($report->validation_status) {
                                            'approved' => $index % 2 === 0 ? 'bg-green-50' : 'bg-green-100/50',
                                            'rejected' => $index % 2 === 0 ? 'bg-red-50' : 'bg-red-100/50',
                                            default => $index % 2 === 0 ? 'bg-yellow-50/50' : 'bg-yellow-100/30',
                                        };

                                        // Grouping visual: border tebal jika ganti FO
                                        $isNewUser = !in_array($report->user_id, $seenUserIds);
                                        $seenUserIds[] = $report->user_id;
                                        $groupBorder =
                                            $isNewUser && $index !== 0
                                                ? 'border-t-2 border-gray-400'
                                                : 'border-t border-gray-100';
                                    @endphp

                                    <tr
                                        class="{{ $rowBg }} {{ $groupBorder }} transition-colors hover:brightness-95">

                                        {{-- FO --}}
                                        <td class="px-4 py-3">
                                            @if ($isNewUser)
                                                <p class="font-semibold text-gray-800">
                                                    {{ $report->user->name }}
                                                </p>
                                            @else
                                                <p class="pl-2 text-xs text-gray-400 border-l-2 border-gray-200">
                                                    {{ $report->user->name }}
                                                </p>
                                            @endif
                                        </td>

                                        {{-- Cabang --}}
                                        @if ($isAllBranches)
                                            <td class="px-4 py-3 text-xs text-gray-500">
                                                {{ $report->branch->name }}
                                                <span class="text-gray-400">
                                                    ({{ $report->branch->timezone }})
                                                </span>
                                            </td>
                                        @endif

                                        {{-- Shift / Slot --}}
                                        <td class="px-4 py-3 text-gray-600">
                                            {{ $report->shift_label }} / Slot {{ $report->slot }}
                                            <span class="text-xs text-gray-400">
                                                ({{ $report->slot_time }})
                                            </span>
                                        </td>

                                        {{-- Upload --}}
                                        <td class="px-4 py-3 text-xs text-gray-500">
                                            {{ $report->uploaded_at->format('H:i:s') }}
                                        </td>

                                        {{-- Metrik --}}
                                        <td class="px-4 py-3 font-medium text-right text-gray-800">
                                            Rp {{ number_format($omset, 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-3 font-medium text-right text-gray-800">
                                            Rp {{ number_format($revenue, 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-3 font-medium text-center text-gray-800">
                                            {{ number_format($akad, 0, ',', '.') }}
                                        </td>

                                        {{-- Status Validasi --}}
                                        <td class="px-4 py-3 text-center">
                                            @if ($report->validation_status === 'approved')
                                                <span
                                                    class="px-2 py-1 text-xs font-semibold text-green-700 bg-green-200 rounded-full">
                                                    ✅ Disetujui
                                                </span>
                                            @elseif ($report->validation_status === 'rejected')
                                                <span
                                                    class="px-2 py-1 text-xs font-semibold text-red-700 bg-red-200 rounded-full">
                                                    ❌ Ditolak
                                                </span>
                                            @else
                                                <span
                                                    class="px-2 py-1 text-xs font-semibold text-yellow-700 bg-yellow-200 rounded-full">
                                                    ⏳ Pending
                                                </span>
                                            @endif
                                        </td>

                                        {{-- Window Status --}}
                                        <td class="px-4 py-3 text-xs text-center">
                                            @if ($windowStatus === 'open')
                                                <span
                                                    class="inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold text-orange-700 bg-orange-100 rounded-full animate-pulse">
                                                    🟠 Buka
                                                </span>
                                                <br>
                                                <span class="text-gray-400">
                                                    s/d {{ $report->manager_window_end->format('H:i') }}
                                                    {{ $report->branch->timezone }}
                                                </span>
                                            @elseif ($windowStatus === 'waiting')
                                                <span class="text-gray-400">⏳ Menunggu</span>
                                                <br>
                                                <span class="text-gray-400">
                                                    mulai {{ $report->manager_window_start->format('H:i') }}
                                                    {{ $report->branch->timezone }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">🔒 Expired</span>
                                            @endif
                                        </td>

                                        {{-- Aksi --}}
                                        <td class="px-4 py-3 text-center">
                                            <a href="{{ route('validation.show', $report->id) }}"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs text-white bg-teal-600 rounded-lg hover:bg-teal-700 transition">
                                                Detail
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Skeleton (hidden, ditampilkan saat filter berubah) --}}
                    <div id="tableSkeleton" class="hidden p-4 space-y-3">
                        @for ($i = 0; $i < 8; $i++)
                            <div class="flex gap-3 animate-pulse">
                                <div class="h-8 bg-gray-200 rounded w-36"></div>
                                <div class="w-24 h-8 bg-gray-200 rounded"></div>
                                <div class="w-20 h-8 bg-gray-200 rounded"></div>
                                <div class="h-8 bg-gray-200 rounded w-28"></div>
                                <div class="h-8 bg-gray-200 rounded w-28"></div>
                                <div class="w-20 h-8 bg-gray-200 rounded"></div>
                                <div class="flex-1 h-8 bg-gray-200 rounded"></div>
                            </div>
                        @endfor
                    </div>

                    {{-- Pagination --}}
                    <div class="flex items-center justify-between px-4 py-3 border-t border-gray-100">
                        <p class="text-xs text-gray-400">
                            Menampilkan {{ $reports->firstItem() }}–{{ $reports->lastItem() }}
                            dari {{ $reports->total() }} laporan
                        </p>
                        {{ $reports->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- MODAL EXPORT                                                   --}}
    {{-- ============================================================ --}}
    <div id="exportModal" class="fixed inset-0 z-50 items-center justify-center hidden bg-black bg-opacity-50">
        <div class="w-full max-w-md mx-4 bg-white rounded-lg shadow-xl">

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">📥 Export Laporan Validasi FO</h3>
                <button onclick="closeExportModal()" class="text-gray-400 hover:text-gray-600">✕</button>
            </div>

            {{-- Body --}}
            <form method="GET" action="{{ route('validation.export') }}">
                <div class="px-6 py-4 space-y-4">

                    {{-- Info batas hari --}}
                    <div class="p-3 text-xs text-blue-700 rounded-lg bg-blue-50">
                        ℹ️ Maksimal export
                        @if (auth()->user()->hasRole('manager'))
                            <strong>7 hari</strong> (role Manager)
                        @else
                            <strong>3 hari</strong> (role {{ ucfirst(auth()->user()->getRoleNames()->first()) }})
                        @endif
                    </div>

                    {{-- Tanggal Dari --}}
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">
                            Tanggal Dari <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="date_from" id="exportDateFrom" value="{{ $tanggal }}"
                            class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
                    </div>

                    {{-- Tanggal Sampai --}}
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">
                            Tanggal Sampai <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="date_to" id="exportDateTo" value="{{ $tanggal }}"
                            class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
                    </div>

                    {{-- Cabang --}}
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">
                            Cabang <span class="text-red-500">*</span>
                        </label>
                        <select name="branch_id"
                            class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
                            <option value="all">🌐 Semua Cabang</option>
                            @foreach ($accessibleBranches as $branch)
                                <option value="{{ $branch->id }}"
                                    {{ $branchIdParam == $branch->id && $branchIdParam !== 'all' ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>

                {{-- Footer --}}
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
        // ============================================================
        // Filter + Skeleton Loading
        // ============================================================
        function submitFilter() {
            const loading = document.getElementById('filterLoading');
            const tableWrapper = document.getElementById('tableWrapper');
            const skeleton = document.getElementById('tableSkeleton');

            // Tampilkan loading indicator di filter
            if (loading) {
                loading.classList.remove('hidden');
                loading.classList.add('flex');
            }

            // Swap tabel → skeleton
            if (tableWrapper) tableWrapper.classList.add('hidden');
            if (skeleton) skeleton.classList.remove('hidden');

            document.getElementById('filterForm').submit();
        }

        // ============================================================
        // Modal Export
        // ============================================================
        function openExportModal() {
            const modal = document.getElementById('exportModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeExportModal() {
            const modal = document.getElementById('exportModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }

        // Tutup modal saat klik backdrop
        document.getElementById('exportModal').addEventListener('click', function(e) {
            if (e.target === this) closeExportModal();
        });

        // Tutup modal saat tekan Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeExportModal();
        });
    </script>
</x-app-layout>
