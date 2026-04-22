<x-app-layout>
    <x-slot name="header">
        <div class="grid items-center justify-between grid-cols-1 gap-4 md:grid-cols-2">
            <div class="">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Manajemen Presensi Karyawan
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Kelola data presensi karyawan Anda di sini.
                </p>
            </div>

            <div class="flex justify-start gap-3 md:justify-end">
                {{-- Tombol Export --}}
                <button onclick="document.getElementById('modalExportAll').classList.remove('hidden')"
                    class="flex items-center gap-2 px-4 py-2 text-green-600 transition bg-green-100 rounded-lg hover:bg-green-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                    </svg>
                    Export Excel
                </button>

                {{-- Tombol Import --}}
                <button onclick="openImportModal()"
                    class="flex items-center gap-2 px-4 py-2 text-teal-600 transition bg-teal-100 rounded-lg hover:bg-teal-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Import Presensi
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            {{-- Alert --}}
            @if (session('success'))
                <div class="p-4 mb-4 text-green-700 bg-green-100 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="mb-6 text-lg font-semibold text-gray-800">
                        Daftar Presensi
                    </h3>
                    <form method="GET" class="flex flex-wrap gap-4 mb-6">

                        {{-- PER PAGE --}}
                        <div>
                            <select name="per_page" onchange="this.form.submit()"
                                class="px-4 py-2 pr-10 border rounded-lg">
                                @foreach ([10, 25, 50, 100] as $size)
                                    <option value="{{ $size }}"
                                        {{ request('per_page', 10) == $size ? 'selected' : '' }}>
                                        {{ $size }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- STATUS PRESENSI --}}
                        <div>
                            <select name="status_presensi" onchange="this.form.submit()"
                                class="px-4 py-2 pr-10 border rounded-lg">
                                <option value="">Semua Status</option>
                                <option value="LENGKAP" {{ request('status_presensi') == 'LENGKAP' ? 'selected' : '' }}>
                                    Lengkap
                                </option>
                                <option value="TIDAK_LENGKAP"
                                    {{ request('status_presensi') == 'TIDAK_LENGKAP' ? 'selected' : '' }}>
                                    Tidak Lengkap
                                </option>
                                <option value="BELUM_ABSEN"
                                    {{ request('status_presensi') == 'BELUM_ABSEN' ? 'selected' : '' }}>
                                    Belum Absen
                                </option>
                                <option value="IZIN_CUTI"
                                    {{ request('status_presensi') == 'IZIN_CUTI' ? 'selected' : '' }}>
                                    Izin/Cuti
                                </option>
                                <option value="SAKIT" {{ request('status_presensi') == 'SAKIT' ? 'selected' : '' }}>
                                    Sakit
                                </option>
                            </select>
                        </div>

                        {{-- TANGGAL --}}
                        <div>
                            <input type="date" name="tanggal" value="{{ request('tanggal', $tanggal) }}"
                                onchange="this.form.submit()" class="px-4 py-2 border rounded-lg">
                        </div>

                        {{-- SEARCH --}}
                        <div class="relative flex-1 min-w-[250px]">
                            {{-- <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari nama karyawan..." class="w-full px-4 py-2 border rounded-lg"> --}}
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari nama karyawan..." oninput="debounceSearch(this)"
                                class="w-full py-2 pl-10 pr-4 border border-gray-300 rounded-lg focus:ring-teal-500" />
                        </div>

                        {{-- <button type="submit" class="px-4 py-2 text-white bg-teal-600 rounded-lg hover:bg-teal-700">
                            Cari
                        </button> --}}
                    </form>

                    <div class="relative w-full overflow-x-auto custom-scrollbar">
                        <table class="w-full text-sm min-w-max whitespace-nowrap">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="px-4 py-4 min-w-[180px] text-left text-gray-600 uppercase">Nama</th>
                                    <th class="px-4 py-4 min-w-[140px] text-left text-gray-600 uppercase">Status</th>
                                    <th class="px-4 py-4 min-w-[320px] text-left text-gray-600 uppercase">Jam Presensi
                                    </th>
                                    <th class="px-4 py-4 min-w-[160px] text-left text-gray-600 uppercase">Telat</th>
                                    <th class="px-4 py-4 min-w-[80px] text-left text-gray-600 uppercase">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($users as $row)
                                    <tr class="border-t">
                                        <td class="px-4 py-3">{{ $row->name ?? '-' }}</td>
                                        <td class="px-4 py-3">
                                            @if ($row->presensi_status === 'LENGKAP')
                                                <span
                                                    class="px-2 py-1 text-green-700 bg-green-100 rounded">Lengkap</span>
                                            @elseif ($row->presensi_status === 'SAKIT')
                                                <span class="px-2 py-1 text-red-700 bg-red-100 rounded">Sakit</span>
                                            @elseif ($row->presensi_status === 'IZIN_CUTI')
                                                <span
                                                    class="px-2 py-1 text-blue-700 bg-blue-100 rounded">Izin/Cuti</span>
                                            @elseif ($row->presensi_status === 'BELUM_ABSEN')
                                                <span class="px-2 py-1 text-gray-700 bg-gray-200 rounded">Belum
                                                    Absen</span>
                                            @else
                                                <span class="px-2 py-1 text-yellow-700 bg-yellow-100 rounded">Tidak
                                                    Lengkap</span>
                                            @endif
                                        </td>

                                        <td class="px-4 py-3 text-xs whitespace-nowrap">
                                            CI: {{ $row->presensi_jam['CHECK_IN'] ?? '-' }} |
                                            IO: {{ $row->presensi_jam['ISTIRAHAT_OUT'] ?? '-' }} |
                                            II: {{ $row->presensi_jam['ISTIRAHAT_IN'] ?? '-' }} |
                                            CO: {{ $row->presensi_jam['CHECK_OUT'] ?? '-' }}
                                        </td>

                                        <td class="px-4 py-3">
                                            @if (count($row->presensi_telat))
                                                <span class="text-red-600">
                                                    {{ implode(', ', $row->presensi_telat) }}
                                                </span>
                                            @else
                                                -
                                            @endif
                                        </td>

                                        <td class="px-4 py-3">
                                            <div class="relative inline-block text-left">
                                                <button type="button" onclick="toggleDropdown({{ $row->id }})"
                                                    class="text-gray-400 hover:text-gray-600">
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path
                                                            d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                                    </svg>
                                                </button>

                                                <div id="dropdown-{{ $row->id }}"
                                                    class="fixed z-50 hidden w-48 bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5">
                                                    <div class="py-1">
                                                        <a href="{{ route('presensi.show', $row->id) }}"
                                                            class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                            </svg>
                                                            Detail
                                                        </a>

                                                        <form action="{{ route('presensi.izin', $row->id) }}"
                                                            method="POST" class="inline">
                                                            @csrf
                                                            <input type="hidden" name="tanggal"
                                                                value="{{ request('tanggal', now()->toDateString()) }}">
                                                            <button type="submit"
                                                                onclick="return confirm('Yakin input izin/cuti untuk {{ $row->name }}?')"
                                                                class="flex items-center w-full gap-2 px-4 py-2 text-sm text-left text-gray-700 hover:bg-gray-100">
                                                                <svg class="w-4 h-4" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                                </svg>
                                                                Cuti/Izin
                                                            </button>
                                                        </form>

                                                        <form action="{{ route('presensi.sakit', $row->id) }}"
                                                            method="POST" class="inline">
                                                            @csrf
                                                            <input type="hidden" name="tanggal"
                                                                value="{{ request('tanggal', now()->toDateString()) }}">
                                                            <button type="submit"
                                                                onclick="return confirm('Yakin input sakit untuk {{ $row->name }}?')"
                                                                class="flex items-center w-full gap-2 px-4 py-2 text-sm text-left text-gray-700 hover:bg-gray-100">
                                                                <svg class="w-4 h-4" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                                                </svg>
                                                                Sakit
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="mt-6">
                            {{ $users->withQueryString()->links() }}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- =====================================================
         MODAL EXPORT ALL
    ====================================================== --}}
    <div id="modalExportAll"
        class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black/50 backdrop-blur-sm">
        <div class="w-full max-w-md p-6 mx-4 bg-white shadow-2xl rounded-2xl">

            {{-- Header Modal --}}
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-lg font-bold text-gray-800">Export Presensi</h3>
                <button type="button" onclick="closeExportModal()"
                    class="text-gray-400 transition hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Form Input Tanggal --}}
            <div id="exportFormArea">
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Tanggal Mulai</label>
                        <input type="date" id="exportStartDate"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                            value="{{ now()->startOfWeek()->toDateString() }}" max="{{ now()->toDateString() }}">
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Tanggal Akhir</label>
                        <input type="date" id="exportEndDate"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                            value="{{ now()->toDateString() }}" max="{{ now()->toDateString() }}">
                    </div>
                </div>

                <p class="mb-4 text-xs text-gray-400">
                    <svg class="inline w-3 h-3 mr-1 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                    Maksimal range 7 hari. Hari tanpa absensi otomatis tercatat sebagai "Tidak Absen".
                </p>

                <button type="button" onclick="startExportAll()"
                    class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2.5 rounded-lg transition text-sm">
                    Mulai Export
                </button>
            </div>

            {{-- Progress Area --}}
            <div id="exportProgressArea" class="hidden">
                <div class="mb-4 text-center">
                    <div class="inline-flex items-center justify-center w-12 h-12 mb-3 bg-green-100 rounded-full">
                        <svg id="progressIcon" class="w-6 h-6 text-green-600 animate-spin" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4" />
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                        </svg>
                    </div>
                    <p id="progressLabel" class="text-sm font-medium text-gray-700">Memproses data...</p>
                </div>

                <div class="w-full h-3 mb-2 overflow-hidden bg-gray-200 rounded-full">
                    <div id="progressBar" class="h-3 transition-all duration-300 bg-green-500 rounded-full"
                        style="width: 0%"></div>
                </div>
                <p id="progressPercent" class="mb-4 text-xs text-center text-gray-500">0%</p>

                {{-- Tombol Download --}}
                <div id="downloadArea" class="hidden">
                    <a id="downloadLink" href="#"
                        class="flex items-center justify-center gap-2 w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 rounded-lg transition text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Download File Excel
                    </a>
                    <button type="button" onclick="resetExportModal()"
                        class="w-full mt-2 text-sm text-center text-gray-500 hover:text-gray-700">
                        Export lagi
                    </button>
                </div>

                {{-- Error area --}}
                <div id="exportErrorArea" class="hidden">
                    <p id="exportErrorMsg" class="mb-3 text-sm text-center text-red-600">Terjadi kesalahan.</p>
                    <button type="button" onclick="resetExportModal()"
                        class="w-full text-sm text-center text-gray-500 hover:text-gray-700">
                        Coba lagi
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- =====================================================
         MODAL IMPORT (tidak diubah dari versi asli)
    ====================================================== --}}
    <div id="importModal" class="fixed inset-0 z-50 hidden w-full h-full overflow-y-auto bg-gray-600 bg-opacity-50">
        <div class="relative w-11/12 p-5 mx-auto bg-white border rounded-lg shadow-lg top-20 md:w-2/3 lg:w-1/2">
            <div class="flex items-center justify-between pb-3 mb-6 border-b">
                <div>
                    <h3 class="text-xl font-semibold text-gray-800">Import Presensi</h3>
                    <p class="mt-1 text-sm text-gray-500">Unggah file untuk menambahkan data presensi secara massal</p>
                </div>
                <button id="closeModal" type="button" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="importForm" action="{{ route('presensi.import') }}" method="POST"
                enctype="multipart/form-data">
                @csrf

                <div class="mb-6">
                    <div class="mb-6">
                        <h4 class="mb-3 font-medium text-gray-700">Format File yang Didukung</h4>
                        <div class="grid grid-cols-1 gap-4 mb-4">
                            <div class="p-4 border border-gray-200 rounded-lg">
                                <div class="flex items-center mb-2">
                                    <div
                                        class="flex items-center justify-center w-10 h-10 mr-3 bg-green-100 rounded-full">
                                        <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z">
                                            </path>
                                            <polyline points="14 2 14 8 20 8"></polyline>
                                            <line x1="16" y1="13" x2="8" y2="13">
                                            </line>
                                            <line x1="16" y1="17" x2="8" y2="17">
                                            </line>
                                            <polyline points="10 9 9 9 8 9"></polyline>
                                        </svg>
                                    </div>
                                    <div>
                                        <h5 class="font-semibold text-gray-800">Excel (.xlsx, .xls)</h5>
                                        <p class="text-sm text-gray-500">Microsoft Excel</p>
                                    </div>
                                </div>
                                <ul class="space-y-1 text-sm text-gray-600 ml-13">
                                    <li>• Format: Kolom sesuai template</li>
                                    <li>• Maksimal 10MB</li>
                                    <a href="{{ route('presensi.template') }}"
                                        class="inline-block px-5 py-2 mt-2 text-white transition bg-teal-500 rounded-lg hover:bg-teal-600">
                                        Download Template
                                    </a>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h4 class="mb-3 font-medium text-gray-700">Unggah File</h4>
                        <div id="dropArea"
                            class="p-8 text-center transition border-2 border-gray-300 border-dashed rounded-lg cursor-pointer hover:border-teal-500">
                            <div
                                class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                            </div>
                            <p class="mb-2 text-gray-700">Drag & drop file di sini atau klik untuk memilih</p>
                            <p class="mb-4 text-sm text-gray-500">Format yang didukung: .xlsx, .xls</p>
                            <button type="button" id="browseButton"
                                class="px-4 py-2 text-white transition bg-teal-500 rounded-lg hover:bg-teal-600">
                                Pilih File
                            </button>
                            <input type="file" name="file" id="fileInput" class="hidden" accept=".xlsx,.xls"
                                required>
                        </div>
                        <div id="fileName" class="hidden mt-3 text-sm text-gray-600">
                            File terpilih: <span class="font-medium" id="selectedFileName"></span>
                        </div>
                        <div id="fileError" class="hidden mt-2 text-sm text-red-600"></div>
                    </div>

                    <div id="progressContainer" class="hidden mb-6">
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Mengunggah...</span>
                            <span id="importProgressPercent" class="text-sm font-medium text-gray-700">0%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div id="importProgressBar" class="bg-teal-600 h-2.5 rounded-full" style="width: 0%">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t">
                    <button type="button" id="cancelButton"
                        class="px-5 py-2 text-gray-700 transition border border-gray-300 rounded-lg hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" id="submitButton"
                        class="px-5 py-2 text-white transition bg-teal-500 rounded-lg hover:bg-teal-600 disabled:opacity-50 disabled:cursor-not-allowed"
                        disabled>
                        Import Data
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let searchTimer;

        function debounceSearch(input) {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                input.closest('form').submit();
            }, 400); // tunggu 400ms setelah user berhenti ketik
        };
        // =====================================================
        // EXPORT ALL - LOGIC (tanpa polling, fake progress bar)
        // =====================================================
        let fakePbInterval = null;

        function closeExportModal() {
            document.getElementById('modalExportAll').classList.add('hidden');
            resetExportModal();
        }

        function resetExportModal() {
            document.getElementById('exportFormArea').classList.remove('hidden');
            document.getElementById('exportProgressArea').classList.add('hidden');
            document.getElementById('downloadArea').classList.add('hidden');
            document.getElementById('exportErrorArea').classList.add('hidden');
            document.getElementById('progressBar').style.width = '0%';
            document.getElementById('progressPercent').textContent = '0%';
            document.getElementById('progressLabel').textContent = 'Memproses data...';

            const icon = document.getElementById('progressIcon');
            icon.classList.add('animate-spin');
            icon.innerHTML = `
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
            `;

            if (fakePbInterval) {
                clearInterval(fakePbInterval);
                fakePbInterval = null;
            }
        }

        function startExportAll() {
            const startDate = document.getElementById('exportStartDate').value;
            const endDate = document.getElementById('exportEndDate').value;

            if (!startDate || !endDate) {
                alert('Harap isi tanggal mulai dan akhir.');
                return;
            }
            const diffDays = (new Date(endDate) - new Date(startDate)) / (1000 * 60 * 60 * 24);
            if (diffDays > 7) {
                alert('Maksimal range export adalah 7 hari.');
                return;
            }
            if (diffDays < 0) {
                alert('Tanggal akhir harus setelah tanggal mulai.');
                return;
            }

            // Tampilkan area progress
            document.getElementById('exportFormArea').classList.add('hidden');
            document.getElementById('exportProgressArea').classList.remove('hidden');

            // Jalankan fake progress bar (animasi saja, berhenti di 85% sambil tunggu server)
            let fakePercent = 0;
            const labels = ['Mengambil data karyawan...', 'Memproses absensi...', 'Membuat file Excel...'];
            fakePbInterval = setInterval(() => {
                if (fakePercent < 85) {
                    fakePercent += Math.random() * 6;
                    fakePercent = Math.min(fakePercent, 85);
                    document.getElementById('progressBar').style.width = fakePercent + '%';
                    document.getElementById('progressPercent').textContent = Math.round(fakePercent) + '%';
                    document.getElementById('progressLabel').textContent =
                        fakePercent < 30 ? labels[0] : fakePercent < 65 ? labels[1] : labels[2];
                }
            }, 300);

            // Kirim request ke server
            fetch('{{ route('presensi.export.all') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        start_date: startDate,
                        end_date: endDate
                    }),
                })
                .then(res => res.json())
                .then(data => {
                    clearInterval(fakePbInterval);

                    if (!data.success) {
                        showExportError(data.message);
                        return;
                    }

                    // Langsung 100% dan tampilkan tombol download
                    document.getElementById('progressBar').style.width = '100%';
                    document.getElementById('progressPercent').textContent = '100%';
                    document.getElementById('progressLabel').textContent = 'Selesai!';

                    setTimeout(() => showDownloadButton(data.download_url), 400);
                })
                .catch(() => {
                    clearInterval(fakePbInterval);
                    showExportError('Gagal terhubung ke server.');
                });
        }

        function showDownloadButton(downloadUrl) {
            const icon = document.getElementById('progressIcon');
            icon.classList.remove('animate-spin');
            icon.innerHTML =
                `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" stroke="currentColor" fill="none"/>`;

            document.getElementById('progressLabel').textContent = 'File siap didownload!';
            document.getElementById('downloadLink').href = downloadUrl;
            document.getElementById('downloadArea').classList.remove('hidden');
        }

        function showExportError(message) {
            document.getElementById('exportErrorMsg').textContent = message || 'Terjadi kesalahan.';
            document.getElementById('exportErrorArea').classList.remove('hidden');
            if (fakePbInterval) {
                clearInterval(fakePbInterval);
                fakePbInterval = null;
            }
        }

        // Tutup modal export saat klik backdrop
        document.getElementById('modalExportAll').addEventListener('click', function(e) {
            if (e.target === this) closeExportModal();
        });

        // =====================================================
        // IMPORT - LOGIC (tidak diubah dari versi asli)
        // =====================================================
        function openImportModal() {
            document.getElementById('importModal').classList.remove('hidden');
        }

        function closeImportModal() {
            document.getElementById('importModal').classList.add('hidden');
            resetImportForm();
        }

        function resetImportForm() {
            document.getElementById('importForm').reset();
            document.getElementById('fileName').classList.add('hidden');
            document.getElementById('fileError').classList.add('hidden');
            document.getElementById('submitButton').disabled = true;
            document.getElementById('progressContainer').classList.add('hidden');
            document.getElementById('importProgressBar').style.width = '0%';
            document.getElementById('importProgressPercent').textContent = '0%';
            document.getElementById('submitButton').textContent = 'Import Data';
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('closeModal').addEventListener('click', closeImportModal);
            document.getElementById('cancelButton').addEventListener('click', closeImportModal);
            document.getElementById('browseButton').addEventListener('click', function() {
                document.getElementById('fileInput').click();
            });

            const dropArea = document.getElementById('dropArea');
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(e => {
                dropArea.addEventListener(e, ev => {
                    ev.preventDefault();
                    ev.stopPropagation();
                });
            });
            ['dragenter', 'dragover'].forEach(e => dropArea.addEventListener(e, () => dropArea.classList.add(
                'border-teal-500', 'bg-teal-50')));
            ['dragleave', 'drop'].forEach(e => dropArea.addEventListener(e, () => dropArea.classList.remove(
                'border-teal-500', 'bg-teal-50')));
            dropArea.addEventListener('drop', e => handleFiles(e.dataTransfer.files));
            document.getElementById('fileInput').addEventListener('change', function() {
                handleFiles(this.files);
            });

            function handleFiles(files) {
                const fileError = document.getElementById('fileError');
                fileError.classList.add('hidden');
                if (!files.length) return;

                const file = files[0];
                if (!/(\.xlsx|\.xls)$/i.test(file.name)) {
                    fileError.textContent = 'Format file tidak didukung. Harap unggah file Excel.';
                    fileError.classList.remove('hidden');
                    return;
                }
                if (file.size > 10 * 1024 * 1024) {
                    fileError.textContent = 'Ukuran file terlalu besar. Maksimal 10MB.';
                    fileError.classList.remove('hidden');
                    return;
                }
                document.getElementById('selectedFileName').textContent = file.name;
                document.getElementById('fileName').classList.remove('hidden');
                document.getElementById('submitButton').disabled = false;
            }

            document.getElementById('importForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const submitButton = document.getElementById('submitButton');

                if (!document.getElementById('fileInput').files.length) {
                    showImportNotification('error', 'Silakan pilih file terlebih dahulu.');
                    return;
                }

                document.getElementById('progressContainer').classList.remove('hidden');
                submitButton.disabled = true;
                submitButton.textContent = 'Mengimport...';

                const xhr = new XMLHttpRequest();
                xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        const pct = Math.round((e.loaded / e.total) * 90);
                        document.getElementById('importProgressBar').style.width = pct + '%';
                        document.getElementById('importProgressPercent').textContent = pct + '%';
                    }
                });
                xhr.addEventListener('load', function() {
                    if (xhr.status === 200) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.success) {
                                document.getElementById('importProgressBar').style.width = '100%';
                                document.getElementById('importProgressPercent').textContent =
                                    '100%';
                                showImportNotification('success', response.message ||
                                    'Import berhasil!');
                                setTimeout(() => {
                                    closeImportModal();
                                    setTimeout(() => window.location.reload(), 500);
                                }, 1500);
                            } else {
                                showImportNotification('error', response.message ||
                                    'Terjadi kesalahan saat import.');
                                submitButton.disabled = false;
                                submitButton.textContent = 'Import Data';
                            }
                        } catch (e) {
                            showImportNotification('error', 'Format response tidak valid.');
                            submitButton.disabled = false;
                            submitButton.textContent = 'Import Data';
                        }
                    } else {
                        showImportNotification('error',
                            `Terjadi kesalahan (Status: ${xhr.status}).`);
                        submitButton.disabled = false;
                        submitButton.textContent = 'Import Data';
                    }
                });
                xhr.addEventListener('error', () => {
                    showImportNotification('error', 'Terjadi kesalahan jaringan.');
                    submitButton.disabled = false;
                    submitButton.textContent = 'Import Data';
                });
                xhr.open('POST', this.action, true);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]')
                    .getAttribute('content'));
                xhr.send(formData);
            });

            document.getElementById('importModal').addEventListener('click', function(e) {
                if (e.target === this) closeImportModal();
            });
        });

        function showImportNotification(type, message) {
            document.querySelectorAll('.import-notification').forEach(n => n.remove());
            const notification = document.createElement('div');
            notification.className =
                `import-notification fixed top-4 right-4 z-[9999] px-6 py-3 rounded-lg shadow-lg ${type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'}`;
            notification.innerHTML =
                `<div class="flex items-center gap-3"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">${type === 'success' ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>' : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>'}</svg><span>${message}</span></div>`;
            document.body.appendChild(notification);
            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => notification.remove(), 300);
            }, 5000);
        }

        // =====================================================
        // DROPDOWN - SHARED
        // =====================================================
        function toggleDropdown(id) {
            event.stopPropagation();
            const button = event.currentTarget;
            const dropdown = document.getElementById(`dropdown-${id}`);

            document.querySelectorAll('[id^="dropdown-"]').forEach(el => {
                if (el !== dropdown) el.classList.add('hidden');
            });

            dropdown.classList.toggle('hidden');

            if (!dropdown.classList.contains('hidden')) {
                const rect = button.getBoundingClientRect();
                dropdown.style.top = `${rect.bottom + 8}px`;
                dropdown.style.left = `${rect.right - dropdown.offsetWidth}px`;
            }
        }

        document.addEventListener('click', () => {
            document.querySelectorAll('[id^="dropdown-"]').forEach(el => el.classList.add('hidden'));
        });

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                closeExportModal();
                closeImportModal();
            }
        });
    </script>

</x-app-layout>
