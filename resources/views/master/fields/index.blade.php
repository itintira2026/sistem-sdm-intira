<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">⚙️ Field Laporan</h2>
        <p class="mt-1 text-sm text-gray-500">Kelola field input pada laporan FO</p>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="p-4 mb-4 text-green-700 bg-green-100 rounded-lg">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="p-4 mb-4 text-red-700 bg-red-100 rounded-lg">{{ session('error') }}</div>
            @endif

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

                {{-- ======================================================== --}}
                {{-- TABEL FIELD                                                --}}
                {{-- ======================================================== --}}
                <div class="lg:col-span-2">
                    <div class="overflow-hidden bg-white rounded-lg shadow-sm">
                        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                            <div>
                                <h3 class="font-semibold text-gray-800">Daftar Field</h3>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $fields->count() }} field terdaftar</p>
                            </div>
                            {{-- Filter kategori --}}
                            <select id="filterCategory" onchange="filterFields()"
                                class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
                                <option value="">Semua Kategori</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="text-xs text-gray-500 uppercase bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left">Nama Field</th>
                                        <th class="px-4 py-3 text-left">Kategori</th>
                                        <th class="px-4 py-3 text-left">Tipe Input</th>
                                        <th class="px-4 py-3 text-center">Wajib</th>
                                        <th class="px-4 py-3 text-center">Order</th>
                                        <th class="px-4 py-3 text-center">Status</th>
                                        <th class="px-4 py-3 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100" id="fieldTableBody">
                                    @foreach ($fields as $field)
                                        <tr class="hover:bg-gray-50 field-row" {{-- data-category="{{ $field->report_category_id }}"> --}}
                                            data-category="{{ $field->category->id ?? '' }}">
                                            <td class="px-4 py-3">
                                                <p class="font-medium text-gray-800">{{ $field->name }}</p>
                                                <code class="text-xs text-gray-400">{{ $field->code }}</code>
                                                @if ($field->helper_text)
                                                    <p class="text-xs text-gray-400 mt-0.5 italic">
                                                        {{ $field->helper_text }}</p>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3">
                                                <span
                                                    class="inline-flex items-center gap-1 px-2 py-0.5 text-xs rounded-full"
                                                    style="background-color: {{ $field->category->color ?? '#e5e7eb' }}20; color: {{ $field->category->color ?? '#6b7280' }}">
                                                    <span class="inline-block w-2 h-2 rounded-full"
                                                        style="background-color: {{ $field->category->color ?? '#6b7280' }}"></span>
                                                    {{ $field->category->name ?? '-' }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="px-2 py-0.5 text-xs bg-gray-100 text-gray-600 rounded">
                                                    {{ $inputTypes[$field->input_type] ?? $field->input_type }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                {{ $field->is_required ? '✅' : '—' }}
                                            </td>
                                            <td class="px-4 py-3 text-center text-gray-500">{{ $field->order }}</td>
                                            <td class="px-4 py-3 text-center">
                                                @if ($field->is_active)
                                                    <span
                                                        class="px-2 py-0.5 text-xs font-semibold text-green-700 bg-green-100 rounded-full">Aktif</span>
                                                @else
                                                    <span
                                                        class="px-2 py-0.5 text-xs font-semibold text-gray-500 bg-gray-100 rounded-full">Nonaktif</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <div class="flex justify-center gap-2">
                                                    <button onclick="openEditField({{ $field->toJson() }})"
                                                        class="px-2 py-1 text-xs text-blue-700 rounded bg-blue-50 hover:bg-blue-100">
                                                        Edit
                                                    </button>
                                                    <form method="POST"
                                                        action="{{ route('master.fields.destroy', $field->id) }}"
                                                        onsubmit="return confirm('Hapus field {{ $field->name }}?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit"
                                                            class="px-2 py-1 text-xs text-red-700 rounded bg-red-50 hover:bg-red-100">
                                                            Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- ======================================================== --}}
                {{-- FORM TAMBAH / EDIT                                         --}}
                {{-- ======================================================== --}}
                <div class="lg:col-span-1">
                    <div class="sticky p-5 bg-white rounded-lg shadow-sm top-4" id="fieldFormCard">
                        <h3 class="mb-4 font-semibold text-gray-800" id="fieldFormTitle">➕ Tambah Field</h3>

                        <form id="fieldForm" method="POST" action="{{ route('master.fields.store') }}">
                            @csrf
                            <span id="fieldMethodField"></span>

                            <div class="space-y-3">
                                <div>
                                    <label class="block mb-1 text-xs font-medium text-gray-600">Nama Field <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" name="name" id="fInputName" required
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500"
                                        placeholder="e.g. Omset Harian">
                                </div>

                                <div>
                                    <label class="block mb-1 text-xs font-medium text-gray-600">Kode <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" name="code" id="fInputCode" required
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500"
                                        placeholder="e.g. mb_omset">
                                </div>

                                <div>
                                    <label class="block mb-1 text-xs font-medium text-gray-600">Kategori <span
                                            class="text-red-500">*</span></label>
                                    <select name="category_id" id="fInputCategory" required
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
                                        <option value="">Pilih Kategori</option>
                                        @foreach ($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block mb-1 text-xs font-medium text-gray-600">Tipe Input <span
                                            class="text-red-500">*</span></label>
                                    <select name="input_type" id="fInputType" required
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
                                        <option value="">Pilih Tipe</option>
                                        @foreach ($inputTypes as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block mb-1 text-xs font-medium text-gray-600">Helper Text</label>
                                    <input type="text" name="helper_text" id="fInputHelper"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500"
                                        placeholder="Petunjuk pengisian (opsional)">
                                </div>

                                <div>
                                    <label class="block mb-1 text-xs font-medium text-gray-600">Urutan</label>
                                    <input type="number" name="order" id="fInputOrder" min="0"
                                        value="0"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
                                </div>

                                <div class="flex items-center gap-4">
                                    <label class="flex items-center gap-2 text-sm text-gray-700">
                                        <input type="checkbox" name="is_required" id="fInputRequired" value="1"
                                            class="w-4 h-4 text-teal-600 border-gray-300 rounded">
                                        Wajib Diisi
                                    </label>
                                    <label class="flex items-center gap-2 text-sm text-gray-700">
                                        <input type="checkbox" name="is_active" id="fInputActive" value="1"
                                            checked class="w-4 h-4 text-teal-600 border-gray-300 rounded">
                                        Aktif
                                    </label>
                                </div>
                            </div>

                            <div class="flex gap-2 mt-5">
                                <button type="submit"
                                    class="flex-1 px-4 py-2 text-sm text-white bg-teal-600 rounded-lg hover:bg-teal-700">
                                    💾 Simpan
                                </button>
                                <button type="button" onclick="resetFieldForm()"
                                    class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">
                                    Reset
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        function filterFields() {
            const catId = document.getElementById('filterCategory').value;

            document.querySelectorAll('.field-row').forEach(row => {
                const rowCat = row.dataset.category;

                console.log('filter:', catId, 'row:', rowCat); // 🔥 debug

                if (catId === '' || rowCat == catId) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
        // function filterFields() {
        //     const catId = document.getElementById('filterCategory').value;
        //     document.querySelectorAll('.field-row').forEach(row => {
        //         if (!catId || row.dataset.category === catId) {
        //             row.classList.remove('hidden');
        //         } else {
        //             row.classList.add('hidden');
        //         }
        //     });
        // }

        function openEditField(field) {
            document.getElementById('fieldFormTitle').textContent = '✏️ Edit Field';
            document.getElementById('fieldForm').action = '/master/fields/' + field.id;
            document.getElementById('fieldMethodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';

            document.getElementById('fInputName').value = field.name;
            document.getElementById('fInputCode').value = field.code;
            document.getElementById('fInputCategory').value = field.category_id;
            document.getElementById('fInputType').value = field.input_type;
            document.getElementById('fInputHelper').value = field.helper_text ?? '';
            document.getElementById('fInputOrder').value = field.order ?? 0;
            document.getElementById('fInputRequired').checked = field.is_required == 1;
            document.getElementById('fInputActive').checked = field.is_active == 1;

            document.getElementById('fieldFormCard').scrollIntoView({
                behavior: 'smooth'
            });
        }

        function resetFieldForm() {
            document.getElementById('fieldFormTitle').textContent = '➕ Tambah Field';
            document.getElementById('fieldForm').action = '{{ route('master.fields.store') }}';
            document.getElementById('fieldMethodField').innerHTML = '';
            document.getElementById('fieldForm').reset();
            document.getElementById('fInputActive').checked = true;
        }
    </script>
</x-app-layout>
