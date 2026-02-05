<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Detail Laporan FO: {{ $foUser->name }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $foUser->email }}
                </p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('daily-reports-fo.manager.dashboard') }}"
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
                    <p class="text-sm text-gray-600">Total Foto</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $stats['total_photos'] }}</p>
                </div>

                <div class="p-6 bg-white rounded-lg shadow-sm">
                    <p class="text-sm text-gray-600">Total Hari Lapor</p>
                    <p class="text-3xl font-bold text-purple-600">{{ $stats['total_days'] }}</p>
                </div>
            </div>

            {{-- Filter --}}
            <div class="p-4 mb-6 bg-white rounded-lg shadow-sm">
                <form method="GET" class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Dari Tanggal</label>
                        <input type="date" name="date_from" value="{{ $dateFrom }}"
                            onchange="this.form.submit()" class="w-full px-4 py-2 border rounded-lg">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Sampai Tanggal</label>
                        <input type="date" name="date_to" value="{{ $dateTo }}" onchange="this.form.submit()"
                            class="w-full px-4 py-2 border rounded-lg">
                    </div>
                </form>
            </div>

            {{-- Reports Table --}}
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="mb-6 text-lg font-semibold text-gray-800">
                        Daftar Laporan
                    </h3>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm min-w-max whitespace-nowrap">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="px-4 py-4 text-left text-gray-600 uppercase">#</th>
                                    <th class="px-4 py-4 text-left text-gray-600 uppercase">Tanggal</th>
                                    <th class="px-4 py-4 text-left text-gray-600 uppercase">Shift</th>
                                    <th class="px-4 py-4 text-left text-gray-600 uppercase">Slot</th>
                                    <th class="px-4 py-4 text-left text-gray-600 uppercase">Upload</th>
                                    <th class="px-4 py-4 text-left text-gray-600 uppercase">Foto</th>
                                    <th class="px-4 py-4 text-left text-gray-600 uppercase">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($reports as $index => $report)
                                    <tr class="border-t hover:bg-gray-50">
                                        <td class="px-4 py-3">
                                            {{ ($reports->currentPage() - 1) * $reports->perPage() + $index + 1 }}
                                        </td>
                                        <td class="px-4 py-3">
                                            {{ $report->tanggal->format('d M Y') }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <span
                                                class="px-2 py-1 text-xs rounded {{ $report->shift == 'pagi' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                                                {{ $report->shift_label }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 font-semibold">
                                            Slot {{ $report->slot }} - {{ $report->formatted_slot_time }}
                                        </td>
                                        <td class="px-4 py-3 text-xs text-gray-600">
                                            {{ $report->uploaded_at->format('H:i:s') }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 text-xs text-teal-700 bg-teal-100 rounded">
                                                📷 {{ $report->photos->count() }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <a href="{{ route('daily-reports-fo.manager.report-detail', $report->id) }}"
                                                class="text-blue-600 hover:text-blue-800">
                                                👁️ Lihat
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                            Tidak ada laporan dalam periode ini.
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
</x-app-layout>