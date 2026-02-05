<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    📈 Analytics Detail - Daily Report FO
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Analisis mendalam per kategori dan cabang
                </p>
            </div>
            <div class="flex gap-3 mt-3 md:mt-0">
                <a href="{{ route('daily-reports-fo.marketing.dashboard') }}"
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

            {{-- Filter --}}
            <div class="p-4 mb-6 bg-white rounded-lg shadow-sm">
                <form method="GET" class="grid grid-cols-1 gap-4 md:grid-cols-4">
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
                        <label class="block mb-2 text-sm font-medium text-gray-700">Filter Cabang</label>
                        <select name="branch_id" onchange="this.form.submit()"
                            class="w-full px-4 py-2 border rounded-lg">
                            <option value="">Semua Cabang</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}"
                                    {{ $selectedBranchId == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-end">
                        <button type="submit"
                            class="w-full px-4 py-2 text-white transition bg-teal-600 rounded-lg hover:bg-teal-700">
                            Filter
                        </button>
                    </div>
                </form>
            </div>

            {{-- Category Stats (Detailed) --}}
            <div class="p-6 mb-6 bg-white rounded-lg shadow-sm">
                <h3 class="mb-6 text-lg font-semibold text-gray-800">📊 Statistik per Kategori</h3>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($categoryStats as $key => $stat)
                        <div class="p-4 border-l-4 rounded-lg bg-gray-50 {{ 
                            str_contains($key, 'fb') ? 'border-blue-500' : 
                            (str_contains($key, 'ig') ? 'border-pink-500' : 'border-purple-500') 
                        }}">
                            <p class="mb-2 text-sm font-medium text-gray-700">{{ $stat['label'] }}</p>
                            <p class="text-3xl font-bold text-gray-800">{{ number_format($stat['total']) }}</p>
                            <p class="mt-1 text-xs text-gray-600">total aktivitas</p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Reports by Shift --}}
            <div class="p-6 mb-6 bg-white rounded-lg shadow-sm">
                <h3 class="mb-6 text-lg font-semibold text-gray-800">🕐 Laporan per Shift</h3>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div class="p-6 text-center rounded-lg bg-blue-50">
                        <p class="mb-2 text-sm font-medium text-blue-700">Shift Pagi</p>
                        <p class="text-4xl font-bold text-blue-600">
                            {{ number_format($reportsByShift['pagi'] ?? 0) }}
                        </p>
                        <p class="mt-2 text-xs text-blue-600">laporan</p>
                    </div>

                    <div class="p-6 text-center rounded-lg bg-purple-50">
                        <p class="mb-2 text-sm font-medium text-purple-700">Shift Siang</p>
                        <p class="text-4xl font-bold text-purple-600">
                            {{ number_format($reportsByShift['siang'] ?? 0) }}
                        </p>
                        <p class="mt-2 text-xs text-purple-600">laporan</p>
                    </div>
                </div>

                {{-- Chart --}}
                <div class="mt-6">
                    <canvas id="shiftChart" height="80"></canvas>
                </div>
            </div>

            {{-- Reports by Branch --}}
            <div class="p-6 bg-white rounded-lg shadow-sm">
                <h3 class="mb-6 text-lg font-semibold text-gray-800">🏢 Laporan per Cabang</h3>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm min-w-max whitespace-nowrap">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="px-4 py-4 text-left text-gray-600 uppercase">#</th>
                                <th class="px-4 py-4 text-left text-gray-600 uppercase">Cabang</th>
                                <th class="px-4 py-4 text-left text-gray-600 uppercase">Total Laporan</th>
                                <th class="px-4 py-4 text-left text-gray-600 uppercase">Progress</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($reportsByBranch as $index => $branchReport)
                                @php
                                    $maxReports = $reportsByBranch->max('total');
                                    $percentage = $maxReports > 0 ? ($branchReport->total / $maxReports) * 100 : 0;
                                @endphp

                                <tr class="border-t hover:bg-gray-50">
                                    <td class="px-4 py-3 font-semibold text-gray-600">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3 font-semibold text-gray-800">
                                        {{ $branchReport->branch->name }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="px-3 py-1 text-sm font-semibold text-teal-700 bg-teal-100 rounded">
                                            {{ number_format($branchReport->total) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <div class="flex-1">
                                                <div class="w-full h-2 bg-gray-200 rounded-full">
                                                    <div class="h-2 bg-teal-600 rounded-full transition-all"
                                                        style="width: {{ $percentage }}%">
                                                    </div>
                                                </div>
                                            </div>
                                            <span class="text-xs font-medium text-gray-600">
                                                {{ number_format($percentage, 0) }}%
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                        Belum ada data.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Shift comparison chart
        const shiftCtx = document.getElementById('shiftChart');
        const shiftData = @json($reportsByShift);

        new Chart(shiftCtx, {
            type: 'bar',
            data: {
                labels: ['Shift Pagi', 'Shift Siang'],
                datasets: [{
                    label: 'Jumlah Laporan',
                    data: [shiftData.pagi || 0, shiftData.siang || 0],
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.7)',
                        'rgba(168, 85, 247, 0.7)'
                    ],
                    borderColor: [
                        'rgb(59, 130, 246)',
                        'rgb(168, 85, 247)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</x-app-layout>