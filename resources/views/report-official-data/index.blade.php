<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    📤 Upload Data Resmi
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Upload data resmi (omset, revenue, akad, nasabah baru) per FO per hari — dipakai untuk rekonsiliasi
                    terhadap laporan self-report FO.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto space-y-6 sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="p-4 text-green-700 bg-green-100 rounded-lg">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="p-4 text-red-700 bg-red-100 rounded-lg">
                    <ul class="ml-4 list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Upload form --}}
            <div class="p-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-medium text-gray-700">Upload File</h3>
                    <a href="{{ route('data-resmi.template') }}"
                        class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 transition bg-gray-100 rounded-lg hover:bg-gray-200">
                        📥 Download Template
                    </a>
                </div>

                <form action="{{ route('data-resmi.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-medium text-gray-700">
                            File Excel/CSV <span class="text-red-500">*</span>
                        </label>
                        <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100">
                        <p class="mt-1 text-xs text-gray-500">
                            Format: .xlsx, .xls, atau .csv (Max: 5MB). Kolom wajib: username, tanggal, omset, revenue.
                            Kolom opsional: jumlah_akad, nasabah_baru, branch_code.
                        </p>
                    </div>

                    <div class="p-3 mb-4 text-xs text-blue-700 rounded-lg bg-blue-50">
                        ℹ️ Tanggal pakai format <strong>YYYY-MM-DD</strong> (contoh: 2026-09-01). Upload ulang untuk
                        username + tanggal yang sama akan <strong>menimpa</strong> data lama (dianggap koreksi), bukan
                        bikin data ganda.
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                            class="flex items-center gap-2 px-6 py-2 text-white transition bg-teal-500 rounded-lg hover:bg-teal-600">
                            📤 Upload & Proses
                        </button>
                    </div>
                </form>
            </div>

            {{-- Hasil import --}}
            @if (session('importResults') && count(session('importResults')) > 0)
                <div class="p-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <h3 class="mb-4 font-medium text-gray-700">
                        ⚠️ Baris yang Gagal Diproses ({{ count(session('importResults')) }})
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="border-b border-gray-200 bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-xs font-semibold text-left text-gray-500 uppercase">Baris
                                        Excel</th>
                                    <th class="px-4 py-2 text-xs font-semibold text-left text-gray-500 uppercase">Alasan
                                        Gagal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach (session('importResults') as $result)
                                    <tr>
                                        <td class="px-4 py-2 font-medium text-gray-700">Baris {{ $result['row'] }}</td>
                                        <td class="px-4 py-2 text-red-600">{{ $result['message'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
