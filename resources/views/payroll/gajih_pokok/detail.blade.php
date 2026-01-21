<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Detail Gaji Pokok - {{ $branch->name }}
                </h2>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('gaji.index') }}"
                    class="flex items-center gap-2 px-4 py-2 text-white transition bg-gray-500 rounded-lg hover:bg-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>

                <a href="{{ route('gaji-pokok.create', ['branch' => $branch->id, 'bulan' => $bulan, 'tahun' => $tahun]) }}"
                    class="flex items-center gap-2 px-4 py-2 text-white transition bg-teal-500 rounded-lg hover:bg-teal-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-6 mb-6 md:grid-cols-4">
                <div class="p-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-blue-500 rounded-md">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="flex-1 w-0 ml-5">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Gaji Pokok</dt>
                                <dd class="text-lg font-semibold text-gray-900">Rp
                                    {{ number_format($totalGajiPokok, 0, ',', '.') }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="p-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-green-500 rounded-md">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div class="flex-1 w-0 ml-5">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">User Periode Ini</dt>
                                <dd class="text-lg font-semibold text-gray-900">{{ $userWithGaji }} Orang</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="p-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-purple-500 rounded-md">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="flex-1 w-0 ml-5">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Periode</dt>
                                <dd class="text-lg font-semibold text-gray-900">
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="p-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-red-500 rounded-md">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="flex-1 w-0 ml-5">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Belum Input</dt>
                                <dd class="text-lg font-semibold text-gray-900">{{ $userWithoutGaji }} Orang</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if (session('success'))
                        <div class="p-4 mb-4 text-green-700 bg-green-100 border border-green-400 rounded-lg">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="GET" class="mb-6">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">Bulan</label>
                                <select name="bulan"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                                    @for ($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>
                                            {{ Carbon\Carbon::create()->month($i)->format('F') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">Tahun</label>
                                <select name="tahun"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                                    @for ($i = 2020; $i <= 2030; $i++)
                                        <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>
                                            {{ $i }}</option>
                                    @endfor
                                </select>
                            </div>

                            <div class="flex items-end">
                                <button type="submit"
                                    class="w-full px-4 py-2 text-white transition bg-teal-500 rounded-lg hover:bg-teal-600">
                                    Filter
                                </button>
                            </div>
                        </div>
                    </form>

                    @if ($users->isEmpty())
                        <div class="p-4 mb-6 border-l-4 border-yellow-400 bg-yellow-50">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        Tidak ada data gaji pokok untuk periode
                                        <strong>{{ Carbon\Carbon::create()->month($bulan)->format('F') }}
                                            {{ $tahun }}</strong> di
                                        cabang ini.
                                        Silakan pilih periode lain atau input gaji pokok terlebih dahulu.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="px-4 py-4 text-sm font-semibold text-left text-gray-600 uppercase">User
                                    </th>
                                    <th class="px-4 py-4 text-sm font-semibold text-left text-gray-600 uppercase">Email
                                    </th>
                                    <th class="px-4 py-4 text-sm font-semibold text-left text-gray-600 uppercase">Role
                                    </th>
                                    <th class="px-4 py-4 text-sm font-semibold text-left text-gray-600 uppercase">
                                        Periode</th>
                                    <th class="px-4 py-4 text-sm font-semibold text-left text-gray-600 uppercase">Gaji
                                        Pokok</th>
                                    <th class="px-4 py-4 text-sm font-semibold text-left text-gray-600 uppercase">
                                        Keterangan</th>
                                    <th class="px-4 py-4 text-sm font-semibold text-left text-gray-600 uppercase">Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $branchUser)
                                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                                        <td class="px-4 py-4">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="flex items-center justify-center w-10 h-10 font-semibold text-blue-600 bg-blue-100 rounded-full">
                                                    {{ strtoupper(substr($branchUser->user->name, 0, 2)) }}
                                                </div>
                                                <span
                                                    class="font-medium text-gray-900">{{ $branchUser->user->name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-gray-600">
                                            {{ $branchUser->user->email }}
                                        </td>
                                        <td class="px-4 py-4">
                                            @foreach ($branchUser->user->roles as $role)
                                                <span class="px-2 py-1 text-xs text-purple-700 bg-purple-100 rounded">
                                                    {{ $role->name }}
                                                </span>
                                            @endforeach
                                        </td>
                                        <td class="px-4 py-4">
                                            <span class="text-sm font-medium text-blue-600">
                                                {{ $branchUser->current_gaji_pokok->periode }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4">
                                            <span class="font-semibold text-gray-900">
                                                Rp
                                                {{ number_format($branchUser->current_gaji_pokok->amount, 0, ',', '.') }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-600">
                                            {{ $branchUser->current_gaji_pokok->keterangan ?? '-' }}
                                        </td>
                                        <td class="px-4 py-4">

                                            <a href="{{ route('gaji-pokok.show', $branchUser->current_gaji_pokok->id) }}"
                                                class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                Detail
                                            </a>

                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-12 text-center">
                                            <div class="flex flex-col items-center justify-center">
                                                <svg class="w-16 h-16 mb-4 text-gray-300" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <p class="text-lg font-medium text-gray-500">Tidak ada data gaji pokok
                                                </p>
                                                <p class="mt-1 text-sm text-gray-400">Belum ada data gaji pokok untuk
                                                    periode ini</p>
                                            </div>
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
</x-app-layout>

{{-- @php
    $gaji = $branchUser->display_gaji;
@endphp

<td>
    @if ($gaji)
        {{ $gaji->periode }}
    @else
        <span class="italic text-gray-400">-</span>
    @endif
</td>

<td>
    @if ($gaji)
        Rp {{ number_format($gaji->amount, 0, ',', '.') }}
    @else
        <span class="italic text-red-500">Belum ada</span>
    @endif
</td>

<td>
    @if ($gaji)
        <a href="{{ route('gaji-pokok.show', $gaji->id) }}">Detail</a>
    @else
        -
    @endif
</td> --}}
