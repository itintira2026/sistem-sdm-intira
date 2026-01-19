<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Tambah Potongan/Tambahan - {{ $branch->name }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Periode: {{ Carbon\Carbon::create()->month($bulan)->format('F') }} {{ $tahun }}
                </p>
            </div>
            <a href="{{ route('potongan.index', $branch) }}" 
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

                    <form action="{{ route('potongan.store', $branch) }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Pilih User -->
                            <div class="md:col-span-2">
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

                            <!-- Bulan -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Bulan <span class="text-red-500">*</span>
                                </label>
                                <select name="bulan" required
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500 @error('bulan') border-red-500 @enderror">
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ old('bulan', $bulan) == $i ? 'selected' : '' }}>
                                            {{ Carbon\Carbon::create()->month($i)->format('F') }}
                                        </option>
                                    @endfor
                                </select>
                                @error('bulan')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tahun -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Tahun <span class="text-red-500">*</span>
                                </label>
                                <select name="tahun" required
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500 @error('tahun') border-red-500 @enderror">
                                    @for($i = 2020; $i <= 2030; $i++)
                                        <option value="{{ $i }}" {{ old('tahun', $tahun) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                                @error('tahun')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tanggal -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanggal <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500 @error('tanggal') border-red-500 @enderror">
                                @error('tanggal')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Divisi -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Divisi <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="divisi" value="{{ old('divisi') }}" required
                                    placeholder="Contoh: Finance, Marketing, IT"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500 @error('divisi') border-red-500 @enderror">
                                @error('divisi')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Jenis -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Jenis <span class="text-red-500">*</span>
                                </label>
                                <select name="jenis" required
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500 @error('jenis') border-red-500 @enderror">
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="potongan" {{ old('jenis') == 'potongan' ? 'selected' : '' }}>Potongan</option>
                                    <option value="tambahan" {{ old('jenis') == 'tambahan' ? 'selected' : '' }}>Tambahan</option>
                                </select>
                                @error('jenis')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Amount -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Jumlah <span class="text-red-500">*</span>
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

                            <!-- Keterangan -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Keterangan <span class="text-red-500">*</span>
                                </label>
                                <textarea name="keterangan" rows="3" required
                                    placeholder="Masukkan keterangan detail..."
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500 @error('keterangan') border-red-500 @enderror">{{ old('keterangan') }}</textarea>
                                @error('keterangan')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="mt-8 flex justify-end gap-3">
                            <a href="{{ route('potongan.index', $branch) }}" 
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
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>