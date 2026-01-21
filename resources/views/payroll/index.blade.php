<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Manajemen Gaji Cabang
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Kelola gaji cabang perusahaan Anda
                </p>
            </div>

            <div class="flex gap-3">
                <button
                    onclick="openImportModal({
    title: 'Import Gaji Pokok',
    action: '{{ route('gaji-pokok.import') }}'
})"
                    class="flex items-center gap-2 px-4 py-2 text-green-600 bg-green-100 rounded-lg hover:bg-green-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Import Gaji
                </button>

                {{-- <button
                    onclick="openImportModal({
    title: 'Import Potongan',
    action: '{{ route('potongan.import') }}'
})"
                    class="flex items-center gap-2 px-4 py-2 text-green-600 bg-green-100 rounded-lg hover:bg-green-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Import Potongan
                </button> --}}

                {{-- <a href="{{ route('branches.create') }}"
                    class="flex items-center gap-2 px-4 py-2 text-white transition bg-teal-500 rounded-lg hover:bg-teal-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Gaji
                </a> --}}
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
                        Daftar Cabang
                    </h3>

                    <form method="GET" action="{{ route('gaji.index') }}" class="flex flex-wrap gap-4 mb-6">

                        {{-- PER PAGE --}}
                        <div>
                            <select name="per_page" onchange="this.form.submit()"
                                class="px-4 py-2 pr-10 border border-gray-300 rounded-lg appearance-none focus:ring-teal-500">
                                @foreach ([10, 25, 50, 100] as $size)
                                    <option value="{{ $size }}"
                                        {{ request('per_page', 10) == $size ? 'selected' : '' }}>
                                        {{ $size }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- STATUS --}}
                        <div>
                            <select name="status" onchange="this.form.submit()"
                                class="px-4 py-2 pr-10 border border-gray-300 rounded-lg appearance-none focus:ring-teal-500">
                                <option value="">Semua Status</option>
                                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Tidak Aktif
                                </option>
                            </select>
                        </div>

                        {{-- SEARCH --}}
                        <div class="relative flex-1 min-w-[250px]">
                            <svg class="absolute w-5 h-5 text-gray-400 left-3 top-3" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>

                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari kode cabang atau nama cabang..."
                                class="w-full py-2 pl-10 pr-4 border border-gray-300 rounded-lg focus:ring-teal-500">
                        </div>

                        <button type="submit" class="px-4 py-2 text-white bg-teal-600 rounded-lg hover:bg-teal-700">
                            Cari
                        </button>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="px-4 py-4 text-sm font-semibold text-left text-gray-600 uppercase">
                                        Kode
                                    </th>
                                    <th class="px-4 py-4 text-sm font-semibold text-left text-gray-600 uppercase">
                                        Nama Cabang
                                    </th>
                                    <th class="px-4 py-4 text-sm font-semibold text-left text-gray-600 uppercase">
                                        Jumlah User
                                    </th>
                                    <th class="px-4 py-4 text-sm font-semibold text-left text-gray-600 uppercase">
                                        Status
                                    </th>
                                    <th class="px-4 py-4 text-sm font-semibold text-left text-gray-600 uppercase">
                                        Dibuat
                                    </th>
                                    <th class="px-4 py-4 text-sm font-semibold text-left text-gray-600 uppercase">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($branches as $branch)
                                    @php
                                        $branchName = $branch->name ?? 'Unknown';
                                        $branchCode = $branch->code ?? '-';
                                        $branchActive = isset($branch->is_active) ? $branch->is_active : false;
                                        $branchUsers = $branch->users ?? collect();
                                        $branchUserCount = $branchUsers->count();
                                        $branchCreated = isset($branch->created_at)
                                            ? $branch->created_at->format('d M Y')
                                            : '-';
                                    @endphp

                                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                                        <td class="px-4 py-4">
                                            <span
                                                class="px-3 py-1 text-sm font-medium rounded bg-cyan-100 text-cyan-700">
                                                {{ $branchCode }}
                                            </span>
                                        </td>

                                        <td class="px-4 py-4 font-medium text-gray-700">
                                            {{ $branchName }}
                                        </td>

                                        <td class="px-4 py-4">
                                            <span class="px-3 py-1 text-sm rounded bg-cyan-100 text-cyan-700">
                                                {{ $branchUserCount }} user
                                            </span>
                                        </td>

                                        <td class="px-4 py-4">
                                            @if ($branchActive)
                                                <span
                                                    class="px-3 py-1 text-sm font-medium text-green-700 bg-green-100 rounded">
                                                    Aktif
                                                </span>
                                            @else
                                                <span
                                                    class="px-3 py-1 text-sm font-medium text-red-700 bg-red-100 rounded">
                                                    Tidak Aktif
                                                </span>
                                            @endif
                                        </td>

                                        <td class="px-4 py-4 text-sm text-gray-500">
                                            {{ $branchCreated }}
                                        </td>

                                        <td class="px-4 py-4">
                                            <div class="relative inline-block text-left">

                                                {{-- <button type="button" onclick="toggleDropdown({{ $branch->id }})"
                                                    class="text-gray-400 hover:text-gray-600"> --}}
                                                <button type="button" onclick="toggleDropdown({{ $branch->id }})"
                                                    class="text-gray-400 hover:text-gray-600">

                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path
                                                            d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                                    </svg>
                                                </button>
                                                {{-- absolute right-0 z-10 hidden w-48 mt-2 bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 --}}
                                                <div id="dropdown-{{ $branch->id }}"
                                                    class="fixed z-50 hidden w-48 bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5">
                                                    <div class="py-1">
                                                        <a href="{{ route('gaji-pokok.detail', parameters: $branch) }}"
                                                            class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                            Gaji Pokok Detail
                                                        </a>


                                                        <a href="{{ route('potongan.index', $branch) }}"
                                                            class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                            Potongan Detail
                                                        </a>

                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                @empty
                                    <tr>
                                        <td colspan="6" class="py-6 text-center text-gray-500">
                                            Data cabang belum tersedia
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-6">
                        {{ $branches->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Modal Import User -->
    <div id="importModal" class="fixed inset-0 z-50 hidden w-full h-full overflow-y-auto bg-gray-600 bg-opacity-50">
        <div class="relative w-11/12 p-5 mx-auto bg-white border rounded-lg shadow-lg top-20 md:w-2/3 lg:w-1/2">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-3 mb-6 border-b">
                <div>
                    {{-- <h3 class="text-xl font-semibold text-gray-800">Import Data Gaji Pokok</h3> --}}
                    <h3 id="importModalTitle" class="text-lg font-semibold"></h3>

                    <p class="mt-1 text-sm text-gray-500">Unggah file untuk menambahkan data Gaji secara massal
                    </p>
                </div>
                <button id="closeModal" type="button" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Modal Content -->
            {{-- <form id="importForm" action="{{ route('gaji-pokok.import') }}" method="POST"
                enctype="multipart/form-data"> --}}
            <form id="importForm" method="POST" enctype="multipart/form-data">

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
                                    {{-- <li>• Kolom waji</li> --}}
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


    <script>
        let currentImport = null;

        function openImportModal(config) {
            currentImport = config;

            document.getElementById('importModalTitle').innerText = config.title;
            document.getElementById('importForm').action = config.action;

            document.getElementById('importModal').classList.remove('hidden');
            resetForm();
        }

        function closeImportModal() {
            document.getElementById('importModal').classList.add('hidden');
            resetForm();
        }

        function resetForm() {
            const form = document.getElementById('importForm');
            form.reset();

            document.getElementById('fileName').classList.add('hidden');
            document.getElementById('fileError').classList.add('hidden');
            document.getElementById('submitButton').disabled = true;

            document.getElementById('progressContainer').classList.add('hidden');
            document.getElementById('progressBar').style.width = '0%';
            document.getElementById('progressPercent').innerText = '0%';

            document.getElementById('submitButton').innerText = 'Import Data';
        }

        document.addEventListener('DOMContentLoaded', () => {

            const dropArea = document.getElementById('dropArea');
            const fileInput = document.getElementById('fileInput');
            const submitButton = document.getElementById('submitButton');

            document.getElementById('closeModal').onclick = closeImportModal;
            document.getElementById('cancelButton').onclick = closeImportModal;
            document.getElementById('browseButton').onclick = () => fileInput.click();

            dropArea.addEventListener('dragover', e => e.preventDefault());
            dropArea.addEventListener('drop', e => {
                e.preventDefault();
                handleFiles(e.dataTransfer.files);
            });

            fileInput.addEventListener('change', e => handleFiles(e.target.files));

            function handleFiles(files) {
                if (!files.length) return;

                const file = files[0];
                const error = document.getElementById('fileError');

                error.classList.add('hidden');

                if (!/\.xlsx|\.xls$/i.test(file.name)) {
                    return showFileError('Format harus Excel (.xlsx / .xls)');
                }

                if (file.size > 10 * 1024 * 1024) {
                    return showFileError('Ukuran maksimal 10MB');
                }

                document.getElementById('selectedFileName').innerText = file.name;
                document.getElementById('fileName').classList.remove('hidden');
                submitButton.disabled = false;
            }

            function showFileError(msg) {
                const error = document.getElementById('fileError');
                error.innerText = msg;
                error.classList.remove('hidden');
            }

            document.getElementById('importForm').addEventListener('submit', function(e) {
                e.preventDefault();

                submitButton.disabled = true;
                submitButton.innerText = 'Mengimport...';
                document.getElementById('progressContainer').classList.remove('hidden');

                const xhr = new XMLHttpRequest();
                const csrf = document.querySelector('meta[name="csrf-token"]').content;

                xhr.upload.onprogress = e => {
                    if (e.lengthComputable) {
                        const percent = Math.round((e.loaded / e.total) * 90);
                        document.getElementById('progressBar').style.width = percent + '%';
                        document.getElementById('progressPercent').innerText = percent + '%';
                    }
                };

                xhr.onload = () => {
                    try {
                        const res = JSON.parse(xhr.responseText);

                        if (xhr.status === 200 && res.success) {
                            document.getElementById('progressBar').style.width = '100%';
                            document.getElementById('progressPercent').innerText = '100%';

                            closeImportModal();
                            showNotification('success', res.message || 'Import berhasil');
                            setTimeout(() => location.reload(), 800);
                        } else {
                            handleError(res.message);
                        }
                    } catch {
                        handleError('Response server tidak valid');
                    }
                };

                xhr.onerror = () => handleError('Kesalahan jaringan');

                xhr.open('POST', this.action);
                xhr.setRequestHeader('X-CSRF-TOKEN', csrf);
                xhr.send(new FormData(this));
            });

            function handleError(msg) {
                closeImportModal();
                showNotification('error', msg || 'Terjadi kesalahan');
            }

            function showNotification(type, message) {
                document.querySelectorAll('.import-notification').forEach(n => n.remove());

                const el = document.createElement('div');
                el.className = `import-notification fixed top-4 right-4 px-6 py-3 rounded-lg text-white ${
                type === 'success' ? 'bg-green-600' : 'bg-red-600'
            }`;
                el.style.whiteSpace = 'pre-line';
                el.innerText = message;

                document.body.appendChild(el);
                setTimeout(() => el.remove(), 5000);
            }
        });



        // function toggleDropdown(id) {
        //     document.querySelectorAll('[id^="dropdown-"]').forEach(el => {
        //         if (el.id !== `dropdown-${id}`) {
        //             el.classList.add('hidden');
        //         }
        //     });

        //     document.getElementById(`dropdown-${id}`).classList.toggle('hidden');
        // }

        // document.addEventListener('click', function(e) {
        //     if (!e.target.closest('.relative')) {
        //         document.querySelectorAll('[id^="dropdown-"]').forEach(el => {
        //             el.classList.add('hidden');
        //         });
        //     }
        // });
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
