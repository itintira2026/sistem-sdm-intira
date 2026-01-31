<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Validasi Laporan Harian
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Review dan validasi laporan harian cabang
                </p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('daily-reports.manager.dashboard', ['tanggal' => $tanggal]) }}"
                    class="flex items-center gap-2 px-4 py-2 text-white transition bg-gray-500 rounded-lg hover:bg-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Dashboard
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            {{-- Alert Success --}}
            @if (session('success'))
                <div class="p-4 mb-6 text-green-700 bg-green-100 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            {{-- 🔥 INFO CABANG YANG DI-MANAGE --}}
            @if (!Auth::user()->hasRole('superadmin'))
                <div class="p-4 mb-6 bg-blue-100 rounded-lg">
                    <p class="font-semibold text-blue-800">📍 Cabang yang Anda Kelola:</p>
                    <div class="flex flex-wrap gap-2 mt-2">
                        @foreach ($managedBranches as $branch)
                            <span class="px-3 py-1 text-sm text-blue-700 bg-white rounded-full">
                                {{ $branch->name }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="p-4 mb-6 bg-gray-100 rounded-lg">
                    <p class="text-sm text-gray-700">
                        🔓 <strong>Superadmin Mode:</strong> Anda bisa melihat semua cabang.
                    </p>
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="mb-6 text-lg font-semibold text-gray-800">
                        Daftar Laporan
                    </h3>

                    {{-- FILTER --}}
                    <form method="GET" class="flex flex-wrap gap-4 mb-6">
                        {{-- TANGGAL --}}
                        <div>
                            <input type="date" name="tanggal" value="{{ request('tanggal', $tanggal) }}"
                                onchange="this.form.submit()" class="px-4 py-2 border rounded-lg">
                        </div>

                        {{-- PER PAGE --}}
                        <div>
                            <select name="per_page" onchange="this.form.submit()"
                                class="px-4 py-2 pr-10 border rounded-lg">
                                @foreach ([10, 25, 50, 100] as $size)
                                    <option value="{{ $size }}"
                                        {{ request('per_page', 25) == $size ? 'selected' : '' }}>
                                        {{ $size }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- 🔥 FILTER CABANG (jika manager punya multiple branches) --}}
                        @if ($managedBranches->count() > 1)
                            <div>
                                <select name="branch_id" onchange="this.form.submit()"
                                    class="px-4 py-2 pr-10 border rounded-lg">
                                    <option value="">Semua Cabang</option>
                                    @foreach ($managedBranches as $branch)
                                        <option value="{{ $branch->id }}"
                                            {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        {{-- SHIFT --}}
                        <div>
                            <select name="shift" onchange="this.form.submit()"
                                class="px-4 py-2 pr-10 border rounded-lg">
                                <option value="">Semua Shift</option>
                                <option value="pagi" {{ request('shift') == 'pagi' ? 'selected' : '' }}>
                                    Shift Pagi
                                </option>
                                <option value="siang" {{ request('shift') == 'siang' ? 'selected' : '' }}>
                                    Shift Siang
                                </option>
                            </select>
                        </div>

                        {{-- STATUS VALIDASI --}}
                        <div>
                            <select name="validasi" onchange="this.form.submit()"
                                class="px-4 py-2 pr-10 border rounded-lg">
                                <option value="">Semua Status</option>
                                <option value="1" {{ request('validasi') === '1' ? 'selected' : '' }}>
                                    Sudah Validasi
                                </option>
                                <option value="0" {{ request('validasi') === '0' ? 'selected' : '' }}>
                                    Belum Validasi
                                </option>
                            </select>
                        </div>

                        <button type="submit" class="px-4 py-2 text-white bg-teal-600 rounded-lg hover:bg-teal-700">
                            Filter
                        </button>
                    </form>
                    {{-- SUMMARY CARDS --}}
                    <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-3">
                        <div class="p-4 rounded-lg bg-teal-50">
                            <p class="text-sm text-teal-700">Total Laporan</p>
                            <p class="mt-1 text-2xl font-bold text-teal-800">{{ $stats['total_laporan'] }}</p>
                        </div>
                        <div class="p-4 rounded-lg bg-green-50">
                            <p class="text-sm text-green-700">Sudah Divalidasi</p>
                            <p class="mt-1 text-2xl font-bold text-green-800">{{ $stats['sudah_validasi'] }}</p>
                        </div>
                        <div class="p-4 rounded-lg bg-orange-50">
                            <p class="text-sm text-orange-700">Belum Divalidasi</p>
                            <p class="mt-1 text-2xl font-bold text-orange-800">{{ $stats['belum_validasi'] }}</p>
                        </div>
                    </div>

                    {{-- 🔥 STATISTIK PENCAIRAN & PELUNASAN --}}
                    <div class="grid grid-cols-1 gap-6 mb-6 md:grid-cols-2">
                        {{-- PENCAIRAN --}}
                        <div class="p-6 bg-white border rounded-lg">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-base font-semibold text-gray-800">📥 Total Pencairan</h4>
                                <div class="p-2 bg-green-100 rounded-full">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                    </svg>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between p-3 rounded-lg bg-green-50">
                                    <span class="text-sm font-medium text-green-700">Jumlah Barang:</span>
                                    <span class="text-lg font-bold text-green-800">
                                        {{ number_format($stats['total_pencairan_barang']) }} unit
                                    </span>
                                </div>
                                <div class="flex items-center justify-between p-3 rounded-lg bg-green-50">
                                    <span class="text-sm font-medium text-green-700">Total Nominal:</span>
                                    <span class="text-lg font-bold text-green-800">
                                        Rp {{ number_format($stats['total_pencairan_nominal'], 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- PELUNASAN --}}
                        <div class="p-6 bg-white border rounded-lg">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-base font-semibold text-gray-800">📤 Total Pelunasan</h4>
                                <div class="p-2 bg-blue-100 rounded-full">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                    </svg>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between p-3 rounded-lg bg-blue-50">
                                    <span class="text-sm font-medium text-blue-700">Jumlah Barang:</span>
                                    <span class="text-lg font-bold text-blue-800">
                                        {{ number_format($stats['total_pelunasan_barang']) }} unit
                                    </span>
                                </div>
                                <div class="flex items-center justify-between p-3 rounded-lg bg-blue-50">
                                    <span class="text-sm font-medium text-blue-700">Total Nominal:</span>
                                    <span class="text-lg font-bold text-blue-800">
                                        Rp {{ number_format($stats['total_pelunasan_nominal'], 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 🔥 STOK AKHIR PER CABANG (COMPACT) --}}
                    <div class="p-6 mb-6 bg-white border rounded-lg">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-base font-semibold text-gray-800">📦 Stok Akhir Per Cabang</h4>
                            <div class="p-2 bg-purple-100 rounded-full">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                        </div>

                        @if (request('branch_id'))
                            {{-- JIKA ADA FILTER CABANG: TAMPILKAN DETAIL --}}
                            @php
                                $filteredBranch = $managedBranches->find(request('branch_id'));
                                $stok = $stokAkhirPerCabang[request('branch_id')] ?? [
                                    'jumlah_barang' => 0,
                                    'nominal' => 0,
                                ];
                            @endphp
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div class="flex items-center justify-between p-4 rounded-lg bg-purple-50">
                                    <div>
                                        <p class="text-sm font-medium text-purple-700">
                                            {{ $filteredBranch->name ?? 'N/A' }}</p>
                                        <p class="mt-1 text-xs text-purple-600">Jumlah Barang</p>
                                    </div>
                                    <p class="text-2xl font-bold text-purple-800">
                                        {{ number_format($stok['jumlah_barang']) }} <span
                                            class="text-base text-purple-600">unit</span>
                                    </p>
                                </div>

                                <div class="flex items-center justify-between p-4 rounded-lg bg-purple-50">
                                    <div>
                                        <p class="text-sm font-medium text-purple-700">
                                            {{ $filteredBranch->name ?? 'N/A' }}</p>
                                        <p class="mt-1 text-xs text-purple-600">Total Nominal</p>
                                    </div>
                                    <p class="text-2xl font-bold text-purple-800">
                                        Rp {{ number_format($stok['nominal'], 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        @else
                            {{-- JIKA TIDAK ADA FILTER: TAMPILKAN SUMMARY TOTAL --}}
                            @php
                                $totalStokBarang = collect($stokAkhirPerCabang)->sum('jumlah_barang');
                                $totalStokNominal = collect($stokAkhirPerCabang)->sum('nominal');
                            @endphp
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div class="flex items-center justify-between p-4 rounded-lg bg-purple-50">
                                    <div>
                                        <p class="text-sm font-medium text-purple-700">Total Semua Cabang</p>
                                        <p class="mt-1 text-xs text-purple-600">Jumlah Barang</p>
                                    </div>
                                    <p class="text-2xl font-bold text-purple-800">
                                        {{ number_format($totalStokBarang) }} <span
                                            class="text-base text-purple-600">unit</span>
                                    </p>
                                </div>

                                <div class="flex items-center justify-between p-4 rounded-lg bg-purple-50">
                                    <div>
                                        <p class="text-sm font-medium text-purple-700">Total Semua Cabang</p>
                                        <p class="mt-1 text-xs text-purple-600">Total Nominal</p>
                                    </div>
                                    <p class="text-2xl font-bold text-purple-800">
                                        Rp {{ number_format($totalStokNominal, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>

                            {{-- Detail per cabang (collapsed) --}}
                            <details class="mt-4">
                                <summary
                                    class="text-sm font-medium text-purple-700 cursor-pointer hover:text-purple-800">
                                    📋 Lihat detail per cabang ({{ $managedBranches->count() }} cabang)
                                </summary>
                                <div class="mt-3 overflow-x-auto">
                                    <table class="w-full text-sm">
                                        <thead>
                                            <tr class="border-b border-gray-200 bg-gray-50">
                                                <th class="px-3 py-2 text-xs font-semibold text-left text-gray-600">#
                                                </th>
                                                <th class="px-3 py-2 text-xs font-semibold text-left text-gray-600">
                                                    Cabang</th>
                                                <th class="px-3 py-2 text-xs font-semibold text-left text-gray-600">
                                                    Jumlah Barang</th>
                                                <th class="px-3 py-2 text-xs font-semibold text-left text-gray-600">
                                                    Total Nominal</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            @foreach ($managedBranches as $index => $branch)
                                                @php
                                                    $stok = $stokAkhirPerCabang[$branch->id] ?? [
                                                        'jumlah_barang' => 0,
                                                        'nominal' => 0,
                                                    ];
                                                @endphp
                                                <tr class="hover:bg-gray-50">
                                                    <td class="px-3 py-2 text-xs text-gray-900">{{ $index + 1 }}
                                                    </td>
                                                    <td class="px-3 py-2 text-xs font-medium text-gray-900">
                                                        {{ $branch->name }}</td>
                                                    <td class="px-3 py-2 text-xs text-purple-700">
                                                        {{ number_format($stok['jumlah_barang']) }} unit</td>
                                                    <td class="px-3 py-2 text-xs text-gray-900">Rp
                                                        {{ number_format($stok['nominal'], 0, ',', '.') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </details>
                        @endif

                        {{-- Info Box --}}
                        <div class="p-3 mt-4 rounded-lg bg-blue-50">
                            <p class="text-xs text-blue-800">
                                💡 <strong>Info:</strong> Stok Akhir = Stok Awal + Pencairan - Pelunasan (update
                                otomatis per laporan)
                            </p>
                        </div>
                    </div>

                    {{-- INFO FILTER AKTIF --}}
                    @if (request('branch_id') || request('shift') || request('validasi'))
                        <div class="p-4 mb-6 rounded-lg bg-blue-50">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div class="text-sm text-blue-800">
                                    <p class="font-semibold">Filter Aktif - Statistik disesuaikan dengan filter:</p>
                                    <ul class="mt-1 ml-4 list-disc">
                                        @if (request('branch_id'))
                                            <li>Cabang:
                                                {{ $managedBranches->find(request('branch_id'))->name ?? 'Semua' }}
                                            </li>
                                        @endif
                                        @if (request('shift'))
                                            <li>Shift: {{ request('shift') == 'pagi' ? 'Pagi' : 'Siang' }}</li>
                                        @endif
                                        @if (request('validasi') !== null)
                                            <li>Status:
                                                {{ request('validasi') == '1' ? 'Sudah Validasi' : 'Belum Validasi' }}
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-200 bg-gray-50">
                                    <th class="px-3 py-3 text-xs font-semibold text-left text-gray-600 uppercase">#
                                    </th>
                                    <th class="px-3 py-3 text-xs font-semibold text-left text-gray-600 uppercase">
                                        Cabang
                                    </th>
                                    <th class="px-3 py-3 text-xs font-semibold text-left text-gray-600 uppercase">
                                        Tanggal</th>
                                    <th class="px-3 py-3 text-xs font-semibold text-left text-gray-600 uppercase">Shift
                                    </th>
                                    <th class="px-3 py-3 text-xs font-semibold text-left text-gray-600 uppercase">
                                        Pencairan</th>
                                    <th class="px-3 py-3 text-xs font-semibold text-left text-gray-600 uppercase">
                                        Pelunasan</th>
                                    <th class="px-3 py-3 text-xs font-semibold text-left text-gray-600 uppercase">
                                        Dibuat
                                        Oleh</th>
                                    <th class="px-3 py-3 text-xs font-semibold text-left text-gray-600 uppercase">Foto
                                    </th>
                                    <th class="px-3 py-3 text-xs font-semibold text-left text-gray-600 uppercase">
                                        Validasi</th>
                                    <th class="px-3 py-3 text-xs font-semibold text-left text-gray-600 uppercase">Aksi
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-200">
                                @forelse ($reports as $index => $report)
                                    <tr
                                        class="hover:bg-gray-50 {{ !$report->validasi_manager ? 'bg-orange-50' : '' }}">
                                        <td class="px-3 py-3 text-sm text-gray-900">
                                            {{ ($reports->currentPage() - 1) * $reports->perPage() + $index + 1 }}
                                        </td>
                                        <td class="px-3 py-3">
                                            <p class="text-sm font-medium text-gray-900 truncate max-w-[150px]"
                                                title="{{ $report->branch->name }}">
                                                {{ $report->branch->name }}
                                            </p>
                                        </td>
                                        <td class="px-3 py-3 text-sm text-gray-900 whitespace-nowrap">
                                            {{ $report->tanggal->format('d M Y') }}
                                        </td>
                                        <td class="px-3 py-3 whitespace-nowrap">
                                            <span
                                                class="inline-flex px-2 py-1 text-xs font-medium rounded
                            {{ $report->shift == 'pagi' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                                                {{ $report->shift == 'pagi' ? 'Pagi' : 'Siang' }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-3">
                                            <div class="text-xs">
                                                <p class="font-medium text-gray-900">
                                                    {{ number_format($report->pencairan_jumlah_barang) }} unit</p>
                                                <p class="text-gray-600 truncate max-w-[120px]"
                                                    title="{{ $report->pencairan_nominal_formatted }}">
                                                    {{ $report->pencairan_nominal_formatted }}
                                                </p>
                                            </div>
                                        </td>
                                        <td class="px-3 py-3">
                                            <div class="text-xs">
                                                <p class="font-medium text-gray-900">
                                                    {{ number_format($report->pelunasan_jumlah_barang) }} unit</p>
                                                <p class="text-gray-600 truncate max-w-[120px]"
                                                    title="{{ $report->pelunasan_nominal_formatted }}">
                                                    {{ $report->pelunasan_nominal_formatted }}
                                                </p>
                                            </div>
                                        </td>
                                        <td class="px-3 py-3">
                                            <p class="text-xs text-gray-600 truncate max-w-[100px]"
                                                title="{{ $report->user->name }}">
                                                {{ $report->user->name }}
                                            </p>
                                        </td>
                                        <td class="px-3 py-3 whitespace-nowrap">
                                            <button onclick="showPhotos({{ $report->id }})"
                                                class="inline-flex items-center px-2 py-1 text-xs font-medium text-teal-700 transition bg-teal-100 rounded hover:bg-teal-200">
                                                📷 {{ $report->photos->count() }}
                                            </button>
                                        </td>
                                        <td class="px-3 py-3">
                                            @if ($report->validasi_manager)
                                                <div class="space-y-1">
                                                    <span
                                                        class="inline-flex px-2 py-1 text-xs font-medium text-green-700 bg-green-100 rounded whitespace-nowrap">
                                                        ✅ Sudah
                                                    </span>
                                                    @if ($report->validator)
                                                        <p class="text-xs text-gray-500 truncate max-w-[100px]"
                                                            title="oleh {{ $report->validator->name }}">
                                                            oleh {{ $report->validator->name }}
                                                        </p>
                                                        <p class="text-xs text-gray-400 whitespace-nowrap">
                                                            {{ $report->validated_at->format('d M Y H:i') }}
                                                        </p>
                                                    @endif
                                                </div>
                                            @else
                                                <span
                                                    class="inline-flex px-2 py-1 text-xs font-medium text-orange-700 bg-orange-100 rounded whitespace-nowrap">
                                                    ⏳ Belum
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-3 whitespace-nowrap">
                                            @if (!$report->validasi_manager)
                                                <form
                                                    action="{{ route('daily-reports.manager.validate', $report->id) }}"
                                                    method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit"
                                                        onclick="return confirm('Validasi laporan ini?')"
                                                        class="inline-flex items-center gap-1 px-3 py-1 text-xs font-medium text-white transition bg-green-600 rounded hover:bg-green-700">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        Validasi
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-xs text-gray-400">-</span>
                                            @endif
                                        </td>
                                    </tr>

                                    {{-- Hidden Photo Modal Data --}}
                                    <div id="photos-{{ $report->id }}" class="hidden">
                                        @foreach ($report->photos as $photo)
                                            <div class="photo-item"
                                                data-src="{{ asset('storage/' . $photo->file_path) }}"
                                                data-caption="{{ $photo->keterangan ?? 'Foto ' . $loop->iteration }}">
                                            </div>
                                        @endforeach
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="10" class="px-3 py-8 text-center text-gray-500">
                                            Tidak ada laporan yang ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <div class="mt-6">
                            {{ $reports->withQueryString()->links() }}
                        </div>
                    </div>

                    {{-- INFO BOX --}}
                    @if ($reports->where('validasi_manager', false)->count() > 0)
                        <div class="p-4 mt-6 rounded-lg bg-orange-50">
                            <p class="text-sm text-orange-800">
                                💡 <strong>Tips:</strong> Klik tombol "Validasi" untuk menyetujui laporan. Setelah
                                divalidasi, laporan tidak bisa diedit oleh FO/Staff.
                            </p>
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>

    {{-- <div id="photoModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-75">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative w-full max-w-4xl bg-white rounded-lg">
                <button onclick="closePhotoModal()"
                    class="absolute z-10 p-2 text-white bg-black bg-opacity-50 rounded-full top-4 right-4 hover:bg-opacity-75">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <div class="p-6">
                    <h3 class="mb-4 text-lg font-semibold text-gray-800">Bukti Foto Laporan</h3>
                    <div id="photoModalContent" class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showPhotos(reportId) {
            const photosDiv = document.getElementById(`photos-${reportId}`);
            const photoItems = photosDiv.querySelectorAll('.photo-item');
            const modalContent = document.getElementById('photoModalContent');

            // Clear previous content
            modalContent.innerHTML = '';

            // Add photos to modal
            photoItems.forEach((item, index) => {
                const src = item.getAttribute('data-src');
                const caption = item.getAttribute('data-caption');

                const photoDiv = document.createElement('div');
                photoDiv.className = 'relative';
                photoDiv.innerHTML = `
                    <img src="${src}" alt="Foto ${index + 1}"
                        class="object-cover w-full h-64 rounded-lg cursor-pointer"
                        onclick="window.open('${src}', '_blank')">
                    <div class="absolute top-2 left-2">
                        <span class="px-2 py-1 text-xs text-white bg-black bg-opacity-50 rounded">
                            Foto ${index + 1}
                        </span>
                    </div>
                    ${caption ? `<p class="mt-2 text-sm text-gray-600">${caption}</p>` : ''}
                `;

                modalContent.appendChild(photoDiv);
            });

            // Show modal
            document.getElementById('photoModal').classList.remove('hidden');
        };

        function closePhotoModal() {
            document.getElementById('photoModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('photoModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closePhotoModal();
            }
        });

        // Close modal with ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closePhotoModal();
            }
        });
    </script> --}}
    {{-- Photo Modal --}}
    <div id="photoModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-90">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative w-full max-w-6xl max-h-screen overflow-y-auto bg-white rounded-lg">
                {{-- Close Button --}}
                <button onclick="closePhotoModal()"
                    class="sticky z-20 float-right p-2 text-white bg-red-600 rounded-full shadow-lg top-4 right-4 hover:bg-red-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                {{-- Modal Content --}}
                <div class="p-6">
                    <h3 class="mb-4 text-lg font-semibold text-gray-800">Bukti Foto Laporan</h3>
                    <div id="photoModalContent" class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        {{-- Photos will be inserted here --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Photo Modal Script --}}
    <script>
        function showPhotos(reportId) {
            const photosDiv = document.getElementById(`photos-${reportId}`);
            const photoItems = photosDiv.querySelectorAll('.photo-item');
            const modalContent = document.getElementById('photoModalContent');

            // Clear previous content
            modalContent.innerHTML = '';

            // Add photos to modal
            photoItems.forEach((item, index) => {
                const src = item.getAttribute('data-src');
                const caption = item.getAttribute('data-caption');

                const photoDiv = document.createElement('div');
                photoDiv.className = 'relative group';
                photoDiv.innerHTML = `
                <div class="relative overflow-hidden transition border-2 border-gray-200 rounded-lg hover:border-teal-500">
                    <img src="${src}" alt="Foto ${index + 1}"
                        class="w-full h-auto transition-transform cursor-pointer group-hover:scale-105"
                        onclick="openImageFullscreen('${src}')">
                    <div class="absolute top-2 left-2">
                        <span class="px-3 py-1 text-sm font-medium text-white bg-black rounded-full bg-opacity-70">
                            📷 Foto ${index + 1}
                        </span>
                    </div>
                    <div class="absolute bottom-0 left-0 right-0 p-3 transition opacity-0 bg-gradient-to-t from-black to-transparent group-hover:opacity-100">
                        <button onclick="openImageFullscreen('${src}')" class="text-sm text-white hover:text-teal-300">
                            🔍 Lihat Fullscreen
                        </button>
                    </div>
                </div>
                ${caption ? `<p class="px-2 mt-2 text-sm text-gray-600">${caption}</p>` : ''}
            `;

                modalContent.appendChild(photoDiv);
            });

            // Show modal
            document.getElementById('photoModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Prevent body scroll
        }

        function closePhotoModal() {
            document.getElementById('photoModal').classList.add('hidden');
            document.body.style.overflow = 'auto'; // Restore body scroll
        }

        function openImageFullscreen(src) {
            window.open(src, '_blank');
        }

        // Close modal when clicking outside
        document.getElementById('photoModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closePhotoModal();
            }
        });

        // Close modal with ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closePhotoModal();
            }
        });
    </script>

</x-app-layout>
{{-- ```

---

## ✅ **SEMUA VIEWS SUDAH LENGKAP!**

### **Recap - File Structure:**
```
resources/views/daily-reports/
├── index.blade.php ✅ Dashboard FO/Staff
├── create.blade.php ✅ Form input laporan baru
├── edit.blade.php ✅ Form edit laporan
└── manager/
├── dashboard.blade.php ✅ Dashboard Manager
└── reports.blade.php ✅ List laporan untuk validasi --}}
