<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Daftar Front Office (FO)
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Monitoring performa FO per tanggal
                </p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('contact90.manager.dashboard', ['tanggal' => $tanggal]) }}"
                    class="flex items-center gap-2 px-4 py-2 text-white transition bg-gray-500 rounded-lg hover:bg-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Dashboard
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            {{-- 🔥 TEMPORARY: SUPERADMIN ONLY NOTICE --}}
            {{-- 🔥 TODO: Nanti ganti middleware ke 'manager' juga bisa akses --}}
            <div class="p-4 mb-6 text-purple-700 bg-purple-100 rounded-lg">
                <p class="font-semibold">🔐 Temporary Access: Superadmin Only</p>
                <p class="mt-1 text-sm">
                    Saat ini halaman ini hanya bisa diakses oleh Superadmin.
                    <strong>Nanti akan dibuka untuk role "manager"</strong> dengan menambahkan middleware di route.
                </p>
            </div>

            {{-- 🔥 INFO CABANG YANG DI-MANAGE --}}
            @if (!Auth::user()->hasRole('superadmin'))
                <div class="p-4 mb-6 bg-blue-100 rounded-lg">
                    <p class="font-semibold text-blue-800">📍 Cabang yang Anda Kelola:</p>
                    <div class="flex flex-wrap gap-2 mt-2">
                        @foreach ($managedBranches as $branch)
                            <span class="px-3 py-1 text-sm text-blue-700 bg-white rounded-full">
                                {{ $branch->name }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="p-4 mb-6 bg-gray-100 rounded-lg">
                    <p class="text-sm text-gray-700">
                        🔓 <strong>Superadmin Mode:</strong> Anda bisa melihat semua cabang dan semua FO.
                    </p>
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="mb-6 text-lg font-semibold text-gray-800">
                        Daftar FO & Progress Kontak
                    </h3>

                    {{-- FILTER --}}
                    <form method="GET" class="flex flex-wrap gap-4 mb-6">
                        {{-- TANGGAL --}}
                        <div>
                            <input type="date" name="tanggal" value="{{ request('tanggal', $tanggal) }}"
                                onchange="this.form.submit()" class="px-4 py-2 border rounded-lg">
                        </div>

                        {{-- 🔥 FILTER CABANG (jika manager punya multiple branches) --}}
                        @if ($managedBranches->count() > 1)
                            <div>
                                <select name="branch_id" onchange="this.form.submit()"
                                    class="px-4 py-2 pr-10 border rounded-lg">
                                    <option value="">Semua Cabang</option>
                                    @foreach ($managedBranches as $branch)
                                        <option value="{{ $branch->id }}"
                                            {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        {{-- SEARCH --}}
                        <div class="relative flex-1 min-w-[250px]">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari nama FO..." class="w-full px-4 py-2 border rounded-lg">
                        </div>

                        <button type="submit" class="px-4 py-2 text-white bg-teal-600 rounded-lg hover:bg-teal-700">
                            Cari
                        </button>
                    </form>

                    {{-- SUMMARY CARDS --}}
                    <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-3">
                        <div class="p-4 rounded-lg bg-teal-50">
                            <p class="text-sm text-teal-700">Total FO</p>
                            <p class="mt-1 text-2xl font-bold text-teal-800">{{ $foList->count() }}</p>
                        </div>
                        <div class="p-4 rounded-lg bg-green-50">
                            <p class="text-sm text-green-700">Target Tercapai</p>
                            <p class="mt-1 text-2xl font-bold text-green-800">
                                {{ $foList->where('kontak_total', '>=', 90)->count() }}
                            </p>
                        </div>
                        <div class="p-4 rounded-lg bg-orange-50">
                            <p class="text-sm text-orange-700">Belum Tercapai</p>
                            <p class="mt-1 text-2xl font-bold text-orange-800">
                                {{ $foList->where('kontak_total', '<', 90)->count() }}
                            </p>
                        </div>
                    </div>

                    {{-- TABLE --}}
                    <div class="relative w-full overflow-x-auto md:overflow-x-visible">
                        <table class="w-full text-sm min-w-max whitespace-nowrap">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="px-4 py-4 min-w-[50px] text-left text-gray-600 uppercase">#</th>
                                    <th class="px-4 py-4 min-w-[180px] text-left text-gray-600 uppercase">Nama FO</th>
                                    <th class="px-4 py-4 min-w-[140px] text-left text-gray-600 uppercase">Cabang</th>
                                    <th class="px-4 py-4 min-w-[120px] text-left text-gray-600 uppercase">Kontak Hari
                                        Ini</th>
                                    <th class="px-4 py-4 min-w-[100px] text-left text-gray-600 uppercase">Progress</th>
                                    <th class="px-4 py-4 min-w-[120px] text-left text-gray-600 uppercase">Validasi</th>
                                    <th class="px-4 py-4 min-w-[100px] text-left text-gray-600 uppercase">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($foList as $index => $fo)
                                    <tr class="border-t hover:bg-gray-50">
                                        <td class="px-4 py-3">{{ $index + 1 }}</td>
                                        <td class="px-4 py-3">
                                            <div>
                                                <p class="font-medium text-gray-800">{{ $fo->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $fo->email }}</p>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex flex-wrap gap-1">
                                                @foreach ($fo->branches as $branch)
                                                    <span class="px-2 py-1 text-xs text-blue-700 bg-blue-100 rounded">
                                                        {{ $branch->name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <p class="text-lg font-bold text-gray-800">
                                                {{ $fo->kontak_total }}<span class="text-sm text-gray-400">/90</span>
                                            </p>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="w-full h-2 bg-gray-200 rounded-full">
                                                <div class="h-2 rounded-full {{ $fo->kontak_total >= 90 ? 'bg-green-600' : 'bg-orange-600' }}"
                                                    style="width: {{ min(($fo->kontak_total / 90) * 100, 100) }}%">
                                                </div>
                                            </div>
                                            <p class="mt-1 text-xs text-gray-600">
                                                {{ number_format(min(($fo->kontak_total / 90) * 100, 100), 0) }}%
                                            </p>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="text-sm">
                                                <p class="text-green-700">✅ {{ $fo->kontak_validated }}</p>
                                                <p class="text-orange-700">⏳ {{ $fo->kontak_pending }}</p>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex gap-2">
                                                <a href="{{ route('contact90.manager.fodetail', ['user' => $fo->id, 'tanggal' => $tanggal]) }}"
                                                    class="flex items-center gap-1 px-3 py-1 text-sm text-white transition bg-teal-600 rounded hover:bg-teal-700">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    Detail
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                            Tidak ada FO yang ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- INFO BOX --}}
                    @if ($foList->isNotEmpty())
                        <div class="p-4 mt-6 rounded-lg bg-gray-50">
                            <p class="text-sm text-gray-700">
                                💡 <strong>Tips:</strong> Klik "Detail" untuk melihat daftar kontak yang diinput oleh
                                FO dan melakukan validasi.
                            </p>
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
