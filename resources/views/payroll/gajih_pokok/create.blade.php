<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Tambah Gaji Pokok - {{ $branch->name }}
                </h2>
               
            </div>
            <a href="{{ route('gaji-pokok.detail', ['branch' => $branch->id, 'bulan' => $bulan, 'tahun' => $tahun]) }}" 
                class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if($users->isEmpty())
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        Semua user di cabang ini sudah memiliki gaji pokok untuk periode ini.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @else
                        <form action="{{ route('gaji-pokok.store', $branch) }}" method="POST">
                            @csrf

                            <div class="space-y-6">
                                <!-- Pilih User & Periode -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div class="md:col-span-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Pilih Karyawan <span class="text-red-500">*</span>
                                        </label>
                                        <select name="branch_user_id" required
                                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500 @error('branch_user_id') border-red-500 @enderror">
                                            <option value="">-- Pilih Karyawan --</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" {{ old('branch_user_id', $branchUserId) == $user->id ? 'selected' : '' }}>
                                                    {{ $user->user->name }} - {{ $user->user->email }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('branch_user_id')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Bulan <span class="text-red-500">*</span>
                                        </label>
                                        <select name="bulan" required
                                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500">
                                            @for($i = 1; $i <= 12; $i++)
                                                <option value="{{ $i }}" {{ old('bulan', $bulan) == $i ? 'selected' : '' }}>
                                                    {{ Carbon\Carbon::create()->month($i)->format('F') }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Tahun <span class="text-red-500">*</span>
                                        </label>
                                        <select name="tahun" required
                                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500">
                                            @for($i = 2020; $i <= 2030; $i++)
                                                <option value="{{ $i }}" {{ old('tahun', $tahun) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>

                                <!-- Gaji Pokok -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Gaji Pokok <span class="text-red-500">*</span>
                                    </label>
                                    <div class="flex">
                                        <span class="inline-flex items-center px-3 border border-r-0 border-gray-300 bg-gray-50 text-gray-500 rounded-l-lg">
                                            Rp
                                        </span>
                                        <input type="number" name="amount" value="{{ old('amount', 0) }}" required min="0" step="0.01"
                                            placeholder="0"
                                            class="flex-1 border border-gray-300 rounded-r-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500 @error('amount') border-red-500 @enderror">
                                    </div>
                                    @error('amount')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Tunjangan -->
                                <div class="border-t pt-6">
                                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Tunjangan</h3>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!-- Tunjangan Makan -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Tunjangan Makan
                                            </label>
                                            <div class="flex">
                                                <span class="inline-flex items-center px-3 border border-r-0 border-gray-300 bg-gray-50 text-gray-500 rounded-l-lg text-sm">
                                                    Rp
                                                </span>
                                                <input type="number" name="tunjangan_makan" value="{{ old('tunjangan_makan', 0) }}" min="0" step="0.01"
                                                    placeholder="0"
                                                    class="flex-1 border border-gray-300 rounded-r-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500">
                                            </div>
                                        </div>

                                        <!-- Tunjangan Transportasi -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Tunjangan Transportasi
                                            </label>
                                            <div class="flex">
                                                <span class="inline-flex items-center px-3 border border-r-0 border-gray-300 bg-gray-50 text-gray-500 rounded-l-lg text-sm">
                                                    Rp
                                                </span>
                                                <input type="number" name="tunjangan_transportasi" value="{{ old('tunjangan_transportasi', 0) }}" min="0" step="0.01"
                                                    placeholder="0"
                                                    class="flex-1 border border-gray-300 rounded-r-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500">
                                            </div>
                                        </div>

                                        <!-- Tunjangan Jabatan -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Tunjangan Jabatan
                                            </label>
                                            <div class="flex">
                                                <span class="inline-flex items-center px-3 border border-r-0 border-gray-300 bg-gray-50 text-gray-500 rounded-l-lg text-sm">
                                                    Rp
                                                </span>
                                                <input type="number" name="tunjangan_jabatan" value="{{ old('tunjangan_jabatan', 0) }}" min="0" step="0.01"
                                                    placeholder="0"
                                                    class="flex-1 border border-gray-300 rounded-r-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500">
                                            </div>
                                        </div>

                                        <!-- Tunjangan Komunikasi -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Tunjangan Komunikasi
                                            </label>
                                            <div class="flex">
                                                <span class="inline-flex items-center px-3 border border-r-0 border-gray-300 bg-gray-50 text-gray-500 rounded-l-lg text-sm">
                                                    Rp
                                                </span>
                                                <input type="number" name="tunjangan_komunikasi" value="{{ old('tunjangan_komunikasi', 0) }}" min="0" step="0.01"
                                                    placeholder="0"
                                                    class="flex-1 border border-gray-300 rounded-r-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Keterangan -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Keterangan
                                    </label>
                                    <textarea name="keterangan" rows="3"
                                        placeholder="Keterangan tambahan (opsional)"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500">{{ old('keterangan') }}</textarea>
                                </div>

                                <!-- Buttons -->
                                <div class="flex justify-end gap-3 pt-6">
                                    <a href="{{ route('gaji-pokok.detail', ['branch' => $branch->id, 'bulan' => $bulan, 'tahun' => $tahun]) }}" 
                                        class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                                        Batal
                                    </a>
                                    <button type="submit" 
                                        class="px-6 py-2 bg-teal-500 text-white rounded-lg hover:bg-teal-600 transition flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Simpan
                                    </button>
                                </div>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>