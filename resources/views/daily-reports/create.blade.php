<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Input Laporan Harian Baru
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Tambahkan laporan harian cabang
                </p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('daily-reports.index') }}"
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
                    <h3 class="mb-6 text-lg font-semibold text-gray-800">
                        Form Input Laporan
                    </h3>

                    <form method="POST" action="{{ route('daily-reports.store') }}" enctype="multipart/form-data">
                        @csrf

                        {{-- 🔥 PILIH CABANG --}}
                        <div class="mb-6">
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Cabang <span class="text-red-600">*</span>
                            </label>
                            <select name="branch_id" required
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 @error('branch_id') border-red-500 @enderror">
                                <option value="">-- Pilih Cabang --</option>
                                @foreach ($branchList as $branch)
                                    <option value="{{ $branch->id }}"
                                        {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('branch_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- 🔥 TANGGAL (Superadmin & Manager) --}}
                        @if (Auth::user()->hasRole('superadmin') || Auth::user()->hasRole('manager'))
                            <div class="mb-6">
                                <label class="block mb-2 text-sm font-medium text-gray-700">
                                    Tanggal <span class="text-red-600">*</span>
                                </label>
                                <input type="date" name="tanggal" value="{{ old('tanggal', now()->toDateString()) }}"
                                    required
                                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 @error('tanggal') border-red-500 @enderror">
                                @error('tanggal')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        @else
                            <div class="p-4 mb-6 rounded-lg bg-blue-50">
                                <p class="text-sm text-blue-800">
                                    📅 Tanggal: <span class="font-semibold">{{ now()->format('d M Y') }}</span>
                                    (Otomatis hari ini)
                                </p>
                            </div>
                        @endif

                        {{-- SHIFT --}}
                        <div class="mb-6">
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Shift <span class="text-red-600">*</span>
                            </label>
                            <select name="shift" required
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 @error('shift') border-red-500 @enderror">
                                <option value="">-- Pilih Shift --</option>
                                <option value="pagi" {{ old('shift') == 'pagi' ? 'selected' : '' }}>
                                    🌅 Shift Pagi (08:00 - 16:00)
                                </option>
                                <option value="siang" {{ old('shift') == 'siang' ? 'selected' : '' }}>
                                    🌆 Shift Siang (14:00 - 22:00)
                                </option>
                            </select>
                            @error('shift')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- DIVIDER --}}
                        <div class="my-6 border-t border-gray-200"></div>

                        {{-- PENCAIRAN (BARANG MASUK) --}}
                        <div class="p-4 mb-6 rounded-lg bg-green-50">
                            <h4 class="mb-4 text-base font-semibold text-green-800">📥 PENCAIRAN (Barang Masuk)</h4>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-700">
                                        Jumlah Barang (unit) <span class="text-red-600">*</span>
                                    </label>
                                    <input type="number" name="pencairan_jumlah_barang"
                                        value="{{ old('pencairan_jumlah_barang', 0) }}" min="0" required
                                        placeholder="Contoh: 25"
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
                                        value="{{ old('pencairan_nominal', 0) }}" min="0" step="0.01"
                                        required placeholder="Contoh: 50000000"
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
                                        value="{{ old('pelunasan_jumlah_barang', 0) }}" min="0" required
                                        placeholder="Contoh: 15"
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
                                        value="{{ old('pelunasan_nominal', 0) }}" min="0" step="0.01"
                                        required placeholder="Contoh: 30000000"
                                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 @error('pelunasan_nominal') border-red-500 @enderror">
                                    @error('pelunasan_nominal')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- DIVIDER --}}
                        <div class="my-6 border-t border-gray-200"></div>

                        {{-- BUKTI FOTO --}}
                        <div class="mb-6">
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Bukti Foto <span class="text-red-600">*</span>
                            </label>
                            <p class="mb-3 text-xs text-gray-500">
                                📷 Minimal 1 foto, maksimal 10 foto. Format: JPG, JPEG, PNG. Ukuran max: 5MB per foto.
                            </p>

                            <input type="file" name="photos[]" id="photoInput"
                                accept="image/jpeg,image/jpg,image/png" multiple required onchange="previewPhotos()"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 @error('photos') border-red-500 @enderror">

                            @error('photos')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            @error('photos.*')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror

                            {{-- Preview Photos --}}
                            <div id="photoPreview" class="grid grid-cols-2 gap-4 mt-4 md:grid-cols-4"></div>
                        </div>

                        {{-- KETERANGAN --}}
                        <div class="mb-6">
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Keterangan (Opsional)
                            </label>
                            <textarea name="keterangan" rows="3" placeholder="Catatan tambahan tentang laporan ini..."
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 @error('keterangan') border-red-500 @enderror">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Maksimal 1000 karakter</p>
                        </div>

                        {{-- INFO BOX --}}
                        <div class="p-4 mb-6 rounded-lg bg-teal-50">
                            <p class="text-sm text-teal-800">
                                💡 <strong>Tips:</strong> Pastikan data yang diinput sudah benar. Setiap cabang hanya
                                bisa input 1 laporan per shift per hari.
                            </p>
                        </div>

                        {{-- BUTTONS --}}
                        <div class="flex justify-end gap-3">
                            <a href="{{ route('daily-reports.index') }}"
                                class="px-6 py-2 text-gray-700 transition border border-gray-300 rounded-lg hover:bg-gray-50">
                                Batal
                            </a>
                            <button type="submit"
                                class="px-6 py-2 text-white transition bg-teal-600 rounded-lg hover:bg-teal-700">
                                💾 Simpan Laporan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    {{-- Photo Preview Script --}}
    <script>
        function previewPhotos() {
            const input = document.getElementById('photoInput');
            const preview = document.getElementById('photoPreview');
            preview.innerHTML = '';

            if (input.files) {
                Array.from(input.files).forEach((file, index) => {
                    if (index < 10) { // Max 10 photos
                        const reader = new FileReader();

                        reader.onload = function(e) {
                            const div = document.createElement('div');
                            div.className = 'relative';
                            div.innerHTML = `
                                <img src="${e.target.result}" class="object-cover w-full h-32 rounded-lg">
                                <div class="absolute top-2 right-2">
                                    <span class="px-2 py-1 text-xs text-white bg-black bg-opacity-50 rounded">
                                        ${index + 1}
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
    </script>
</x-app-layout>
