<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Manajemen Pengguna
                </h2>
                <p class="text-sm text-gray-500 mt-1">Kelola data Pengguna perusahaan Anda</p>
            </div>
            <div class="flex gap-3">
                <button class="px-4 py-2 bg-green-100 text-green-600 rounded-lg hover:bg-green-200 transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Import User
                </button>
                <a href="{{ route('users.create') }}" class="px-4 py-2 bg-teal-500 text-white rounded-lg hover:bg-teal-600 transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Pengguna
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-6">Data Pengguna</h3>

                    <div class="flex gap-4 mb-6">
                        <div class="relative">
                            <select class="appearance-none border border-gray-300 rounded-lg px-4 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-teal-500">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>

                        <div class="relative">
                            <select class="appearance-none border border-gray-300 rounded-lg px-4 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-teal-500">
                                <option value="">Semua Role</option>
                                @if(isset($roles) && count($roles) > 0)
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="flex-1 relative">
                            <svg class="absolute left-3 top-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input type="text" placeholder="Cari nama, email, atau username..." class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="text-left py-4 px-4 text-sm font-semibold text-gray-600 uppercase">Nama</th>
                                    <th class="text-left py-4 px-4 text-sm font-semibold text-gray-600 uppercase">Cabang</th>
                                    <th class="text-left py-4 px-4 text-sm font-semibold text-gray-600 uppercase">Role</th>
                                    <th class="text-left py-4 px-4 text-sm font-semibold text-gray-600 uppercase">Status</th>
                                    <th class="text-left py-4 px-4 text-sm font-semibold text-gray-600 uppercase">Dibuat</th>
                                    <th class="text-left py-4 px-4 text-sm font-semibold text-gray-600 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $userList = isset($users) ? $users : [];
                                @endphp
                                
                                @forelse($userList as $user)
                                    @php
                                        $userName = $user->name ?? 'Unknown';
                                        $userEmail = $user->email ?? '-';
                                        $userInitials = strtoupper(substr($userName, 0, 2));
                                        $userActive = isset($user->is_active) ? $user->is_active : false;
                                        $userRoles = $user->roles ?? collect();
                                        $userCreated = isset($user->created_at) ? $user->created_at->format('d M Y') : '-';
                                    @endphp
                                    
                                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                                        <td class="py-4 px-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-full bg-teal-100 flex items-center justify-center text-teal-600 font-semibold">
                                                    {{ $userInitials }}
                                                </div>
                                                <div>
                                                    <div class="font-medium text-gray-700">{{ $userName }}</div>
                                                    <div class="text-sm text-gray-500">{{ $userEmail }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-4">
                                            <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded text-sm">
                                                Tidak ada cabang
                                            </span>
                                        </td>
                                        <td class="py-4 px-4">
                                            @if($userRoles->isNotEmpty())
                                                <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded text-sm">
                                                    {{ $userRoles->first()->name }}
                                                </span>
                                            @else
                                                <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded text-sm">
                                                    Tidak ada role
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4">
                                            @if($userActive)
                                                <span class="px-3 py-1 bg-green-100 text-green-700 rounded text-sm font-medium inline-flex items-center gap-1">
                                                    <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                                    Aktif
                                                </span>
                                            @else
                                                <span class="px-3 py-1 bg-red-100 text-red-700 rounded text-sm font-medium">
                                                    Tidak Aktif
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4 text-gray-700">
                                            {{ $userCreated }}
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="relative inline-block text-left dropdown-container">
                                                <button type="button" onclick="toggleDropdown({{ $user->id }})" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                                    </svg>
                                                </button>
                                                
                                                <div id="dropdown-{{ $user->id }}" class="hidden absolute right-0 mt-2 w-48 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                                    <div class="py-1">
                                                        <a href="{{ route('users.show', $user->id) }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                            </svg>
                                                            Detail
                                                        </a>
                                                        
                                                        @if($userActive)
                                                            <form action="{{ route('users.deactivate', $user->id) }}" method="POST">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="flex items-center gap-2 w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                                    </svg>
                                                                    Nonaktifkan
                                                                </button>
                                                            </form>
                                                        @else
                                                            <form action="{{ route('users.activate', $user->id) }}" method="POST">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="flex items-center gap-2 w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                                    </svg>
                                                                    Aktifkan
                                                                </button>
                                                            </form>
                                                        @endif
                                                        
                                                        <a href="{{ route('users.edit', $user->id) }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                            </svg>
                                                            Edit
                                                        </a>
                                                        
                                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="flex items-center gap-2 w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                </svg>
                                                                Hapus
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-8 text-center text-gray-500">
                                            Tidak ada data pengguna
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if(isset($users) && method_exists($users, 'links'))
                        <div class="mt-6">
                            {{ $users->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleDropdown(userId) {
            var dropdown = document.getElementById('dropdown-' + userId);
            var allDropdowns = document.querySelectorAll('[id^="dropdown-"]');
            
            for (var i = 0; i < allDropdowns.length; i++) {
                if (allDropdowns[i].id !== 'dropdown-' + userId) {
                    allDropdowns[i].classList.add('hidden');
                }
            }
            
            dropdown.classList.toggle('hidden');
        }

        document.addEventListener('click', function(event) {
            var isButton = false;
            var isDropdown = false;
            
            var target = event.target;
            while (target) {
                if (target.tagName === 'BUTTON') {
                    isButton = true;
                    break;
                }
                if (target.id && target.id.indexOf('dropdown-') === 0) {
                    isDropdown = true;
                    break;
                }
                target = target.parentElement;
            }
            
            if (!isButton && !isDropdown) {
                var allDropdowns = document.querySelectorAll('[id^="dropdown-"]');
                for (var i = 0; i < allDropdowns.length; i++) {
                    allDropdowns[i].classList.add('hidden');
                }
            }
        });
    </script>
</x-app-layout>