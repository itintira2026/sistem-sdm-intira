<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            📝 Catat Tugas Tambahan
        </h2>
        <p class="mt-1 text-sm text-gray-500">
            Aktivitas pribadi di luar Tugas KPI — bukan penugasan atasan, tidak berbobot, tidak divalidasi.
            Begitu disimpan, langsung final.
        </p>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="p-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">

                @if (session('success'))
                    <div class="p-4 mb-6 text-green-700 bg-green-100 rounded-lg">{{ session('success') }}</div>
                @endif
                @if ($errors->any())
                    <div class="p-4 mb-6 text-red-700 bg-red-100 rounded-lg">
                        <ul class="ml-4 list-disc">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('tasks.self-record.store') }}" method="POST" x-data="{ trackingMode: '{{ old('tracking_mode', 'checklist') }}' }">
                    @csrf

                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-medium text-gray-700">
                            Judul <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title" value="{{ old('title') }}"
                            placeholder="misal: Bantu training FO baru"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>

                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-medium text-gray-700">Deskripsi</label>
                        <textarea name="description" rows="2"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">{{ old('description') }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-medium text-gray-700">
                            Cara Catat <span class="text-red-500">*</span>
                        </label>
                        <div class="flex gap-3">
                            <label class="flex items-center flex-1 gap-2 px-4 py-3 border-2 rounded-lg cursor-pointer"
                                :class="trackingMode === 'checklist' ? 'border-teal-500 bg-teal-50' : 'border-gray-200'">
                                <input type="radio" name="tracking_mode" value="checklist" x-model="trackingMode"
                                    class="text-teal-600">
                                <span class="text-sm font-medium">Selesai / Tidak</span>
                            </label>
                            <label class="flex items-center flex-1 gap-2 px-4 py-3 border-2 rounded-lg cursor-pointer"
                                :class="trackingMode === 'numeric' ? 'border-teal-500 bg-teal-50' : 'border-gray-200'">
                                <input type="radio" name="tracking_mode" value="numeric" x-model="trackingMode"
                                    class="text-teal-600">
                                <span class="text-sm font-medium">Angka</span>
                            </label>
                        </div>
                    </div>

                    <div class="mb-4" x-show="trackingMode === 'checklist'">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="value_checklist" value="1" checked
                                class="w-5 h-5 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                            <span class="text-sm font-medium text-gray-700">Sudah selesai dikerjakan</span>
                        </label>
                    </div>

                    <div class="mb-4" x-show="trackingMode === 'numeric'">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">Nilai</label>
                                <input type="number" step="0.01" min="0" name="value_number"
                                    value="{{ old('value_number') }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">Satuan <span
                                        class="font-normal text-gray-400">(opsional)</span></label>
                                <input type="text" name="unit" value="{{ old('unit') }}"
                                    placeholder="misal: orang, jam"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                            </div>
                        </div>
                    </div>

                    <div class="p-3 mb-6 text-xs text-gray-500 rounded-lg bg-gray-50">
                        ℹ️ Ini bukan penugasan dari atasan — tetap kelihatan oleh atasan/hierarki di atas Anda
                        sebagai informasi/nilai plus, tapi gak masuk hitungan KPI dan gak perlu divalidasi siapapun.
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('dashboard') }}"
                            class="px-6 py-2 text-gray-700 transition bg-gray-200 rounded-lg hover:bg-gray-300">
                            Batal
                        </a>
                        <button type="submit"
                            class="px-6 py-2 text-white transition bg-teal-500 rounded-lg hover:bg-teal-600">
                            💾 Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
