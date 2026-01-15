<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Manajemen Gaji Cabang
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Kelola gaji cabang perusahaan Anda
                </p>
            </div>

            <a href="{{ route('branches.create') }}"
                class="px-4 py-2 bg-teal-500 text-white rounded-lg hover:bg-teal-600 transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Import Gaji
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Alert --}}
            @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <h3 class="text-lg font-semibold text-gray-800 mb-6">
                        Daftar Cabang
                    </h3>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="py-4 px-4 text-left text-sm font-semibold text-gray-600 uppercase">
                                        Kode
                                    </th>
                                    <th class="py-4 px-4 text-left text-sm font-semibold text-gray-600 uppercase">
                                        Nama Cabang
                                    </th>
                                    <th class="py-4 px-4 text-left text-sm font-semibold text-gray-600 uppercase">
                                        Jumlah User
                                    </th>
                                    <th class="py-4 px-4 text-left text-sm font-semibold text-gray-600 uppercase">
                                        Status
                                    </th>
                                    <th class="py-4 px-4 text-left text-sm font-semibold text-gray-600 uppercase">
                                        Dibuat
                                    </th>
                                    <th class="py-4 px-4 text-left text-sm font-semibold text-gray-600 uppercase">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($branches as $branch)

                                @php
                                $branchName = $branch->name ?? 'Unknown';
                                $branchCode = $branch->code ?? '-';
                                $branchActive = isset($branch->is_active) ? $branch->is_active : false;
                                $branchUsers = $branch->users ?? collect();
                                $branchUserCount = $branchUsers->count();
                                $branchCreated = isset($branch->created_at)
                                ? $branch->created_at->format('d M Y')
                                : '-';
                                @endphp

                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="py-4 px-4">
                                        <span class="px-3 py-1 bg-cyan-100 text-cyan-700 rounded text-sm font-medium">
                                            {{ $branchCode }}
                                        </span>
                                    </td>

                                    <td class="py-4 px-4 font-medium text-gray-700">
                                        {{ $branchName }}
                                    </td>

                                    <td class="py-4 px-4">
                                        <span class="px-3 py-1 bg-cyan-100 text-cyan-700 rounded text-sm">
                                            {{ $branchUserCount }} user
                                        </span>
                                    </td>

                                    <td class="py-4 px-4">
                                        @if ($branchActive)
                                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded text-sm font-medium">
                                            Aktif
                                        </span>
                                        @else
                                        <span class="px-3 py-1 bg-red-100 text-red-700 rounded text-sm font-medium">
                                            Tidak Aktif
                                        </span>
                                        @endif
                                    </td>

                                    <td class="py-4 px-4 text-sm text-gray-500">
                                        {{ $branchCreated }}
                                    </td>

                                    <td class="py-4 px-4">
                                        <div class="relative inline-block text-left">

                                            <button type="button" onclick="toggleDropdown({{ $branch->id }})"
                                                class="text-gray-400 hover:text-gray-600">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path
                                                        d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                                </svg>
                                            </button>

                                            <div id="dropdown-{{ $branch->id }}"
                                                class="hidden absolute right-0 mt-2 w-48 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                                <div class="py-1">

                                            
                                                    <a href="{{ route('branchesusers.create', $branch) }}"
                                                        class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                        Gaji Detail
                                                    </a>

                                                    <a href="{{ route('branches.edit', $branch) }}"
                                                        class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                        Potongan Detail
                                                    </a>

                                                   
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                @empty
                                <tr>
                                    <td colspan="6" class="py-6 text-center text-gray-500">
                                        Data cabang belum tersedia
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-6">
                        {{ $branches->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Dropdown Script --}}
    <script>
        function toggleDropdown(id) {
            document.querySelectorAll('[id^="dropdown-"]').forEach(el => {
                if (el.id !== `dropdown-${id}`) {
                    el.classList.add('hidden');
                }
            });

            document.getElementById(`dropdown-${id}`).classList.toggle('hidden');
        }

        document.addEventListener('click', function (e) {
            if (!e.target.closest('.relative')) {
                document.querySelectorAll('[id^="dropdown-"]').forEach(el => {
                    el.classList.add('hidden');
                });
            }
        });
    </script>

</x-app-layout>