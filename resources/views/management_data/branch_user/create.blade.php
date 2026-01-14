<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Tambah User ke Cabang {{ $branch->name }}
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
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Form Tambah User -->
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

                    <form action="{{ route('branches.users.store', $branch) }}" method="POST" class="flex items-end gap-4">
                        @csrf
                        
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Pilih User <span class="text-red-500">*</span>
                            </label>
                            <select name="user_id" required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500">
                                <option value="">Select value</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="is_manager" value="1" id="is_manager" 
                                class="w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                            <label for="is_manager" class="text-sm text-gray-700">Manager</label>
                        </div>

                        <button type="submit" 
                            class="px-6 py-2 bg-teal-500 text-white rounded-lg hover:bg-teal-600 transition">
                            + Tambah
                        </button>
                    </form>
                </div>
            </div>

            <!-- User di Cabang Ini -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">User di Cabang Ini</h3>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="text-left py-4 px-4 text-sm font-semibold text-gray-600 uppercase">User</th>
                                    <th class="text-left py-4 px-4 text-sm font-semibold text-gray-600 uppercase">Email</th>
                                    <th class="text-left py-4 px-4 text-sm font-semibold text-gray-600 uppercase">Role</th>
                                    <th class="text-left py-4 px-4 text-sm font-semibold text-gray-600 uppercase">Status</th>
                                    <th class="text-left py-4 px-4 text-sm font-semibold text-gray-600 uppercase">Manager</th>
                                    <th class="text-left py-4 px-4 text-sm font-semibold text-gray-600 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($assignedUsers as $user)
                                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                                        <td class="py-4 px-4">
                                            <div class="flex items-center gap-3">
                                                @if($user->profile_photo)
                                                    <img src="{{ Storage::url($user->profile_photo) }}" 
                                                        class="w-10 h-10 rounded-full object-cover">
                                                @else
                                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-semibold">
                                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                                    </div>
                                                @endif
                                                <span class="font-medium text-gray-700">{{ $user->name }}</span>
                                            </div>
                                        </td>
                                        <td class="py-4 px-4 text-gray-600">{{ $user->email }}</td>
                                        <td class="py-4 px-4">
                                            @foreach($user->roles as $role)
                                                <span class="px-3 py-1 bg-cyan-100 text-cyan-700 rounded text-sm">
                                                    {{ $role->name }}
                                                </span>
                                            @endforeach
                                        </td>
                                        <td class="py-4 px-4">
                                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded text-sm font-medium">
                                                {{ $user->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-4">
                                            <form action="{{ route('branches.users.toggle-manager', [$branch, $user]) }}" 
                                                method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                    class="text-2xl {{ $user->pivot->is_manager ? 'text-yellow-500' : 'text-gray-300' }}">
                                                    ★
                                                </button>
                                            </form>
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="flex gap-2">
                                                <form action="{{ route('branches.users.toggle-manager', [$branch, $user]) }}" 
                                                    method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" 
                                                        class="text-yellow-500 hover:text-yellow-600">
                                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                        </svg>
                                                    </button>
                                                </form>

                                                <form action="{{ route('branches.users.destroy', [$branch, $user]) }}" 
                                                    method="POST" class="inline"
                                                    onsubmit="return confirm('Yakin ingin menghapus user ini dari cabang?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                        class="text-red-500 hover:text-red-600">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-8 text-center text-gray-500">
                                            Belum ada user di cabang ini
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