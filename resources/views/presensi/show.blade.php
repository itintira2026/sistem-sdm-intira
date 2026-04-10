<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">Detail Presensi</h2>
                <p class="text-sm text-gray-500">
                    {{ $user->name }} — {{ \Carbon\Carbon::parse($bulan . '-01')->translatedFormat('F Y') }}
                </p>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('modalExportUser').classList.remove('hidden')"
                    class="flex items-center gap-2 px-4 py-2 text-white transition bg-green-600 rounded-lg hover:bg-green-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                    </svg>
                    <span class="hidden sm:inline">Export Excel</span>
                </button>
                <a href="{{ route('presensi.index') }}"
                    class="flex items-center gap-2 px-4 py-2 text-white transition bg-gray-500 rounded-lg hover:bg-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span class="hidden sm:inline">Kembali</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
            <div class="p-4 text-green-700 bg-green-100 rounded-lg">{{ session('success') }}</div>
            @endif

            {{-- Info Card --}}
            <div class="p-5 bg-white rounded-lg shadow-sm">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-medium">Karyawan</p>
                        <p class="mt-1 font-semibold text-gray-800">{{ $user->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-medium">Cabang</p>
                        @if ($branch)
                        <p class="mt-1 font-semibold text-gray-800">{{ $branch->name }}</p>
                        <p class="text-xs text-gray-400">{{ $branch->address ?? '-' }} · {{ $branch->timezone ?? '-' }}</p>
                        @else
                        <p class="mt-1 text-gray-400">Tidak ada cabang</p>
                        @endif
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-medium mb-1">Filter Bulan</p>
                        <form method="GET" class="flex gap-2">
                            <input type="month" name="bulan" value="{{ $bulan }}"
                                class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
                            <button type="submit"
                                class="px-4 py-2 text-sm text-white bg-teal-600 rounded-lg hover:bg-teal-700">
                                Tampilkan
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Summary Cards --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-green-500">
                    <p class="text-xs text-gray-500 uppercase font-medium">Hadir</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ $summary['total_hadir'] }}</p>
                    <p class="text-xs text-gray-400 mt-1">hari</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-yellow-500">
                    <p class="text-xs text-gray-500 uppercase font-medium">Terlambat</p>
                    <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $summary['total_terlambat'] }}</p>
                    <p class="text-xs text-gray-400 mt-1">hari</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-blue-500">
                    <p class="text-xs text-gray-500 uppercase font-medium">Izin / Sakit</p>
                    <p class="text-2xl font-bold text-blue-600 mt-1">{{ $summary['total_izin'] }}</p>
                    <p class="text-xs text-gray-400 mt-1">hari</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-red-500">
                    <p class="text-xs text-gray-500 uppercase font-medium">Alpha</p>
                    <p class="text-2xl font-bold text-red-600 mt-1">{{ $summary['total_alpha'] }}</p>
                    <p class="text-xs text-gray-400 mt-1">hari</p>
                </div>
            </div>

            {{-- Accordion per Hari --}}
            @forelse($grouped as $tanggal => $entries)
            @php
                $checkIn      = $entries->where('status', 'CHECK_IN')->first();
                $checkOut     = $entries->where('status', 'CHECK_OUT')->first();
                $istirahatIn  = $entries->where('status', 'ISTIRAHAT_IN')->first();
                $istirahatOut = $entries->where('status', 'ISTIRAHAT_OUT')->first();
                $outfitPhoto  = $checkIn?->photo_outfit;

                $borderColor = 'border-green-400';
                $badgeClass  = 'bg-green-100 text-green-700';
                $badgeLabel  = 'Hadir';

                if (!$checkIn) {
                    $borderColor = 'border-red-400';
                    $badgeClass  = 'bg-red-100 text-red-700';
                    $badgeLabel  = 'Tidak Ada Data';
                } elseif ($checkIn->keterangan && (
                    str_contains(strtolower($checkIn->keterangan), 'izin') ||
                    str_contains(strtolower($checkIn->keterangan), 'sakit')
                )) {
                    $borderColor = 'border-blue-400';
                    $badgeClass  = 'bg-blue-100 text-blue-700';
                    $badgeLabel  = str_contains(strtolower($checkIn->keterangan), 'sakit') ? 'Sakit' : 'Izin';
                } elseif ($checkIn) {
                    $jamCI = \Carbon\Carbon::parse($checkIn->jam);
                    $hour  = $jamCI->hour;
                    $late  = false;
                    if ($hour >= 8 && $hour < 12) {
                        $late = $jamCI->gt(\Carbon\Carbon::parse($tanggal . ' 08:00:00'));
                    } elseif ($hour > 13 && $hour <= 21) {
                        $late = $jamCI->gt(\Carbon\Carbon::parse($tanggal . ' 13:00:00'));
                    }
                    if ($late) {
                        $borderColor = 'border-yellow-400';
                        $badgeClass  = 'bg-yellow-100 text-yellow-700';
                        $badgeLabel  = 'Terlambat';
                    }
                }
            @endphp

            <div class="bg-white rounded-lg shadow-sm border-l-4 {{ $borderColor }} overflow-hidden">

                {{-- Accordion Header --}}
                <button type="button" onclick="toggleAccordion('acc-{{ $loop->index }}')"
                    class="w-full flex items-center justify-between px-5 py-3 bg-gray-50 border-b border-gray-100 hover:bg-gray-100 transition text-left">
                    <div class="flex items-center gap-3">
                        <div class="text-center min-w-[32px]">
                            <p class="text-xs text-gray-400 font-medium uppercase leading-none">
                                {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('D') }}
                            </p>
                            <p class="text-xl font-bold text-gray-800 leading-tight">
                                {{ \Carbon\Carbon::parse($tanggal)->format('d') }}
                            </p>
                            <p class="text-xs text-gray-400 leading-none">
                                {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('M Y') }}
                            </p>
                        </div>
                        <div class="w-px h-10 bg-gray-200"></div>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $badgeClass }}">
                            {{ $badgeLabel }}
                        </span>
                        @if($checkIn && $checkIn->branch)
                        <span class="hidden sm:inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-600">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z" />
                            </svg>
                            {{ $checkIn->branch->name }}
                        </span>
                        @endif
                        @if($checkIn && $checkOut)
                        @php
                            $durasi = \Carbon\Carbon::parse($checkIn->jam)->diffInMinutes(\Carbon\Carbon::parse($checkOut->jam));
                            $jam    = intdiv($durasi, 60);
                            $menit  = $durasi % 60;
                        @endphp
                        <span class="text-xs text-gray-500 font-medium">⏱ {{ $jam }}j {{ $menit }}m</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0 ml-2">
                        @if($checkIn)
                        <span class="text-xs text-green-600 font-semibold hidden sm:inline">
                            {{ \Carbon\Carbon::parse($checkIn->jam)->format('H:i') }}
                        </span>
                        @endif
                        <svg class="w-5 h-5 text-gray-400 transform transition-transform duration-200"
                            id="icon-acc-{{ $loop->index }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                </button>

                {{-- Accordion Body --}}
                <div id="acc-{{ $loop->index }}"
                    style="max-height:0; opacity:0; overflow:hidden; transition: max-height 0.35s ease, opacity 0.25s ease;">
                    <div class="p-5 space-y-5">

                        {{-- Timeline + Foto --}}
                        <div class="flex flex-col gap-5">

                            {{-- Timeline --}}
                            <div class="relative">
                                <div class="absolute left-3.5 top-0 bottom-0 w-px bg-gray-200"></div>
                                <div class="space-y-4">
                                    @php
                                    $statusList = [
                                        ['label' => 'Check In',      'item' => $checkIn,      'dot' => 'bg-green-500',  'time_class' => 'text-green-700'],
                                        ['label' => 'Istirahat In',  'item' => $istirahatIn,  'dot' => 'bg-yellow-500', 'time_class' => 'text-yellow-700'],
                                        ['label' => 'Istirahat Out', 'item' => $istirahatOut, 'dot' => 'bg-blue-500',   'time_class' => 'text-blue-700'],
                                        ['label' => 'Check Out',     'item' => $checkOut,     'dot' => 'bg-red-500',    'time_class' => 'text-red-700'],
                                    ];
                                    @endphp

                                    @foreach($statusList as $s)
                                    <div class="flex items-start gap-4 relative">
                                        <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 z-10 {{ $s['item'] ? $s['dot'] : 'bg-gray-200' }}">
                                            @if($s['item'])
                                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                            </svg>
                                            @else
                                            <div class="w-2 h-2 rounded-full bg-gray-400"></div>
                                            @endif
                                        </div>
                                        <div class="flex-1 pb-1">
                                            <div class="flex items-center justify-between">
                                                <p class="text-sm font-medium {{ $s['item'] ? 'text-gray-800' : 'text-gray-400' }}">
                                                    {{ $s['label'] }}
                                                </p>
                                                @if($s['item'])
                                                <div class="flex items-center gap-2">
                                                    <span class="text-sm font-semibold {{ $s['time_class'] }}">
                                                        {{ \Carbon\Carbon::parse($s['item']->jam)->format('H:i') }}
                                                    </span>
                                                    {{-- Tombol aksi per status --}}
                                                    <div class="relative inline-block">
                                                        <button type="button"
                                                            onclick="toggleDropdown({{ $s['item']->id }})"
                                                            class="text-gray-300 hover:text-gray-500 transition">
                                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                                            </svg>
                                                        </button>
                                                        <div id="dropdown-{{ $s['item']->id }}"
                                                            class="fixed z-50 hidden w-44 bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5">
                                                            <div class="py-1">
                                                                @if($s['item']->photo)
                                                                <button onclick="openPhoto('{{ Storage::url($s['item']->photo) }}', '{{ $s['label'] }}', '{{ \Carbon\Carbon::parse($s['item']->jam)->format('H:i') }}')"
                                                                    class="flex items-center w-full gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                                    </svg>
                                                                    Lihat Foto
                                                                </button>
                                                                @endif
                                                                <button onclick="openEditModal('{{ $s['item']->status }}', '{{ \Carbon\Carbon::parse($s['item']->jam)->format('H:i:s') }}', '{{ $s['item']->wilayah }}', '{{ $s['item']->keterangan }}', '{{ $tanggal }}')"
                                                                    class="flex items-center w-full gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                                    </svg>
                                                                    Edit
                                                                </button>
                                                                <form action="{{ route('presensi.destroy', $s['item']->id) }}" method="POST">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                        onclick="return confirm('Yakin hapus {{ $s['label'] }}?')"
                                                                        class="flex items-center w-full gap-2 px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                        </svg>
                                                                        Hapus
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @else
                                                <button onclick="openEditModal('{{ $s['label'] === 'Check In' ? 'CHECK_IN' : ($s['label'] === 'Check Out' ? 'CHECK_OUT' : ($s['label'] === 'Istirahat In' ? 'ISTIRAHAT_IN' : 'ISTIRAHAT_OUT')) }}', '', '', '', '{{ $tanggal }}')"
                                                    class="text-xs text-teal-600 hover:text-teal-800 font-medium">
                                                    + Tambah
                                                </button>
                                                @endif
                                            </div>
                                            @if($s['item'])
                                            <div class="flex flex-wrap gap-x-3 mt-0.5 text-xs text-gray-400">
                                                @if($s['item']->jarak)
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    </svg>
                                                    {{ $s['item']->jarak_formatted }} dari kantor
                                                </span>
                                                @endif
                                                @if($s['item']->keterangan)
                                                <span class="italic">{{ $s['item']->keterangan }}</span>
                                                @endif
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Foto Absensi + Outfit --}}
                            @php
                                $photosToShow = $entries->filter(fn($e) => !empty($e->photo))->values();
                            @endphp
                            @if($photosToShow->count() > 0 || $outfitPhoto)
                            <div class="border-t border-gray-100 pt-4">
                                <p class="text-xs text-gray-400 uppercase font-medium mb-3">Foto Absensi</p>
                                <div class="flex flex-row flex-wrap gap-3">
                                    @if($outfitPhoto)
                                    <div class="group relative cursor-pointer"
                                        onclick="openPhoto('{{ Storage::url($outfitPhoto) }}', 'Foto Outfit', '{{ $checkIn ? \Carbon\Carbon::parse($checkIn->jam)->format('H:i') : '-' }}')">
                                        <img src="{{ Storage::url($outfitPhoto) }}" alt="Outfit"
                                            class="w-20 h-20 object-cover rounded-lg border-2 border-purple-200 group-hover:border-purple-400 transition-all">
                                        <p class="text-center text-xs text-purple-400 mt-1">Outfit</p>
                                    </div>
                                    @endif
                                    @foreach($photosToShow as $entry)
                                    <div class="group relative cursor-pointer"
                                        onclick="openPhoto('{{ Storage::url($entry->photo) }}', '{{ str_replace('_', ' ', $entry->status) }}', '{{ \Carbon\Carbon::parse($entry->jam)->format('H:i') }}')">
                                        <img src="{{ Storage::url($entry->photo) }}" alt="{{ $entry->status }}"
                                            class="w-20 h-20 object-cover rounded-lg border-2 border-gray-200 group-hover:border-teal-400 transition-all">
                                        <p class="text-center text-xs text-gray-400 mt-1">{{ str_replace('_', ' ', $entry->status) }}</p>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <svg class="w-16 h-16 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-gray-400 font-medium">Tidak ada data presensi</p>
                <p class="text-sm text-gray-300 mt-1">
                    Belum ada presensi di bulan {{ \Carbon\Carbon::parse($bulan . '-01')->translatedFormat('F Y') }}
                </p>
            </div>
            @endforelse

        </div>
    </div>

    {{-- MODAL EXPORT --}}
    <div id="modalExportUser"
        class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black/50 backdrop-blur-sm">
        <div class="w-full max-w-md p-6 mx-4 bg-white shadow-2xl rounded-2xl">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Export Presensi</h3>
                    <p class="text-sm text-gray-500">{{ $user->name }}</p>
                </div>
                <button type="button" onclick="document.getElementById('modalExportUser').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form method="POST" action="{{ route('presensi.export.user', $user->id) }}">
                @csrf
                <div class="grid grid-cols-3 gap-2 mb-4">
                    <button type="button" onclick="setExportRange('week')"
                        class="text-xs py-1.5 px-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-600">Minggu Ini</button>
                    <button type="button" onclick="setExportRange('month')"
                        class="text-xs py-1.5 px-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-600">Bulan Ini</button>
                    <button type="button" onclick="setExportRange('lastmonth')"
                        class="text-xs py-1.5 px-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-600">Bulan Lalu</button>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Tanggal Mulai</label>
                        <input type="date" name="start_date" id="userExportStart"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                            value="{{ now()->startOfMonth()->toDateString() }}" max="{{ now()->toDateString() }}" required>
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Tanggal Akhir</label>
                        <input type="date" name="end_date" id="userExportEnd"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                            value="{{ now()->toDateString() }}" max="{{ now()->toDateString() }}" required>
                    </div>
                </div>
                <button type="submit"
                    class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2.5 rounded-lg text-sm flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Download Excel
                </button>
            </form>
        </div>
    </div>

    {{-- MODAL EDIT --}}
    <div id="editModal" class="fixed inset-0 z-50 flex items-center justify-center hidden p-4 bg-black bg-opacity-50"
        onclick="if(event.target === this) closeEditModal()">
        <div class="w-full max-w-md bg-white rounded-lg shadow-xl" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Edit Presensi</h3>
                <button onclick="closeEditModal()" class="p-1 text-gray-400 rounded-lg hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form method="POST" action="{{ route('presensi.update', $user->id) }}">
                @csrf
                @method('PUT')
                <div class="p-6 space-y-4">
                    <input type="hidden" name="tanggal" id="editTanggal">
                    <input type="hidden" name="status" id="editStatus">
                    <div class="p-3 rounded-lg bg-gray-50">
                        <p class="text-sm text-gray-600">Status:</p>
                        <p id="editStatusDisplay" class="font-semibold text-gray-800"></p>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Jam <span class="text-red-500">*</span></label>
                        <input type="time" name="jam" id="editJam" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Keterangan</label>
                        <textarea name="keterangan" id="editKeterangan" rows="3"
                            placeholder="Tambahkan keterangan (opsional)"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500"></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-3 p-6 border-t border-gray-200">
                    <button type="button" onclick="closeEditModal()"
                        class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-4 py-2 text-white bg-teal-600 rounded-lg hover:bg-teal-700">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL FOTO --}}
    <div id="photoModal" class="fixed inset-0 z-50 items-center justify-center hidden p-4 bg-black bg-opacity-80"
        onclick="if(event.target===this) closePhoto()">
        <div class="relative w-full max-w-sm overflow-hidden bg-white shadow-2xl rounded-xl"
            onclick="event.stopPropagation()">
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 bg-gray-50">
                <div>
                    <p class="text-sm font-semibold text-gray-800" id="photoModalStatus"></p>
                    <p class="text-xs text-gray-500" id="photoModalTime"></p>
                </div>
                <button onclick="closePhoto()" class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <img id="photoModalImg" src="" alt="Foto" class="object-cover w-full">
        </div>
    </div>

    <script>
        function toggleAccordion(id) {
            const body  = document.getElementById(id);
            const index = id.replace('acc-', '');
            const icon  = document.getElementById('icon-acc-' + index);
            const isOpen = body.style.maxHeight && body.style.maxHeight !== '0px';
            if (isOpen) {
                body.style.maxHeight = '0px';
                body.style.opacity   = '0';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />';
            } else {
                body.style.maxHeight = body.scrollHeight + 'px';
                body.style.opacity   = '1';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />';
            }
        }

        function openEditModal(status, jam, wilayah, keterangan, tanggal) {
            document.getElementById('editStatus').value        = status;
            document.getElementById('editStatusDisplay').textContent = status.replace(/_/g, ' ');
            document.getElementById('editJam').value          = jam ? jam.substring(0, 5) : '';
            document.getElementById('editKeterangan').value   = keterangan || '';
            document.getElementById('editTanggal').value      = tanggal || '';
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        function openPhoto(src, status, time) {
            document.getElementById('photoModalImg').src = src;
            document.getElementById('photoModalStatus').textContent = status;
            document.getElementById('photoModalTime').textContent   = 'Jam ' + time;
            const modal = document.getElementById('photoModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closePhoto() {
            const modal = document.getElementById('photoModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.getElementById('photoModalImg').src = '';
        }

        function toggleDropdown(id) {
            event.stopPropagation();
            const button   = event.currentTarget;
            const dropdown = document.getElementById(`dropdown-${id}`);
            document.querySelectorAll('[id^="dropdown-"]').forEach(el => {
                if (el !== dropdown) el.classList.add('hidden');
            });
            dropdown.classList.toggle('hidden');
            if (!dropdown.classList.contains('hidden')) {
                const rect = button.getBoundingClientRect();
                dropdown.style.top  = `${rect.bottom + 8}px`;
                dropdown.style.left = `${rect.right - dropdown.offsetWidth}px`;
            }
        }

        function setExportRange(type) {
            const today = new Date();
            const fmt   = d => d.toISOString().split('T')[0];
            let start, end;
            if (type === 'week') {
                const day  = today.getDay();
                const diff = (day === 0) ? -6 : 1 - day;
                start = new Date(today); start.setDate(today.getDate() + diff);
                end   = today;
            } else if (type === 'month') {
                start = new Date(today.getFullYear(), today.getMonth(), 1);
                end   = today;
            } else if (type === 'lastmonth') {
                start = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                end   = new Date(today.getFullYear(), today.getMonth(), 0);
            }
            document.getElementById('userExportStart').value = fmt(start);
            document.getElementById('userExportEnd').value   = fmt(end);
        }

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                closeEditModal();
                closePhoto();
                document.getElementById('modalExportUser').classList.add('hidden');
            }
        });

        document.addEventListener('click', () => {
            document.querySelectorAll('[id^="dropdown-"]').forEach(el => el.classList.add('hidden'));
        });
    </script>

</x-app-layout>