{{-- FILTER --}}
<div class="p-4 mb-6 bg-white rounded-lg shadow-sm">
    <form method="GET" class="grid grid-cols-1 gap-4 md:grid-cols-4">
        {{-- Branch Filter --}}
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">Pilih Cabang</label>
            <select name="branch_id" onchange="this.form.submit()"
                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500">
                @foreach ($managedBranches as $branch)
                    <option value="{{ $branch->id }}" {{ $selectedBranch->id == $branch->id ? 'selected' : '' }}>
                        {{ $branch->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Date Filter --}}
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">Tanggal</label>
            <input type="date" name="date" value="{{ $selectedDate }}" onchange="this.form.submit()"
                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500">
        </div>

        {{-- Actions --}}
        <div class="flex items-end col-span-2 gap-2">
            <a href="{{ route('daily-reports.3hour-manager.create', ['branch_id' => $selectedBranch->id, 'date' => $selectedDate]) }}"
                class="flex items-center gap-2 px-4 py-2 text-white transition bg-teal-600 rounded-lg hover:bg-teal-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Laporan
            </a>

            <a href="{{ route('daily-reports.3hour-manager.export', ['branch_id' => $selectedBranch->id, 'date_from' => $selectedDate, 'date_to' => $selectedDate]) }}"
                class="flex items-center gap-2 px-4 py-2 text-white transition bg-green-600 rounded-lg hover:bg-green-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export
            </a>
        </div>
    </form>
</div>

{{-- STATS CARDS --}}
<div class="grid grid-cols-1 gap-6 mb-6 lg:grid-cols-3">

    {{-- OMZET --}}
    <div class="p-6 bg-white rounded-lg shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">💰 OMZET</h3>
            <span class="text-2xl">📈</span>
        </div>

        <div class="space-y-3">
            <div class="p-3 border-l-4 border-blue-500 rounded-lg bg-blue-50">
                <p class="text-xs text-gray-600">Hari Ini ({{ \Carbon\Carbon::parse($selectedDate)->format('d M Y') }})
                </p>
                <p class="mt-1 text-xl font-bold text-blue-600">
                    Rp {{ number_format($stats['omzet_hari_ini'], 0, ',', '.') }}
                </p>
            </div>

            <div class="p-3 rounded-lg bg-gray-50">
                <p class="text-xs text-gray-600">Bulan Ini</p>
                <p class="mt-1 text-lg font-semibold text-gray-700">
                    Rp {{ number_format($stats['omzet_bulan_ini'], 0, ',', '.') }}
                </p>
            </div>
        </div>
    </div>

    {{-- NASABAH --}}
    <div class="p-6 bg-white rounded-lg shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">👥 NASABAH</h3>
            <span class="text-2xl">📊</span>
        </div>

        <div class="space-y-3">
            <div class="p-3 border-l-4 border-green-500 rounded-lg bg-green-50">
                <p class="text-xs text-gray-600">Hari Ini</p>
                <p class="mt-1 text-xl font-bold text-green-600">
                    {{ number_format($stats['nasabah_hari_ini']) }} Nasabah
                </p>
            </div>

            <div class="p-3 rounded-lg bg-gray-50">
                <p class="text-xs text-gray-600">Bulan Ini</p>
                <p class="mt-1 text-lg font-semibold text-gray-700">
                    {{ number_format($stats['nasabah_bulan_ini']) }} Nasabah
                </p>
            </div>
        </div>
    </div>

    {{-- REVENUE --}}
    <div class="p-6 bg-white rounded-lg shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">💵 REVENUE</h3>
            <span class="text-2xl">💸</span>
        </div>

        <div class="space-y-3">
            <div class="p-3 border-l-4 border-teal-500 rounded-lg bg-teal-50">
                <p class="text-xs text-gray-600">Hari Ini</p>
                <p class="mt-1 text-xl font-bold text-teal-600">
                    Rp {{ number_format($stats['revenue_hari_ini'], 0, ',', '.') }}
                </p>
            </div>

            <div class="p-3 rounded-lg bg-gray-50">
                <p class="text-xs text-gray-600">Bulan Ini</p>
                <p class="mt-1 text-lg font-semibold text-gray-700">
                    Rp {{ number_format($stats['revenue_bulan_ini'], 0, ',', '.') }}
                </p>
            </div>
        </div>
    </div>

</div>

{{-- PROGRESS LAPORAN HARI INI --}}
<div class="p-6 mb-6 bg-white rounded-lg shadow-sm">
    <h3 class="mb-4 text-lg font-semibold text-gray-800">⏰ Progress Laporan Hari Ini</h3>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        @foreach ($slots as $slot => $data)
            <div
                class="p-4 border-2 rounded-lg {{ $data['status_color'] === 'green'
                    ? 'border-green-500 bg-green-50'
                    : ($data['status_color'] === 'orange'
                        ? 'border-orange-500 bg-orange-50'
                        : ($data['status_color'] === 'red'
                            ? 'border-red-500 bg-red-50'
                            : 'border-gray-300 bg-gray-50')) }}">

                <div class="flex items-center justify-between mb-3">
                    <div>
                        <p class="text-sm font-semibold text-gray-700">{{ $data['label'] }}</p>
                        <p class="text-xs text-gray-600">Window: {{ $data['time'] }} - {{ $data['deadline'] }}</p>
                    </div>

                    @if ($data['status'] === 'done')
                        <span class="text-3xl">✅</span>
                    @elseif ($data['status'] === 'open')
                        <span class="text-3xl">⏰</span>
                    @elseif ($data['status'] === 'late')
                        <span class="text-3xl">❌</span>
                    @else
                        <span class="text-3xl">⏳</span>
                    @endif
                </div>

                <div class="flex items-center justify-between">
                    <span
                        class="px-3 py-1 text-xs font-semibold rounded-full {{ $data['status_color'] === 'green'
                            ? 'bg-green-200 text-green-800'
                            : ($data['status_color'] === 'orange'
                                ? 'bg-orange-200 text-orange-800'
                                : ($data['status_color'] === 'red'
                                    ? 'bg-red-200 text-red-800'
                                    : 'bg-gray-200 text-gray-800')) }}">
                        {{ $data['status_label'] }}
                    </span>

                    @if ($data['can_report'])
                        <a href="{{ route('daily-reports.3hour-manager.create', ['slot' => $slot, 'date' => $selectedDate, 'branch_id' => $selectedBranch->id]) }}"
                            class="text-xs font-medium text-blue-600 hover:text-blue-800">
                            Lapor Sekarang →
                        </a>
                    @endif
                </div>

                @if (isset($data['reported_at']))
                    <p class="mt-2 text-xs text-gray-600">
                        Upload: {{ $data['reported_at']->format('H:i:s') }}
                    </p>
                @endif
            </div>
        @endforeach
    </div>
</div>

{{-- HISTORY TABLE --}}
<div class="bg-white rounded-lg shadow-sm">
    <div class="p-6">
        <h3 class="mb-4 text-lg font-semibold text-gray-800">📜 History 7 Hari Terakhir</h3>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="px-4 py-4 text-left text-gray-600 uppercase">Tanggal</th>
                        <th class="px-4 py-4 text-left text-gray-600 uppercase">Cabang</th>
                        <th class="px-4 py-4 text-center text-gray-600 uppercase">12:00</th>
                        <th class="px-4 py-4 text-center text-gray-600 uppercase">16:00</th>
                        <th class="px-4 py-4 text-center text-gray-600 uppercase">20:00</th>
                        <th class="px-4 py-4 text-center text-gray-600 uppercase">Kelengkapan</th>
                        <th class="px-4 py-4 text-left text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($reports as $report)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-4 py-3">
                                {{ $report->created_at->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $report->branchUser->branch->name ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                {!! $report->report_12_at ? '<span class="text-2xl">✅</span>' : '<span class="text-2xl">❌</span>' !!}
                            </td>
                            <td class="px-4 py-3 text-center">
                                {!! $report->report_16_at ? '<span class="text-2xl">✅</span>' : '<span class="text-2xl">❌</span>' !!}
                            </td>
                            <td class="px-4 py-3 text-center">
                                {!! $report->report_20_at ? '<span class="text-2xl">✅</span>' : '<span class="text-2xl">❌</span>' !!}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span
                                    class="px-3 py-1 text-xs font-semibold rounded-full {{ $report->is_complete ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                                    {{ $report->total_reports }}/3
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('daily-reports.3hour-manager.show', $report->id) }}"
                                    class="text-blue-600 hover:text-blue-800">
                                    👁️ Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                Belum ada history laporan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
