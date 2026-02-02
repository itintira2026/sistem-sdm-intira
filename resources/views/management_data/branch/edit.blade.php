<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Edit Cabang
            </h2>
            <a href="{{ route('branches.index') }}"
                class="flex items-center gap-2 px-4 py-2 text-white transition bg-gray-500 rounded-lg hover:bg-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('branches.update', $branch) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <!-- Nama Cabang -->
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">
                                    Nama Cabang <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="name" value="{{ old('name', $branch->name) }}"
                                    placeholder="Contoh: Cabang Jakarta Pusat"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500 @error('name') border-red-500 @enderror">
                                @error('name')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Kode Cabang -->
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">
                                    Kode Cabang <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="code" value="{{ old('code', $branch->code) }}"
                                    placeholder="Contoh: JKT-PST"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500 @error('code') border-red-500 @enderror">
                                @error('code')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Nomor Telepon -->
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">
                                    Nomor Telepon <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="phone" value="{{ old('phone', $branch->phone) }}"
                                    placeholder="Contoh: 021-12345678"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500 @error('phone') border-red-500 @enderror">
                                @error('phone')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- WILAYAH --}}
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">
                                    Wilayah Waktu <span class="text-red-500">*</span>
                                </label>
                                <select name="timezone" required
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500 @error('timezone') border-red-500 @enderror">
                                    <option {{ old('timezone', $branch->timezone) == 'WIB' ? 'selected' : '' }}
                                        value="WIB">WIB (Waktu Indonesia Barat)</option>
                                    <option {{ old('timezone', $branch->timezone) == 'WITA' ? 'selected' : '' }}
                                        value="WITA">WITA (Waktu Indonesia Tengah)</option>
                                    <option {{ old('timezone', $branch->timezone) == 'WIT' ? 'selected' : '' }}
                                        value="WIT">WIT (Waktu Indonesia Timur)</option>
                                </select>
                            </div>

                            <!-- Longitute -->
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">
                                    Longitude
                                </label>
                                <input type="text" name="longitude"
                                    value="{{ old('longitude', $branch->longitude) }}" placeholder="Contoh: 106.827152"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500 @error('longitude') border-red-500 @enderror">
                                @error('longitude')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <!-- Latitude -->
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">
                                    Latitude
                                </label>
                                <input type="text" name="latitude" value="{{ old('latitude', $branch->latitude) }}"
                                    placeholder="Contoh: -6.208763"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500 @error('latitude') border-red-500 @enderror">
                                @error('latitude')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>

                        <!-- Alamat -->
                        <div class="mt-6">
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Alamat <span class="text-red-500">*</span>
                            </label>
                            <textarea name="address" rows="4" placeholder="Alamat lengkap cabang"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500 @error('address') border-red-500 @enderror">{{ old('address', $branch->address) }}</textarea>
                            @error('address')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Toggle Aktif -->
                        <div class="flex items-center gap-3 mt-6">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" class="sr-only peer"
                                    {{ old('is_active', $branch->is_active) ? 'checked' : '' }}>
                                <div
                                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-teal-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-teal-600">
                                </div>
                            </label>
                            <div>
                                <p class="font-medium text-gray-700">Aktif</p>
                                <p class="text-sm text-gray-500">Cabang aktif dapat digunakan untuk transaksi</p>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="flex justify-end gap-3 mt-8">
                            <a href="{{ route('branches.index') }}"
                                class="px-6 py-2 text-gray-700 transition bg-gray-200 rounded-lg hover:bg-gray-300">
                                Batal
                            </a>
                            <button type="submit"
                                class="flex items-center gap-2 px-6 py-2 text-white transition bg-teal-500 rounded-lg hover:bg-teal-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
