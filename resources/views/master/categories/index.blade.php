<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">⚙️ Kategori Laporan</h2>
        <p class="mt-1 text-sm text-gray-500">Kelola kategori field laporan FO</p>
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

                {{-- TABEL --}}
                <div class="lg:col-span-2">
                    <div class="overflow-hidden bg-white rounded-lg shadow-sm">
                        <div class="px-5 py-4 border-b border-gray-100">
                            <h3 class="font-semibold text-gray-800">Daftar Kategori</h3>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $categories->count() }} kategori terdaftar</p>
                        </div>

                        @if ($categories->isEmpty())
                            <div class="py-12 text-center text-gray-400">
                                <p class="mb-2 text-3xl">📂</p>
                                <p class="text-sm">Belum ada kategori.</p>
                            </div>
                        @else
                            <table class="w-full text-sm">
                                <thead class="text-xs text-gray-500 uppercase bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left">Nama</th>
                                        <th class="px-4 py-3 text-left">Kode</th>
                                        <th class="px-4 py-3 text-center">Order</th>
                                        <th class="px-4 py-3 text-center">Status</th>
                                        <th class="px-4 py-3 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($categories as $category)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 font-medium text-gray-800">{{ $category->name }}</td>
                                            <td class="px-4 py-3">
                                                <code
                                                    class="px-1.5 py-0.5 text-xs bg-gray-100 rounded text-gray-600">{{ $category->code }}</code>
                                            </td>
                                            <td class="px-4 py-3 text-center text-gray-500">{{ $category->order }}</td>
                                            <td class="px-4 py-3 text-center">
                                                @if ($category->is_active)
                                                    <span
                                                        class="px-2 py-0.5 text-xs font-semibold text-green-700 bg-green-100 rounded-full">Aktif</span>
                                                @else
                                                    <span
                                                        class="px-2 py-0.5 text-xs font-semibold text-gray-500 bg-gray-100 rounded-full">Nonaktif</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <div class="flex justify-center gap-2">
                                                    <button onclick="openEditCategory({{ $category->toJson() }})"
                                                        class="px-2 py-1 text-xs text-blue-700 rounded bg-blue-50 hover:bg-blue-100">Edit</button>
                                                    <form method="POST"
                                                        action="{{ route('master.categories.destroy', $category->id) }}"
                                                        onsubmit="return confirm('Hapus kategori {{ $category->name }}?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit"
                                                            class="px-2 py-1 text-xs text-red-700 rounded bg-red-50 hover:bg-red-100">Hapus</button>
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

                {{-- FORM --}}
                <div class="lg:col-span-1">
                    <div class="sticky p-5 bg-white rounded-lg shadow-sm top-4" id="formCard">
                        <h3 class="mb-4 font-semibold text-gray-800" id="formTitle">➕ Tambah Kategori</h3>

                        <form id="categoryForm" method="POST" action="{{ route('master.categories.store') }}">
                            @csrf
                            <span id="methodField"></span>

                            <div class="space-y-4">
                                <div>
                                    <label class="block mb-1 text-xs font-medium text-gray-600">Nama <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" name="name" id="inputName" required
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500"
                                        placeholder="e.g. Metrik Bisnis">
                                </div>

                                <div>
                                    <label class="block mb-1 text-xs font-medium text-gray-600">Kode <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" name="code" id="inputCode" required
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500"
                                        placeholder="e.g. metrik_bisnis">
                                    <p class="mt-0.5 text-xs text-gray-400">Huruf kecil, angka, underscore.</p>
                                </div>

                                <div>
                                    <label class="block mb-1 text-xs font-medium text-gray-600">Urutan</label>
                                    <input type="number" name="order" id="inputOrder" min="0" value="0"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
                                    <p class="mt-0.5 text-xs text-gray-400">Angka lebih kecil tampil lebih dulu. Boleh
                                        duplikat.</p>
                                </div>

                                <div class="flex items-center gap-2">
                                    <input type="checkbox" name="is_active" id="inputActive" value="1" checked
                                        class="w-4 h-4 text-teal-600 border-gray-300 rounded">
                                    <label for="inputActive" class="text-sm text-gray-700">Aktif</label>
                                </div>
                            </div>

                            <div class="flex gap-2 mt-5">
                                <button type="submit"
                                    class="flex-1 px-4 py-2 text-sm text-white bg-teal-600 rounded-lg hover:bg-teal-700">💾
                                    Simpan</button>
                                <button type="button" onclick="resetForm()"
                                    class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">Reset</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        function openEditCategory(category) {
            document.getElementById('formTitle').textContent = '✏️ Edit Kategori';
            document.getElementById('categoryForm').action = '/master/categories/' + category.id;
            document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';
            document.getElementById('inputName').value = category.name;
            document.getElementById('inputCode').value = category.code;
            document.getElementById('inputOrder').value = category.order ?? 0;
            document.getElementById('inputActive').checked = category.is_active == 1;
            document.getElementById('formCard').scrollIntoView({
                behavior: 'smooth'
            });
        }

        function resetForm() {
            document.getElementById('formTitle').textContent = '➕ Tambah Kategori';
            document.getElementById('categoryForm').action = '{{ route('master.categories.store') }}';
            document.getElementById('methodField').innerHTML = '';
            document.getElementById('categoryForm').reset();
        }
    </script>
</x-app-layout>
