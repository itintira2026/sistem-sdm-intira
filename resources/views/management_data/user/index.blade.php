<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Manajemen Pengguna
                </h2>
                <p class="mt-1 text-sm text-gray-500">Kelola data Pengguna perusahaan Anda</p>
            </div>
            <div class="flex gap-3">
                <!-- Ganti tombol import yang sudah ada dengan yang ini -->
                <button onclick="openImportModal()"
                    class="flex items-center gap-2 px-4 py-2 text-green-600 transition bg-green-100 rounded-lg hover:bg-green-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Import User
                </button>
                <a href="{{ route('users.create') }}"
                    class="flex items-center gap-2 px-4 py-2 text-white transition bg-teal-500 rounded-lg hover:bg-teal-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Pengguna
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="mb-6 text-lg font-semibold text-gray-800">Data Pengguna</h3>

                    {{-- <div class="flex gap-4 mb-6">
                        <div class="relative">
                            <select
                                class="px-4 py-2 pr-10 border border-gray-300 rounded-lg appearance-none focus:outline-none focus:ring-2 focus:ring-teal-500">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>

                        <div class="relative">
                            <select
                                class="px-4 py-2 pr-10 border border-gray-300 rounded-lg appearance-none focus:outline-none focus:ring-2 focus:ring-teal-500">
                                <option value="">Semua Role</option>
                                @if (isset($roles) && count($roles) > 0)
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="relative flex-1">
                            <svg class="absolute w-5 h-5 text-gray-400 left-3 top-3" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input type="text" placeholder="Cari nama, email, atau username..."
                                class="w-full py-2 pl-10 pr-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>
                    </div> --}}
                    <form method="GET" action="{{ route('users.index') }}" class="flex gap-4 mb-6">
                        {{-- PER PAGE --}}
                        <div class="relative">
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

                        {{-- ROLE --}}
                        <div class="relative">
                            <select name="role" onchange="this.form.submit()"
                                class="px-4 py-2 pr-10 border border-gray-300 rounded-lg appearance-none focus:ring-teal-500">
                                <option value="">Semua Role</option>
                                @foreach ($roles as $r)
                                    <option value="{{ $r->name }}"
                                        {{ request('role') == $r->name ? 'selected' : '' }}>
                                        {{ ucfirst($r->name) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- SEARCH --}}
                        <div class="relative flex-1">
                            <svg class="absolute w-5 h-5 text-gray-400 left-3 top-3" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>

                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari nama, email, atau username..."
                                class="w-full py-2 pl-10 pr-4 border border-gray-300 rounded-lg focus:ring-teal-500" />
                        </div>

                        <button type="submit" class="px-4 py-2 text-white bg-teal-600 rounded-lg hover:bg-teal-700">
                            Cari
                        </button>
                    </form>


                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="px-4 py-4 text-sm font-semibold text-left text-gray-600 uppercase">Nama
                                    </th>
                                    <th class="px-4 py-4 text-sm font-semibold text-left text-gray-600 uppercase">Cabang
                                    </th>
                                    <th class="px-4 py-4 text-sm font-semibold text-left text-gray-600 uppercase">Role
                                    </th>
                                    <th class="px-4 py-4 text-sm font-semibold text-left text-gray-600 uppercase">Status
                                    </th>
                                    <th class="px-4 py-4 text-sm font-semibold text-left text-gray-600 uppercase">Dibuat
                                    </th>
                                    <th class="px-4 py-4 text-sm font-semibold text-left text-gray-600 uppercase">Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $userList = isset($users) ? $users : [];
                                @endphp

                                @forelse($userList as $user)
                                    @php
                                        $userName = $user->name ?? 'Unknown';
                                        $userEmail = $user->email ?? '-';
                                        $userInitials = strtoupper(substr($userName, 0, 2));
                                        $userActive = isset($user->is_active) ? $user->is_active : false;
                                        $userRoles = $user->roles ?? collect();
                                        $userCreated = isset($user->created_at)
                                            ? $user->created_at->format('d M Y')
                                            : '-';
                                    @endphp

                                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                                        <td class="px-4 py-4">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="flex items-center justify-center w-10 h-10 font-semibold text-teal-600 bg-teal-100 rounded-full">
                                                    {{ $userInitials }}
                                                </div>
                                                <div>
                                                    <div class="font-medium text-gray-700">{{ $userName }}</div>
                                                    <div class="text-sm text-gray-500">{{ $userEmail }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        {{-- <td class="px-4 py-4">
                                            <span class="px-3 py-1 text-sm text-gray-600 bg-gray-100 rounded">
                                                {{ $user->branch->name ?? 'Tidak ada cabang' }}
                                            </span>
                                        </td> --}}
                                        <td class="px-4 py-4">
                                            <span class="px-3 py-1 text-sm text-gray-600 bg-gray-100 rounded">
                                                {{ $user->displayBranchName() }}
                                            </span>
                                        </td>

                                        <td class="px-4 py-4">
                                            @if ($userRoles->isNotEmpty())
                                                <span class="px-3 py-1 text-sm text-blue-700 bg-blue-100 rounded">
                                                    {{ $userRoles->first()->name }}
                                                </span>
                                            @else
                                                <span class="px-3 py-1 text-sm text-gray-600 bg-gray-100 rounded">
                                                    Tidak ada role
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4">
                                            @if ($userActive)
                                                <span
                                                    class="inline-flex items-center gap-1 px-3 py-1 text-sm font-medium text-green-700 bg-green-100 rounded">
                                                    <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                                    Aktif
                                                </span>
                                            @else
                                                <span
                                                    class="px-3 py-1 text-sm font-medium text-red-700 bg-red-100 rounded">
                                                    Tidak Aktif
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 text-gray-700">
                                            {{ $userCreated }}
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="relative inline-block text-left dropdown-container">
                                                <button type="button" onclick="toggleDropdown({{ $user->id }})"
                                                    class="text-gray-400 hover:text-gray-600 focus:outline-none">
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path
                                                            d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                                    </svg>
                                                </button>

                                                <div id="dropdown-{{ $user->id }}"
                                                    class="absolute right-0 z-10 hidden w-48 mt-2 bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5">
                                                    <div class="py-1">
                                                        <a href="{{ route('users.show', $user->id) }}"
                                                            class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                            </svg>
                                                            Detail
                                                        </a>

                                                        @if ($userActive)
                                                            <form action="{{ route('users.deactivate', $user->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit"
                                                                    class="flex items-center w-full gap-2 px-4 py-2 text-sm text-left text-gray-700 hover:bg-gray-100">
                                                                    <svg class="w-4 h-4" fill="none"
                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                                    </svg>
                                                                    Nonaktifkan
                                                                </button>
                                                            </form>
                                                        @else
                                                            <form action="{{ route('users.activate', $user->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit"
                                                                    class="flex items-center w-full gap-2 px-4 py-2 text-sm text-left text-gray-700 hover:bg-gray-100">
                                                                    <svg class="w-4 h-4" fill="none"
                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M5 13l4 4L19 7" />
                                                                    </svg>
                                                                    Aktifkan
                                                                </button>
                                                            </form>
                                                        @endif

                                                        <a href="{{ route('users.edit', $user->id) }}"
                                                            class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                            </svg>
                                                            Edit
                                                        </a>

                                                        <form action="{{ route('users.destroy', $user->id) }}"
                                                            method="POST"
                                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="flex items-center w-full gap-2 px-4 py-2 text-sm text-left text-red-600 hover:bg-gray-100">
                                                                <svg class="w-4 h-4" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                </svg>
                                                                Hapus
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-8 text-center text-gray-500">
                                            Tidak ada data pengguna
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if (isset($users) && method_exists($users, 'links'))
                        <div class="mt-6">
                            {{ $users->links() }}
                        </div>
                    @endif
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
                    <h3 class="text-xl font-semibold text-gray-800">Import Data User</h3>
                    <p class="mt-1 text-sm text-gray-500">Unggah file untuk menambahkan data User secara massal</p>
                </div>
                <button id="closeModal" type="button" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Modal Content -->
            <form id="importForm" action="{{ route('users.import.import') }}" method="POST"
                enctype="multipart/form-data">
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
        // Fungsi untuk membuka modal import
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

        function toggleDropdown(userId) {
            var dropdown = document.getElementById('dropdown-' + userId);
            var allDropdowns = document.querySelectorAll('[id^="dropdown-"]');

            for (var i = 0; i < allDropdowns.length; i++) {
                if (allDropdowns[i].id !== 'dropdown-' + userId) {
                    allDropdowns[i].classList.add('hidden');
                }
            }

            dropdown.classList.toggle('hidden');
        }

        document.addEventListener('click', function(event) {
            var isButton = false;
            var isDropdown = false;

            var target = event.target;
            while (target) {
                if (target.tagName === 'BUTTON') {
                    isButton = true;
                    break;
                }
                if (target.id && target.id.indexOf('dropdown-') === 0) {
                    isDropdown = true;
                    break;
                }
                target = target.parentElement;
            }

            if (!isButton && !isDropdown) {
                var allDropdowns = document.querySelectorAll('[id^="dropdown-"]');
                for (var i = 0; i < allDropdowns.length; i++) {
                    allDropdowns[i].classList.add('hidden');
                }
            }
        });
    </script>
</x-app-layout>
