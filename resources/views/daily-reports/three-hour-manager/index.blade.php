<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    @if ($viewType === 'manager')
                        📊 Dashboard Laporan 3 Jam - Area Manager
                    @elseif($viewType === 'superadmin')
                        🔍 Monitoring Laporan 3 Jam - Superadmin
                    @else
                        📈 Analytics Laporan 3 Jam - Marketing
                    @endif
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Wajib 3 laporan per hari: 12:00, 16:00, 20:00 (Toleransi +1 jam)
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            {{-- Alerts --}}
            @if (session('success'))
                <div class="p-4 mb-6 text-green-700 bg-green-100 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 mb-6 text-red-700 bg-red-100 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('info'))
                <div class="p-4 mb-6 text-blue-700 bg-blue-100 rounded-lg">
                    {{ session('info') }}
                </div>
            @endif

            {{-- CONDITIONAL RENDERING BASED ON VIEW TYPE --}}
            @if ($viewType === 'manager')
                @include('daily-reports.three-hour-manager.partials.manager-dashboard')
            @elseif($viewType === 'superadmin')
                @include('daily-reports.three-hour-manager.partials.superadmin-table')
            @else
                @include('daily-reports.three-hour-manager.partials.marketing-analytics')
            @endif

        </div>
    </div>
</x-app-layout>
