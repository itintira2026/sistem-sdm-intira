{{-- FILTER --}}
<div class="p-4 mb-6 bg-white rounded-lg shadow-sm">
    <form method="GET" class="grid grid-cols-1 gap-4 md:grid-cols-4">
        {{-- Branch Filter (All) --}}
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">Filter Cabang</label>
            <select name="branch_id" onchange="this.form.submit()"
                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500">
                <option value="">Semua Cabang</option>
                @foreach ($allBranches as $branch)
                    <option value="{{ $branch->id }}"
                        {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                        {{ $branch->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Date Filter --}}
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">Tanggal</label>
            <input type="date" name="date" value="{{ $selectedDate }}"
                onchange="this.form.submit()"
                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500">
        </div>

        {{-- Export --}}
        <div class="flex items-end col-span-2 gap-2">
            <a href="{{ route('daily-reports.3hour-manager.export', array_merge(request()->query(), ['view' => 'superadmin'])) }}"
                class="flex items-center gap-2 px-4 py-2 text-white transition bg-green-600 rounded-lg hover:bg-green-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export Global
            </a>
        </div>
    </form>
</div>

{{-- GLOBAL STATS --}}
<div class="grid grid-cols-1 gap-6 mb-6 lg:grid-cols-4">
    <div class="p-6 bg-white rounded-lg shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <p class="text-sm text-gray-600">Total Cabang</p>
            <span class="text-2xl">🏢</span>
        </div>
        <p class="text-3xl font-bold text-blue-600">{{ $allBranches->count() }}</p>
    </div>

    <div class="p-6 bg-white rounded-lg shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <p class="text-sm text-gray-600">Laporan Hari Ini</p>
            <span class="text-2xl">📋</span>
        </div>
        <p class="text-3xl font-bold text-teal-600">{{ $reports->total() }}</p>
    </div>

    <div class="p-6 bg-white rounded-lg shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <p class="text-sm text-gray-600">Total Omzet</p>
            <span class="text-2xl">💰</span>
        </div>
        <p class="text-xl font-bold text-green-600">
            Rp {{ number_format($stats['omzet_hari_ini'], 0, ',', '.') }}
        </p>
    </div>

    <div class="p-6 bg-white rounded-lg shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <p class="text-sm text-gray-600">Total Revenue</p>
            <span class="text-2xl">💵</span>
        </div>
        <p class="text-xl font-bold text-purple-600">
            Rp {{ number_format($stats['revenue_hari_ini'], 0, ',', '.') }}
        </p>
    </div>
</div>

{{-- ALERT: LATE REPORTS --}}
@if($lateReports->count() > 0)
    <div class="p-4 mb-6 bg-red-50 border-l-4 border-red-500 rounded-lg">
        <div class="flex items-start">
            <span class="text-2xl mr-3">⚠️</span>
            <div class="flex-1">
                <h4 class="font-semibold text-red-800">Peringatan: Laporan Terlambat</h4>
                <div class="mt-2 space-y-1">
                    @foreach($lateReports->take(5) as $late)
                        <p class="text-sm text-red-700">
                            • {{ $late['branch']->name }} - {{ $late['manager']->name }} - Slot {{ $late['slot'] }}
                        </p>
                    @endforeach
                    @if($lateReports->count() > 5)
                        <p class="text-sm text-red-700 font-semibold">
                            + {{ $lateReports->count() - 5 }} lainnya
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif

{{-- MONITORING TABLE --}}
<div class="mb-6 bg-white rounded-lg shadow-sm">
    <div class="p-6">
        <h3 class="mb-4 text-lg font-semibold text-gray-800">📊 Status Laporan Per Cabang (Real-time)</h3>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="px-4 py-4 text-left text-gray-600 uppercase">Cabang</th>
                        <th class="px-4 py-4 text-left text-gray-600 uppercase">Area Manager</th>
                        <th class="px-4 py-4 text-center text-gray-600 uppercase">Slot 12:00</th>
                        <th class="px-4 py-4 text-center text-gray-600 uppercase">Slot 16:00</th>
                        <th class="px-4 py-4 text-center text-gray-600 uppercase">Slot 20:00</th>
                        <th class="px-4 py-4 text-center text-gray-600 uppercase">Progress</th>
                        <th class="px-4 py-4 text-left text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($branchesMonitoring as $monitoring)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-4 py-3 font-semibold">
                                {{ $monitoring['branch']->name }}
                            </td>
                            <td class="px-4 py-3">
                                <div>
                                    <p class="font-medium">{{ $monitoring['manager']->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $monitoring['manager']->email }}</p>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($monitoring['slots'][12]['status'] === 'done')
                                    <span class="text-2xl">✅</span>
                                    <p class="text-xs text-gray-600">{{ $monitoring['slots'][12]['reported_at']->format('H:i') }}</p>
                                @elseif($monitoring['slots'][12]['status'] === 'open')
                                    <span class="text-2xl">⏰</span>
                                @elseif($monitoring['slots'][12]['status'] === 'late')
                                    <span class="text-2xl">❌</span>
                                @else
                                    <span class="text-2xl">⏳</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($monitoring['slots'][16]['status'] === 'done')
                                    <span class="text-2xl">✅</span>
                                    <p class="text-xs text-gray-600">{{ $monitoring['slots'][16]['reported_at']->format('H:i') }}</p>
                                @elseif($monitoring['slots'][16]['status'] === 'open')
                                    <span class="text-2xl">⏰</span>
                                @elseif($monitoring['slots'][16]['status'] === 'late')
                                    <span class="text-2xl">❌</span>
                                @else
                                    <span class="text-2xl">⏳</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($monitoring['slots'][20]['status'] === 'done')
                                    <span class="text-2xl">✅</span>
                                    <p class="text-xs text-gray-600">{{ $monitoring['slots'][20]['reported_at']->format('H:i') }}</p>
                                @elseif($monitoring['slots'][20]['status'] === 'open')
                                    <span class="text-2xl">⏰</span>
                                @elseif($monitoring['slots'][20]['status'] === 'late')
                                    <span class="text-2xl">❌</span>
                                @else
                                    <span class="text-2xl">⏳</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <div class="w-16 h-2 bg-gray-200 rounded-full">
                                        <div class="h-2 rounded-full {{ 
                                            $monitoring['completion'] >= 100 ? 'bg-green-600' : 
                                            ($monitoring['completion'] >= 66 ? 'bg-blue-600' : 
                                            ($monitoring['completion'] >= 33 ? 'bg-orange-600' : 'bg-red-600')) 
                                        }}" style="width: {{ $monitoring['completion'] }}%"></div>
                                    </div>
                                    <span class="text-xs font-semibold">{{ number_format($monitoring['completion'], 0) }}%</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @if($monitoring['report'])
                                    <a href="{{ route('daily-reports.3hour-manager.show', $monitoring['report']->id) }}"
                                        class="text-blue-600 hover:text-blue-800">
                                        👁️ Detail
                                    </a>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                Tidak ada data monitoring.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- HISTORY TABLE --}}
<div class="bg-white rounded-lg shadow-sm">
    <div class="p-6">
        <h3 class="mb-4 text-lg font-semibold text-gray-800">📜 History Semua Laporan</h3>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="px-4 py-4 text-left text-gray-600 uppercase">Tanggal</th>
                        <th class="px-4 py-4 text-left text-gray-600 uppercase">Cabang</th>
                        <th class="px-4 py-4 text-left text-gray-600 uppercase">AM</th>
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
                            <td class="px-4 py-3 font-medium">
                                {{ $report->branchUser->branch->name ?? '-' }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $report->branchUser->user->name ?? '-' }}
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
                                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ 
                                    $report->is_complete ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' 
                                }}">
                                    {{ $report->total_reports }}/3
                                </span>
                            </td>
                            <td class="px-4 py-3 flex gap-2">
                                <a href="{{ route('daily-reports.3hour-manager.show', $report->id) }}"
                                    class="text-blue-600 hover:text-blue-800">
                                    👁️ Detail
                                </a>
                                <form method="POST" action="{{ route('daily-reports.3hour-manager.destroy', $report->id) }}"
                                    onsubmit="return confirm('Yakin hapus laporan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">
                                        🗑️ Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                Tidak ada history laporan.
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