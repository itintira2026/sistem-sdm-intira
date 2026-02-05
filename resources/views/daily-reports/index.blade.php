<x-app-layout>
    <x-slot name="header">
        <div class="grid items-center justify-between grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Laporan Harian Cabang
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Wajib 2 laporan per hari (Shift Pagi & Shift Siang)
                </p>
            </div>

            <div class="flex justify-start gap-3 md:justify-end">
                <a href="{{ route('daily-reports.create') }}"
                    class="flex items-center gap-2 px-4 py-2 text-white transition bg-teal-600 rounded-lg hover:bg-teal-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Input Laporan Baru
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

            {{-- Alert Error --}}
            @if (session('error'))
                <div class="p-4 mb-6 text-red-700 bg-red-100 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            {{-- 📊 PROGRESS LAPORAN HARI INI --}}
            <div class="p-6 mb-6 bg-white rounded-lg shadow-sm">
                <h3 class="mb-4 text-lg font-semibold text-gray-800">Progress Laporan Hari Ini</h3>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                    {{-- Progress Card --}}
                    <div class="p-4 rounded-lg bg-teal-50">
                        <p class="text-sm text-teal-700">Total Laporan</p>
                        <p class="mt-2 text-3xl font-bold text-teal-800">
                            {{ $stats['total_hari_ini'] }}<span
                                class="text-lg text-teal-400">/{{ $stats['target'] }}</span>
                        </p>
                        <div class="w-full h-2 mt-3 bg-gray-200 rounded-full">
                            <div class="h-2 bg-teal-600 rounded-full"
                                style="width: {{ min(($stats['total_hari_ini'] / $stats['target']) * 100, 100) }}%">
                            </div>
                        </div>
                    </div>

                    {{-- Shift Pagi --}}
                    <div class="p-4 rounded-lg {{ $stats['shift_pagi'] ? 'bg-green-50' : 'bg-orange-50' }}">
                        <p class="text-sm {{ $stats['shift_pagi'] ? 'text-green-700' : 'text-orange-700' }}">
                            Shift Pagi (08:00 - 16:00)
                        </p>
                        <p
                            class="mt-2 text-2xl font-bold {{ $stats['shift_pagi'] ? 'text-green-800' : 'text-orange-800' }}">
                            @if ($stats['shift_pagi'])
                                ✅ Sudah
                            @else
                                ⏳ Belum
                            @endif
                        </p>
                    </div>

                    {{-- Shift Siang --}}
                    <div class="p-4 rounded-lg {{ $stats['shift_siang'] ? 'bg-green-50' : 'bg-orange-50' }}">
                        <p class="text-sm {{ $stats['shift_siang'] ? 'text-green-700' : 'text-orange-700' }}">
                            Shift Siang (14:00 - 22:00)
                        </p>
                        <p
                            class="mt-2 text-2xl font-bold {{ $stats['shift_siang'] ? 'text-green-800' : 'text-orange-800' }}">
                            @if ($stats['shift_siang'])
                                ✅ Sudah
                            @else
                                ⏳ Belum
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Status Validasi --}}
                <div class="grid grid-cols-2 gap-4 mt-4">
                    <div class="p-3 rounded-lg bg-green-50">
                        <p class="text-xs text-green-700">✅ Sudah Divalidasi</p>
                        <p class="text-xl font-bold text-green-800">{{ $stats['validated'] }}</p>
                    </div>
                    <div class="p-3 rounded-lg bg-orange-50">
                        <p class="text-xs text-orange-700">⏳ Belum Divalidasi</p>
                        <p class="text-xl font-bold text-orange-800">{{ $stats['pending'] }}</p>
                    </div>
                </div>
            </div>

            {{-- 🔥 STATISTIK PENCAIRAN & PELUNASAN --}}
            <div class="grid grid-cols-1 gap-6 mb-6 md:grid-cols-2">
                {{-- PENCAIRAN --}}
                <div class="p-6 bg-white rounded-lg shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">📥 Total Pencairan Hari Ini</h3>
                        <div class="p-3 bg-green-100 rounded-full">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                            </svg>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 rounded-lg bg-green-50">
                            <span class="text-sm font-medium text-green-700">Jumlah Barang:</span>
                            <span class="text-xl font-bold text-green-800">
                                {{ number_format($stats['total_pencairan_barang']) }} unit
                            </span>
                        </div>
                        <div class="flex items-center justify-between p-3 rounded-lg bg-green-50">
                            <span class="text-sm font-medium text-green-700">Total Nominal:</span>
                            <span class="text-xl font-bold text-green-800">
                                Rp {{ number_format($stats['total_pencairan_nominal'], 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- PELUNASAN --}}
                <div class="p-6 bg-white rounded-lg shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">📤 Total Pelunasan Hari Ini</h3>
                        <div class="p-3 bg-blue-100 rounded-full">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                            </svg>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 rounded-lg bg-blue-50">
                            <span class="text-sm font-medium text-blue-700">Jumlah Barang:</span>
                            <span class="text-xl font-bold text-blue-800">
                                {{ number_format($stats['total_pelunasan_barang']) }} unit
                            </span>
                        </div>
                        <div class="flex items-center justify-between p-3 rounded-lg bg-blue-50">
                            <span class="text-sm font-medium text-blue-700">Total Nominal:</span>
                            <span class="text-xl font-bold text-blue-800">
                                Rp {{ number_format($stats['total_pelunasan_nominal'], 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
                {{-- 🔥 STOK AKHIR --}}
                <div class="col-span-1 p-6 mb-6 bg-white rounded-lg shadow-sm md:col-span-2">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">📦 Stok Akhir</h3>
                        <div class="p-3 bg-purple-100 rounded-full">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="flex items-center justify-between p-4 rounded-lg bg-purple-50">
                            <div>
                                <p class="text-sm font-medium text-purple-700">Jumlah Barang</p>
                                <p class="mt-1 text-xs text-purple-600">Stok tersisa</p>
                            </div>
                            <p class="text-2xl font-bold text-purple-800">
                                {{ number_format($stokAkhir['jumlah_barang']) }} <span
                                    class="text-base text-purple-600">unit</span>
                            </p>
                        </div>

                        <div class="flex items-center justify-between p-4 rounded-lg bg-purple-50">
                            <div>
                                <p class="text-sm font-medium text-purple-700">Total Nominal</p>
                                <p class="mt-1 text-xs text-purple-600">Nilai stok</p>
                            </div>
                            <p class="text-2xl font-bold text-purple-800">
                                Rp {{ number_format($stokAkhir['nominal'], 0, ',', '.') }}
                            </p>
                        </div>
                    </div>

                    {{-- Info Box --}}
                    <div class="p-3 mt-4 rounded-lg bg-blue-50">
                        <p class="text-xs text-blue-800">
                            💡 <strong>Info:</strong> Stok Akhir = Stok Awal + Pencairan - Pelunasan (update otomatis
                            setiap input laporan)
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="mb-6 text-lg font-semibold text-gray-800">
                        Daftar Laporan
                    </h3>

                    {{-- 🔥 FILTER GABUNGAN --}}
                    <form method="GET" class="mb-6">
                        {{-- 🔥 SUPERADMIN & MANAGER: PILIH CABANG --}}
                        @if ((Auth::user()->hasRole('superadmin') || Auth::user()->hasRole('manager')) && isset($branchList))
                            <div
                                class="p-4 mb-4 rounded-lg {{ Auth::user()->hasRole('superadmin') ? 'bg-yellow-50' : 'bg-blue-50' }}">
                                <label class="block mb-2 text-sm font-medium text-gray-700">
                                    @if (Auth::user()->hasRole('superadmin'))
                                        🔐 Superadmin Mode: Lihat Laporan Cabang
                                    @else
                                        👨‍💼 Manager Mode: Lihat Laporan Cabang
                                    @endif
                                </label>
                                <select name="branch_id" onchange="this.form.submit()"
                                    class="w-full px-4 py-2 border rounded-lg">
                                    <option value="">-- Pilih Cabang --</option>
                                    @foreach ($branchList as $branch)
                                        <option value="{{ $branch->id }}"
                                            {{ request('branch_id', $selectedBranchId) == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        {{-- FILTER ROW --}}
                        <div class="flex flex-wrap gap-4">
                            {{-- PER PAGE --}}
                            <div>
                                <select name="per_page" onchange="this.form.submit()"
                                    class="px-4 py-2 pr-10 border rounded-lg">
                                    @foreach ([10, 25, 50] as $size)
                                        <option value="{{ $size }}"
                                            {{ request('per_page', 10) == $size ? 'selected' : '' }}>
                                            {{ $size }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

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

                            {{-- TANGGAL --}}
                            <div>
                                <input type="date" name="tanggal" value="{{ request('tanggal', $tanggal) }}"
                                    onchange="this.form.submit()" class="px-4 py-2 border rounded-lg">
                            </div>
                        </div>
                    </form>

                    {{-- TABLE --}}
                    <div class="relative w-full overflow-x-auto md:overflow-x-visible">
                        <table class="w-full text-sm min-w-max whitespace-nowrap">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="px-4 py-4 min-w-[50px] text-left text-gray-600 uppercase">#</th>
                                    <th class="px-4 py-4 min-w-[120px] text-left text-gray-600 uppercase">Tanggal</th>
                                    <th class="px-4 py-4 min-w-[100px] text-left text-gray-600 uppercase">Shift</th>
                                    <th class="px-4 py-4 min-w-[150px] text-left text-gray-600 uppercase">Pencairan
                                    </th>
                                    <th class="px-4 py-4 min-w-[150px] text-left text-gray-600 uppercase">Pelunasan
                                    </th>
                                    <th class="px-4 py-4 min-w-[80px] text-left text-gray-600 uppercase">Foto</th>
                                    <th class="px-4 py-4 min-w-[150px] text-left text-gray-600 uppercase">Stok Akhir
                                    </th>
                                    <th class="px-4 py-4 min-w-[100px] text-left text-gray-600 uppercase">Validasi</th>
                                    <th class="px-4 py-4 min-w-[100px] text-left text-gray-600 uppercase">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($reports as $index => $report)
                                    <tr class="border-t hover:bg-gray-50">
                                        <td class="px-4 py-3">
                                            {{ ($reports->currentPage() - 1) * $reports->perPage() + $index + 1 }}
                                        </td>
                                        <td class="px-4 py-3">
                                            {{ $report->tanggal->format('d M Y') }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <span
                                                class="px-2 py-1 text-xs rounded
                                                {{ $report->shift == 'pagi' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                                                {{ $report->shift_label }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="text-xs">
                                                <p class="font-medium text-gray-800">
                                                    {{ $report->pencairan_jumlah_barang }} unit</p>
                                                <p class="text-gray-600">{{ $report->pencairan_nominal_formatted }}
                                                </p>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="text-xs">
                                                <p class="font-medium text-gray-800">
                                                    {{ $report->pelunasan_jumlah_barang }} unit</p>
                                                <p class="text-gray-600">{{ $report->pelunasan_nominal_formatted }}
                                                </p>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 text-xs text-teal-700 bg-teal-100 rounded">
                                                📷 {{ $report->photos->count() }}
                                            </span>
                                        </td>
                                        <!-- Di tbody -->
                                        <td class="px-4 py-3">
                                            <div class="text-xs">
                                                <p class="font-medium text-purple-800">
                                                    {{ number_format($report->final_jumlah_barang) }} unit</p>
                                                <p class="text-purple-600">{{ $report->final_nominal_formatted }}</p>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            @if ($report->validasi_manager)
                                                <span class="px-2 py-1 text-xs text-green-700 bg-green-100 rounded">
                                                    ✅ Sudah
                                                </span>
                                            @else
                                                <span class="px-2 py-1 text-xs text-orange-700 bg-orange-100 rounded">
                                                    ⏳ Belum
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="relative inline-block text-left">
                                                <button type="button" onclick="toggleDropdown({{ $report->id }})"
                                                    class="text-gray-400 hover:text-gray-600">
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path
                                                            d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                                    </svg>
                                                </button>

                                                <div id="dropdown-{{ $report->id }}"
                                                    class="fixed z-50 hidden w-48 bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5">
                                                    <div class="py-1">
                                                        {{-- Edit --}}
                                                        @if (!$report->validasi_manager || Auth::user()->hasRole('superadmin') || Auth::user()->hasRole('manager'))
                                                            <a href="{{ route('daily-reports.edit', $report->id) }}"
                                                                class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                                <svg class="w-4 h-4" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                                </svg>
                                                                Edit
                                                            </a>
                                                        @endif

                                                        {{-- Delete --}}
                                                        @if (!$report->validasi_manager || Auth::user()->hasRole('superadmin') || Auth::user()->hasRole('manager'))
                                                            <form
                                                                action="{{ route('daily-reports.destroy', $report->id) }}"
                                                                method="POST" class="inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    onclick="return confirm('Yakin hapus laporan ini?')"
                                                                    class="flex items-center w-full gap-2 px-4 py-2 text-sm text-left text-red-700 hover:bg-gray-100">
                                                                    <svg class="w-4 h-4" fill="none"
                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                    </svg>
                                                                    Hapus
                                                                </button>
                                                            </form>
                                                        @endif

                                                        {{-- Locked --}}
                                                        @if ($report->validasi_manager && !Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('manager'))
                                                            <div class="px-4 py-2 text-sm text-gray-500">
                                                                🔒 Sudah Divalidasi
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                            Belum ada laporan yang diinput.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <div class="mt-6">
                            {{ $reports->withQueryString()->links() }}
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    {{-- Dropdown Script --}}
    <script>
        function toggleDropdown(id) {
            event.stopPropagation();

            const button = event.currentTarget;
            const dropdown = document.getElementById(`dropdown-${id}`);

            // Tutup dropdown lain
            document.querySelectorAll('[id^="dropdown-"]').forEach(el => {
                if (el !== dropdown) el.classList.add('hidden');
            });

            // Toggle
            dropdown.classList.toggle('hidden');

            if (!dropdown.classList.contains('hidden')) {
                const rect = button.getBoundingClientRect();
                dropdown.style.top = `${rect.bottom + 8}px`;
                dropdown.style.left = `${rect.right - dropdown.offsetWidth}px`;
            }
        }

        document.addEventListener('click', () => {
            document.querySelectorAll('[id^="dropdown-"]').forEach(el => {
                el.classList.add('hidden');
            });
        });
    </script>

</x-app-layout>
