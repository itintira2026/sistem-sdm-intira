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
                    <span class="hidden sm:inline">Kembali</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            {{-- Alert Success --}}
            @if (session('success'))
                <div class="p-4 mb-6 text-green-700 bg-green-100 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Info Card --}}
            <div class="p-6 mb-6 bg-white rounded-lg shadow-sm">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div>
                        <p class="text-sm text-gray-500">Karyawan</p>
                        <p class="mt-1 text-lg font-semibold text-gray-800">{{ $user->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Tanggal</p>
                        <p class="mt-1 text-lg font-semibold text-gray-800">
                            {{ \Carbon\Carbon::parse($tanggal)->format('d M Y') }}
                        </p>
                    </div>
                    <div>
                        <form method="GET" class="flex gap-2">
                            <input type="date" name="tanggal" value="{{ $tanggal }}"
                                class="flex-1 px-3 py-2 text-sm border rounded-lg focus:ring-2 focus:ring-teal-500">
                            <button type="submit"
                                class="px-4 py-2 text-sm text-white bg-teal-600 rounded-lg hover:bg-teal-700">
                                Ganti
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Table Card --}}
            <div class="bg-white rounded-lg shadow-sm">
                <div class="p-6">
                    <h3 class="mb-6 text-lg font-semibold text-gray-800">
                        Rincian Presensi
                    </h3>

                    {{-- Desktop Table --}}
                    <div class="hidden overflow-x-auto md:block">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-xs font-medium text-left text-gray-600 uppercase">Status
                                    </th>
                                    <th class="px-4 py-3 text-xs font-medium text-left text-gray-600 uppercase">Jam</th>
                                    <th class="px-4 py-3 text-xs font-medium text-left text-gray-600 uppercase">Wilayah
                                    </th>
                                    <th class="px-4 py-3 text-xs font-medium text-left text-gray-600 uppercase">
                                        Keterangan</th>
                                    <th class="px-4 py-3 text-xs font-medium text-left text-gray-600 uppercase">Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($rows as $row)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 font-medium text-gray-800">
                                            {{ str_replace('_', ' ', $row['status']) }}
                                        </td>
                                        <td class="px-4 py-3">
                                            @if ($row['jam'])
                                                <span class="px-2 py-1 text-sm text-blue-700 rounded bg-blue-50">
                                                    {{ $row['jam'] }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            @if ($row['wilayah'])
                                                <span class="px-2 py-1 text-xs text-gray-700 bg-gray-100 rounded">
                                                    {{ $row['wilayah'] }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-gray-600">
                                            {{ $row['keterangan'] ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <button
                                                onclick="openEditModal('{{ $row['status'] }}', '{{ $row['jam'] }}', '{{ $row['wilayah'] }}', '{{ $row['keterangan'] }}')"
                                                class="text-sm font-medium text-teal-600 hover:text-teal-800">
                                                Edit
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile Cards --}}
                    <div class="space-y-4 md:hidden">
                        @foreach ($rows as $row)
                            <div class="p-4 border border-gray-200 rounded-lg">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="font-semibold text-gray-800">
                                        {{ str_replace('_', ' ', $row['status']) }}
                                    </h4>
                                    <button
                                        onclick="openEditModal('{{ $row['status'] }}', '{{ $row['jam'] }}', '{{ $row['wilayah'] }}', '{{ $row['keterangan'] }}')"
                                        class="px-3 py-1 text-xs text-white bg-teal-600 rounded-lg hover:bg-teal-700">
                                        Edit
                                    </button>
                                </div>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Jam:</span>
                                        @if ($row['jam'])
                                            <span class="px-2 py-1 font-medium text-blue-700 rounded bg-blue-50">
                                                {{ $row['jam'] }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Wilayah:</span>
                                        @if ($row['wilayah'])
                                            <span class="px-2 py-1 text-xs text-gray-700 bg-gray-100 rounded">
                                                {{ $row['wilayah'] }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Keterangan:</span>
                                        <span class="text-gray-800 text-right max-w-[60%]">
                                            {{ $row['keterangan'] ?? '-' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>

        </div>
    </div>

    {{-- Modal Edit --}}
    <div id="editModal" class="fixed inset-0 z-50 flex items-center justify-center hidden p-4 bg-black bg-opacity-50"
        onclick="if(event.target === this) closeEditModal()">

        <div class="w-full max-w-md bg-white rounded-lg shadow-xl" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Edit Presensi</h3>
                <button onclick="closeEditModal()"
                    class="p-1 text-gray-400 rounded-lg hover:text-gray-600 hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('presensi.update', $user->id) }}">
                @csrf
                @method('PUT')

                <div class="p-6 space-y-4">
                    <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                    <input type="hidden" name="status" id="editStatus">

                    {{-- Status Display --}}
                    <div class="p-3 rounded-lg bg-gray-50">
                        <p class="text-sm text-gray-600">Status:</p>
                        <p id="editStatusDisplay" class="font-semibold text-gray-800"></p>
                    </div>

                    {{-- JAM --}}
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">
                            Jam <span class="text-red-500">*</span>
                        </label>
                        <input type="time" name="jam" id="editJam" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                    </div>

                    {{-- WILAYAH --}}
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">
                            Wilayah Waktu <span class="text-red-500">*</span>
                        </label>
                        <select name="wilayah" id="editWilayah" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                            <option value="WIB">WIB (Waktu Indonesia Barat)</option>
                            <option value="WITA">WITA (Waktu Indonesia Tengah)</option>
                            <option value="WIT">WIT (Waktu Indonesia Timur)</option>
                        </select>
                    </div>

                    {{-- KETERANGAN --}}
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">
                            Keterangan
                        </label>
                        <textarea name="keterangan" id="editKeterangan" rows="3" placeholder="Tambahkan keterangan (opsional)"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500"></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-3 p-6 border-t border-gray-200">
                    <button type="button" onclick="closeEditModal()"
                        class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 text-white bg-teal-600 rounded-lg hover:bg-teal-700">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(status, jam, wilayah, keterangan) {
            document.getElementById('editStatus').value = status;
            document.getElementById('editStatusDisplay').textContent = status.replace(/_/g, ' ');
            document.getElementById('editJam').value = jam || '';
            document.getElementById('editWilayah').value = wilayah || 'WIB';
            document.getElementById('editKeterangan').value = keterangan || '';
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        // Close modal with ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeEditModal();
            }
        });
    </script>

</x-app-layout>
