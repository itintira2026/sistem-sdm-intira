{{-- resources/views/superadmin/daily-report-fo/photo-cleanup/index.blade.php --}}
{{-- GANTI SELURUH FILE dengan ini --}}

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    🗑️ Manajemen Storage Foto Absensi
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Hanya dapat diakses oleh Superadmin
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto space-y-6 sm:px-6 lg:px-8">

            {{-- ============================================================ --}}
            {{-- ALERTS                                                         --}}
            {{-- ============================================================ --}}
            @if (session('success'))
                <div class="p-4 text-green-700 bg-green-100 rounded-lg">
                    ✅ {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 text-red-700 bg-red-100 rounded-lg">
                    <p class="font-semibold">Terjadi kesalahan:</p>
                    <ul class="mt-1 ml-4 list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- ============================================================ --}}
            {{-- STORAGE STATS SAAT INI                                        --}}
            {{-- ============================================================ --}}
            <div class="p-6 bg-white rounded-lg shadow-sm">
                <h3 class="mb-4 text-sm font-semibold tracking-wider text-gray-400 uppercase">
                    📊 Storage Foto Saat Ini
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 text-center rounded-lg bg-blue-50">
                        <p class="text-xs text-blue-500">Total Foto Tersimpan</p>
                        <p class="text-3xl font-bold text-blue-700">
                            {{ number_format($storageStats['total_photos']) }}
                        </p>
                        <p class="text-xs text-blue-400">foto</p>
                    </div>
                    <div class="p-4 text-center rounded-lg bg-purple-50">
                        <p class="text-xs text-purple-500">Total Ukuran</p>
                        <p class="text-3xl font-bold text-purple-700">
                            {{ $storageStats['total_size_human'] }}
                        </p>
                        <p class="text-xs text-purple-400">di storage server</p>
                    </div>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- RETENTION POLICY INFO                                          --}}
            {{-- ============================================================ --}}
            <div class="p-4 border border-blue-200 rounded-lg bg-blue-50">
                <p class="text-sm font-semibold text-blue-800">ℹ️ Kebijakan Retensi Foto</p>
                <p class="mt-1 text-sm text-blue-700">
                    Sistem menyimpan foto dari <strong>bulan ini</strong> dan
                    <strong>bulan lalu</strong>. Foto dari laporan sebelum
                    <strong>{{ $threshold->translatedFormat('1 F Y') }}</strong>
                    dianggap sudah kadaluarsa dan boleh dihapus.
                </p>
                <div class="flex flex-wrap gap-2 mt-3 text-xs">
                    <span class="px-2 py-1 font-semibold text-green-700 bg-green-100 rounded-full">
                        ✅ {{ now()->translatedFormat('F Y') }} — Aman
                    </span>
                    <span class="px-2 py-1 font-semibold text-green-700 bg-green-100 rounded-full">
                        ✅ {{ now()->subMonth()->translatedFormat('F Y') }} — Aman
                    </span>
                    <span class="px-2 py-1 font-semibold text-red-700 bg-red-100 rounded-full">
                        ❌ {{ now()->subMonths(2)->translatedFormat('F Y') }} ke bawah — Eligible hapus
                    </span>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- OPSI 1: HAPUS OTOMATIS (threshold)                            --}}
            {{-- ============================================================ --}}
            <div class="p-6 bg-white border-l-4 border-orange-400 rounded-lg shadow-sm">
                <h3 class="mb-1 text-sm font-semibold tracking-wider text-gray-400 uppercase">
                    🔄 Opsi 1 — Hapus Otomatis
                </h3>
                <p class="mb-4 text-xs text-gray-400">
                    Hapus semua foto dari laporan sebelum
                    <strong>{{ $threshold->translatedFormat('1 F Y') }}</strong>.
                    Data laporan (angka, teks) tetap tersimpan.
                </p>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="p-4 text-center rounded-lg bg-orange-50">
                        <p class="text-xs text-orange-500">Foto yang Akan Dihapus</p>
                        <p class="text-3xl font-bold text-orange-700">
                            {{ number_format($preview['total_photos']) }}
                        </p>
                        <p class="text-xs text-orange-400">foto</p>
                    </div>
                    <div class="p-4 text-center rounded-lg bg-red-50">
                        <p class="text-xs text-red-500">Storage yang Dibebaskan</p>
                        <p class="text-3xl font-bold text-red-700">
                            {{ $preview['total_size_human'] }}
                        </p>
                        <p class="text-xs text-red-400">estimasi</p>
                    </div>
                </div>

                @if ($preview['total_photos'] > 0)
                    <form method="POST" action="{{ route('superadmin.absensi.photo-cleanup.execute') }}"
                        onsubmit="return validateConfirm('confirmInput1')">
                        @csrf
                        <div class="p-4 mb-4 border border-red-200 rounded-lg bg-red-50">
                            <p class="mb-1 text-sm font-semibold text-red-700">
                                ⚠️ Tindakan Tidak Dapat Dibatalkan
                            </p>
                            <p class="text-xs text-red-600">
                                File foto akan dihapus permanen dari server.
                                Data laporan (angka & teks) tetap aman.
                            </p>
                        </div>
                        <div class="mb-4">
                            <label class="block mb-1 text-sm font-medium text-gray-700">
                                Ketik <strong class="text-red-600">HAPUS</strong> untuk konfirmasi
                            </label>
                            <input type="text" name="confirm" id="confirmInput1" placeholder="HAPUS"
                                autocomplete="off"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                        </div>
                        <button type="submit"
                            class="px-6 py-2 text-white transition bg-red-600 rounded-lg hover:bg-red-700">
                            🗑️ Hapus {{ number_format($preview['total_photos']) }} Foto Sekarang
                        </button>
                    </form>
                @else
                    <div class="p-4 text-sm text-green-700 rounded-lg bg-green-50">
                        ✅ Tidak ada foto yang perlu dihapus saat ini.
                    </div>
                @endif
            </div>

            {{-- ============================================================ --}}
            {{-- OPSI 2: HAPUS BY RANGE TANGGAL                                --}}
            {{-- ============================================================ --}}
            <div class="p-6 bg-white border-l-4 border-blue-400 rounded-lg shadow-sm">
                <h3 class="mb-1 text-sm font-semibold tracking-wider text-gray-400 uppercase">
                    📅 Opsi 2 — Hapus by Range Tanggal
                </h3>
                <p class="mb-4 text-xs text-gray-400">
                    Pilih range tanggal laporan secara manual. Superadmin memiliki
                    kebebasan penuh memilih range apapun.
                </p>

                {{-- Form Filter Range --}}
                <form method="GET" action="{{ route('superadmin.absensi.photo-cleanup.preview-range') }}"
                    class="mb-6">
                    <div class="grid grid-cols-1 gap-4 mb-4 md:grid-cols-2">
                        <div>
                            <label class="block mb-1 text-xs font-medium text-gray-600">
                                Tanggal Dari
                            </label>
                            <input type="date" name="date_from" value="{{ $dateFrom ?? '' }}"
                                class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block mb-1 text-xs font-medium text-gray-600">
                                Tanggal Sampai
                            </label>
                            <input type="date" name="date_to" value="{{ $dateTo ?? '' }}"
                                class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    <button type="submit"
                        class="px-5 py-2 text-sm text-white transition bg-blue-600 rounded-lg hover:bg-blue-700">
                        🔍 Cek Preview
                    </button>
                </form>

                {{-- Hasil Preview Range --}}
                @if (isset($previewRange))

                    {{-- Warning zona proteksi --}}
                    @if ($previewRange['warning'])
                        <div class="p-4 mb-4 border border-yellow-300 rounded-lg bg-yellow-50">
                            <p class="text-sm font-semibold text-yellow-800">
                                ⚠️ Peringatan
                            </p>
                            <p class="mt-1 text-sm text-yellow-700">
                                {{ $previewRange['warning'] }}
                            </p>
                        </div>
                    @endif

                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="p-4 text-center rounded-lg bg-orange-50">
                            <p class="text-xs text-orange-500">Foto yang Akan Dihapus</p>
                            <p class="text-3xl font-bold text-orange-700">
                                {{ number_format($previewRange['total_photos']) }}
                            </p>
                            <p class="text-xs text-orange-400">
                                {{ $previewRange['date_from']->translatedFormat('d F Y') }}
                                –
                                {{ $previewRange['date_to']->translatedFormat('d F Y') }}
                            </p>
                        </div>
                        <div class="p-4 text-center rounded-lg bg-red-50">
                            <p class="text-xs text-red-500">Storage yang Dibebaskan</p>
                            <p class="text-3xl font-bold text-red-700">
                                {{ $previewRange['total_size_human'] }}
                            </p>
                            <p class="text-xs text-red-400">estimasi</p>
                        </div>
                    </div>

                    @if ($previewRange['total_photos'] > 0)
                        <form method="POST"
                            action="{{ route('superadmin.absensi.photo-cleanup.execute-range') }}"
                            onsubmit="return validateConfirm('confirmInput2')">
                            @csrf
                            <input type="hidden" name="date_from" value="{{ $dateFrom }}">
                            <input type="hidden" name="date_to" value="{{ $dateTo }}">

                            <div class="p-4 mb-4 border border-red-200 rounded-lg bg-red-50">
                                <p class="mb-1 text-sm font-semibold text-red-700">
                                    ⚠️ Tindakan Tidak Dapat Dibatalkan
                                </p>
                                <p class="text-xs text-red-600">
                                    File foto dari laporan
                                    <strong>
                                        {{ $previewRange['date_from']->translatedFormat('d F Y') }}
                                        –
                                        {{ $previewRange['date_to']->translatedFormat('d F Y') }}
                                    </strong>
                                    akan dihapus permanen. Data laporan tetap aman.
                                </p>
                            </div>

                            <div class="mb-4">
                                <label class="block mb-1 text-sm font-medium text-gray-700">
                                    Ketik <strong class="text-red-600">HAPUS</strong> untuk konfirmasi
                                </label>
                                <input type="text" name="confirm" id="confirmInput2" placeholder="HAPUS"
                                    autocomplete="off"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                            </div>

                            <button type="submit"
                                class="px-6 py-2 text-white transition bg-red-600 rounded-lg hover:bg-red-700">
                                🗑️ Hapus {{ number_format($previewRange['total_photos']) }} Foto dari Range Ini
                            </button>
                        </form>
                    @else
                        <div class="p-4 text-sm text-green-700 rounded-lg bg-green-50">
                            ✅ Tidak ada foto dalam range tanggal ini.
                        </div>
                    @endif
                @endif
            </div>

            {{-- ============================================================ --}}
            {{-- RIWAYAT PENGHAPUSAN                                           --}}
            {{-- ============================================================ --}}
            <div class="bg-white rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-sm font-semibold tracking-wider text-gray-400 uppercase">
                        📜 Riwayat Penghapusan
                    </h3>
                </div>

                {{-- Filter History --}}
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <form method="GET" action="{{ route('superadmin.absensi.photo-cleanup.index') }}"
                        class="flex flex-wrap items-end gap-3">
                        <div>
                            <label class="block mb-1 text-xs font-medium text-gray-500">
                                Tanggal Eksekusi Dari
                            </label>
                            <input type="date" name="history_date_from" value="{{ $historyDateFrom ?? '' }}"
                                onchange="this.form.submit()"
                                class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
                        </div>
                        <div>
                            <label class="block mb-1 text-xs font-medium text-gray-500">
                                Tanggal Eksekusi Sampai
                            </label>
                            <input type="date" name="history_date_to" value="{{ $historyDateTo ?? '' }}"
                                onchange="this.form.submit()"
                                class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
                        </div>
                        <div>
                            <label class="block mb-1 text-xs font-medium text-gray-500">
                                Tipe
                            </label>
                            <select name="history_type" onchange="this.form.submit()"
                                class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
                                <option value="">Semua Tipe</option>
                                <option value="manual" {{ ($historyType ?? '') === 'manual' ? 'selected' : '' }}>
                                    👤 Manual
                                </option>
                                <option value="auto" {{ ($historyType ?? '') === 'auto' ? 'selected' : '' }}>
                                    🤖 Auto
                                </option>
                            </select>
                        </div>
                        @if ($historyDateFrom || $historyDateTo || $historyType)
                            <a href="{{ route('superadmin.absensi.photo-cleanup.index') }}"
                                class="px-3 py-2 text-sm text-gray-600 transition border border-gray-300 rounded-lg hover:bg-gray-100">
                                ✕ Reset Filter
                            </a>
                        @endif
                    </form>
                </div>

                @if ($logs->isEmpty())
                    <div class="p-8 text-center text-gray-400">
                        <p class="mb-2 text-3xl">📭</p>
                        <p class="text-sm">Belum ada riwayat penghapusan.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm min-w-max whitespace-nowrap">
                            <thead class="border-b border-gray-200 bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-xs font-semibold text-left text-gray-500 uppercase">
                                        Waktu Eksekusi
                                    </th>
                                    <th class="px-4 py-3 text-xs font-semibold text-left text-gray-500 uppercase">
                                        Oleh
                                    </th>
                                    <th class="px-4 py-3 text-xs font-semibold text-left text-gray-500 uppercase">
                                        Range Hapus
                                    </th>
                                    <th class="px-4 py-3 text-xs font-semibold text-right text-gray-500 uppercase">
                                        Foto Dihapus
                                    </th>
                                    <th class="px-4 py-3 text-xs font-semibold text-right text-gray-500 uppercase">
                                        Size Dibebaskan
                                    </th>
                                    <th class="px-4 py-3 text-xs font-semibold text-center text-gray-500 uppercase">
                                        Tipe
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($logs as $index => $log)
                                    <tr
                                        class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-blue-50 transition-colors">
                                        <td class="px-4 py-3 text-gray-600">
                                            {{ $log->executed_at->translatedFormat('d F Y, H:i') }}
                                        </td>
                                        <td class="px-4 py-3 font-medium text-gray-800">
                                            {{ $log->executor->name ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-gray-600">
                                            s/d {{ $log->deleted_before_date->subDay()->translatedFormat('d F Y') }}
                                        </td>
                                        <td class="px-4 py-3 font-semibold text-right text-red-600">
                                            {{ number_format($log->total_photos_deleted) }}
                                        </td>
                                        <td class="px-4 py-3 font-semibold text-right text-purple-600">
                                            {{ $log->size_freed_human }}
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            @if ($log->execution_type === 'manual')
                                                <span
                                                    class="px-2 py-0.5 text-xs font-semibold text-blue-700 bg-blue-100 rounded-full">
                                                    👤 Manual
                                                </span>
                                            @else
                                                <span
                                                    class="px-2 py-0.5 text-xs font-semibold text-gray-600 bg-gray-100 rounded-full">
                                                    🤖 Auto
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="px-4 py-3 border-t border-gray-100">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    <script>
        function validateConfirm(inputId) {
            const val = document.getElementById(inputId).value;

            if (val !== 'HAPUS') {
                alert('Ketik HAPUS (huruf kapital) untuk melanjutkan.');
                return false;
            }

            return confirm(
                'Yakin ingin menghapus foto?\n\nTindakan ini TIDAK DAPAT dibatalkan.\nData laporan tetap aman, hanya file foto yang dihapus.'
            );
        }
    </script>
</x-app-layout>
