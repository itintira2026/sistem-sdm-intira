<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Manajemen Presensi Karyawan
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Kelola data presensi karyawan Anda di sini.
                </p>
            </div>

            <div class="flex gap-3">
                <!-- Ganti tombol import yang sudah ada dengan yang ini -->
                <button onclick="openImportModal()"
                    class="flex items-center gap-2 px-4 py-2 text-green-600 transition bg-green-100 rounded-lg hover:bg-green-200">
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
                            </select>
                        </div>


                        {{-- TANGGAL --}}
                        <div>
                            <input type="date" name="tanggal" value="{{ request('tanggal', $tanggal) }}"
                                onchange="this.form.submit()" class="px-4 py-2 border rounded-lg">
                        </div>

                        {{-- SEARCH --}}
                        <div class="relative flex-1 min-w-[250px]">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari nama karyawan..." class="w-full px-4 py-2 border rounded-lg">
                        </div>

                        <button type="submit" class="px-4 py-2 text-white bg-teal-600 rounded-lg hover:bg-teal-700">
                            Cari
                        </button>
                    </form>

                    {{-- TABLE --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="px-4 py-4 text-sm font-semibold text-left text-gray-600 uppercase">
                                        Nama
                                    </th>
                                    <th class="px-4 py-4 text-sm font-semibold text-left text-gray-600 uppercase">
                                        Status
                                    </th>
                                    <th class="px-4 py-4 text-sm font-semibold text-left text-gray-600 uppercase">
                                        Jam Presensi
                                    </th>
                                    <th class="px-4 py-4 text-sm font-semibold text-left text-gray-600 uppercase">
                                        Telat
                                    </th>
                                    <th class="px-4 py-4 text-sm font-semibold text-left text-gray-600 uppercase">
                                        Aksi
                                    </th>
                                    {{-- <th class="px-4 py-4 text-sm font-semibold text-left text-gray-600 uppercase">
                                        Aksi
                                    </th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $row)
                                    <tr class="border-t">
                                        <td class="px-4 py-3">
                                            {{ $row->name ?? '-' }}
                                        </td>

                                        {{-- STATUS --}}
                                        <td class="px-4 py-3">
                                            @if ($row->presensi_status === 'LENGKAP')
                                                <span class="px-2 py-1 text-green-700 bg-green-100 rounded">
                                                    Lengkap
                                                </span>
                                            @elseif ($row->presensi_status === 'BELUM_ABSEN')
                                                <span class="px-2 py-1 text-gray-700 bg-gray-200 rounded">
                                                    Belum Absen
                                                </span>
                                            @else
                                                <span class="px-2 py-1 text-yellow-700 bg-yellow-100 rounded">
                                                    Tidak Lengkap
                                                </span>
                                            @endif
                                        </td>

                                        {{-- JAM --}}
                                        <td class="px-4 py-3 text-xs">
                                            CI: {{ $row->presensi_jam['CHECK_IN'] ?? '-' }} |
                                            IO: {{ $row->presensi_jam['ISTIRAHAT_OUT'] ?? '-' }} |
                                            II: {{ $row->presensi_jam['ISTIRAHAT_IN'] ?? '-' }} |
                                            CO: {{ $row->presensi_jam['CHECK_OUT'] ?? '-' }}
                                        </td>

                                        {{-- TELAT --}}
                                        <td class="px-4 py-3">
                                            @if (count($row->presensi_telat))
                                                <span class="text-red-600">
                                                    {{ implode(', ', $row->presensi_telat) }}
                                                </span>
                                            @else
                                                -
                                            @endif
                                        </td>

                                        {{-- AKSI --}}
                                        {{-- <a href="#" class="text-blue-600 hover:underline">
                                            Detail
                                        </a> --}}
                                        <td class="px-4 py-3">
                                            <div class="relative inline-block text-left">
                                                <button type="button" onclick="toggleDropdown({{ $row->id }})"
                                                    class="text-gray-400 hover:text-gray-600">

                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path
                                                            d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                                    </svg>
                                                </button>
                                                {{-- absolute right-0 z-10 hidden w-48 mt-2 bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 --}}
                                                <div id="dropdown-{{ $row->id }}"
                                                    class="fixed z-50 hidden w-48 bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5">
                                                    <div class="py-1">
                                                        {{-- <a href=""
                                                            class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                            Detail
                                                        </a> --}}
                                                        <a href="{{ route('presensi.show', $row->id) }}"
                                                            class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                            Detail
                                                        </a>

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
            {{-- <h1>hello world</h1> --}}
            {{-- FILTER TANGGAL --}}
        </div>
    </div>

    <!-- Modal Import Cabang -->
    <div id="importModal" class="fixed inset-0 z-50 hidden w-full h-full overflow-y-auto bg-gray-600 bg-opacity-50">
        <div class="relative w-11/12 p-5 mx-auto bg-white border rounded-lg shadow-lg top-20 md:w-2/3 lg:w-1/2">
            <!-- Modal Header -->
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

            <!-- Modal Content -->
            <form id="importForm" action="{{ route('presensi.import') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-6">
                    <!-- Format File Section -->
                    <div class="mb-6">
                        <h4 class="mb-3 font-medium text-gray-700">Format File yang Didukung</h4>
                        <div class="grid grid-cols-1 gap-4 mb-4">
                            <div class="p-4 border border-gray-200 rounded-lg">
                                <div class="flex items-center mb-2">
                                    <div
                                        class="flex items-center justify-center w-10 h-10 mr-3 bg-green-100 rounded-full">
                                        <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
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
                                    {{-- <li>• Kolom wajib: Kode Cabang, Nama Cabang</li> --}}
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Upload Area -->
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

                    <!-- Progress Bar -->
                    <div id="progressContainer" class="hidden mb-6">
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Mengunggah...</span>
                            <span id="progressPercent" class="text-sm font-medium text-gray-700">0%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div id="progressBar" class="bg-teal-600 h-2.5 rounded-full" style="width: 0%"></div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
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

    {{-- Dropdown Script --}}
    <script>
        function openImportModal() {
            console.log('Opening import modal');
            document.getElementById('importModal').classList.remove('hidden');
        }

        // Fungsi untuk menutup modal import
        function closeImportModal() {
            console.log('Closing import modal');
            document.getElementById('importModal').classList.add('hidden');
            resetForm();
        }

        // Reset form
        function resetForm() {
            document.getElementById('importForm').reset();
            document.getElementById('fileName').classList.add('hidden');
            document.getElementById('fileError').classList.add('hidden');
            document.getElementById('submitButton').disabled = true;
            document.getElementById('progressContainer').classList.add('hidden');
            document.getElementById('progressBar').style.width = '0%';
            document.getElementById('progressPercent').textContent = '0%';
            document.getElementById('submitButton').textContent = 'Import Data';
        }

        // Event listener untuk tombol-tombol modal
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, setting up import modal listeners');

            // Tombol close modal
            document.getElementById('closeModal').addEventListener('click', closeImportModal);

            // Tombol batal
            document.getElementById('cancelButton').addEventListener('click', closeImportModal);

            // Tombol browse file
            document.getElementById('browseButton').addEventListener('click', function() {
                document.getElementById('fileInput').click();
            });

            // Area drop file
            const dropArea = document.getElementById('dropArea');

            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                dropArea.addEventListener(eventName, highlight, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, unhighlight, false);
            });

            function highlight() {
                dropArea.classList.add('border-teal-500', 'bg-teal-50');
            }

            function unhighlight() {
                dropArea.classList.remove('border-teal-500', 'bg-teal-50');
            }

            // Handle drop file
            dropArea.addEventListener('drop', handleDrop, false);

            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                handleFiles(files);
            }

            // Handle file input change
            document.getElementById('fileInput').addEventListener('change', function() {
                handleFiles(this.files);
            });

            // Fungsi untuk menangani file yang dipilih
            function handleFiles(files) {
                console.log('Handling files:', files);
                const fileError = document.getElementById('fileError');
                fileError.classList.add('hidden');

                if (files.length > 0) {
                    const file = files[0];
                    const allowedExtensions = /(\.xlsx|\.xls)$/i;
                    const maxSize = 10 * 1024 * 1024; // 10MB

                    console.log('File selected:', file.name, 'Size:', file.size);

                    if (!allowedExtensions.exec(file.name)) {
                        fileError.textContent =
                            'Format file tidak didukung. Harap unggah file Excel (.xlsx atau .xls).';
                        fileError.classList.remove('hidden');
                        return;
                    }

                    if (file.size > maxSize) {
                        fileError.textContent = 'Ukuran file terlalu besar. Maksimal 10MB.';
                        fileError.classList.remove('hidden');
                        return;
                    }

                    // Tampilkan nama file
                    document.getElementById('selectedFileName').textContent = file.name;
                    document.getElementById('fileName').classList.remove('hidden');

                    // Aktifkan tombol submit
                    document.getElementById('submitButton').disabled = false;
                }
            }

            // Handle form submission dengan AJAX
            document.getElementById('importForm').addEventListener('submit', function(e) {
                e.preventDefault();

                console.log('Form submitted');

                const formData = new FormData(this);
                const submitButton = document.getElementById('submitButton');
                const fileInput = document.getElementById('fileInput');

                // Validasi file
                if (!fileInput.files.length) {
                    showNotification('error', 'Silakan pilih file terlebih dahulu.');
                    return;
                }

                // Tampilkan progress bar
                document.getElementById('progressContainer').classList.remove('hidden');

                // Disable tombol submit
                submitButton.disabled = true;
                submitButton.textContent = 'Mengimport...';

                // CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                console.log('CSRF Token:', csrfToken);

                // Buat XMLHttpRequest untuk upload dengan progress tracking
                const xhr = new XMLHttpRequest();
                const url = this.action;
                console.log('Upload URL:', url);

                // Track upload progress
                xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        const percentComplete = (e.loaded / e.total) * 90; // 90% untuk upload
                        console.log('Upload progress:', percentComplete + '%');
                        document.getElementById('progressBar').style.width = percentComplete + '%';
                        document.getElementById('progressPercent').textContent = Math.round(
                            percentComplete) + '%';
                    }
                });

                xhr.addEventListener('load', function() {
                    console.log('Response received:', xhr.status, xhr.responseText);

                    if (xhr.status === 200) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            console.log('Parsed response:', response);

                            if (response.success) {
                                // Import berhasil
                                document.getElementById('progressBar').style.width = '100%';
                                document.getElementById('progressPercent').textContent = '100%';

                                // Tampilkan notifikasi sukses
                                showNotification('success', response.message || 'Import berhasil!');

                                // Tutup modal setelah 1.5 detik
                                setTimeout(() => {
                                    closeImportModal();

                                    // Refresh halaman untuk menampilkan data baru
                                    setTimeout(() => {
                                        window.location.reload();
                                    }, 500);
                                }, 1500);
                            } else {
                                // Import gagal
                                showNotification('error', response.message ||
                                    'Terjadi kesalahan saat import.');
                                submitButton.disabled = false;
                                submitButton.textContent = 'Import Data';
                            }
                        } catch (e) {
                            console.error('Error parsing JSON:', e);
                            showNotification('error', 'Format response tidak valid dari server.');
                            submitButton.disabled = false;
                            submitButton.textContent = 'Import Data';
                        }
                    } else if (xhr.status === 422) {
                        // Validation error
                        try {
                            const response = JSON.parse(xhr.responseText);
                            showNotification('error', response.message || 'Validasi gagal.');
                        } catch (e) {
                            showNotification('error', 'Validasi gagal (Status 422)');
                        }
                        submitButton.disabled = false;
                        submitButton.textContent = 'Import Data';
                    } else if (xhr.status === 401) {
                        // Unauthorized
                        showNotification('error', 'Anda harus login untuk melakukan import.');
                        submitButton.disabled = false;
                        submitButton.textContent = 'Import Data';
                    } else if (xhr.status === 419) {
                        // CSRF token mismatch
                        showNotification('error',
                            'Session expired. Silakan refresh halaman dan coba lagi.');
                        submitButton.disabled = false;
                        submitButton.textContent = 'Import Data';
                    } else {
                        // Error dari server
                        console.error('Server error:', xhr.status, xhr.responseText);
                        showNotification('error',
                            `Terjadi kesalahan pada server (Status: ${xhr.status}).`);
                        submitButton.disabled = false;
                        submitButton.textContent = 'Import Data';
                    }
                });

                xhr.addEventListener('error', function() {
                    console.error('Network error');
                    showNotification('error', 'Terjadi kesalahan jaringan. Periksa koneksi Anda.');
                    submitButton.disabled = false;
                    submitButton.textContent = 'Import Data';
                });

                xhr.addEventListener('abort', function() {
                    console.log('Request aborted');
                    submitButton.disabled = false;
                    submitButton.textContent = 'Import Data';
                });

                // Kirim request
                xhr.open('POST', url, true);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                console.log('Sending request...');
                xhr.send(formData);
            });

            // Fungsi untuk menampilkan notifikasi
            function showNotification(type, message) {
                console.log('Showing notification:', type, message);

                // Hapus notifikasi lama jika ada
                const oldNotifications = document.querySelectorAll('.import-notification');
                oldNotifications.forEach(notification => {
                    document.body.removeChild(notification);
                });

                // Buat elemen notifikasi
                const notification = document.createElement('div');
                notification.className = `import-notification fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg transform transition-transform duration-300 translate-y-0 ${
                type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
            }`;
                notification.innerHTML = `
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        ${type === 'success'
                            ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />'
                            : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />'
                        }
                    </svg>
                    <span>${message}</span>
                </div>
            `;

                // Tambahkan ke body
                document.body.appendChild(notification);

                // Hapus notifikasi setelah 5 detik
                setTimeout(() => {
                    notification.style.transform = 'translateY(-100px)';
                    setTimeout(() => {
                        if (notification.parentNode) {
                            document.body.removeChild(notification);
                        }
                    }, 300);
                }, 5000);
            }

            // Tutup modal saat klik di luar area modal
            document.getElementById('importModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeImportModal();
                }
            });

            // Tutup modal dengan tombol Escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeImportModal();
                }
            });

            console.log('Import modal setup complete');
        });

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
