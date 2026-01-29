<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Input Kontak Baru
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Tambahkan kontak nasabah yang berhasil dihubungi hari ini
                </p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('contact90.index') }}"
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
                    <h3 class="mb-6 text-lg font-semibold text-gray-800">
                        Form Input Kontak
                    </h3>

                    <form method="POST" action="{{ route('contact90.store') }}">
                        @csrf

                        {{-- 🔥 SUPERADMIN: PILIH FO --}}
                        {{-- 🔥 SUPERADMIN & MANAGER: PILIH FO --}}
                        @if ((Auth::user()->hasRole('superadmin') || Auth::user()->hasRole('manager')) && isset($foList))
                            <div
                                class="p-4 mb-6 rounded-lg {{ Auth::user()->hasRole('superadmin') ? 'bg-yellow-50' : 'bg-blue-50' }}">
                                <label class="block mb-2 text-sm font-medium text-gray-700">
                                    @if (Auth::user()->hasRole('superadmin'))
                                        🔐 Superadmin Mode: Input Atas Nama FO <span class="text-red-600">*</span>
                                    @else
                                        👨‍💼 Manager Mode: Input Atas Nama FO di Cabang Anda <span
                                            class="text-red-600">*</span>
                                    @endif
                                </label>
                                <select name="user_id" required
                                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500">
                                    <option value="">-- Pilih FO --</option>
                                    @foreach ($foList as $fo)
                                        <option value="{{ $fo->id }}"
                                            {{ old('user_id') == $fo->id ? 'selected' : '' }}>
                                            {{ $fo->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-6">
                                <label class="block mb-2 text-sm font-medium text-gray-700">
                                    Tanggal <span class="text-red-600">*</span>
                                </label>
                                <input type="date" name="tanggal" value="{{ old('tanggal', now()->toDateString()) }}"
                                    required
                                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500">
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
                        {{-- @if (Auth::user()->hasRole('superadmin') && isset($foList))
                            <div class="p-4 mb-6 rounded-lg bg-yellow-50">
                                <label class="block mb-2 text-sm font-medium text-gray-700">
                                    🔐 Superadmin Mode: Input Atas Nama FO <span class="text-red-600">*</span>
                                </label>
                                <select name="user_id" required
                                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500">
                                    <option value="">-- Pilih FO --</option>
                                    @foreach ($foList as $fo)
                                        <option value="{{ $fo->id }}"
                                            {{ old('user_id') == $fo->id ? 'selected' : '' }}>
                                            {{ $fo->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-6">
                                <label class="block mb-2 text-sm font-medium text-gray-700">
                                    Tanggal <span class="text-red-600">*</span>
                                </label>
                                <input type="date" name="tanggal" value="{{ old('tanggal', now()->toDateString()) }}"
                                    required
                                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500">
                                @error('tanggal')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        @else
                            <div class="p-4 mb-6 rounded-lg bg-blue-50">
                                <p class="text-sm text-blue-800">
                                    📅 Tanggal: <span class="font-semibold">{{ now()->format('d M Y') }}</span>
                                    (Otomatis
                                    hari ini)
                                </p>
                            </div>
                        @endif --}}

                        {{-- NAMA NASABAH --}}
                        <div class="mb-6">
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Nama Nasabah <span class="text-red-600">*</span>
                            </label>
                            <input type="text" name="nama_nasabah" value="{{ old('nama_nasabah') }}" required
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
                            <input type="text" name="akun_or_notelp" value="{{ old('akun_or_notelp') }}" required
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
                                <option value="DM_IG" {{ old('sosmed') == 'DM_IG' ? 'selected' : '' }}>
                                    📷 DM Instagram
                                </option>
                                <option value="CHAT_WA" {{ old('sosmed') == 'CHAT_WA' ? 'selected' : '' }}>
                                    💬 Chat WhatsApp
                                </option>
                                <option value="INBOX_FB" {{ old('sosmed') == 'INBOX_FB' ? 'selected' : '' }}>
                                    📘 Inbox Facebook
                                </option>
                                <option value="MRKT_PLACE_FB" {{ old('sosmed') == 'MRKT_PLACE_FB' ? 'selected' : '' }}>
                                    🛒 Marketplace Facebook
                                </option>
                                <option value="TIKTOK" {{ old('sosmed') == 'TIKTOK' ? 'selected' : '' }}>
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
                                <option value="tdk_merespon" {{ old('situasi') == 'tdk_merespon' ? 'selected' : '' }}>
                                    ❌ Tidak Merespon
                                </option>
                                <option value="merespon" {{ old('situasi') == 'merespon' ? 'selected' : '' }}>
                                    💬 Merespon
                                </option>
                                <option value="tertarik" {{ old('situasi') == 'tertarik' ? 'selected' : '' }}>
                                    ⭐ Tertarik
                                </option>
                                <option value="closing" {{ old('situasi') == 'closing' ? 'selected' : '' }}>
                                    ✅ Closing
                                </option>
                            </select>
                            @error('situasi')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- KETERANGAN --}}
                        <div class="mb-6">
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Keterangan (Opsional)
                            </label>
                            <textarea name="keterangan" rows="3" placeholder="Catatan tambahan tentang kontak ini..."
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500 @error('keterangan') border-red-500 @enderror">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Maksimal 500 karakter</p>
                        </div>

                        {{-- INFO BOX --}}
                        <div class="p-4 mb-6 rounded-lg bg-teal-50">
                            <p class="text-sm text-teal-800">
                                💡 <strong>Tips:</strong> Pastikan data yang diinput sudah benar. Nama nasabah tidak
                                boleh duplikat di tanggal yang sama.
                            </p>
                        </div>

                        {{-- BUTTONS --}}
                        <div class="flex justify-end gap-3">
                            <a href="{{ route('contact90.index') }}"
                                class="px-6 py-2 text-gray-700 transition border border-gray-300 rounded-lg hover:bg-gray-50">
                                Batal
                            </a>
                            <button type="submit"
                                class="px-6 py-2 text-white transition bg-teal-600 rounded-lg hover:bg-teal-700">
                                💾 Simpan Kontak
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
