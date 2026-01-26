<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    Detail Presensi
                </h2>
                <p class="text-sm text-gray-500">
                    {{ $user->name }} — {{ \Carbon\Carbon::parse($tanggal)->format('d M Y') }}
                </p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('presensi.index') }}"
                    class="flex items-center gap-2 px-4 py-2 text-white transition bg-gray-500 rounded-lg hover:bg-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white rounded-lg shadow-sm">
                <div class="p-6">

                    {{-- FILTER TANGGAL --}}
                    <form method="GET" class="mb-6">
                        <input type="date" name="tanggal" value="{{ $tanggal }}" onchange="this.form.submit()"
                            class="px-4 py-2 border rounded-lg">
                    </form>

                    {{-- TABLE --}}
                    <table class="w-full text-sm border">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left">Status</th>
                                <th class="px-4 py-3 text-left">Jam</th>
                                <th class="px-4 py-3 text-left">Wilayah</th>
                                <th class="px-4 py-3 text-left">Keterangan</th>
                                <th class="px-4 py-3 text-left">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rows as $row)
                                <tr class="border-t">
                                    <td class="px-4 py-3 font-medium">
                                        {{ str_replace('_', ' ', $row['status']) }}
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ $row['jam'] ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ $row['wilayah'] ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ $row['keterangan'] ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <button
                                            onclick="openEditModal('{{ $row['status'] }}', '{{ $row['jam'] }}', '{{ $row['wilayah'] }}', '{{ $row['keterangan'] }}')"
                                            class="text-sm text-blue-600 hover:underline">
                                            Edit Jam
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{-- <div class="mt-6">
                        <a href="{{ route('presensi.index') }}" class="text-sm text-blue-600 hover:underline">
                            ← Kembali ke daftar
                        </a>
                    </div> --}}

                </div>
            </div>

        </div>
    </div>

    <div id="editModal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-40">

        <div class="w-full max-w-md p-6 bg-white rounded-lg shadow">
            <h3 class="mb-4 text-lg font-semibold">Edit Presensi</h3>

            <form method="POST" action="{{ route('presensi.update', $user->id) }}">
                @csrf
                @method('PUT')

                <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                <input type="hidden" name="status" id="editStatus">

                {{-- JAM --}}
                <div class="mb-3">
                    <label class="block mb-1 text-sm font-medium">Jam</label>
                    <input type="time" name="jam" id="editJam" class="w-full px-3 py-2 border rounded-lg">
                </div>

                {{-- WILAYAH --}}
                <div class="mb-3">
                    <label class="block mb-1 text-sm font-medium">Wilayah</label>
                    <select name="wilayah" id="editWilayah" class="w-full px-3 py-2 border rounded-lg">
                        <option value="WIB">WIB</option>
                        <option value="WITA">WITA</option>
                        <option value="WIT">WIT</option>
                    </select>
                </div>

                {{-- KETERANGAN --}}
                <div class="mb-4">
                    <label class="block mb-1 text-sm font-medium">Keterangan</label>
                    <input name="keterangan" class="w-full px-3 py-2 border rounded-lg" id="editKeterangan" />
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 border rounded-lg">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 text-white bg-teal-600 rounded-lg">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // function openEditModal(status, jam) {
        //     document.getElementById('editStatus').value = status;
        //     document.getElementById('editJam').value = jam ?? '';
        //     document.getElementById('editModal').classList.remove('hidden');
        // }

        // function closeEditModal() {
        //     document.getElementById('editModal').classList.add('hidden');
        // }
        function openEditModal(status, jam, wilayah, keterangan) {
            document.getElementById('editStatus').value = status;
            document.getElementById('editKeterangan').value = keterangan;
            document.getElementById('editJam').value = jam;
            document.getElementById('editWilayah').value = wilayah;
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }
    </script>

</x-app-layout>
