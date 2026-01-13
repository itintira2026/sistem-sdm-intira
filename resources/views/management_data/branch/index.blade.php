<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Manajemen Cabang
                </h2>
                <p class="text-sm text-gray-500 mt-1">Kelola data cabang perusahaan Anda</p>
            </div>
            <div class="flex gap-3">
                <button
                    class="px-4 py-2 bg-green-100 text-green-600 rounded-lg hover:bg-green-200 transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Import Cabang
                </button>
                <a href="{{ route('branches.create') }}"
                    class="px-4 py-2 bg-teal-500 text-white rounded-lg hover:bg-teal-600 transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Cabang
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Header Section -->
                    <h3 class="text-lg font-semibold text-gray-800 mb-6">Daftar Cabang</h3>

                    <!-- Filter Section -->
                    <div class="flex gap-4 mb-6">
                        <!-- Entries per page -->
                        <div class="relative">
                            <select
                                class="appearance-none border border-gray-300 rounded-lg px-4 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-teal-500">
                                <option>10</option>
                                <option>25</option>
                                <option>50</option>
                                <option>100</option>
                            </select>
                            <svg class="absolute right-3 top-3 w-4 h-4 text-gray-400 pointer-events-none" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>

                        <!-- Status Filter -->
                        <div class="relative">
                            <select
                                class="appearance-none border border-gray-300 rounded-lg px-4 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-teal-500">
                                <option>Semua Status</option>
                                <option>Aktif</option>
                                <option>Tidak Aktif</option>
                            </select>
                            <svg class="absolute right-3 top-3 w-4 h-4 text-gray-400 pointer-events-none" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>

                        <!-- Search -->
                        <div class="flex-1 relative">
                            <svg class="absolute left-3 top-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input type="text" placeholder="Cari nama, kode, telepon, atau alamat..."
                                class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="text-left py-4 px-4 text-sm font-semibold text-gray-600 uppercase">Kode
                                    </th>
                                    <th class="text-left py-4 px-4 text-sm font-semibold text-gray-600 uppercase">Nama
                                        Cabang</th>
                                    <th class="text-left py-4 px-4 text-sm font-semibold text-gray-600 uppercase">Jumlah
                                        User</th>
                                    <th class="text-left py-4 px-4 text-sm font-semibold text-gray-600 uppercase">Saldo
                                        Kas</th>
                                    <th class="text-left py-4 px-4 text-sm font-semibold text-gray-600 uppercase">Status
                                    </th>
                                    <th class="text-left py-4 px-4 text-sm font-semibold text-gray-600 uppercase">Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Row 1 -->
                                @foreach ( $branches as $branch )
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="py-4 px-4">
                                        <span class="px-3 py-1 bg-cyan-100 text-cyan-700 rounded text-sm font-medium">{{
                                            $branch->code }}</span>
                                    </td>
                                    <td class="py-4 px-4 font-medium text-gray-700">{{ $branch->name }}</td>
                                    <td class="py-4 px-4">
                                        <span class="px-3 py-1 bg-cyan-100 text-cyan-700 rounded text-sm">3 user</span>
                                    </td>
                                    <td class="py-4 px-4 text-gray-700">Rp 0</td>
                                    <td class="py-4 px-4">
                                        <span
                                            class="px-3 py-1 bg-green-100 text-green-700 rounded text-sm font-medium">Aktif</span>
                                    </td>
                                    <td class="py-4 px-4">
                                        <button class="text-gray-400 hover:text-gray-600">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                                {{-- @empty
                                <tr class="border-b border-gray-100 hover:bg-gray-50"> </tr>
                                <td colspan="6" class="py-4 px-4 text-center text-gray-500">Tidak ada data cabang</td>
                                </tr> --}}

                                @endforeach
                                {{-- <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="py-4 px-4">
                                        <span
                                            class="px-3 py-1 bg-cyan-100 text-cyan-700 rounded text-sm font-medium">JBI-004</span>
                                    </td>
                                    <td class="py-4 px-4 font-medium text-gray-700">KC PATTIMURA JAMBI</td>
                                    <td class="py-4 px-4">
                                        <span class="px-3 py-1 bg-cyan-100 text-cyan-700 rounded text-sm">3 user</span>
                                    </td>
                                    <td class="py-4 px-4 text-gray-700">Rp 0</td>
                                    <td class="py-4 px-4">
                                        <span
                                            class="px-3 py-1 bg-green-100 text-green-700 rounded text-sm font-medium">Aktif</span>
                                    </td>
                                    <td class="py-4 px-4">
                                        <button class="text-gray-400 hover:text-gray-600">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr> --}}

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>