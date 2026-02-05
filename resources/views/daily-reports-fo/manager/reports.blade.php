<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    📋 Semua Laporan Daily Report FO
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $selectedBranch->name }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            {{-- Stats --}}
            <div class="grid grid-cols-1 gap-6 mb-6 md:grid-cols-2">
                <div class="p-6 bg-white rounded-lg shadow-sm">
                    <p class="text-sm text-gray-600">Total Laporan</p>
                    <p class="text-3xl font-bold text-teal-600">{{ $stats['total_reports'] }}</p>
                </div>

                <div class="p-6 bg-white rounded-lg shadow-sm">
                    <p class="text-sm text-gray-600">Total Foto</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $stats['total_photos'] }}</p>
                </div>
            </div>

            {{-- Filter --}}
            <div class="p-4 mb-6 bg-white rounded-lg shadow-sm">
                <form method="GET" class="grid grid-cols-1 gap-4 md:grid-cols-4">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Cabang</label>
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

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Tanggal</label>
                        <input type="date" name="tanggal" value="{{ $tanggal }}"
                            onchange="this.form.submit()" class="w-full px-4 py-2 border rounded-lg">
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

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Per Page</label>
                        <select name="per_page" onchange="this.form.submit()"
                            class="w-full px-4 py-2 border rounded-lg">
                            @foreach ([10, 25, 50] as $size)
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
                                            <a href="{{ route('daily-reports-fo.manager.report-detail', $report->id) }}"
                                                class="text-blue-600 hover:text-blue-800">
                                                👁️ Detail
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-4 py-8 text-center text-gray-500">
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