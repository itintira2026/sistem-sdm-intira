<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    📜 History Laporan Harian FO
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $branch->name }} | {{ config('daily_report_fo.history_days', 30) }} hari terakhir
                </p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('daily-reports-fo.index') }}"
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

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            {{-- Stats --}}
            <div class="grid grid-cols-1 gap-6 mb-6 md:grid-cols-3">
                <div class="p-6 bg-white rounded-lg shadow-sm">
                    <p class="text-sm text-gray-600">Total Laporan</p>
                    <p class="text-3xl font-bold text-teal-600">{{ $stats['total_reports'] }}</p>
                </div>
                <div class="p-6 bg-white rounded-lg shadow-sm">
                    <p class="text-sm text-gray-600">Total Hari Lapor</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $stats['total_days'] }}</p>
                </div>
                <div class="p-6 bg-white rounded-lg shadow-sm">
                    <p class="text-sm text-gray-600">Total Foto</p>
                    <p class="text-3xl font-bold text-purple-600">{{ $stats['total_photos'] }}</p>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="mb-6 text-lg font-semibold text-gray-800">Daftar History</h3>

                    {{-- Filter --}}
                    <form method="GET" class="mb-6">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                            <div>
                                <select name="per_page" onchange="this.form.submit()"
                                    class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg">
                                    @foreach ([10, 25, 50] as $size)
                                        <option value="{{ $size }}"
                                            {{ request('per_page', 10) == $size ? 'selected' : '' }}>
                                            {{ $size }} per halaman
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <select name="shift" onchange="this.form.submit()"
                                    class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg">
                                    <option value="">Semua Shift</option>
                                    <option value="pagi" {{ request('shift') == 'pagi' ? 'selected' : '' }}>Shift Pagi
                                    </option>
                                    <option value="siang" {{ request('shift') == 'siang' ? 'selected' : '' }}>Shift
                                        Siang</option>
                                </select>
                            </div>
                            <div>
                                <input type="date" name="date_from" value="{{ request('date_from') }}"
                                    onchange="this.form.submit()"
                                    class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg">
                            </div>
                            <div>
                                <input type="date" name="date_to" value="{{ request('date_to') }}"
                                    onchange="this.form.submit()"
                                    class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg">
                            </div>
                        </div>
                    </form>

                    {{-- Table --}}
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="border-b border-gray-200 bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-xs font-semibold text-left text-gray-600 uppercase">#</th>
                                    <th class="px-4 py-3 text-xs font-semibold text-left text-gray-600 uppercase">
                                        Tanggal</th>
                                    <th class="px-4 py-3 text-xs font-semibold text-left text-gray-600 uppercase">Cabang
                                    </th>
                                    <th class="px-4 py-3 text-xs font-semibold text-left text-gray-600 uppercase">Shift
                                        / Slot</th>
                                    <th class="px-4 py-3 text-xs font-semibold text-left text-gray-600 uppercase">Upload
                                    </th>
                                    <th class="px-4 py-3 text-xs font-semibold text-center text-gray-600 uppercase">Foto
                                    </th>
                                    <th class="px-4 py-3 text-xs font-semibold text-center text-gray-600 uppercase">
                                        Validasi</th>
                                    <th class="px-4 py-3 text-xs font-semibold text-center text-gray-600 uppercase">Aksi
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-100">
                                @forelse ($reports as $index => $report)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-gray-600">
                                            {{ ($reports->currentPage() - 1) * $reports->perPage() + $index + 1 }}
                                        </td>
                                        <td class="px-4 py-3 font-medium text-gray-800">
                                            {{ $report->tanggal->format('d M Y') }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <span
                                                class="text-sm text-gray-700">{{ $report->branch->name ?? '-' }}</span>
                                            <span
                                                class="block text-xs text-gray-400">{{ $report->branch->timezone ?? '' }}</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span
                                                class="px-2 py-1 text-xs rounded {{ $report->shift == 'pagi' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                                                {{ $report->shift_label }}
                                            </span>
                                            <span class="ml-1 text-xs text-gray-500">/ Slot {{ $report->slot }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-xs text-gray-500">
                                            {{ $report->uploaded_at->format('H:i') }}
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            @php
                                                $totalPhotos = $report->details->sum(function ($detail) {
                                                    return $detail->photos->count();
                                                });
                                            @endphp
                                            <span class="px-2 py-1 text-xs text-teal-700 bg-teal-100 rounded">
                                                📷 {{ $totalPhotos }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            @if ($report->validation_status === 'approved')
                                                <span
                                                    class="px-2 py-0.5 text-xs font-semibold text-green-700 bg-green-100 rounded-full">✅
                                                    Disetujui</span>
                                            @elseif ($report->validation_status === 'rejected')
                                                <span
                                                    class="px-2 py-0.5 text-xs font-semibold text-red-700 bg-red-100 rounded-full">❌
                                                    Ditolak</span>
                                            @else
                                                <span
                                                    class="px-2 py-0.5 text-xs font-semibold text-yellow-700 bg-yellow-100 rounded-full">⏳
                                                    Pending</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <button onclick="showDetail({{ $report->id }})"
                                                class="px-3 py-1 text-xs text-blue-600 transition hover:text-blue-800">
                                                👁️ Detail
                                            </button>
                                        </td>
                                    </tr>

                                    {{-- Detail Row (Hidden) --}}
                                    <tr id="detail-{{ $report->id }}" class="hidden bg-gray-50">
                                        <td colspan="8" class="px-4 py-4">
                                            <div class="p-4 bg-white rounded-lg">
                                                <h4 class="mb-3 font-semibold text-gray-800">Detail Laporan</h4>

                                                {{-- Validation Status --}}
                                                @if ($report->validation)
                                                    <div
                                                        class="p-3 mb-4 rounded-lg {{ $report->validation_status === 'approved' ? 'bg-green-50' : 'bg-red-50' }}">
                                                        <p
                                                            class="text-sm font-semibold {{ $report->validation_status === 'approved' ? 'text-green-700' : 'text-red-700' }}">
                                                            Status Validasi:
                                                            @if ($report->validation_status === 'approved')
                                                                ✅ Disetujui
                                                            @else
                                                                ❌ Ditolak
                                                            @endif
                                                        </p>
                                                        <p class="mt-1 text-xs text-gray-600">
                                                            Tindakan: {{ $report->validation->action->name ?? '-' }}
                                                        </p>
                                                        @if ($report->validation->catatan)
                                                            <p class="mt-1 text-xs text-gray-600">
                                                                Catatan: {{ $report->validation->catatan }}
                                                            </p>
                                                        @endif
                                                    </div>
                                                @else
                                                    <div class="p-3 mb-4 rounded-lg bg-yellow-50">
                                                        <p class="text-sm font-semibold text-yellow-700">
                                                            ⏳ Menunggu Validasi Manager
                                                        </p>
                                                    </div>
                                                @endif

                                                {{-- Keterangan --}}
                                                @if ($report->keterangan)
                                                    <div class="p-3 mb-4 rounded-lg bg-blue-50">
                                                        <p class="text-sm font-semibold text-blue-700">Keterangan:</p>
                                                        <p class="text-sm text-blue-600">{{ $report->keterangan }}</p>
                                                    </div>
                                                @endif

                                                {{-- Details by Category --}}
                                                @php
                                                    $detailsByCategory = $report->details->groupBy(function ($detail) {
                                                        return $detail->field->category->name ?? 'Lainnya';
                                                    });
                                                @endphp

                                                <div class="space-y-3">
                                                    @foreach ($detailsByCategory as $categoryName => $details)
                                                        <div class="p-3 border rounded-lg">
                                                            <p class="mb-2 text-sm font-semibold text-gray-700">
                                                                {{ $categoryName }}</p>
                                                            <div class="space-y-2">
                                                                @foreach ($details as $detail)
                                                                    <div>
                                                                        <div
                                                                            class="flex items-start justify-between text-sm">
                                                                            <span
                                                                                class="text-gray-600">{{ $detail->field->name }}:</span>
                                                                            <span class="font-medium text-gray-800">
                                                                                @if ($detail->field->input_type === 'checkbox')
                                                                                    {{ $detail->value_boolean ? '✅ Ya' : '❌ Tidak' }}
                                                                                @elseif ($detail->field->input_type === 'number' || $detail->field->input_type === 'photo_number')
                                                                                    {{ $detail->value_number ?? '-' }}
                                                                                @else
                                                                                    {{ $detail->value_text ?? '-' }}
                                                                                @endif
                                                                            </span>
                                                                        </div>

                                                                        {{-- Photos (jika ada) --}}
                                                                        @if ($detail->photos->count() > 0)
                                                                            <div class="grid grid-cols-4 gap-2 mt-2">
                                                                                @foreach ($detail->photos as $photo)
                                                                                    <a href="{{ $photo->url }}"
                                                                                        target="_blank"
                                                                                        class="block">
                                                                                        <img src="{{ $photo->url }}"
                                                                                            alt="{{ $detail->field->name }}"
                                                                                            class="object-cover w-full h-16 transition rounded hover:opacity-80">
                                                                                    </a>
                                                                                @endforeach
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                            Belum ada history laporan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <div class="mt-6">
                            {{ $reports->withQueryString()->links() }}
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <script>
        function showDetail(id) {
            const row = document.getElementById(`detail-${id}`);
            row.classList.toggle('hidden');
        }
    </script>
</x-app-layout>
