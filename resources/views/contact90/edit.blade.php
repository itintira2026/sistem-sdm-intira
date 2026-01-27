<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Edit Kontak
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Perbarui data kontak nasabah
                </p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('contact90.index', ['tanggal' => $contact90->tanggal]) }}"
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
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

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
                            Form Edit Kontak
                        </h3>

                        {{-- Status Validasi Badge --}}
                        @if ($contact90->validasi_manager)
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
                                <p><strong>FO:</strong> {{ $contact90->user->name }}</p>
                                <p><strong>Tanggal:</strong> {{ $contact90->tanggal->format('d M Y') }}</p>
                                <p><strong>Waktu Input:</strong> {{ $contact90->created_at->format('d M Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Warning jika sudah divalidasi --}}
                    @if ($contact90->validasi_manager && !Auth::user()->hasRole('superadmin'))
                        <div class="p-4 mb-6 text-yellow-700 bg-yellow-100 rounded-lg">
                            <p class="font-semibold">⚠️ Kontak ini sudah divalidasi oleh Manager</p>
                            <p class="mt-1 text-sm">Anda tidak dapat mengedit kontak yang sudah divalidasi.</p>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('contact90.update', $contact90->id) }}">
                        @csrf
                        @method('PUT')

                        {{-- NAMA NASABAH --}}
                        <div class="mb-6">
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Nama Nasabah <span class="text-red-600">*</span>
                            </label>
                            <input type="text" name="nama_nasabah"
                                value="{{ old('nama_nasabah', $contact90->nama_nasabah) }}" required
                                placeholder="Contoh: Budi Santoso"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 @error('nama_nasabah') border-red-500 @enderror">
                            @error('nama_nasabah')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- AKUN/NO TELP --}}
                        <div class="mb-6">
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Akun/No Telp <span class="text-red-600">*</span>
                            </label>
                            <input type="text" name="akun_or_notelp"
                                value="{{ old('akun_or_notelp', $contact90->akun_or_notelp) }}" required
                                placeholder="Contoh: 0812xxx atau @username"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 @error('akun_or_notelp') border-red-500 @enderror">
                            @error('akun_or_notelp')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- SOSMED --}}
                        <div class="mb-6">
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Platform Media Sosial <span class="text-red-600">*</span>
                            </label>
                            <select name="sosmed" required
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 @error('sosmed') border-red-500 @enderror">
                                <option value="">-- Pilih Platform --</option>
                                <option value="DM_IG"
                                    {{ old('sosmed', $contact90->sosmed) == 'DM_IG' ? 'selected' : '' }}>
                                    📷 DM Instagram
                                </option>
                                <option value="CHAT_WA"
                                    {{ old('sosmed', $contact90->sosmed) == 'CHAT_WA' ? 'selected' : '' }}>
                                    💬 Chat WhatsApp
                                </option>
                                <option value="INBOX_FB"
                                    {{ old('sosmed', $contact90->sosmed) == 'INBOX_FB' ? 'selected' : '' }}>
                                    📘 Inbox Facebook
                                </option>
                                <option value="MRKT_PLACE_FB"
                                    {{ old('sosmed', $contact90->sosmed) == 'MRKT_PLACE_FB' ? 'selected' : '' }}>
                                    🛒 Marketplace Facebook
                                </option>
                                <option value="TIKTOK"
                                    {{ old('sosmed', $contact90->sosmed) == 'TIKTOK' ? 'selected' : '' }}>
                                    🎵 TikTok
                                </option>
                            </select>
                            @error('sosmed')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- SITUASI --}}
                        <div class="mb-6">
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Situasi/Respon <span class="text-red-600">*</span>
                            </label>
                            <select name="situasi" required
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 @error('situasi') border-red-500 @enderror">
                                <option value="">-- Pilih Situasi --</option>
                                <option value="tdk_merespon"
                                    {{ old('situasi', $contact90->situasi) == 'tdk_merespon' ? 'selected' : '' }}>
                                    ❌ Tidak Merespon
                                </option>
                                <option value="merespon"
                                    {{ old('situasi', $contact90->situasi) == 'merespon' ? 'selected' : '' }}>
                                    💬 Merespon
                                </option>
                                <option value="tertarik"
                                    {{ old('situasi', $contact90->situasi) == 'tertarik' ? 'selected' : '' }}>
                                    ⭐ Tertarik
                                </option>
                                <option value="closing"
                                    {{ old('situasi', $contact90->situasi) == 'closing' ? 'selected' : '' }}>
                                    ✅ Closing
                                </option>
                            </select>
                            @error('situasi')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">
                                💡 Update situasi sesuai dengan perkembangan terkini
                            </p>
                        </div>

                        {{-- KETERANGAN --}}
                        <div class="mb-6">
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Keterangan (Opsional)
                            </label>
                            <textarea name="keterangan" rows="3" placeholder="Catatan tambahan tentang kontak ini..."
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 @error('keterangan') border-red-500 @enderror">{{ old('keterangan', $contact90->keterangan) }}</textarea>
                            @error('keterangan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Maksimal 500 karakter</p>
                        </div>

                        {{-- WARNING BOX --}}
                        @if (!$contact90->validasi_manager)
                            <div class="p-4 mb-6 rounded-lg bg-orange-50">
                                <p class="text-sm text-orange-800">
                                    ⚠️ <strong>Perhatian:</strong> Setelah kontak ini divalidasi oleh Manager, Anda
                                    tidak akan bisa mengeditnya lagi.
                                </p>
                            </div>
                        @endif

                        {{-- BUTTONS --}}
                        <div class="flex justify-between">
                            {{-- Delete Button (jika belum validasi atau superadmin) --}}
                            @if (!$contact90->validasi_manager || Auth::user()->hasRole('superadmin'))
                                <button type="button" onclick="confirmDelete()"
                                    class="px-6 py-2 text-red-600 transition border border-red-600 rounded-lg hover:bg-red-50">
                                    🗑️ Hapus Kontak
                                </button>
                            @else
                                <div></div>
                            @endif

                            <div class="flex gap-3">
                                <a href="{{ route('contact90.index', ['tanggal' => $contact90->tanggal]) }}"
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
                    @if (!$contact90->validasi_manager || Auth::user()->hasRole('superadmin'))
                        <form id="deleteForm" action="{{ route('contact90.destroy', $contact90->id) }}"
                            method="POST" class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                    @endif

                </div>
            </div>

        </div>
    </div>

    <script>
        function confirmDelete() {
            if (confirm(
                    '⚠️ Yakin hapus kontak "{{ $contact90->nama_nasabah }}"?\n\nData yang dihapus tidak dapat dikembalikan!'
                    )) {
                document.getElementById('deleteForm').submit();
            }
        }
    </script>
</x-app-layout>
