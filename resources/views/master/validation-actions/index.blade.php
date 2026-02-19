<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">⚙️ Tindakan Validasi</h2>
        <p class="mt-1 text-sm text-gray-500">Kelola opsi tindakan yang dipilih manager saat validasi laporan FO</p>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="p-4 mb-4 text-green-700 bg-green-100 rounded-lg">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="p-4 mb-4 text-red-700 bg-red-100 rounded-lg">{{ session('error') }}</div>
            @endif

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

                {{-- ======================================================== --}}
                {{-- TABEL TINDAKAN                                             --}}
                {{-- ======================================================== --}}
                <div class="lg:col-span-2">
                    <div class="overflow-hidden bg-white rounded-lg shadow-sm">
                        <div class="px-5 py-4 border-b border-gray-100">
                            <h3 class="font-semibold text-gray-800">Daftar Tindakan</h3>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $actions->count() }} tindakan terdaftar</p>
                        </div>

                        @if ($actions->isEmpty())
                            <div class="py-12 text-center text-gray-400">
                                <p class="mb-2 text-3xl">📋</p>
                                <p class="text-sm">Belum ada tindakan validasi.</p>
                            </div>
                        @else
                            <table class="w-full text-sm">
                                <thead class="text-xs text-gray-500 uppercase bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left">Nama Tindakan</th>
                                        <th class="px-4 py-3 text-left">Kode</th>
                                        <th class="px-4 py-3 text-center">Order</th>
                                        <th class="px-4 py-3 text-center">Status</th>
                                        <th class="px-4 py-3 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($actions as $action)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 font-medium text-gray-800">
                                                {{ $action->name }}
                                            </td>
                                            <td class="px-4 py-3">
                                                <code
                                                    class="px-1.5 py-0.5 text-xs bg-gray-100 rounded text-gray-600">{{ $action->code }}</code>
                                            </td>
                                            <td class="px-4 py-3 text-center text-gray-500">{{ $action->order }}</td>
                                            <td class="px-4 py-3 text-center">
                                                @if ($action->is_active)
                                                    <span
                                                        class="px-2 py-0.5 text-xs font-semibold text-green-700 bg-green-100 rounded-full">Aktif</span>
                                                @else
                                                    <span
                                                        class="px-2 py-0.5 text-xs font-semibold text-gray-500 bg-gray-100 rounded-full">Nonaktif</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <div class="flex justify-center gap-2">
                                                    <button onclick="openEditAction({{ $action->toJson() }})"
                                                        class="px-2 py-1 text-xs text-blue-700 rounded bg-blue-50 hover:bg-blue-100">
                                                        Edit
                                                    </button>
                                                    <form method="POST"
                                                        action="{{ route('master.validation-actions.destroy', $action->id) }}"
                                                        onsubmit="return confirm('Hapus tindakan {{ $action->name }}?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit"
                                                            class="px-2 py-1 text-xs text-red-700 rounded bg-red-50 hover:bg-red-100 disabled:opacity-40 disabled:cursor-not-allowed"
                                                            {{ $action->validations_count > 0 ? 'disabled title="Sudah digunakan di laporan"' : '' }}>
                                                            Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>

                {{-- ======================================================== --}}
                {{-- FORM TAMBAH / EDIT                                         --}}
                {{-- ======================================================== --}}
                <div class="lg:col-span-1">
                    <div class="sticky p-5 bg-white rounded-lg shadow-sm top-4" id="actionFormCard">
                        <h3 class="mb-4 font-semibold text-gray-800" id="actionFormTitle">➕ Tambah Tindakan</h3>

                        <form id="actionForm" method="POST" action="{{ route('master.validation-actions.store') }}">
                            @csrf
                            <span id="actionMethodField"></span>

                            <div class="space-y-4">
                                <div>
                                    <label class="block mb-1 text-xs font-medium text-gray-600">Nama Tindakan <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" name="name" id="aInputName" required
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500"
                                        placeholder="e.g. Memberikan Coaching">
                                    @error('name')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block mb-1 text-xs font-medium text-gray-600">Kode <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" name="code" id="aInputCode" required
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500"
                                        placeholder="e.g. memberikan_coaching">
                                    <p class="mt-0.5 text-xs text-gray-400">Huruf kecil, angka, underscore.</p>
                                    @error('code')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block mb-1 text-xs font-medium text-gray-600">Urutan</label>
                                    <input type="number" name="order" id="aInputOrder" min="0" value="0"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
                                </div>

                                <div class="flex items-center gap-2">
                                    <input type="checkbox" name="is_active" id="aInputActive" value="1" checked
                                        class="w-4 h-4 text-teal-600 border-gray-300 rounded">
                                    <label for="aInputActive" class="text-sm text-gray-700">Aktif</label>
                                </div>
                            </div>

                            <div class="flex gap-2 mt-5">
                                <button type="submit"
                                    class="flex-1 px-4 py-2 text-sm text-white bg-teal-600 rounded-lg hover:bg-teal-700">
                                    💾 Simpan
                                </button>
                                <button type="button" onclick="resetActionForm()"
                                    class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">
                                    Reset
                                </button>
                            </div>
                        </form>

                        {{-- Info box --}}
                        <div class="p-3 mt-4 text-xs text-blue-700 rounded-lg bg-blue-50">
                            💡 Tindakan yang sudah digunakan di laporan tidak bisa dihapus, tapi bisa dinonaktifkan agar
                            tidak muncul di form validasi baru.
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        function openEditAction(action) {
            document.getElementById('actionFormTitle').textContent = '✏️ Edit Tindakan';
            document.getElementById('actionForm').action = '/master/validation-actions/' + action.id;
            document.getElementById('actionMethodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';

            document.getElementById('aInputName').value = action.name;
            document.getElementById('aInputCode').value = action.code;
            document.getElementById('aInputOrder').value = action.order ?? 0;
            document.getElementById('aInputActive').checked = action.is_active == 1;

            document.getElementById('actionFormCard').scrollIntoView({
                behavior: 'smooth'
            });
        }

        function resetActionForm() {
            document.getElementById('actionFormTitle').textContent = '➕ Tambah Tindakan';
            document.getElementById('actionForm').action = '{{ route('master.validation-actions.store') }}';
            document.getElementById('actionMethodField').innerHTML = '';
            document.getElementById('actionForm').reset();
            document.getElementById('aInputActive').checked = true;
        }
    </script>
</x-app-layout>
