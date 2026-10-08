<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">🏢 Kelola Divisi</h2>
            <a href="{{ route('divisions.create') }}"
                class="px-4 py-2 text-sm text-white transition bg-teal-500 rounded-lg hover:bg-teal-600">
                + Tambah Divisi
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="p-4 mb-6 text-green-700 bg-green-100 rounded-lg">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="p-4 mb-6 text-red-700 bg-red-100 rounded-lg">{{ session('error') }}</div>
            @endif

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <table class="w-full text-sm">
                    <thead class="border-b border-gray-200 bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-xs font-semibold text-left text-gray-500 uppercase">Nama</th>
                            <th class="px-6 py-3 text-xs font-semibold text-left text-gray-500 uppercase">Kode</th>
                            <th class="px-6 py-3 text-xs font-semibold text-center text-gray-500 uppercase">Jumlah User
                            </th>
                            <th class="px-6 py-3 text-xs font-semibold text-center text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($divisions as $division)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 font-medium text-gray-800">{{ $division->name }}</td>
                                <td class="px-6 py-3 text-gray-500">{{ $division->code }}</td>
                                <td class="px-6 py-3 text-center text-gray-600">{{ $division->users_count }}</td>
                                <td class="px-6 py-3 text-center">
                                    <a href="{{ route('divisions.edit', $division->id) }}"
                                        class="mr-3 text-xs text-teal-600 hover:underline">Edit</a>
                                    <form action="{{ route('divisions.destroy', $division->id) }}" method="POST"
                                        class="inline" onsubmit="return confirm('Hapus divisi {{ $division->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-xs text-red-600 hover:underline">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
