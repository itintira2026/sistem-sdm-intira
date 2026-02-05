<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    📋 Semua Laporan - Marketing View
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Data laporan dari semua cabang
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            {{-- Filter --}}
            <div class="p-4 mb-6 bg-white rounded-lg shadow-sm">
                <form method="GET" class="grid grid-cols-1 gap-4 md:grid-cols-5">
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

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Cabang</label>
                        <select name="branch_id" onchange="this.form.submit()"
                            class="w-full px-4 py-2 border rounded-lg">
                            <option value="">Semua Cabang</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}"
                                    {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Shift</label>
                        <select name="shift" onchange="this.form.submit()"
                            class="w-full px-4 py-2 border rounded-lg">
                            <option value="">Semua Shift</option>
                            <option value="pagi" {{ request('shift') == 'pagi' ? 'selected' : '' }}>
                                Shift Pagi
                            </option>
                            <option value="siang" {{ request('shift') == 'siang' ? 'selected' : '' }}>
                                Shift Siang
                            </option>
                        </select>
                    </div>
                    {{-- <div class="flex items-end">
                        <a href="{{ route('daily-reports-fo.manager.export', request()->query()) }}"
                            class="w-full flex items-center justify-center gap-2 px-4 py-2 text-white transition bg-green-600 rounded-lg hover:bg-green-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Export Excel
                        </a>
                    </div> --}}

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Per Page</label>
                        <select name="per_page" onchange="this.form.submit()"
                            class="w-full px-4 py-2 border rounded-lg">
                            @foreach ([10, 25, 50, 100] as $size)
                                <option value="{{ $size }}"
                                    {{ request('per_page', 25) == $size ? 'selected' : '' }}>
                                    {{ $size }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>

            {{-- Table --}}
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm min-w-max whitespace-nowrap">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="px-4 py-4 text-left text-gray-600 uppercase">#</th>
                                    <th class="px-4 py-4 text-left text-gray-600 uppercase">FO</th>
                                    <th class="px-4 py-4 text-left text-gray-600 uppercase">Cabang</th>
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
                                            <div>
                                                <p class="font-semibold text-gray-800">{{ $report->user->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $report->user->email }}</p>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-gray-700">
                                            {{ $report->branch->name }}
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
                                            Slot {{ $report->slot }}
                                        </td>
                                        <td class="px-4 py-3 text-xs text-gray-600">
                                            {{ $report->uploaded_at->format('H:i') }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 text-xs text-teal-700 bg-teal-100 rounded">
                                                📷 {{ $report->photos->count() }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <a href="{{ route('daily-reports-fo.marketing.report-detail', $report->id) }}"
                                                class="text-blue-600 hover:text-blue-800">
                                                👁️ Detail
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                                            Tidak ada laporan.
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