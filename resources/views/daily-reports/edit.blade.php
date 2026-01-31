<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Edit Laporan Harian
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Perbarui data laporan harian cabang
                </p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('daily-reports.index', ['tanggal' => $dailyReport->tanggal, 'branch_id' => $dailyReport->branch_id]) }}"
                    class="flex items-center gap-2 px-4 py-2 text-white transition bg-gray-500 rounded-lg hover:bg-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            {{-- Alert Error --}}
            @if ($errors->any())
                <div class="p-4 mb-6 text-red-700 bg-red-100 rounded-lg">
                    <p class="font-semibold">Terjadi kesalahan:</p>
                    <ul class="mt-2 ml-4 list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-800">
                            Form Edit Laporan
                        </h3>

                        {{-- Status Validasi Badge --}}
                        @if ($dailyReport->validasi_manager)
                            <span class="px-3 py-1 text-sm text-green-700 bg-green-100 rounded-full">
                                ✅ Sudah Divalidasi Manager
                            </span>
                        @else
                            <span class="px-3 py-1 text-sm text-orange-700 bg-orange-100 rounded-full">
                                ⏳ Belum Divalidasi
                            </span>
                        @endif
                    </div>

                    {{-- Info Box --}}
                    <div class="p-4 mb-6 rounded-lg bg-blue-50">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="text-sm text-blue-800">
                                <p><strong>Cabang:</strong> {{ $dailyReport->branch->name }}</p>
                                <p><strong>Tanggal:</strong> {{ $dailyReport->tanggal->format('d M Y') }}</p>
                                <p><strong>Shift:</strong> {{ $dailyReport->shift_label }}</p>
                                <p><strong>Dibuat oleh:</strong> {{ $dailyReport->user->name }}</p>
                                <p><strong>Waktu Input:</strong> {{ $dailyReport->created_at->format('d M Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Warning jika sudah divalidasi --}}
                    @if ($dailyReport->validasi_manager && !Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('manager'))
                        <div class="p-4 mb-6 text-yellow-700 bg-yellow-100 rounded-lg">
                            <p class="font-semibold">⚠️ Laporan ini sudah divalidasi oleh Manager</p>
                            <p class="mt-1 text-sm">Anda tidak dapat mengedit laporan yang sudah divalidasi.</p>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('daily-reports.update', $dailyReport->id) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- PENCAIRAN (BARANG MASUK) --}}
                        <div class="p-4 mb-6 rounded-lg bg-green-50">
                            <h4 class="mb-4 text-base font-semibold text-green-800">📥 PENCAIRAN (Barang Masuk)</h4>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-700">
                                        Jumlah Barang (unit) <span class="text-red-600">*</span>
                                    </label>
                                    <input type="number" name="pencairan_jumlah_barang"
                                        value="{{ old('pencairan_jumlah_barang', $dailyReport->pencairan_jumlah_barang) }}"
                                        min="0" required
                                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 @error('pencairan_jumlah_barang') border-red-500 @enderror">
                                    @error('pencairan_jumlah_barang')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-700">
                                        Total Nominal (Rp) <span class="text-red-600">*</span>
                                    </label>
                                    <input type="number" name="pencairan_nominal"
                                        value="{{ old('pencairan_nominal', $dailyReport->pencairan_nominal) }}"
                                        min="0" step="0.01" required
                                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 @error('pencairan_nominal') border-red-500 @enderror">
                                    @error('pencairan_nominal')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- PELUNASAN (BARANG KELUAR) --}}
                        <div class="p-4 mb-6 rounded-lg bg-blue-50">
                            <h4 class="mb-4 text-base font-semibold text-blue-800">📤 PELUNASAN (Barang Keluar)</h4>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-700">
                                        Jumlah Barang (unit) <span class="text-red-600">*</span>
                                    </label>
                                    <input type="number" name="pelunasan_jumlah_barang"
                                        value="{{ old('pelunasan_jumlah_barang', $dailyReport->pelunasan_jumlah_barang) }}"
                                        min="0" required
                                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 @error('pelunasan_jumlah_barang') border-red-500 @enderror">
                                    @error('pelunasan_jumlah_barang')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-700">
                                        Total Nominal (Rp) <span class="text-red-600">*</span>
                                    </label>
                                    <input type="number" name="pelunasan_nominal"
                                        value="{{ old('pelunasan_nominal', $dailyReport->pelunasan_nominal) }}"
                                        min="0" step="0.01" required
                                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 @error('pelunasan_nominal') border-red-500 @enderror">
                                    @error('pelunasan_nominal')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- DIVIDER --}}
                        <div class="my-6 border-t border-gray-200"></div>

                        {{-- FOTO YANG SUDAH ADA --}}
                        @if ($dailyReport->photos->count() > 0)
                            <div class="mb-6">
                                <label class="block mb-2 text-sm font-medium text-gray-700">
                                    Foto yang Sudah Ada
                                </label>
                                <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                                    @foreach ($dailyReport->photos as $photo)
                                        <div class="relative">
                                            <img src="{{ asset('storage/' . $photo->file_path) }}"
                                                alt="Foto {{ $loop->iteration }}"
                                                class="object-cover w-full h-32 rounded-lg">
                                            <div class="absolute top-2 right-2">
                                                <label
                                                    class="flex items-center gap-1 px-2 py-1 text-xs text-white bg-red-600 rounded cursor-pointer hover:bg-red-700">
                                                    <input type="checkbox" name="delete_photos[]"
                                                        value="{{ $photo->id }}" class="w-3 h-3">
                                                    Hapus
                                                </label>
                                            </div>
                                            @if ($photo->keterangan)
                                                <p class="mt-1 text-xs text-gray-600">{{ $photo->keterangan }}</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                <p class="mt-2 text-xs text-gray-500">
                                    ✓ Centang "Hapus" untuk menghapus foto
                                </p>
                            </div>
                        @endif

                        {{-- UPLOAD FOTO BARU --}}
                        <div class="mb-6">
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Upload Foto Baru (Opsional)
                            </label>
                            <p class="mb-3 text-xs text-gray-500">
                                📷 Maksimal 10 foto. Format: JPG, JPEG, PNG. Ukuran max: 5MB per foto.
                            </p>

                            <input type="file" name="photos[]" id="photoInput"
                                accept="image/jpeg,image/jpg,image/png" multiple onchange="previewPhotos()"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500">

                            {{-- Preview Photos --}}
                            <div id="photoPreview" class="grid grid-cols-2 gap-4 mt-4 md:grid-cols-4"></div>
                        </div>

                        {{-- KETERANGAN --}}
                        <div class="mb-6">
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Keterangan (Opsional)
                            </label>
                            <textarea name="keterangan" rows="3" placeholder="Catatan tambahan tentang laporan ini..."
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 @error('keterangan') border-red-500 @enderror">{{ old('keterangan', $dailyReport->keterangan) }}</textarea>
                            @error('keterangan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Maksimal 1000 karakter</p>
                        </div>

                        {{-- WARNING BOX --}}
                        @if (!$dailyReport->validasi_manager)
                            <div class="p-4 mb-6 rounded-lg bg-orange-50">
                                <p class="text-sm text-orange-800">
                                    ⚠️ <strong>Perhatian:</strong> Setelah laporan ini divalidasi oleh Manager, Anda
                                    tidak akan bisa mengeditnya lagi.
                                </p>
                            </div>
                        @endif

                        {{-- BUTTONS --}}
                        <div class="flex justify-between">
                            {{-- Delete Button --}}
                            @if (!$dailyReport->validasi_manager || Auth::user()->hasRole('superadmin') || Auth::user()->hasRole('manager'))
                                <button type="button" onclick="confirmDelete()"
                                    class="px-6 py-2 text-red-600 transition border border-red-600 rounded-lg hover:bg-red-50">
                                    🗑️ Hapus Laporan
                                </button>
                            @else
                                <div></div>
                            @endif

                            <div class="flex gap-3">
                                <a href="{{ route('daily-reports.index', ['tanggal' => $dailyReport->tanggal, 'branch_id' => $dailyReport->branch_id]) }}"
                                    class="px-6 py-2 text-gray-700 transition border border-gray-300 rounded-lg hover:bg-gray-50">
                                    Batal
                                </a>
                                <button type="submit"
                                    class="px-6 py-2 text-white transition bg-teal-600 rounded-lg hover:bg-teal-700">
                                    💾 Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </form>

                    {{-- Hidden Delete Form --}}
                    @if (!$dailyReport->validasi_manager || Auth::user()->hasRole('superadmin') || Auth::user()->hasRole('manager'))
                        <form id="deleteForm" action="{{ route('daily-reports.destroy', $dailyReport->id) }}"
                            method="POST" class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                    @endif

                </div>
            </div>

        </div>
    </div>

    {{-- Scripts --}}
    <script>
        function previewPhotos() {
            const input = document.getElementById('photoInput');
            const preview = document.getElementById('photoPreview');
            preview.innerHTML = '';

            if (input.files) {
                Array.from(input.files).forEach((file, index) => {
                    if (index < 10) {
                        const reader = new FileReader();

                        reader.onload = function(e) {
                            const div = document.createElement('div');
                            div.className = 'relative';
                            div.innerHTML = `
                                <img src="${e.target.result}" class="object-cover w-full h-32 rounded-lg">
                                <div class="absolute top-2 right-2">
                                    <span class="px-2 py-1 text-xs text-white bg-black bg-opacity-50 rounded">
                                        Baru ${index + 1}
                                    </span>
                                </div>
                            `;
                            preview.appendChild(div);
                        }

                        reader.readAsDataURL(file);
                    }
                });

                if (input.files.length > 10) {
                    alert('Maksimal 10 foto. Hanya 10 foto pertama yang akan diupload.');
                }
            }
        }

        function confirmDelete() {
            if (confirm('⚠️ Yakin hapus laporan ini?\n\nData yang dihapus tidak dapat dikembalikan!')) {
                document.getElementById('deleteForm').submit();
            }
        }
    </script>
</x-app-layout>
