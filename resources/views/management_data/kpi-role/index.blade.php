<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                🏷️ Set Role untuk KPI
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Atur role struktural untuk Sistem Tugas &amp; KPI (Ketua, Direktur, Kadiv, Staff, Korwil, dst).
                Halaman ini terpisah dari manajemen user utama — role dasar (Manager/FO/dll) tidak diubah di sini.
            </p>
        </div>
    </x-slot>

    <div class="py-12" x-data="{
        open: false,
        userId: null,
        userName: '',
        structuralRole: '',
        korwil: false,
        divisionId: '',
        needsDivision() {
            return this.structuralRole === 'kadiv' || this.structuralRole === 'staff';
        }
    }">
        <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="p-4 text-green-700 bg-green-100 rounded-lg">{{ session('success') }}</div>
            @endif

            {{-- Search --}}
            <div class="p-4 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <form method="GET" action="{{ route('kpi-roles.index') }}">
                    <input type="text" name="search" value="{{ $search }}"
                        placeholder="Cari nama atau username..."
                        class="w-full max-w-sm px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                </form>
            </div>

            {{-- Table --}}
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="border-b border-gray-200 bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-xs font-semibold text-left text-gray-500 uppercase">Nama</th>
                                <th class="px-4 py-3 text-xs font-semibold text-left text-gray-500 uppercase">Role Dasar
                                </th>
                                <th class="px-4 py-3 text-xs font-semibold text-left text-gray-500 uppercase">Role KPI
                                </th>
                                {{-- BARU --}}
                                <th class="px-4 py-3 text-xs font-semibold text-left text-gray-500 uppercase">Divisi
                                </th>
                                <th class="px-4 py-3 text-xs font-semibold text-center text-gray-500 uppercase">Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($users as $u)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-gray-800">{{ $u->name }}</p>
                                        <p class="text-xs text-gray-400">{{ $u->username }}</p>
                                    </td>
                                    <td class="px-4 py-3">
                                        @forelse ($u->base_roles as $role)
                                            <span
                                                class="inline-block px-2 py-0.5 mr-1 text-xs font-medium bg-gray-100 text-gray-600 rounded-full">
                                                {{ $role }}
                                            </span>
                                        @empty
                                            <span class="text-xs text-gray-300">—</span>
                                        @endforelse
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($u->current_structural_role)
                                            <span
                                                class="inline-block px-2 py-0.5 mr-1 text-xs font-semibold bg-teal-100 text-teal-700 rounded-full">
                                                {{ ucwords(str_replace('_', ' ', $u->current_structural_role)) }}
                                            </span>
                                        @endif
                                        @if ($u->has_korwil)
                                            <span
                                                class="inline-block px-2 py-0.5 text-xs font-semibold bg-blue-100 text-blue-700 rounded-full">
                                                Korwil
                                            </span>
                                        @endif
                                        @if (!$u->current_structural_role && !$u->has_korwil)
                                            <span class="text-xs text-gray-300">—</span>
                                        @endif
                                    </td>
                                    {{-- BARU: kolom divisi --}}
                                    <td class="px-4 py-3">
                                        @if ($u->division)
                                            <span
                                                class="inline-block px-2 py-0.5 text-xs font-medium bg-purple-100 text-purple-700 rounded-full">
                                                {{ $u->division->name }}
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-300">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <button type="button"
                                            @click="
                                                open = true;
                                                userId = {{ $u->id }};
                                                userName = @js($u->name);
                                                structuralRole = @js($u->current_structural_role ?? '');
                                                korwil = {{ $u->has_korwil ? 'true' : 'false' }};
                                                divisionId = @js($u->division_id ?? '');
                                            "
                                            class="px-3 py-1.5 text-xs text-white bg-teal-500 rounded-lg hover:bg-teal-600 transition">
                                            Atur Role KPI
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-400">Tidak ada user
                                        ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-4 py-3 border-t border-gray-100">
                    {{ $users->links() }}
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- MODAL — Atur Role KPI                                         --}}
        {{-- ============================================================ --}}
        <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="w-full max-w-md mx-4 bg-white rounded-lg shadow-xl" @click.away="open = false">

                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">
                        Atur Role KPI — <span x-text="userName"></span>
                    </h3>
                    <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>

                <form method="POST" :action="'{{ url('/kpi-roles') }}/' + userId">
                    @csrf
                    @method('PUT')

                    <div class="px-6 py-4 space-y-4">

                        {{-- Role Struktural --}}
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Role Struktural
                            </label>
                            <p class="mb-2 text-xs text-gray-400">Pilih 1 — atau "Tidak ada" kalau user ini gak punya
                                role struktural KPI.</p>

                            <div class="space-y-1.5 max-h-56 overflow-y-auto border border-gray-200 rounded-lg p-3">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="structural_role" value="" x-model="structuralRole"
                                        class="w-4 h-4 text-teal-600 focus:ring-teal-500">
                                    <span class="text-sm text-gray-600">Tidak ada</span>
                                </label>
                                @foreach ($structuralRoles as $role)
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="structural_role" value="{{ $role }}"
                                            x-model="structuralRole" class="w-4 h-4 text-teal-600 focus:ring-teal-500">
                                        <span
                                            class="text-sm text-gray-700">{{ ucwords(str_replace('_', ' ', $role)) }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- BARU: Divisi — muncul cuma kalau role Kadiv/Staff dipilih --}}
                        <div x-show="needsDivision()" x-transition class="pt-3 border-t border-gray-100">
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Divisi <span class="text-red-500">*</span>
                            </label>
                            <select name="division_id" x-model="divisionId"
                                class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                                <option value="">Pilih Divisi</option>
                                @foreach ($divisions as $division)
                                    <option value="{{ $division->id }}">{{ $division->name }}</option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-400">Wajib diisi untuk role Kadiv/Staff.</p>
                            @error('division_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Korwil --}}
                        <div class="pt-3 border-t border-gray-100">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="korwil" value="1" x-model="korwil"
                                    class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                                <span class="text-sm font-medium text-gray-700">Korwil</span>
                            </label>
                            <p class="mt-1 ml-6 text-xs text-gray-400">
                                Tambahan di atas role Manager yang sudah ada — dipakai untuk user yang jadi
                                koordinator wilayah, menaungi beberapa AM/branch sekaligus.
                            </p>
                        </div>

                    </div>

                    <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-100">
                        <button type="button" @click="open = false"
                            class="px-4 py-2 text-sm text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-5 py-2 text-sm text-white bg-teal-500 rounded-lg hover:bg-teal-600">
                            💾 Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
