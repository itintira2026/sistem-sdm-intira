<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Detail Potongan & Tambahan - {{ $branch->name }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Periode: {{ Carbon\Carbon::create()->month($bulan)->format('F') }} {{ $tahun }}
                </p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('gaji.index') }}"
                    class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
                
                <a href="{{ route('potongan.create', ['branch' => $branch->id, 'bulan' => $bulan, 'tahun' => $tahun]) }}"
                    class="px-4 py-2 bg-teal-500 text-white rounded-lg hover:bg-teal-600 transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-red-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Potongan</dt>
                                <dd class="text-lg font-semibold text-red-600">Rp {{ number_format($totalPotongan, 0, ',', '.') }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Tambahan</dt>
                                <dd class="text-lg font-semibold text-green-600">Rp {{ number_format($totalTambahan, 0, ',', '.') }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Items</dt>
                                <dd class="text-lg font-semibold text-gray-900">{{ $totalItems }} Items</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-teal-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">User Periode Ini</dt>
                                <dd class="text-lg font-semibold text-gray-900">{{ $userWithPotongan }} Orang</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Filter Periode -->
                    <form method="GET" class="mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                                <select name="bulan" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500">
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>
                                            {{ Carbon\Carbon::create()->month($i)->format('F') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                                <select name="tahun" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500">
                                    @for($i = 2020; $i <= 2030; $i++)
                                        <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>

                            <div class="flex items-end">
                                <button type="submit" class="w-full px-4 py-2 bg-teal-500 text-white rounded-lg hover:bg-teal-600 transition">
                                    Filter
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Info jika tidak ada data -->
                    @if($users->isEmpty())
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        Tidak ada data potongan untuk periode <strong>{{ Carbon\Carbon::create()->month($bulan)->format('F') }} {{ $tahun }}</strong> di cabang ini.
                                        Silakan pilih periode lain atau input potongan terlebih dahulu.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="text-left py-4 px-4 text-sm font-semibold text-gray-600 uppercase">User</th>
                                    <th class="text-left py-4 px-4 text-sm font-semibold text-gray-600 uppercase">Email</th>
                                    <th class="text-left py-4 px-4 text-sm font-semibold text-gray-600 uppercase">Role</th>
                                    <th class="text-left py-4 px-4 text-sm font-semibold text-gray-600 uppercase">Potongan</th>
                                    <th class="text-left py-4 px-4 text-sm font-semibold text-gray-600 uppercase">Tambahan</th>
                                    <th class="text-left py-4 px-4 text-sm font-semibold text-gray-600 uppercase">Total Items</th>
                                    <th class="text-left py-4 px-4 text-sm font-semibold text-gray-600 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $branchUser)
                                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                                        <td class="py-4 px-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-semibold">
                                                    {{ strtoupper(substr($branchUser->user->name, 0, 2)) }}
                                                </div>
                                                <span class="font-medium text-gray-900">{{ $branchUser->user->name }}</span>
                                            </div>
                                        </td>
                                        <td class="py-4 px-4 text-gray-600">
                                            {{ $branchUser->user->email }}
                                        </td>
                                        <td class="py-4 px-4">
                                            @foreach($branchUser->user->roles as $role)
                                                <span class="px-2 py-1 bg-purple-100 text-purple-700 rounded text-xs">
                                                    {{ $role->name }}
                                                </span>
                                            @endforeach
                                        </td>
                                        <td class="py-4 px-4">
                                            <span class="font-semibold {{ $branchUser->total_potongan > 0 ? 'text-red-600' : 'text-gray-400' }}">
                                                Rp {{ number_format($branchUser->total_potongan, 0, ',', '.') }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-4">
                                            <span class="font-semibold {{ $branchUser->total_tambahan > 0 ? 'text-green-600' : 'text-gray-400' }}">
                                                Rp {{ number_format($branchUser->total_tambahan, 0, ',', '.') }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-4">
                                            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded text-sm">
                                                {{ $branchUser->jumlah_item_potongan }} item
                                            </span>
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="flex gap-2">
                                                {{-- <a href="{{ route('potongan.show', $branchUser->id) }}?bulan={{ $bulan }}&tahun={{ $tahun }}"
                                                    class="text-blue-500 hover:text-blue-600" title="Detail">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                </a> --}}

                                            <a href="{{ route('potongan.destroy', $branchUser->id) }}"
                                                class="text-red-500 hover:text-red-600" title="Hapus Potongan">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-12 text-center">
                                            <div class="flex flex-col items-center justify-center">
                                                <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                <p class="text-gray-500 text-lg font-medium">Tidak ada data potongan</p>
                                                <p class="text-gray-400 text-sm mt-1">Belum ada data potongan untuk periode ini</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>