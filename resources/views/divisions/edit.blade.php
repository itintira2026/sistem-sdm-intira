<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">✏️ Edit Divisi</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">
            <div class="p-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                @if ($errors->any())
                    <div class="p-4 mb-6 text-red-700 bg-red-100 rounded-lg">
                        <ul class="ml-4 list-disc">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('divisions.update', $division->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-medium text-gray-700">Nama Divisi</label>
                        <input type="text" name="name" value="{{ old('name', $division->name) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>
                    <div class="mb-6">
                        <label class="block mb-2 text-sm font-medium text-gray-700">Kode</label>
                        <input type="text" name="code" value="{{ old('code', $division->code) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>
                    <div class="flex justify-end gap-3">
                        <a href="{{ route('divisions.index') }}"
                            class="px-6 py-2 text-gray-700 transition bg-gray-200 rounded-lg hover:bg-gray-300">Batal</a>
                        <button type="submit"
                            class="px-6 py-2 text-white transition bg-teal-500 rounded-lg hover:bg-teal-600">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
