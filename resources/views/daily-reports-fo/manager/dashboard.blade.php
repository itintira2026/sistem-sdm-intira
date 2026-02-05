<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    👨‍💼 Manager Dashboard - Daily Report FO
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Monitoring aktivitas FO real-time
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            {{-- Filter --}}
            <div class="p-4 mb-6 bg-white rounded-lg shadow-sm">
                <form method="GET" class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    {{-- Branch Selection --}}
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Pilih Cabang</label>
                        <select name="branch_id" onchange="this.form.submit()"
                            class="w-full px-4 py-2 border rounded-lg">
                            @foreach ($managedBranches as $branch)
                                <option value="{{ $branch->id }}"
                                    {{ $selectedBranch->id == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Date --}}
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Tanggal</label>
                        <input type="date" name="tanggal" value="{{ $tanggal }}"
                            onchange="this.form.submit()" class="w-full px-4 py-2 border rounded-lg">
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-end gap-2">
                        <a href="{{ route('daily-reports-fo.manager.reports', ['branch_id' => $selectedBranch->id, 'tanggal' => $tanggal]) }}"
                            class="flex items-center gap-2 px-4 py-2 text-white transition bg-blue-600 rounded-lg hover:bg-blue-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Lihat Semua Laporan
                        </a>
                        {{-- 🔥 EXPORT BUTTON --}}
                        {{-- <a href="{{ route('daily-reports-fo.manager.export', ['branch_id' => $selectedBranch->id, 'date_from' => $tanggal, 'date_to' => $tanggal]) }}"
                            class="flex items-center gap-2 px-4 py-2 text-white transition bg-green-600 rounded-lg hover:bg-green-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Export Excel
                        </a> --}}
                    </div>
                </form>
            </div>

            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 gap-6 mb-6 md:grid-cols-4">
                <div class="p-6 bg-white rounded-lg shadow-sm">
                    <p class="text-sm text-gray-600">Total FO</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $stats['total_fo'] }}</p>
                </div>

                <div class="p-6 bg-white rounded-lg shadow-sm">
                    <p class="text-sm text-gray-600">Laporan Hari Ini</p>
                    <p class="text-3xl font-bold text-teal-600">
                        {{ $stats['total_reports_today'] }}<span
                            class="text-lg text-gray-400">/{{ $stats['target_reports'] }}</span>
                    </p>
                </div>

                <div class="p-6 bg-white rounded-lg shadow-sm">
                    <p class="text-sm text-gray-600">FO Selesai</p>
                    <p class="text-3xl font-bold text-green-600">{{ $stats['fo_complete'] }}</p>
                </div>

                <div class="p-6 bg-white rounded-lg shadow-sm">
                    <p class="text-sm text-gray-600">FO Belum Mulai</p>
                    <p class="text-3xl font-bold text-orange-600">{{ $stats['fo_not_started'] }}</p>
                </div>
            </div>

            {{-- FO Progress Table --}}
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="mb-6 text-lg font-semibold text-gray-800">
                        Progress FO Real-time
                    </h3>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm min-w-max whitespace-nowrap">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="px-4 py-4 text-left text-gray-600 uppercase">FO Name</th>
                                    <th class="px-4 py-4 text-left text-gray-600 uppercase">Shift</th>
                                    <th class="px-4 py-4 text-center text-gray-600 uppercase">Slot 1</th>
                                    <th class="px-4 py-4 text-center text-gray-600 uppercase">Slot 2</th>
                                    <th class="px-4 py-4 text-center text-gray-600 uppercase">Slot 3</th>
                                    <th class="px-4 py-4 text-center text-gray-600 uppercase">Slot 4</th>
                                    <th class="px-4 py-4 text-left text-gray-600 uppercase">Progress</th>
                                    <th class="px-4 py-4 text-left text-gray-600 uppercase">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($foProgress as $fp)
                                    <tr class="border-t hover:bg-gray-50">
                                        <td class="px-4 py-3">
                                            <div>
                                                <p class="font-semibold text-gray-800">{{ $fp['user']->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $fp['user']->email }}</p>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span
                                                class="px-2 py-1 text-xs rounded {{ $fp['shift'] === 'pagi' ? 'bg-blue-100 text-blue-700' : ($fp['shift'] === 'siang' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-700') }}">
                                                {{ $fp['shift_label'] }}
                                            </span>
                                        </td>

                                        {{-- Slot Status --}}
                                        @foreach ([1, 2, 3, 4] as $slotNum)
                                            <td class="px-4 py-3 text-center">
                                                @if ($fp['slot_status'][$slotNum]['has_report'])
                                                    <div class="flex flex-col items-center">
                                                        <span class="text-2xl">✅</span>
                                                        <span class="text-xs text-gray-600">
                                                            📷 {{ $fp['slot_status'][$slotNum]['photo_count'] }}
                                                        </span>
                                                    </div>
                                                @else
                                                    <span class="text-2xl">⏳</span>
                                                @endif
                                            </td>
                                        @endforeach

                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2">
                                                <div class="flex-1">
                                                    <div class="w-full h-2 bg-gray-200 rounded-full">
                                                        <div class="h-2 transition-all rounded-full {{ $fp['progress_percentage'] == 100 ? 'bg-green-600' : 'bg-teal-600' }}"
                                                            style="width: {{ $fp['progress_percentage'] }}%">
                                                        </div>
                                                    </div>
                                                </div>
                                                <span class="text-sm font-semibold text-gray-700">
                                                    {{ $fp['total_reports'] }}/4
                                                </span>
                                            </div>
                                        </td>

                                        <td class="px-4 py-3">
                                            <a href="{{ route('daily-reports-fo.manager.fo-detail', $fp['user']->id) }}"
                                                class="text-blue-600 hover:text-blue-800">
                                                👁️ Detail
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                            Tidak ada FO di cabang ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Auto Refresh --}}
    <script>
        // Auto refresh every 2 minutes
        setTimeout(() => {
            location.reload();
        }, 2 * 60 * 1000);
    </script>
</x-app-layout>