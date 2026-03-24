{{-- resources/views/absensi/riwayat.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">Riwayat Absensi</h2>
                <p class="text-sm text-gray-500 mt-1">
                    {{ $user->name }} — {{ \Carbon\Carbon::parse($bulan . '-01')->translatedFormat('F Y') }}
                </p>
            </div>
            <a href="{{ route('absensi.index') }}"
                class="flex items-center gap-2 px-4 py-2 text-white bg-gray-500 rounded-lg hover:bg-gray-600 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span class="hidden sm:inline">Absensi</span>
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Filter Bulan --}}
            <div class="bg-white rounded-lg shadow-sm p-4">
                <form method="GET" class="flex flex-wrap items-end gap-3">
                    <div class="flex-1 min-w-[160px]">
                        <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Bulan</label>
                        <input type="month" name="bulan" value="{{ $bulan }}"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                    </div>
                    <button type="submit"
                        class="px-5 py-2 text-sm text-white bg-teal-600 rounded-lg hover:bg-teal-700 transition font-medium">
                        Tampilkan
                    </button>
                </form>
            </div>

            {{-- Summary Cards --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @php
                    $totalHadir   = $summary['total_hadir']   ?? 0;
                    $totalTerlambat = $summary['total_terlambat'] ?? 0;
                    $totalIzin    = $summary['total_izin']    ?? 0;
                    $totalAlpha   = $summary['total_alpha']   ?? 0;
                @endphp

                <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-green-500">
                    <p class="text-xs text-gray-500 uppercase font-medium">Hadir</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ $totalHadir }}</p>
                    <p class="text-xs text-gray-400 mt-1">hari</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-yellow-500">
                    <p class="text-xs text-gray-500 uppercase font-medium">Terlambat</p>
                    <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $totalTerlambat }}</p>
                    <p class="text-xs text-gray-400 mt-1">hari</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-blue-500">
                    <p class="text-xs text-gray-500 uppercase font-medium">Izin / Sakit</p>
                    <p class="text-2xl font-bold text-blue-600 mt-1">{{ $totalIzin }}</p>
                    <p class="text-xs text-gray-400 mt-1">hari</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-red-500">
                    <p class="text-xs text-gray-500 uppercase font-medium">Alpha</p>
                    <p class="text-2xl font-bold text-red-600 mt-1">{{ $totalAlpha }}</p>
                    <p class="text-xs text-gray-400 mt-1">hari</p>
                </div>
            </div>

            {{-- Riwayat per Hari --}}
        {{-- Riwayat per Hari --}}
@forelse($grouped as $tanggal => $entries)
@php
    $checkIn      = $entries->where('status', 'CHECK_IN')->first();
    $checkOut     = $entries->where('status', 'CHECK_OUT')->first();
    $istirahatIn  = $entries->where('status', 'ISTIRAHAT_IN')->first();
    $istirahatOut = $entries->where('status', 'ISTIRAHAT_OUT')->first();

    $borderColor = 'border-green-400';
    $badgeClass  = 'bg-green-100 text-green-700';
    $badgeLabel  = 'Hadir';

    if (!$checkIn) {
        $borderColor = 'border-red-400';
        $badgeClass  = 'bg-red-100 text-red-700';
        $badgeLabel  = 'Tidak Ada Data';
    } elseif ($checkIn && $checkIn->keterangan && (
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

    $photosToShow = $entries->filter(fn($e) => !empty($e->photo))->values();
@endphp

<div class="bg-white rounded-lg shadow-sm border-l-4 {{ $borderColor }} overflow-hidden">

    {{-- Header — selalu tampil, klik untuk toggle --}}
    <button type="button"
        onclick="toggleAccordion('acc-{{ $loop->index }}')"
        class="w-full flex items-center justify-between px-5 py-3 bg-gray-50 border-b border-gray-100 hover:bg-gray-100 transition text-left">

        <div class="flex items-center gap-3">
            {{-- Tanggal --}}
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

            {{-- Badge status --}}
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $badgeClass }}">
                {{ $badgeLabel }}
            </span>

            {{-- Cabang --}}
            @if($checkIn && $checkIn->branch)
            <span class="hidden sm:inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-600">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z" />
                </svg>
                {{ $checkIn->branch->name }}
            </span>
            @endif

            {{-- Durasi --}}
            @if($checkIn && $checkOut)
            @php
                $durasi = \Carbon\Carbon::parse($checkIn->jam)->diffInMinutes(\Carbon\Carbon::parse($checkOut->jam));
                $jam    = intdiv($durasi, 60);
                $menit  = $durasi % 60;
            @endphp
            <span class="text-xs text-gray-500 font-medium">⏱ {{ $jam }}j {{ $menit }}m</span>
            @endif
        </div>

        {{-- Ikon + / chevron --}}
        <div class="flex items-center gap-2 flex-shrink-0 ml-2">
            {{-- Jam check-in ringkas --}}
            @if($checkIn)
            <span class="text-xs text-green-600 font-semibold hidden sm:inline">
                {{ \Carbon\Carbon::parse($checkIn->jam)->format('H:i') }}
            </span>
            @endif
            <svg class="w-5 h-5 text-gray-400 transform transition-transform duration-200 accordion-icon"
                id="icon-acc-{{ $loop->index }}"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
        </div>
    </button>

    {{-- Body — tersembunyi by default --}}
    <div id="acc-{{ $loop->index }}" class="hidden">
        <div class="p-5">
            <div class="flex flex-col gap-5">

                {{-- Timeline Absensi --}}
                <div class="flex-1">
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
                                <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 z-10
                                    {{ $s['item'] ? $s['dot'] : 'bg-gray-200' }}">
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
                                        <span class="text-sm font-semibold {{ $s['time_class'] }}">
                                            {{ \Carbon\Carbon::parse($s['item']->jam)->format('H:i') }}
                                        </span>
                                        @else
                                        <span class="text-xs text-gray-400">—</span>
                                        @endif
                                    </div>
                                    @if($s['item'])
                                    <div class="flex flex-wrap gap-x-3 mt-0.5 text-xs text-gray-400">
                                        @if($s['item']->jarak)
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            </svg>
                                            {{ $s['item']->jarak_formatted }} dari kantor
                                        </span>
                                        @endif
                                        @if($s['item']->keterangan)
                                        <span class="italic text-gray-400">{{ $s['item']->keterangan }}</span>
                                        @endif
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Foto-foto di bawah (bukan samping) --}}
                @if($photosToShow->count() > 0)
                <div class="border-t border-gray-100 pt-4">
                    <p class="text-xs text-gray-400 uppercase font-medium mb-3">Foto Absensi</p>
                    <div class="flex flex-row flex-wrap gap-3">
                        @foreach($photosToShow as $entry)
                        <div class="group relative">
                            <div class="relative overflow-hidden rounded-lg cursor-pointer"
                                onclick="openPhoto('{{ Storage::url($entry->photo) }}', '{{ str_replace('_', ' ', $entry->status) }}', '{{ \Carbon\Carbon::parse($entry->jam)->format('H:i') }}')">
                                <img src="{{ Storage::url($entry->photo) }}"
                                    alt="{{ $entry->status }}"
                                    class="w-20 h-20 object-cover rounded-lg border-2 border-gray-200 group-hover:border-teal-400 transition-all"
                                    onerror="this.closest('.relative').innerHTML='<div class=\'w-20 h-20 bg-gray-100 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center\'><svg class=\'w-6 h-6 text-gray-300\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\'/></svg></div>'">
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white opacity-0 group-hover:opacity-100 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                                    </svg>
                                </div>
                            </div>
                            <p class="text-center text-xs text-gray-400 mt-1 leading-tight">
                                {{ str_replace('_', ' ', $entry->status) }}
                            </p>
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
    <p class="text-gray-400 font-medium">Tidak ada data absensi</p>
    <p class="text-sm text-gray-300 mt-1">
        Belum ada absensi di bulan {{ \Carbon\Carbon::parse($bulan . '-01')->translatedFormat('F Y') }}
    </p>
</div>
@endforelse

        </div>
    </div>

    {{-- Modal Foto --}}
   <div id="photoModal"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-80 p-4"
    onclick="if(event.target===this) closePhoto()">
    <div class="relative max-w-sm w-full bg-white rounded-xl overflow-hidden shadow-2xl" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between px-4 py-3 bg-gray-50 border-b border-gray-200">
            <div>
                <p class="font-semibold text-gray-800 text-sm" id="photoModalStatus"></p>
                <p class="text-xs text-gray-500" id="photoModalTime"></p>
            </div>
            <div class="flex items-center gap-1">
                {{-- Tombol Download --}}
                <button onclick="downloadPhoto()" title="Download foto"
                    class="p-1.5 rounded-lg text-gray-400 hover:bg-teal-50 hover:text-teal-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                </button>
                {{-- Tombol Close --}}
                <button onclick="closePhoto()" title="Tutup"
                    class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-200 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
        <img id="photoModalImg" src="" alt="Foto Absensi" class="w-full object-cover">
    </div>
</div>

    <script>
        function downloadPhoto() {
    const img      = document.getElementById('photoModalImg');
    const status   = document.getElementById('photoModalStatus').textContent;
    const time     = document.getElementById('photoModalTime').textContent.replace('Jam ', '').replace(':', '-');
    const filename = `absensi_${status}_${time}.jpg`.replace(/\s+/g, '_').toLowerCase();

    fetch(img.src)
        .then(res => res.blob())
        .then(blob => {
            const url  = URL.createObjectURL(blob);
            const a    = document.createElement('a');
            a.href     = url;
            a.download = filename;
            a.click();
            URL.revokeObjectURL(url);
        })
        .catch(() => {
            // Fallback jika fetch gagal (misal cross-origin)
            const a    = document.createElement('a');
            a.href     = img.src;
            a.download = filename;
            a.target   = '_blank';
            a.click();
        });
}
function toggleAccordion(id) {
        const body = document.getElementById(id);
        const index = id.replace('acc-', '');
        const icon  = document.getElementById('icon-acc-' + index);
        const isOpen = !body.classList.contains('hidden');

        if (isOpen) {
            body.classList.add('hidden');
            // Ganti ke ikon +
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />';
        } else {
            body.classList.remove('hidden');
            // Ganti ke ikon −
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />';
        }
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

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closePhoto();
    });

        function openPhoto(src, status, time) {
            document.getElementById('photoModalImg').src    = src;
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

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') closePhoto();
        });
    </script>
</x-app-layout>