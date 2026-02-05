<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    📊 Marketing Dashboard - Daily Report FO
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Analytics & Performance Overview
                </p>
            </div>
            <div class="flex gap-3 mt-3 md:mt-0">
                <a href="{{ route('daily-reports-fo.marketing.analytics') }}"
                    class="flex items-center gap-2 px-4 py-2 text-white transition bg-purple-600 rounded-lg hover:bg-purple-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Analytics Detail
                </a>
                <a href="{{ route('daily-reports-fo.marketing.reports') }}"
                    class="flex items-center gap-2 px-4 py-2 text-white transition bg-blue-600 rounded-lg hover:bg-blue-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Lihat Semua Laporan
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            {{-- Date Filter --}}
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

                    <div class="flex items-end">
                        <button type="submit"
                            class="w-full px-4 py-2 text-white transition bg-teal-600 rounded-lg hover:bg-teal-700">
                            Filter
                        </button>
                    </div>
                </form>
            </div>

            {{-- Overall Stats --}}
            <div class="grid grid-cols-1 gap-6 mb-6 md:grid-cols-4">
                <div class="p-6 bg-white rounded-lg shadow-sm">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm text-gray-600">Total Laporan</p>
                        <span class="text-2xl">📋</span>
                    </div>
                    <p class="text-3xl font-bold text-teal-600">{{ number_format($stats['total_reports']) }}</p>
                </div>

                <div class="p-6 bg-white rounded-lg shadow-sm">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm text-gray-600">Total Foto</p>
                        <span class="text-2xl">📷</span>
                    </div>
                    <p class="text-3xl font-bold text-blue-600">{{ number_format($stats['total_photos']) }}</p>
                </div>

                <div class="p-6 bg-white rounded-lg shadow-sm">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm text-gray-600">Total FO</p>
                        <span class="text-2xl">👥</span>
                    </div>
                    <p class="text-3xl font-bold text-purple-600">{{ number_format($stats['total_fo']) }}</p>
                </div>

                <div class="p-6 bg-white rounded-lg shadow-sm">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm text-gray-600">Total Cabang</p>
                        <span class="text-2xl">🏢</span>
                    </div>
                    <p class="text-3xl font-bold text-orange-600">{{ number_format($stats['total_branches']) }}</p>
                </div>
            </div>

            {{-- Chart: Reports Per Day --}}
            <div class="p-6 mb-6 bg-white rounded-lg shadow-sm">
                <h3 class="mb-6 text-lg font-semibold text-gray-800">📈 Trend Laporan Harian</h3>
                <canvas id="reportsChart" height="80"></canvas>
            </div>

            {{-- Photos by Category --}}
            <div class="p-6 mb-6 bg-white rounded-lg shadow-sm">
                <h3 class="mb-6 text-lg font-semibold text-gray-800">📊 Aktivitas per Kategori</h3>

                <div class="grid grid-cols-2 gap-4 md:grid-cols-3">
                    @foreach (config('daily_report_fo.photo_categories') as $key => $label)
                        @php
                            $total = $photosByCategory[$key] ?? 0;
                            $colors = [
                                'like_fb' => 'bg-blue-100 text-blue-700',
                                'comment_fb' => 'bg-blue-200 text-blue-800',
                                'like_ig' => 'bg-pink-100 text-pink-700',
                                'comment_ig' => 'bg-pink-200 text-pink-800',
                                'like_tiktok' => 'bg-purple-100 text-purple-700',
                                'comment_tiktok' => 'bg-purple-200 text-purple-800',
                            ];
                            $color = $colors[$key] ?? 'bg-gray-100 text-gray-700';
                        @endphp

                        <div class="p-4 rounded-lg {{ $color }}">
                            <p class="text-sm font-medium">{{ $label }}</p>
                            <p class="mt-2 text-2xl font-bold">{{ number_format($total) }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Top Performing FO --}}
            <div class="p-6 bg-white rounded-lg shadow-sm">
                <h3 class="mb-6 text-lg font-semibold text-gray-800">🏆 Top 10 Performing FO</h3>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm min-w-max whitespace-nowrap">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="px-4 py-4 text-left text-gray-600 uppercase">Rank</th>
                                <th class="px-4 py-4 text-left text-gray-600 uppercase">FO Name</th>
                                <th class="px-4 py-4 text-left text-gray-600 uppercase">Email</th>
                                <th class="px-4 py-4 text-left text-gray-600 uppercase">Total Laporan</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($topFO as $index => $fo)
                                <tr class="border-t hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        @if ($index < 3)
                                            <span class="text-2xl">
                                                {{ $index == 0 ? '🥇' : ($index == 1 ? '🥈' : '🥉') }}
                                            </span>
                                        @else
                                            <span class="font-semibold text-gray-600">#{{ $index + 1 }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 font-semibold text-gray-800">{{ $fo->user->name }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $fo->user->email }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-3 py-1 text-sm font-semibold text-teal-700 bg-teal-100 rounded">
                                            {{ number_format($fo->total_reports) }} laporan
                                        </span>
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
        // Reports per day chart
        const ctx = document.getElementById('reportsChart');
        const reportsData = @json($reportsPerDay);

        const labels = Object.keys(reportsData);
        const data = Object.values(reportsData);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Laporan',
                    data: data,
                    borderColor: 'rgb(13, 148, 136)',
                    backgroundColor: 'rgba(13, 148, 136, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
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
</x-app-layout>