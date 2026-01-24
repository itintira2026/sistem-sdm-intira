<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    Detail Konten Harian
                </h2>
                <p class="text-sm text-gray-500">
                    {{ $branch->code }} — {{ $branch->name }}
                    — {{ \Carbon\Carbon::parse($tanggal)->format('d M Y') }}
                </p>
            </div>

            <a href="{{ route('daily-contents.index') }}"
                class="flex items-center gap-2 px-4 py-2 text-white bg-gray-500 rounded-lg hover:bg-gray-600">
                ← Kembali
            </a>
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
                                <th class="px-4 py-3 text-left">No</th>
                                <th class="px-4 py-3 text-left">Tanggal</th>
                                <th class="px-4 py-3 text-left">Keterangan</th>
                                <th class="px-4 py-3 text-left">Input</th>
                                <th class="px-4 py-3 text-left">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($contents as $i => $content)
                                <tr class="border-t">
                                    <td class="px-4 py-3">{{ $i + 1 }}</td>
                                    <td class="px-4 py-3">{{ $content->tanggal }}</td>
                                    <td class="px-4 py-3">
                                        {{ $content->keterangan ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500">
                                        {{ $content->created_at->format('H:i') }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <button
                                            onclick="openEditModal('{{ $content->id }}', '{{ $content->keterangan }}')"
                                            class="text-sm text-blue-600 hover:underline">
                                            Edit
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                                        Belum ada konten hari ini
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>
            </div>

        </div>
    </div>

    {{-- MODAL EDIT --}}
    <div id="editModal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-40">

        <div class="w-full max-w-md p-6 bg-white rounded-lg shadow">
            <h3 class="mb-4 text-lg font-semibold">Edit Keterangan Konten</h3>

            <form method="POST" id="editForm">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block mb-1 text-sm font-medium">
                        Keterangan
                    </label>
                    <input type="text" name="keterangan" id="editKeterangan"
                        class="w-full px-3 py-2 border rounded-lg">
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
        function openEditModal(id, keterangan) {
            document.getElementById('editKeterangan').value = keterangan ?? '';
            document.getElementById('editForm').action = `/daily-contents/${id}`;
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }
    </script>
</x-app-layout>
