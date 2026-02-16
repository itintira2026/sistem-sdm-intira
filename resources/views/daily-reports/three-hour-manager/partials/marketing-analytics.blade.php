{{-- FILTER --}}
<div class="p-4 mb-6 bg-white rounded-lg shadow-sm">
    <form method="GET" class="grid grid-cols-1 gap-4 md:grid-cols-7">
        {{-- Branch Filter --}}
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">Filter Cabang</label>
            <select name="branch_id" onchange="this.form.submit()"
                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500">
                <option value="">Semua Cabang</option>
                @foreach ($allBranches as $branch)
                    <option value="{{ $branch->id }}" {{ $selectedBranchId == $branch->id ? 'selected' : '' }}>
                        {{ $branch->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Manager Filter --}}
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">Filter Area Manager</label>
            <select name="manager_id" onchange="this.form.submit()"
                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500">
                <option value="">Semua AM</option>
                @foreach ($allManagers as $manager)
                    <option value="{{ $manager->id }}" {{ $selectedManagerId == $manager->id ? 'selected' : '' }}>
                        {{ $manager->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Date Filter (untuk table) --}}
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">Tanggal</label>
            <input type="date" name="date" value="{{ $selectedDate }}"
                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500">
        </div>

        {{-- Date Range (untuk chart) --}}
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">Chart Dari</label>
            <input type="date" name="date_from" value="{{ $dateFrom }}"
                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500">
        </div>

        <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">Chart Sampai</label>
            <input type="date" name="date_to" value="{{ $dateTo }}"
                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500">
        </div>

        {{-- Per Page --}}
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">Per Page</label>
            <select name="per_page" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-teal-500">
                <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
            </select>
        </div>

        {{-- Actions --}}
        <div class="flex items-end gap-2">
            <button type="submit"
                class="flex items-center gap-2 px-4 py-2 text-white transition bg-teal-600 rounded-lg hover:bg-teal-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                Filter
            </button>

            <a href="{{ route('daily-reports.3hour-manager.export', request()->query()) }}"
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
<div class="grid grid-cols-1 gap-6 mb-6 lg:grid-cols-4">
    <div class="p-6 bg-white rounded-lg shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <p class="text-sm text-gray-600">Total FO Aktif</p>
            <span class="text-2xl">👥</span>
        </div>
        <p class="text-3xl font-bold text-blue-600">{{ number_format($globalStats['total_fo']) }}</p>
    </div>

    <div class="p-6 bg-white rounded-lg shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <p class="text-sm text-gray-600">Total Omzet</p>
            <span class="text-2xl">💰</span>
        </div>
        <p class="text-2xl font-bold text-green-600">
            Rp {{ number_format($globalStats['total_omzet'], 0, ',', '.') }}
        </p>
    </div>

    <div class="p-6 bg-white rounded-lg shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <p class="text-sm text-gray-600">Total Revenue</p>
            <span class="text-2xl">💵</span>
        </div>
        <p class="text-2xl font-bold text-purple-600">
            Rp {{ number_format($globalStats['total_revenue'], 0, ',', '.') }}
        </p>
    </div>

    <div class="p-6 bg-white rounded-lg shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <p class="text-sm text-gray-600">Total Nasabah</p>
            <span class="text-2xl">📊</span>
        </div>
        <p class="text-3xl font-bold text-teal-600">{{ number_format($globalStats['total_nasabah']) }}</p>
    </div>
</div>

{{-- CHARTS --}}
<div class="grid grid-cols-1 gap-6 mb-6 lg:grid-cols-3">
    {{-- Omzet Chart --}}
    <div class="p-6 bg-white rounded-lg shadow-sm">
        <h3 class="mb-4 text-lg font-semibold text-gray-800">📈 Trend Omzet</h3>
        <canvas id="omzetChart" height="200"></canvas>
    </div>

    {{-- Revenue Chart --}}
    <div class="p-6 bg-white rounded-lg shadow-sm">
        <h3 class="mb-4 text-lg font-semibold text-gray-800">💵 Trend Revenue</h3>
        <canvas id="revenueChart" height="200"></canvas>
    </div>

    {{-- Nasabah Chart --}}
    <div class="p-6 bg-white rounded-lg shadow-sm">
        <h3 class="mb-4 text-lg font-semibold text-gray-800">👥 Trend Nasabah</h3>
        <canvas id="nasabahChart" height="200"></canvas>
    </div>
</div>

{{-- TABLE FO (SAMA SEPERTI SUPERADMIN, TAPI TANPA EDIT/DELETE) --}}
<div class="bg-white rounded-lg shadow-sm">
    <div class="p-6">
        <h3 class="mb-4 text-lg font-semibold text-gray-800">📋 Data Field Officer (FO)</h3>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="px-4 py-4 text-left text-gray-600 uppercase">#</th>
                        <th class="px-4 py-4 text-left text-gray-600 uppercase">FO Name</th>
                        <th class="px-4 py-4 text-left text-gray-600 uppercase">Area Manager</th>
                        <th class="px-4 py-4 text-left text-gray-600 uppercase">Cabang</th>
                        <th class="px-4 py-4 text-center text-gray-600 uppercase">12:00</th>
                        <th class="px-4 py-4 text-center text-gray-600 uppercase">16:00</th>
                        <th class="px-4 py-4 text-center text-gray-600 uppercase">20:00</th>
                        <th class="px-4 py-4 text-right text-gray-600 uppercase">Omzet</th>
                        <th class="px-4 py-4 text-left text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($foData as $index => $item)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-4 py-3">
                                {{ ($foData->currentPage() - 1) * $foData->perPage() + $index + 1 }}
                            </td>
                            <td class="px-4 py-3">
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $item['fo']->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $item['fo']->email }}</p>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-sm text-gray-700">
                                    {{ $item['manager'] ? $item['manager']->name : '-' }}
                                </p>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs font-semibold text-blue-700 bg-blue-100 rounded">
                                    {{ $item['branch']->name }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if ($item['report'] && $item['report']->report_12_at)
                                    <div>
                                        <span class="text-2xl">✅</span>
                                        <p class="text-xs text-gray-600">
                                            {{ $item['report']->report_12_at->format('H:i') }}</p>
                                    </div>
                                @else
                                    <span class="text-2xl">❌</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if ($item['report'] && $item['report']->report_16_at)
                                    <div>
                                        <span class="text-2xl">✅</span>
                                        <p class="text-xs text-gray-600">
                                            {{ $item['report']->report_16_at->format('H:i') }}</p>
                                    </div>
                                @else
                                    <span class="text-2xl">❌</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if ($item['report'] && $item['report']->report_20_at)
                                    <div>
                                        <span class="text-2xl">✅</span>
                                        <p class="text-xs text-gray-600">
                                            {{ $item['report']->report_20_at->format('H:i') }}</p>
                                    </div>
                                @else
                                    <span class="text-2xl">❌</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <p class="font-semibold text-green-600">
                                    Rp {{ number_format($item['stats_today']['omzet'], 0, ',', '.') }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    Rev: Rp {{ number_format($item['stats_today']['revenue'], 0, ',', '.') }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    Nasabah: {{ $item['stats_today']['nasabah'] }}
                                </p>
                            </td>
                            <td class="px-4 py-3">
                                {{-- MARKETING: READ-ONLY, NO EDIT/DELETE --}}
                                @if ($item['report'])
                                    <a href="{{ route('daily-reports.3hour-manager.show', $item['report']->id) }}"
                                        class="text-blue-600 hover:text-blue-800" title="Detail">
                                        👁️ Detail
                                    </a>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                                Tidak ada data FO.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-6">
                {{ $foData->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>

{{-- Chart.js Script --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const chartsData = @json($chartsData);

    // Omzet Chart
    new Chart(document.getElementById('omzetChart'), {
        type: 'line',
        data: {
            labels: chartsData.labels,
            datasets: [{
                label: 'Omzet',
                data: chartsData.omzet,
                borderColor: 'rgb(34, 197, 94)',
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });

    // Revenue Chart
    new Chart(document.getElementById('revenueChart'), {
        type: 'line',
        data: {
            labels: chartsData.labels,
            datasets: [{
                label: 'Revenue',
                data: chartsData.revenue,
                borderColor: 'rgb(168, 85, 247)',
                backgroundColor: 'rgba(168, 85, 247, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });

    // Nasabah Chart
    new Chart(document.getElementById('nasabahChart'), {
        type: 'line',
        data: {
            labels: chartsData.labels,
            datasets: [{
                label: 'Nasabah',
                data: chartsData.nasabah,
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
</script>
