<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                📊 Rekonsiliasi FO vs Data Resmi
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Perbandingan input FO (dari laporan yang sudah disetujui) dengan data resmi. Halaman ini sifatnya
                informasi/monitoring — tidak mengubah data apapun secara otomatis.
            </p>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">

            @if ($errors->any())
                <div class="p-4 text-red-700 bg-red-100 rounded-lg">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            {{-- Filter (tidak berubah dari versi sebelumnya) --}}
            <div class="p-4 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <form method="GET" action="{{ route('rekonsiliasi.index') }}" class="flex flex-wrap items-end gap-3">
                    <div>
                        <label class="block mb-1 text-xs font-medium text-gray-500">Cabang</label>
                        <select name="branch_id" onchange="this.form.submit()"
                            class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                            <option value="">🌐 Semua Cabang</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}" {{ $branchId == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block mb-1 text-xs font-medium text-gray-500">Tanggal Dari</label>
                        <input type="date" name="start_date" value="{{ $startDate }}"
                            onchange="this.form.submit()"
                            class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>
                    <div>
                        <label class="block mb-1 text-xs font-medium text-gray-500">Tanggal Sampai</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" onchange="this.form.submit()"
                            class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>
                </form>
                @if ($startDate !== $endDate)
                    <p class="mt-2 text-xs text-blue-600">
                        ℹ️ Rentang lebih dari 1 hari — angka di bawah adalah TOTAL dijumlahkan dari
                        {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }}
                        sampai {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}.
                    </p>
                @endif
            </div>

            {{-- Stats — DIUBAH: kembali ke 4 kartu ringkas, "selisih" gak
                 lagi dipecah kelebihan/kekurangan di level kartu (biar gak
                 rancu antara Omset vs Revenue). Detail kelebihan/kekurangan
                 sekarang ada di tabel, per metrik. --}}
            <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
                <div class="p-4 border border-gray-200 bg-gray-50 rounded-xl">
                    <p class="text-[10px] font-semibold tracking-wider text-gray-500 uppercase">Total FO</p>
                    <p class="text-2xl font-medium text-gray-800">{{ $stats['total_fo'] }}</p>
                </div>
                <div class="p-4 bg-[#EAF3DE] border border-[#C0DD97] rounded-xl">
                    <p class="text-[10px] font-semibold tracking-wider text-[#27500A] uppercase">✅ Sesuai (Semua Metrik)
                    </p>
                    <p class="text-2xl font-medium text-[#27500A]">{{ $stats['sesuai'] }}</p>
                </div>
                <div class="p-4 bg-[#FAEEDA] border border-[#FAC775] rounded-xl">
                    <p class="text-[10px] font-semibold tracking-wider text-[#854F0B] uppercase">⚠️ Ada Selisih</p>
                    <p class="text-2xl font-medium text-[#854F0B]">{{ $stats['selisih'] }}</p>
                </div>
                <div class="p-4 bg-gray-100 border border-gray-300 rounded-xl">
                    <p class="text-[10px] font-semibold tracking-wider text-gray-500 uppercase">⚪ Belum Ada Data</p>
                    <p class="text-2xl font-medium text-gray-600">{{ $stats['no_data'] }}</p>
                </div>
            </div>

            {{-- Table — DIUBAH: kolom "Status" tunggal diganti jadi 2 kolom
                 badge kecil, nempel di bawah masing-masing angka Omset & Revenue --}}
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                @if (empty($rows))
                    <div class="p-12 text-center text-gray-400">
                        <p class="mb-3 text-4xl">📋</p>
                        <p>Tidak ada FO untuk filter ini.</p>
                    </div>
                @else
                    @php
                        $badge = function ($status) {
                            return match ($status) {
                                'sesuai'
                                    => '<span class="inline-block px-1.5 py-0.5 text-[10px] font-semibold text-green-700 bg-green-100 rounded-full">✅ Sesuai</span>',
                                'lebih'
                                    => '<span class="inline-block px-1.5 py-0.5 text-[10px] font-semibold text-red-700 bg-red-100 rounded-full">⬆️ Lebih</span>',
                                'kurang'
                                    => '<span class="inline-block px-1.5 py-0.5 text-[10px] font-semibold text-yellow-700 bg-yellow-100 rounded-full">⬇️ Kurang</span>',
                                default
                                    => '<span class="inline-block px-1.5 py-0.5 text-[10px] font-semibold text-gray-400 bg-gray-100 rounded-full">⚪ —</span>',
                            };
                        };
                    @endphp
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="border-b border-gray-200 bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-xs font-semibold text-left text-gray-500 uppercase">FO
                                    </th>
                                    <th class="px-4 py-3 text-xs font-semibold text-left text-gray-500 uppercase">Cabang
                                    </th>
                                    <th class="px-4 py-3 text-xs font-semibold text-right text-gray-500 uppercase">Omset
                                        (FO)</th>
                                    <th class="px-4 py-3 text-xs font-semibold text-right text-gray-500 uppercase">Omset
                                        (Resmi)</th>
                                    <th class="px-4 py-3 text-xs font-semibold text-center text-gray-500 uppercase">
                                        Status Omset</th>
                                    <th class="px-4 py-3 text-xs font-semibold text-right text-gray-500 uppercase">
                                        Revenue (FO)</th>
                                    <th class="px-4 py-3 text-xs font-semibold text-right text-gray-500 uppercase">
                                        Revenue (Resmi)</th>
                                    <th class="px-4 py-3 text-xs font-semibold text-center text-gray-500 uppercase">
                                        Status Revenue</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($rows as $row)
                                    @php
                                        $rowBg = match ($row['overall_status']) {
                                            'sesuai' => 'bg-green-50/50',
                                            'selisih' => 'bg-yellow-50/30',
                                            default => '',
                                        };
                                    @endphp
                                    <tr class="{{ $rowBg }}">
                                        <td class="px-4 py-3 font-medium text-gray-800">{{ $row['user']->name }}</td>
                                        <td class="px-4 py-3 text-gray-500">{{ $row['branch'] }}</td>
                                        <td class="px-4 py-3 text-right text-gray-700">
                                            Rp {{ number_format($row['fo_omset'], 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-3 text-right text-gray-700">
                                            {{ $row['official_omset'] !== null ? 'Rp ' . number_format($row['official_omset'], 0, ',', '.') : '—' }}
                                        </td>
                                        <td class="px-4 py-3 text-center">{!! $badge($row['omset_status']) !!}</td>
                                        <td class="px-4 py-3 text-right text-gray-700">
                                            Rp {{ number_format($row['fo_revenue'], 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-3 text-right text-gray-700">
                                            {{ $row['official_revenue'] !== null ? 'Rp ' . number_format($row['official_revenue'], 0, ',', '.') : '—' }}
                                        </td>
                                        <td class="px-4 py-3 text-center">{!! $badge($row['revenue_status']) !!}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
