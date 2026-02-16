<x-app-layout>
    <x-slot name="header">
        <div class="grid items-center justify-between grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Laporan Per 3 Jam Area Manager
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Wajib 3 laporan per hari (Pada Jam tertera di toleransi lambat mengisi laporan selama 1 jam di jam
                    terakhir)
                </p>
            </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            {{-- Alert Nasabah --}}
            @if (session('success_nasabah'))
                <div class="p-4 mb-6 text-blue-700 bg-blue-100 rounded-lg">
                    {{ session('success_nasabah') }}
                </div>
            @endif

            @if (session('error_nasabah'))
                <div class="p-4 mb-6 text-blue-700 bg-blue-100 rounded-lg">
                    {{ session('error_nasabah') }}
                </div>
            @endif


            {{-- Alert Revenue --}}
            @if (session('success_revenue'))
                <div class="p-4 mb-6 text-green-700 bg-green-100 rounded-lg">
                    {{ session('success_revenue') }}
                </div>
            @endif

            @if (session('error_revenue'))
                <div class="p-4 mb-6 text-red-700 bg-red-100 rounded-lg">
                    {{ session('error_revenue') }}
                </div>
            @endif


            {{-- Progress Card --}}
            @if (isset($stats))
                <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="mb-4 text-lg font-semibold text-gray-800">Progress Laporan Hari Ini</h3>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                            <div class="p-4 rounded-lg bg-teal-50">
                                <p class="text-sm text-teal-700">Total Laporan</p>
                                <p class="mt-2 text-3xl font-bold text-teal-800">
                                    {{ $stats['total_today'] }}<span
                                        class="text-lg text-teal-400">/{{ $stats['target'] }}</span>
                                </p>
                                <div class="w-full h-2 mt-3 bg-gray-200 rounded-full">
                                    <div class="h-2 bg-teal-600 rounded-full"
                                        style="width: {{ min(($stats['total_today'] / $stats['target']) * 100, 100) }}%">
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 rounded-lg {{ $stats['report_12'] ? 'bg-green-50' : 'bg-orange-50' }}">
                                <p class="text-sm {{ $stats['report_12'] ? 'text-green-700' : 'text-orange-700' }}">
                                    Laporan 12:00
                                </p>
                                <p
                                    class="mt-2 text-2xl font-bold {{ $stats['report_12'] ? 'text-green-800' : 'text-orange-800' }}">
                                    @if ($stats['report_12'])
                                        ✅ Sudah
                                    @else
                                        ⏳ Belum
                                    @endif
                                </p>
                            </div>

                            <div class="p-4 rounded-lg {{ $stats['report_16'] ? 'bg-green-50' : 'bg-orange-50' }}">
                                <p class="text-sm {{ $stats['report_16'] ? 'text-green-700' : 'text-orange-700' }}">
                                    Laporan 16:00
                                </p>
                                <p
                                    class="mt-2 text-2xl font-bold {{ $stats['report_16'] ? 'text-green-800' : 'text-orange-800' }}">
                                    @if ($stats['report_16'])
                                        ✅ Sudah
                                    @else
                                        ⏳ Belum
                                    @endif
                                </p>
                            </div>

                            <div class="p-4 rounded-lg {{ $stats['report_20'] ? 'bg-green-50' : 'bg-orange-50' }}">
                                <p class="text-sm {{ $stats['report_20'] ? 'text-green-700' : 'text-orange-700' }}">
                                    Laporan 20:00
                                </p>
                                <p
                                    class="mt-2 text-2xl font-bold {{ $stats['report_20'] ? 'text-green-800' : 'text-orange-800' }}">
                                    @if ($stats['report_20'])
                                        ✅ Sudah
                                    @else
                                        ⏳ Belum
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Form Input --}}
            <form action="{{ route('daily-reports.3hour-manager.store') }}" method="POST"
                enctype="multipart/form-data">
                @csrf

                <!-- Time Slots Card -->
                <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="mb-4 text-lg font-semibold text-gray-800">Pilih Waktu <span
                                class="text-red-500">*</span></h3>

                        <input type="hidden" name="time_slot" id="time_slot" required>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div class="p-4 transition rounded-lg cursor-pointer bg-gray-100 hover:bg-teal-100 time-slot {{ isset($stats) && $stats['report_12'] ? 'opacity-50 cursor-not-allowed' : '' }}"
                                onclick="selectTime(this, '12:00')" data-time="12:00"
                                {{ isset($stats) && $stats['report_12'] ? 'disabled' : '' }}>
                                <div class="text-center">
                                    <p class="text-sm font-medium text-gray-700">Shift Pagi</p>
                                    <p class="mt-2 text-3xl font-bold text-gray-800">12:00</p>
                                    @if (isset($stats) && $stats['report_12'])
                                        <p class="mt-2 text-xs text-green-600">✅ Sudah diinput</p>
                                    @endif
                                </div>
                            </div>

                            <div class="p-4 transition rounded-lg cursor-pointer bg-gray-100 hover:bg-teal-100 time-slot {{ isset($stats) && $stats['report_16'] ? 'opacity-50 cursor-not-allowed' : '' }}"
                                onclick="selectTime(this, '16:00')" data-time="16:00"
                                {{ isset($stats) && $stats['report_16'] ? 'disabled' : '' }}>
                                <div class="text-center">
                                    <p class="text-sm font-medium text-gray-700">Shift Tengah</p>
                                    <p class="mt-2 text-3xl font-bold text-gray-800">16:00</p>
                                    @if (isset($stats) && $stats['report_16'])
                                        <p class="mt-2 text-xs text-green-600">✅ Sudah diinput</p>
                                    @endif
                                </div>
                            </div>

                            <div class="p-4 transition rounded-lg cursor-pointer bg-gray-100 hover:bg-teal-100 time-slot {{ isset($stats) && $stats['report_20'] ? 'opacity-50 cursor-not-allowed' : '' }}"
                                onclick="selectTime(this, '20:00')" data-time="20:00"
                                {{ isset($stats) && $stats['report_20'] ? 'disabled' : '' }}>
                                <div class="text-center">
                                    <p class="text-sm font-medium text-gray-700">Shift Siang</p>
                                    <p class="mt-2 text-3xl font-bold text-gray-800">20:00</p>
                                    @if (isset($stats) && $stats['report_20'])
                                        <p class="mt-2 text-xs text-green-600">✅ Sudah diinput</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @error('time_slot')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Import Files Card -->
                <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="mb-6 text-lg font-semibold text-gray-800">Import Data</h3>

                        <!-- Import Omzet (Active) -->
                        <div class="p-4 transition rounded-lg bg-gray-50 hover:bg-gray-100">
                            <div class="flex flex-col items-start gap-4 md:flex-row md:items-center">

                                <label class="text-sm font-medium text-gray-700 md:w-40">
                                    Import Omzet
                                </label>

                                <div class="flex-1 w-full">

                                    <div class="flex items-center justify-between w-full gap-3 p-3 bg-white border-2 border-gray-300 border-dashed rounded-lg cursor-pointer hover:border-teal-500 hover:bg-teal-50"
                                        onclick="document.getElementById('file-omzet').click()">

                                        <div class="flex items-center flex-1 gap-2">

                                            <span class="flex-shrink-0 w-2 h-2 bg-gray-400 rounded-full status-dot"
                                                id="dot-omzet"></span>

                                            <span class="text-sm text-gray-600 file-name" id="name-omzet">
                                                Belum ada file dipilih
                                            </span>

                                        </div>

                                        <button type="button"
                                            class="px-4 py-2 text-sm font-medium text-white transition bg-teal-600 rounded-lg hover:bg-teal-700">
                                            Import File
                                        </button>

                                    </div>

                                    <input type="file" name="file_omzet" id="file-omzet" class="hidden"
                                        onchange="handleFileSelect(event, 'omzet')" accept=".xlsx,.xls,.csv">

                                    <p class="mt-2 text-xs text-gray-500">
                                        Format: Excel (.xlsx, .xls) atau CSV. Max 2MB
                                    </p>

                                    <p class="mt-1 text-xs text-gray-500">
                                        Header: no_akad, tanggal, nama, no_telepon, rahn, tunggakan, jenis_barang, merk,
                                        type, keterangan, tanggal_angkut
                                    </p>

                                </div>

                            </div>

                            @error('file_omzet')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror

                        </div>


                        <!-- Import Nasabah -->
                        <div class="p-4 transition rounded-lg bg-gray-50 hover:bg-gray-100">
                            <div class="flex flex-col items-start gap-4 md:flex-row md:items-center">

                                <label class="text-sm font-medium text-gray-700 md:w-40">
                                    Import Nasabah
                                </label>

                                <div class="flex-1 w-full">

                                    <div class="flex items-center justify-between w-full gap-3 p-3 bg-white border-2 border-gray-300 border-dashed rounded-lg cursor-pointer hover:border-teal-500 hover:bg-teal-50"
                                        onclick="document.getElementById('file-nasabah').click()">

                                        <div class="flex items-center flex-1 gap-2">

                                            <span class="flex-shrink-0 w-2 h-2 bg-gray-400 rounded-full status-dot"
                                                id="dot-nasabah">
                                            </span>

                                            <span class="text-sm text-gray-600 file-name" id="name-nasabah">

                                                Belum ada file dipilih

                                            </span>

                                        </div>

                                        <button type="button"
                                            class="px-4 py-2 text-sm font-medium text-white transition bg-teal-600 rounded-lg hover:bg-teal-700">

                                            Import File

                                        </button>

                                    </div>

                                    <input type="file" name="file_nasabah" id="file-nasabah" class="hidden"
                                        onchange="handleFileSelect(event, 'nasabah')" accept=".xlsx,.xls,.csv">

                                    <p class="mt-2 text-xs text-gray-500">
                                        Format: Excel (.xlsx, .xls) atau CSV. Max 2MB
                                    </p>

                                    <p class="mt-1 text-xs text-gray-500">
                                        Header: no_member, nama, nik, tanggal_lahir, email, no_telepon, dll
                                    </p>

                                </div>

                            </div>

                            @error('file_nasabah')
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>



                        <!-- Import Revenue (Active) -->
                        <div class="p-4 transition rounded-lg bg-gray-50 hover:bg-gray-100">
                            <div class="flex flex-col items-start gap-4 md:flex-row md:items-center">
                                <label class="text-sm font-medium text-gray-700 md:w-40">
                                    Import Revenue
                                </label>
                                <div class="flex-1 w-full">
                                    <div class="flex items-center justify-between w-full gap-3 p-3 bg-white border-2 border-gray-300 border-dashed rounded-lg cursor-pointer hover:border-teal-500 hover:bg-teal-50"
                                        onclick="document.getElementById('file-revenue').click()">
                                        <div class="flex items-center flex-1 gap-2">
                                            <span class="flex-shrink-0 w-2 h-2 bg-gray-400 rounded-full status-dot"
                                                id="dot-revenue"></span>
                                            <span class="text-sm text-gray-600 file-name" id="name-revenue">Belum ada
                                                file dipilih</span>
                                        </div>
                                        <button type="button"
                                            class="px-4 py-2 text-sm font-medium text-white transition bg-teal-600 rounded-lg hover:bg-teal-700">
                                            Import File
                                        </button>
                                    </div>
                                    <input type="file" name="file_revenue" id="file-revenue" class="hidden"
                                        onchange="handleFileSelect(event, 'revenue')" accept=".xlsx,.xls,.csv">
                                    <p class="mt-2 text-xs text-gray-500">Format: Excel (.xlsx, .xls) atau CSV. Max 2MB
                                    </p>
                                    <p class="mt-1 text-xs text-gray-500">Header: no_akad, jumlah_pembayaran,
                                        tanggal_transaksi, keterangan (optional)</p>
                                </div>
                            </div>
                            @error('file_revenue')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Keterangan Card -->
                <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <label class="block mb-3 text-sm font-medium text-gray-700">
                            Keterangan
                        </label>
                        <textarea name="keterangan"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg resize-vertical focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                            rows="8" placeholder="Masukkan keterangan di sini...">{{ old('keterangan') }}</textarea>

                        @error('keterangan')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        <!-- Action Buttons -->
                        <div class="flex justify-end gap-3 mt-4">
                            <a href="{{ route('daily-reports.3hour-manager.index') }}"
                                class="px-6 py-2 text-sm font-medium text-gray-700 transition bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                                Batal
                            </a>
                            <button type="submit"
                                class="px-6 py-2 text-sm font-medium text-white transition bg-teal-600 rounded-lg hover:bg-teal-700">
                                Simpan Laporan
                            </button>
                        </div>
                    </div>
                </div>

            </form>

        </div>
    </div>

    <script>
        function selectTime(element, time) {
            // Check if disabled - PERBAIKAN INI
            if (element.hasAttribute('disabled') || element.classList.contains('cursor-not-allowed')) {
                console.log('Time slot disabled:', time);
                return false; // Stop execution
            }

            // Remove active class from all time slots
            document.querySelectorAll('.time-slot').forEach(slot => {
                if (!slot.hasAttribute('disabled') && !slot.classList.contains('cursor-not-allowed')) {
                    slot.classList.remove('active', 'bg-teal-50');
                    slot.classList.add('bg-gray-100');

                    const label = slot.querySelector('p:first-child');
                    const timeText = slot.querySelector('p:nth-child(2)');
                    if (label) {
                        label.classList.remove('text-teal-700');
                        label.classList.add('text-gray-700');
                    }
                    if (timeText) {
                        timeText.classList.remove('text-teal-800');
                        timeText.classList.add('text-gray-800');
                    }
                }
            });

            // Add active class to clicked slot
            element.classList.add('active', 'bg-teal-50');
            element.classList.remove('bg-gray-100');

            const label = element.querySelector('p:first-child');
            const timeText = element.querySelector('p:nth-child(2)');
            if (label) {
                label.classList.add('text-teal-700');
                label.classList.remove('text-gray-700');
            }
            if (timeText) {
                timeText.classList.add('text-teal-800');
                timeText.classList.remove('text-gray-800');
            }

            // Set hidden input value
            document.getElementById('time_slot').value = time;

            console.log('Selected time:', time);
        }

        function handleFileSelect(event, type) {
            const file = event.target.files[0];
            if (file) {
                // Update file name
                const nameElement = document.getElementById('name-' + type);
                nameElement.textContent = file.name;
                nameElement.classList.remove('text-gray-600');
                nameElement.classList.add('text-gray-800', 'font-medium');

                // Update status dot
                const dotElement = document.getElementById('dot-' + type);
                dotElement.classList.remove('bg-gray-400');
                dotElement.classList.add('bg-green-500');

                console.log('File selected for', type + ':', file.name);
            }
        }

        // VALIDASI SEBELUM SUBMIT
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');

            form.addEventListener('submit', function(e) {
                const timeSlot = document.getElementById('time_slot').value;

                if (!timeSlot) {
                    e.preventDefault();
                    alert('Silakan pilih waktu laporan terlebih dahulu!');
                    return false;
                }
            });
        });
    </script>
</x-app-layout>
