<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Dashboard') }}
        </h2>
        <p class="mt-1 text-sm text-gray-500">Untuk melihat informasi dan statistik sistem</p>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-gray-900">Dashboard</h2>
                <p class="mt-1 text-sm text-gray-500">
                    Selamat datang, <span class="font-semibold text-indigo-600">{{ Auth::user()->name }}</span> —
                    {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                </p>
            </div>
            <div class="flex items-center gap-2 rounded-xl bg-indigo-50 px-4 py-2 text-sm font-medium text-indigo-700">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m6-2a10 10 0 11-20 0 10 10 0 0120 0z"/>
                </svg>
                {{ \Carbon\Carbon::now()->format('H:i') }} WIB
            </div>
        </div>
    </x-slot>

    {{-- ------------------------------------------------------------------ --}}
    {{-- Style Overrides                                                      --}}
    {{-- ------------------------------------------------------------------ --}}
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        .dash-root { font-family: 'Plus Jakarta Sans', sans-serif; }

        /* Card hover lift */
        .stat-card {
            transition: transform .2s ease, box-shadow .2s ease;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 32px -4px rgba(0,0,0,.10);
        }

        /* Animated number */
        @keyframes countUp {
            from { opacity: 0; transform: translateY(6px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .anim-num { animation: countUp .5s ease both; }

        /* Progress bar */
        .progress-bar { transition: width 1s cubic-bezier(.25,.8,.25,1); }

        /* Badge status */
        .badge-hadir   { background:#dcfce7; color:#16a34a; }
        .badge-terlambat{ background:#fef9c3; color:#ca8a04; }
        .badge-absen   { background:#fee2e2; color:#dc2626; }
        .badge-izin    { background:#e0f2fe; color:#0284c7; }

        /* Table hover */
        .dash-table tbody tr:hover td { background:#f5f7ff; }
    </style>

    <div class="py-8 dash-root">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- ============================================================ --}}
            {{-- ROW 1 — 4 Stat Cards                                         --}}
            {{-- ============================================================ --}}
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-2 lg:grid-cols-4">

                {{-- Total Karyawan --}}
                <div class="stat-card relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 to-indigo-500 p-5 text-white shadow">
                    <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-white/10"></div>
                    <div class="relative">
                        <p class="text-xs font-semibold uppercase tracking-widest text-indigo-200">Total Karyawan</p>
                        <p class="anim-num mt-2 text-4xl font-extrabold">{{ $totalKaryawan }}</p>
                        <p class="mt-1 text-xs text-indigo-200">terdaftar di sistem</p>
                    </div>
                    <div class="absolute bottom-4 right-4 opacity-20">
                        <svg class="h-10 w-10" fill="currentColor" viewBox="0 0 24 24"><path d="M17 20h5v-2a4 4 0 00-5.5-3.7M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a4 4 0 015.5-3.7M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                </div>

                {{-- Hadir Hari Ini --}}
                <div class="stat-card relative overflow-hidden rounded-2xl bg-white p-5 shadow ring-1 ring-gray-100">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">Hadir Hari Ini</p>
                            <p class="anim-num mt-2 text-4xl font-extrabold text-gray-900">{{ $hadirHariIni }}</p>
                            <p class="mt-1 text-xs text-gray-400">dari {{ $totalKaryawan }} karyawan</p>
                        </div>
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-green-100 text-green-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                    </div>
                    {{-- Progress --}}
                    <div class="mt-4 h-1.5 w-full overflow-hidden rounded-full bg-gray-100">
                        @php $pctHadir = $totalKaryawan > 0 ? round(($hadirHariIni / $totalKaryawan) * 100) : 0; @endphp
                        <div class="progress-bar h-full rounded-full bg-green-500" style="width: {{ $pctHadir }}%"></div>
                    </div>
                    <p class="mt-1 text-right text-xs font-medium text-gray-400">{{ $pctHadir }}%</p>
                </div>

                {{-- Terlambat Hari Ini --}}
                <div class="stat-card relative overflow-hidden rounded-2xl bg-white p-5 shadow ring-1 ring-gray-100">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">Terlambat Hari Ini</p>
                            <p class="anim-num mt-2 text-4xl font-extrabold text-yellow-500">{{ $terlambatHariIni }}</p>
                            <p class="mt-1 text-xs text-gray-400">karyawan</p>
                        </div>
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-yellow-100 text-yellow-500">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                    </div>
                    <div class="mt-4 h-1.5 w-full overflow-hidden rounded-full bg-gray-100">
                        @php $pctTerlambat = $totalKaryawan > 0 ? round(($terlambatHariIni / $totalKaryawan) * 100) : 0; @endphp
                        <div class="progress-bar h-full rounded-full bg-yellow-400" style="width: {{ $pctTerlambat }}%"></div>
                    </div>
                    <p class="mt-1 text-right text-xs font-medium text-gray-400">{{ $pctTerlambat }}%</p>
                </div>

                {{-- Daily Report Pending --}}
                <div class="stat-card relative overflow-hidden rounded-2xl bg-white p-5 shadow ring-1 ring-gray-100">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">Report Pending</p>
                            <p class="anim-num mt-2 text-4xl font-extrabold text-rose-500">{{ $reportPending }}</p>
                            <p class="mt-1 text-xs text-gray-400">menunggu validasi</p>
                        </div>
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-100 text-rose-500">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </span>
                    </div>
                    <div class="mt-4 flex gap-2">
                        <span class="rounded-lg bg-green-50 px-2 py-1 text-xs font-medium text-green-600">
                            ✓ {{ $reportApproved }} approved
                        </span>
                        <span class="rounded-lg bg-rose-50 px-2 py-1 text-xs font-medium text-rose-600">
                            ✗ {{ $reportRejected }} rejected
                        </span>
                    </div>
                </div>

            </div>

            {{-- ============================================================ --}}
            {{-- ROW 2 — Presensi Bulan Ini + Report Status                   --}}
            {{-- ============================================================ --}}
            <div class="grid gap-6 lg:grid-cols-3">

                {{-- Rekap Presensi Bulan Ini (2/3) --}}
                <div class="lg:col-span-2 rounded-2xl bg-white p-6 shadow ring-1 ring-gray-100">
                    <div class="mb-5 flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-bold text-gray-900">Rekap Presensi</h3>
                            <p class="text-xs text-gray-400">{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</p>
                        </div>
                        <span class="rounded-lg bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-600">
                            {{ \Carbon\Carbon::now()->format('M Y') }}
                        </span>
                    </div>

                    {{-- Summary Bar --}}
                    <div class="mb-6 grid grid-cols-4 gap-3">
                        @foreach([
                            ['label'=>'Hadir','value'=>$rekapBulanIni['hadir'],'color'=>'bg-green-500','text'=>'text-green-600','bg'=>'bg-green-50'],
                            ['label'=>'Terlambat','value'=>$rekapBulanIni['terlambat'],'color'=>'bg-yellow-400','text'=>'text-yellow-600','bg'=>'bg-yellow-50'],
                            ['label'=>'Izin','value'=>$rekapBulanIni['izin'],'color'=>'bg-blue-400','text'=>'text-blue-600','bg'=>'bg-blue-50'],
                            ['label'=>'Absen','value'=>$rekapBulanIni['absen'],'color'=>'bg-red-400','text'=>'text-red-600','bg'=>'bg-red-50'],
                        ] as $item)
                        <div class="rounded-xl {{ $item['bg'] }} p-3 text-center">
                            <p class="text-2xl font-extrabold {{ $item['text'] }}">{{ $item['value'] }}</p>
                            <p class="mt-0.5 text-xs font-medium text-gray-500">{{ $item['label'] }}</p>
                        </div>
                        @endforeach
                    </div>

                    {{-- Tabel 5 karyawan terlambat teratas bulan ini --}}
                    <h4 class="mb-3 text-xs font-bold uppercase tracking-widest text-gray-400">Top Keterlambatan Bulan Ini</h4>
                    @if($topTerlambat->isEmpty())
                        <p class="text-sm text-gray-400 italic">Tidak ada data keterlambatan.</p>
                    @else
                    <div class="overflow-x-auto">
                        <table class="dash-table w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-100 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">
                                    <th class="pb-3 pr-4">Karyawan</th>
                                    <th class="pb-3 pr-4 text-center">Jml Terlambat</th>
                                    <th class="pb-3 text-right">Total Potongan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($topTerlambat as $row)
                                <tr>
                                    <td class="py-2.5 pr-4">
                                        <div class="flex items-center gap-2.5">
                                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100 text-xs font-bold text-indigo-600">
                                                {{ strtoupper(substr($row->user->name ?? '-', 0, 2)) }}
                                            </div>
                                            <span class="font-medium text-gray-800">{{ $row->user->name ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="py-2.5 pr-4 text-center">
                                        <span class="inline-block rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-semibold text-yellow-700">
                                            {{ $row->jumlah_terlambat }}×
                                        </span>
                                    </td>
                                    <td class="py-2.5 text-right font-semibold text-red-500">
                                        Rp {{ number_format($row->total_potongan, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>

                {{-- Report Status Donut (1/3) --}}
                <div class="rounded-2xl bg-white p-6 shadow ring-1 ring-gray-100">
                    <h3 class="mb-1 text-base font-bold text-gray-900">Status Daily Report</h3>
                    <p class="mb-5 text-xs text-gray-400">Hari ini · semua branch</p>

                    {{-- Donut Chart via SVG --}}
                    @php
                        $total    = max(1, $reportPending + $reportApproved + $reportRejected);
                        $pctP     = round(($reportPending  / $total) * 100);
                        $pctA     = round(($reportApproved / $total) * 100);
                        $pctR     = 100 - $pctP - $pctA;

                        // Donut segments (circumference = 2π×36 ≈ 226)
                        $c = 226;
                        $approvedDash  = ($reportApproved / $total) * $c;
                        $pendingDash   = ($reportPending  / $total) * $c;
                        $rejectedDash  = ($reportRejected / $total) * $c;
                        $approvedOff   = 0;
                        $pendingOff    = -$approvedDash;
                        $rejectedOff   = -($approvedDash + $pendingDash);
                    @endphp

                    <div class="flex justify-center">
                        <div class="relative">
                            <svg viewBox="0 0 100 100" class="h-36 w-36 -rotate-90">
                                <circle cx="50" cy="50" r="36" fill="none" stroke="#f3f4f6" stroke-width="14"/>
                                {{-- Approved (green) --}}
                                <circle cx="50" cy="50" r="36" fill="none" stroke="#22c55e" stroke-width="14"
                                    stroke-dasharray="{{ $approvedDash }} {{ $c }}"
                                    stroke-dashoffset="{{ $approvedOff }}"
                                    stroke-linecap="butt"/>
                                {{-- Pending (yellow) --}}
                                <circle cx="50" cy="50" r="36" fill="none" stroke="#facc15" stroke-width="14"
                                    stroke-dasharray="{{ $pendingDash }} {{ $c }}"
                                    stroke-dashoffset="{{ $pendingOff }}"
                                    stroke-linecap="butt"/>
                                {{-- Rejected (red) --}}
                                <circle cx="50" cy="50" r="36" fill="none" stroke="#f87171" stroke-width="14"
                                    stroke-dasharray="{{ $rejectedDash }} {{ $c }}"
                                    stroke-dashoffset="{{ $rejectedOff }}"
                                    stroke-linecap="butt"/>
                            </svg>
                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                <span class="text-2xl font-extrabold text-gray-900">{{ $total }}</span>
                                <span class="text-xs text-gray-400">total</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 space-y-2">
                        @foreach([
                            ['label'=>'Approved','value'=>$reportApproved,'color'=>'bg-green-500'],
                            ['label'=>'Pending','value'=>$reportPending,'color'=>'bg-yellow-400'],
                            ['label'=>'Rejected','value'=>$reportRejected,'color'=>'bg-red-400'],
                        ] as $seg)
                        <div class="flex items-center justify-between text-sm">
                            <div class="flex items-center gap-2">
                                <span class="h-2.5 w-2.5 rounded-full {{ $seg['color'] }}"></span>
                                <span class="text-gray-600">{{ $seg['label'] }}</span>
                            </div>
                            <span class="font-semibold text-gray-900">{{ $seg['value'] }}</span>
                        </div>
                        @endforeach
                    </div>

                    {{-- Shortcuts --}}
                    <div class="mt-6 space-y-2">
                        <a href=""
                           class="flex w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/>
                            </svg>
                            Lihat Daily Report
                        </a>
                        <a href="{{ route('presensi.index') }}"
                           class="flex w-full items-center justify-center gap-2 rounded-xl bg-gray-100 px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-200">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Lihat Presensi
                        </a>
                    </div>
                </div>

            </div>

            {{-- ============================================================ --}}
            {{-- ROW 3 — Presensi Terbaru Hari Ini                            --}}
            {{-- ============================================================ --}}
            <div class="rounded-2xl bg-white p-6 shadow ring-1 ring-gray-100">
                <div class="mb-5 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Presensi Terbaru</h3>
                        <p class="text-xs text-gray-400">Check-in hari ini · realtime</p>
                    </div>
                    <a href="{{ route('presensi.index') }}" class="rounded-lg bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-600 hover:bg-indigo-100 transition">
                        Lihat Semua →
                    </a>
                </div>

                @if($presensiTerbaru->isEmpty())
                    <div class="flex flex-col items-center justify-center py-10 text-gray-300">
                        <svg class="h-12 w-12" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5"/>
                        </svg>
                        <p class="mt-2 text-sm font-medium">Belum ada presensi hari ini</p>
                    </div>
                @else
                <div class="overflow-x-auto">
                    <table class="dash-table w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-100 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">
                                <th class="pb-3 pr-4">Karyawan</th>
                                <th class="pb-3 pr-4">Branch</th>
                                <th class="pb-3 pr-4 text-center">Jam</th>
                                <th class="pb-3 pr-4 text-center">Status</th>
                                <th class="pb-3 pr-4">Wilayah</th>
                                <th class="pb-3 text-right">Jarak</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($presensiTerbaru as $p)
                            @php
                                $pot = $p->hitungPotonganTerlambat();
                                $isTerlambat = $pot['menit_terlambat'] > 0;
                                $ket = strtolower($p->keterangan ?? '');
                                $isIzin = str_contains($ket,'izin');
                                $isSakit= str_contains($ket,'sakit');
                            @endphp
                            <tr>
                                <td class="py-3 pr-4">
                                    <div class="flex items-center gap-2.5">
                                        @if($p->photo)
                                        <img src="{{ asset('storage/'.$p->photo) }}" class="h-8 w-8 rounded-full object-cover ring-2 ring-gray-100" alt="">
                                        @else
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100 text-xs font-bold text-indigo-600">
                                            {{ strtoupper(substr($p->user->name ?? '-', 0, 2)) }}
                                        </div>
                                        @endif
                                        <div>
                                            <p class="font-semibold text-gray-800 leading-tight">{{ $p->user->name ?? '-' }}</p>
                                            <p class="text-xs text-gray-400">{{ $p->user->jabatan ?? '' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 pr-4 text-xs text-gray-600">{{ $p->branch->name ?? '-' }}</td>
                                <td class="py-3 pr-4 text-center">
                                    <span class="font-mono text-gray-800">{{ \Carbon\Carbon::parse($p->jam)->format('H:i') }}</span>
                                    @if($isTerlambat)
                                        <span class="ml-1 text-xs text-yellow-500">+{{ $pot['menit_terlambat'] }}m</span>
                                    @endif
                                </td>
                                <td class="py-3 pr-4 text-center">
                                    @if($isSakit)
                                        <span class="badge-izin rounded-full px-2.5 py-0.5 text-xs font-semibold">Sakit</span>
                                    @elseif($isIzin)
                                        <span class="badge-izin rounded-full px-2.5 py-0.5 text-xs font-semibold">Izin</span>
                                    @elseif($isTerlambat)
                                        <span class="badge-terlambat rounded-full px-2.5 py-0.5 text-xs font-semibold">Terlambat</span>
                                    @else
                                        <span class="badge-hadir rounded-full px-2.5 py-0.5 text-xs font-semibold">Hadir</span>
                                    @endif
                                </td>
                                <td class="py-3 pr-4 max-w-xs">
                                    <p class="truncate text-xs text-gray-500">{{ $p->wilayah ?? '-' }}</p>
                                </td>
                                <td class="py-3 text-right text-xs font-medium text-gray-600">{{ $p->jarak_formatted }}</td>
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