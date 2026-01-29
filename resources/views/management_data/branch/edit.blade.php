<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Cabang
            </h2>
            <a href="{{ route('branches.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('branches.update', $branch) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nama Cabang -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Nama Cabang <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="name" value="{{ old('name', $branch->name) }}" 
                                    placeholder="Contoh: Cabang Jakarta Pusat"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500 @error('name') border-red-500 @enderror">
                                @error('name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Kode Cabang -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Kode Cabang <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="code" value="{{ old('code', $branch->code) }}"
                                    placeholder="Contoh: JKT-PST"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500 @error('code') border-red-500 @enderror">
                                @error('code')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Nomor Telepon -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Nomor Telepon <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="phone" value="{{ old('phone', $branch->phone) }}"
                                    placeholder="Contoh: 021-12345678"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500 @error('phone') border-red-500 @enderror">
                                @error('phone')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>

                        <!-- Alamat -->
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Alamat <span class="text-red-500">*</span>
                            </label>
                            <textarea name="address" rows="4"
                                placeholder="Alamat lengkap cabang"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500 @error('address') border-red-500 @enderror">{{ old('address', $branch->address) }}</textarea>
                            @error('address')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Toggle Aktif -->
                        <div class="mt-6 flex items-center gap-3">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" class="sr-only peer" 
                                    {{ old('is_active', $branch->is_active) ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-teal-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-teal-600"></div>
                            </label>
                            <div>
                                <p class="font-medium text-gray-700">Aktif</p>
                                <p class="text-sm text-gray-500">Cabang aktif dapat digunakan untuk transaksi</p>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="mt-8 flex justify-end gap-3">
                            <a href="{{ route('branches.index') }}" 
                                class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                                Batal
                            </a>
                            <button type="submit" 
                                class="px-6 py-2 bg-teal-500 text-white rounded-lg hover:bg-teal-600 transition flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
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