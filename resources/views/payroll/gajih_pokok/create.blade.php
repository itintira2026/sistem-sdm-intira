<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Input Gaji Pokok - {{ $branch->name }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Periode: {{ Carbon\Carbon::create()->month($bulanSekarang)->format('F') }} {{ $tahunSekarang }}
                </p>
            </div>
            <a href="{{ route('branches.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('gaji-pokok.bulk-store', $branch) }}" method="POST">
                        @csrf

                        <div class="mb-6 grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                                <select name="bulan" required class="w-full border border-gray-300 rounded-lg px-4 py-2">
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ $bulanSekarang == $i ? 'selected' : '' }}>
                                            {{ Carbon\Carbon::create()->month($i)->format('F') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                                <select name="tahun" required class="w-full border border-gray-300 rounded-lg px-4 py-2">
                                    @for($i = 2020; $i <= 2030; $i++)
                                        <option value="{{ $i }}" {{ $tahunSekarang == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-gray-200">
                                        <th class="text-left py-4 px-4 text-sm font-semibold text-gray-600">User</th>
                                        <th class="text-left py-4 px-4 text-sm font-semibold text-gray-600">Role</th>
                                        <th class="text-left py-4 px-4 text-sm font-semibold text-gray-600">Manager</th>
                                        <th class="text-left py-4 px-4 text-sm font-semibold text-gray-600">Gaji Pokok</th>
                                        <th class="text-left py-4 px-4 text-sm font-semibold text-gray-600">History</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($users as $index => $branchUser)
                                        @php
                                            $existingGaji = $branchUser->gajiPokok->first();
                                        @endphp
                                        <tr class="border-b border-gray-100">
                                            <td class="py-4 px-4">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-semibold">
                                                        {{ strtoupper(substr($branchUser->user->name, 0, 2)) }}
                                                    </div>
                                                    <div>
                                                        <p class="font-medium">{{ $branchUser->user->name }}</p>
                                                        <p class="text-sm text-gray-500">{{ $branchUser->user->email }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4 px-4">
                                                @foreach($branchUser->user->roles as $role)
                                                    <span class="px-2 py-1 bg-cyan-100 text-cyan-700 rounded text-xs">
                                                        {{ $role->name }}
                                                    </span>
                                                @endforeach
                                            </td>
                                            <td class="py-4 px-4 text-center">
                                                @if($branchUser->is_manager)
                                                    <span class="text-yellow-500 text-xl">★</span>
                                                @else
                                                    <span class="text-gray-300 text-xl">☆</span>
                                                @endif
                                            </td>
                                            <td class="py-4 px-4">
                                                <input type="hidden" name="gaji_pokok[{{ $index }}][branch_user_id]" value="{{ $branchUser->id }}">
                                                <div class="flex">
                                                    <span class="inline-flex items-center px-3 border border-r-0 border-gray-300 bg-gray-50 text-gray-500 rounded-l-lg text-sm">
                                                        Rp
                                                    </span>
                                                    <input type="number" 
                                                        name="gaji_pokok[{{ $index }}][amount]" 
                                                        value="{{ $existingGaji ? $existingGaji->amount : 0 }}"
                                                        required
                                                        class="flex-1 border border-gray-300 rounded-r-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500">
                                                </div>
                                            </td>
                                            <td class="py-4 px-4">
                                                <a href="{{ route('gaji-pokok.history', [$branch, $branchUser]) }}" 
                                                    class="text-blue-500 hover:text-blue-600 text-sm">
                                                    Lihat History
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="py-8 text-center text-gray-500">
                                                Belum ada user di cabang ini
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($users->count() > 0)
                            <div class="mt-6 flex justify-end">
                                <button type="submit" 
                                    class="px-6 py-2 bg-teal-500 text-white rounded-lg hover:bg-teal-600 transition">
                                    Simpan Semua Gaji Pokok
                                </button>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>