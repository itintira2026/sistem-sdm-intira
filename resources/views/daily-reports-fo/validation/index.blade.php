<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    ✅ Validasi Laporan FO
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $isAllBranches ? 'Semua Cabang' : $selectedBranch->name }}
                    | {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="p-4 mb-6 text-green-700 bg-green-100 rounded-lg">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="p-4 mb-6 text-red-700 bg-red-100 rounded-lg">{{ session('error') }}</div>
            @endif

            {{-- ============================================================ --}}
            {{-- FILTER — onchange, tanpa tombol submit                        --}}
            {{-- ============================================================ --}}
            <div class="p-4 mb-6 bg-white rounded-lg shadow-sm">
                <form id="filterForm" method="GET" action="{{ route('validation.index') }}">
                    <div class="flex flex-wrap items-end gap-3">

                        {{-- Cabang --}}
                        <div>
                            <label class="block mb-1 text-xs font-medium text-gray-500">Cabang</label>
                            {{-- <select name="branch_id" onchange="submitFilter()"
                                class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
                                @if ($canViewAll)
                                    <option value="all" {{ $branchIdParam === 'all' ? 'selected' : '' }}>
                                        🌐 Semua Cabang
                                    </option>
                                @endif
                                @foreach ($accessibleBranches as $branch)
                                    <option value="{{ $branch->id }}"
                                        {{ $branchIdParam == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select> --}}
                            {{--
                                Ganti bagian <select name="branch_id"> di filter form dengan ini.
                                Perubahan: hapus kondisi @if ($canViewAll), semua role dapat opsi "Semua Cabang"
                                tapi isinya dibatasi $accessibleBranches dari controller (manager hanya cabangnya)
                            --}}

                            <select name="branch_id" onchange="submitFilter()"
                                class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">

                                {{-- Opsi "Semua Cabang" untuk semua role --}}
                                <option value="all" {{ $branchIdParam === 'all' ? 'selected' : '' }}>
                                    🌐 Semua Cabang
                                    {{-- @if (!auth()->user()->hasRole('superadmin') && !auth()->user()->hasRole('marketing'))
                                        (yang saya kelola)
                                    @endif --}}
                                </option>

                                {{-- Daftar cabang sesuai akses masing-masing role --}}
                                @foreach ($accessibleBranches as $branch)
                                    <option value="{{ $branch->id }}"
                                        {{ $branchIdParam == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach

                            </select>

                            {{--
                                Juga hapus variabel $canViewAll dari view karena tidak dipakai lagi.
                                Dan pastikan header subtitle juga sudah handle isAllBranches untuk manager:
                            --}}

                            {{-- Ganti subtitle di header dengan ini: --}}
                            <p class="mt-1 text-sm text-gray-500">
                                @if ($isAllBranches)
                                    Semua Cabang ({{ $accessibleBranches->count() }} cabang)
                                @else
                                    {{ $selectedBranch->name }}
                                @endif
                                | {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}
                            </p>
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
                                <option value="pagi" {{ request('shift') === 'pagi' ? 'selected' : '' }}>🌅 Shift
                                    Pagi</option>
                                <option value="siang" {{ request('shift') === 'siang' ? 'selected' : '' }}>🌆 Shift
                                    Siang</option>
                            </select>
                        </div>

                        {{-- Status Validasi --}}
                        <div>
                            <label class="block mb-1 text-xs font-medium text-gray-500">Status Validasi</label>
                            <select name="validation_status" onchange="submitFilter()"
                                class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
                                <option value="">Semua Status</option>
                                <option value="pending"
                                    {{ request('validation_status') === 'pending' ? 'selected' : '' }}>⏳ Pending
                                </option>
                                <option value="approved"
                                    {{ request('validation_status') === 'approved' ? 'selected' : '' }}>✅ Disetujui
                                </option>
                                <option value="rejected"
                                    {{ request('validation_status') === 'rejected' ? 'selected' : '' }}>❌ Ditolak
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
            {{-- STATS — 7 card                                                --}}
            {{-- ============================================================ --}}
            <div class="grid grid-cols-2 gap-3 mb-6 sm:grid-cols-4 lg:grid-cols-7">

                {{-- Total Laporan --}}
                <div class="col-span-1 p-4 text-center bg-white rounded-lg shadow-sm">
                    <p class="text-xs text-gray-400">Total</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
                    <p class="text-xs text-gray-400">laporan</p>
                </div>

                {{-- Pending --}}
                <div class="p-4 text-center rounded-lg shadow-sm bg-yellow-50">
                    <p class="text-xs text-yellow-600">⏳ Pending</p>
                    <p class="text-2xl font-bold text-yellow-700">{{ $stats['pending'] }}</p>
                </div>

                {{-- Approved --}}
                <div class="p-4 text-center rounded-lg shadow-sm bg-green-50">
                    <p class="text-xs text-green-600">✅ Disetujui</p>
                    <p class="text-2xl font-bold text-green-700">{{ $stats['approved'] }}</p>
                </div>

                {{-- Rejected --}}
                <div class="p-4 text-center rounded-lg shadow-sm bg-red-50">
                    <p class="text-xs text-red-600">❌ Ditolak</p>
                    <p class="text-2xl font-bold text-red-700">{{ $stats['rejected'] }}</p>
                </div>

                {{-- Total Omset --}}
                <div class="col-span-1 p-4 text-center rounded-lg shadow-sm bg-blue-50 sm:col-span-1">
                    <p class="text-xs text-blue-600">💰 Total Omset</p>
                    <p class="text-lg font-bold leading-tight text-blue-800">
                        Rp {{ number_format($stats['total_omset'], 0, ',', '.') }}
                    </p>
                </div>

                {{-- Total Revenue --}}
                <div class="p-4 text-center rounded-lg shadow-sm bg-indigo-50">
                    <p class="text-xs text-indigo-600">📈 Total Revenue</p>
                    <p class="text-lg font-bold leading-tight text-indigo-800">
                        Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}
                    </p>
                </div>

                {{-- Total Akad --}}
                <div class="p-4 text-center rounded-lg shadow-sm bg-purple-50">
                    <p class="text-xs text-purple-600">🤝 Total Akad</p>
                    <p class="text-2xl font-bold text-purple-700">
                        {{ number_format($stats['total_akad'], 0, ',', '.') }}
                    </p>
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
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="border-b border-gray-200 bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-xs font-semibold text-left text-gray-500 uppercase">FO
                                    </th>
                                    @if ($isAllBranches)
                                        <th class="px-4 py-3 text-xs font-semibold text-left text-gray-500 uppercase">
                                            Cabang</th>
                                    @endif
                                    <th class="px-4 py-3 text-xs font-semibold text-left text-gray-500 uppercase">Shift
                                        / Slot</th>
                                    <th class="px-4 py-3 text-xs font-semibold text-left text-gray-500 uppercase">Upload
                                    </th>
                                    <th class="px-4 py-3 text-xs font-semibold text-right text-gray-500 uppercase">Omset
                                    </th>
                                    <th class="px-4 py-3 text-xs font-semibold text-right text-gray-500 uppercase">
                                        Revenue</th>
                                    <th class="px-4 py-3 text-xs font-semibold text-center text-gray-500 uppercase">Akad
                                    </th>
                                    <th class="px-4 py-3 text-xs font-semibold text-center text-gray-500 uppercase">
                                        Status</th>
                                    <th class="px-4 py-3 text-xs font-semibold text-center text-gray-500 uppercase">
                                        Window</th>
                                    <th class="px-4 py-3 text-xs font-semibold text-center text-gray-500 uppercase">Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($reports as $report)
                                    @php
                                        $metrikDetails = $report->details->keyBy(fn($d) => $d->field->code);
                                        $omset = $metrikDetails->get('mb_omset')?->value_number ?? 0;
                                        $revenue = $metrikDetails->get('mb_revenue')?->value_number ?? 0;
                                        $akad = $metrikDetails->get('mb_jumlah_akad')?->value_number ?? 0;
                                        $windowStatus = $report->manager_window_status;
                                    @endphp

                                    <tr class="transition-colors hover:bg-gray-50">
                                        {{-- FO --}}
                                        <td class="px-4 py-3">
                                            <p class="font-medium text-gray-800">{{ $report->user->name }}</p>
                                        </td>

                                        {{-- Cabang (hanya di mode all) --}}
                                        @if ($isAllBranches)
                                            <td class="px-4 py-3 text-xs text-gray-500">
                                                {{ $report->branch->name }}
                                                <span class="text-gray-400">({{ $report->branch->timezone }})</span>
                                            </td>
                                        @endif

                                        {{-- Shift / Slot --}}
                                        <td class="px-4 py-3 text-gray-600">
                                            {{ $report->shift_label }} / Slot {{ $report->slot }}
                                            <span class="text-xs text-gray-400">({{ $report->slot_time }})</span>
                                        </td>

                                        {{-- Upload time --}}
                                        <td class="px-4 py-3 text-xs text-gray-500">
                                            {{ $report->uploaded_at->format('H:i:s') }}
                                        </td>

                                        {{-- Metrik Bisnis --}}
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
                                                    class="px-2 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full">✅
                                                    Disetujui</span>
                                            @elseif ($report->validation_status === 'rejected')
                                                <span
                                                    class="px-2 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded-full">❌
                                                    Ditolak</span>
                                            @else
                                                <span
                                                    class="px-2 py-1 text-xs font-semibold text-yellow-700 bg-yellow-100 rounded-full">⏳
                                                    Pending</span>
                                            @endif
                                        </td>

                                        {{-- Window Status --}}
                                        <td class="px-4 py-3 text-xs text-center">
                                            @if ($windowStatus === 'open')
                                                <span class="font-semibold text-orange-600">🟠 Buka</span>
                                                <br>
                                                <span class="text-gray-400">s/d
                                                    {{ $report->manager_window_end->format('H:i') }}
                                                    {{ $report->branch->timezone }}</span>
                                            @elseif ($windowStatus === 'waiting')
                                                <span class="text-gray-400">⏳ Menunggu</span>
                                                <br>
                                                <span class="text-gray-400">mulai
                                                    {{ $report->manager_window_start->format('H:i') }}
                                                    {{ $report->branch->timezone }}</span>
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

                    {{-- Pagination --}}
                    <div class="flex items-center justify-between px-4 py-3 border-t border-gray-100">
                        <p class="text-xs text-gray-400">
                            Menampilkan {{ $reports->firstItem() }}–{{ $reports->lastItem() }} dari
                            {{ $reports->total() }} laporan
                        </p>
                        {{ $reports->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    <script>
        function submitFilter() {
            // Tampilkan loading
            const loading = document.getElementById('filterLoading');
            loading.classList.remove('hidden');
            loading.classList.add('flex');

            document.getElementById('filterForm').submit();
        }
    </script>
</x-app-layout>
