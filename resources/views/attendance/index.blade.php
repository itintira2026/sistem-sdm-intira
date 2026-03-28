{{-- resources/views/absensi/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Absensi Hari Ini
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    {{ now()->translatedFormat('l, d F Y') }}
                </p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('absensi.riwayat') }}"
                    class="flex items-center gap-2 px-4 py-2 text-violet-950 transition bg-violet-50 rounded-lg hover:bg-violet-100 text-sm font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="hidden sm:inline">Riwayat</span>
                </a>

                {{-- Branch Switcher (untuk user dengan multiple branch) --}}
                @if($userBranches->count() > 1)
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                        class="flex items-center gap-2 bg-white border border-gray-300 rounded-lg px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z" />
                        </svg>
                        <span>{{ $activeBranch->name ?? 'Pilih Cabang' }}</span>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" @click.away="open = false"
                        class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                        <div class="p-2">
                            @foreach($userBranches as $branch)
                            <a href="{{ route('branch.switch', $branch->id) }}"
                                class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-50
                                {{ $branch->id === ($activeBranch->id ?? null) ? 'bg-blue-50 text-blue-600' : 'text-gray-700' }}">
                                <div class="flex-1">
                                    <p class="text-sm font-medium">{{ $branch->name }}</p>
                                    <p class="text-xs text-gray-500">
                                        @if($branch->pivot->is_manager ?? false)
                                        <span class="text-yellow-600">Manager</span>
                                        @else
                                        Staff
                                        @endif
                                    </p>
                                </div>
                                @if($branch->id === ($activeBranch->id ?? null))
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                @endif
                            </a>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            {{-- Alert Messages --}}
            @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg" role="alert">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
            @endif

            @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg" role="alert">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{!! session('error') !!}</span>
                </div>
            </div>
            @endif

            {{-- Info Card --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

                        {{-- Nama --}}
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <p class="text-xs text-gray-500 uppercase font-medium mb-1">Nama</p>
                            <div class="flex items-center gap-2">
                                @if($user->profile_photo)
                                <img src="{{ Storage::url($user->profile_photo) }}" alt=""
                                    class="w-8 h-8 rounded-full object-cover">
                                @else
                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                                    <span class="text-sm font-medium text-blue-600">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </span>
                                </div>
                                @endif
                                <p class="text-sm font-semibold text-gray-800">{{ $user->name }}</p>
                            </div>
                        </div>

                        {{-- Cabang Aktif --}}
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <p class="text-xs text-gray-500 uppercase font-medium mb-1">Cabang Aktif</p>
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z" />
                                </svg>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">
                                        {{ $activeBranch->name ?? '-' }}
                                    </p>
                                    @if($userBranches->count() > 1)
                                    <p class="text-xs text-gray-500">{{ $userBranches->count() }} cabang terdaftar</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Lokasi Kantor --}}
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <p class="text-xs text-gray-500 uppercase font-medium mb-1">Lokasi Kantor</p>
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                @if($activeBranch)
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">{{ $activeBranch->name }}</p>
                                    <p class="text-xs text-gray-500">
                                        Lat: {{ substr($activeBranch->latitude, 0, 8) }}... |
                                        Long: {{ substr($activeBranch->longitude, 0, 8) }}...
                                    </p>
                                </div>
                                @else
                                <p class="text-sm text-gray-500">Tidak ada data</p>
                                @endif
                            </div>
                        </div>

                        {{-- Tanggal --}}
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <p class="text-xs text-gray-500 uppercase font-medium mb-1">Tanggal</p>
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">{{ now()->format('d M Y') }}</p>
                                    <p class="text-xs text-gray-500">{{ now()->format('H:i') }} WIB</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Info multiple branch --}}
                    @if($userBranches->count() > 1)
                    <div class="mt-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-blue-800">
                                    Anda terdaftar di {{ $userBranches->count() }} cabang
                                </p>
                                <p class="text-xs text-blue-600 mt-1">
                                    Sistem akan otomatis memilih cabang terdekat dari lokasi Anda.
                                </p>
                                <div class="flex flex-wrap gap-2 mt-2">
                                    @foreach($userBranches as $branch)
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        {{ $branch->id === ($activeBranch->id ?? null) ? 'bg-blue-600 text-white' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $branch->name }}
                                        @if($branch->pivot->is_manager ?? false)
                                        (Mgr)
                                        @endif
                                    </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Riwayat Absensi Hari Ini --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Riwayat Absensi Hari Ini</h3>
                        <span class="text-sm text-gray-500">{{ $todayPresensis->count() }} kali absensi</span>
                    </div>

                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        @forelse($todayPresensis as $item)
                        @php
                        $colorMap = [
                        'CHECK_IN' => ['bg' => 'bg-green-100', 'text' => 'text-green-600', 'badge' => 'bg-green-100
                        text-green-800'],
                        'CHECK_OUT' => ['bg' => 'bg-red-100', 'text' => 'text-red-600', 'badge' => 'bg-red-100
                        text-red-800'],
                        'ISTIRAHAT_IN' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-600', 'badge' =>
                        'bg-yellow-100 text-yellow-800'],
                        'ISTIRAHAT_OUT' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-600', 'badge' => 'bg-blue-100
                        text-blue-800'],
                        ];
                        $color = $colorMap[$item->status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'badge'
                        => 'bg-gray-100 text-gray-800'];
                        @endphp
                        <div class="flex items-center justify-between px-4 py-3
                            {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $color['bg'] }}">
                                    <svg class="w-4 h-4 {{ $color['text'] }}" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        @if($item->status === 'CHECK_IN')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                        @elseif($item->status === 'CHECK_OUT')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        @elseif($item->status === 'ISTIRAHAT_IN')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        @endif
                                    </svg>
                                </div>

                                <div>
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $color['badge'] }}">
                                        {{ str_replace('_', ' ', $item->status) }}
                                    </span>
                                    @if($item->branch)
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $item->branch->name }}
                                        @if($item->jarak)
                                        <span class="ml-2">({{ $item->jarak_formatted }} dari kantor)</span>
                                        @endif
                                    </p>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">
                                    {{ \Carbon\Carbon::parse($item->jam)->format('H:i:s') }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($item->jam)->format('d M Y') }}
                                </p>
                            </div>
                        </div>
                        @empty
                        <div class="px-4 py-8 text-center">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-gray-400 text-sm">Belum ada absensi hari ini</p>
                            <p class="text-xs text-gray-300 mt-1">Silakan lakukan absensi menggunakan tombol di bawah
                            </p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Form Absensi --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @php
                    // $hasCheckIn = $todayPresensis->where('status', 'CHECK_IN')->isNotEmpty();
                    // $hasCheckOut = $todayPresensis->where('status', 'CHECK_OUT')->isNotEmpty();
                    // $istirahatIn = $todayPresensis->where('status', 'ISTIRAHAT_IN')->count();
                    // $istirahatOut = $todayPresensis->where('status', 'ISTIRAHAT_OUT')->count();
                    // $sedangIstirahat = $istirahatIn > $istirahatOut;

                    // // Disabled logic per tombol
                    // $disableCheckIn = $hasCheckIn;
                    // $disableCheckOut = !$hasCheckIn || $hasCheckOut;
                    // $disableIstirahatIn = !$hasCheckIn || $sedangIstirahat || $hasCheckOut;
                    // $disableIstirahatOut= $istirahatIn === 0 || !$sedangIstirahat;
                    $hasCheckIn = $todayPresensis->where('status', 'CHECK_IN')->isNotEmpty();
                    $hasCheckOut = $todayPresensis->where('status', 'CHECK_OUT')->isNotEmpty();
                    $istirahatOut = $todayPresensis->where('status', 'ISTIRAHAT_OUT')->count();
                    $istirahatIn = $todayPresensis->where('status', 'ISTIRAHAT_IN')->count();
                    $sedangIstirahat = $istirahatOut > $istirahatIn; // Sedang di luar (istirahat)

                    $disableCheckIn = $hasCheckIn;
                    $disableCheckOut = !$hasCheckIn || $hasCheckOut || $sedangIstirahat;
                    $disableIstirahatOut = !$hasCheckIn || $hasCheckOut || $sedangIstirahat; 
                    $disableIstirahatIn = !$hasCheckIn || $hasCheckOut || !$sedangIstirahat; 
                    @endphp

                    <form method="POST" action="{{ route('absensi.store') }}" id="attendanceForm"
                        enctype="multipart/form-data">
                        @csrf
                        {{-- Upload Foto Outfit --}}
                        {{-- Upload Foto Outfit --}}
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 16V4m0 0L3 8m4-4l4 4m6 4v8m0 0l-4-4m4 4l4-4" />
                                    </svg>
                                    Upload Foto Outfit
                                </span>
                            </label>

                            @if($disableCheckIn)
                            {{-- Sudah Check In: tampilkan pesan disabled --}}
                            <div class="border-2 border-dashed border-gray-200 rounded-lg p-6 bg-gray-50 text-center">
                                <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                </svg>
                                <p class="text-sm text-gray-400 font-medium">Upload foto outfit tidak tersedia</p>
                                <p class="text-xs text-gray-300 mt-1">Foto outfit hanya dapat diupload saat Check In</p>

                                {{-- Tampilkan foto outfit yang sudah diupload jika ada --}}
                                @php
                                $outfitPhoto = $todayPresensis->where('status', 'CHECK_IN')->first()?->photo_outfit;
                                @endphp
                                @if($outfitPhoto)
                                <div class="mt-3">
                                    <img src="{{ Storage::url($outfitPhoto) }}"
                                        class="mx-auto rounded-lg shadow-md max-h-32 object-cover opacity-60">
                                    <p class="text-xs text-gray-400 mt-1">Foto outfit hari ini</p>
                                </div>
                                @endif
                            </div>
                            <input type="hidden" name="photo_outfit" value="">

                            @else
                            {{-- Belum Check In: tampilkan upload --}}
                            <p class="text-xs text-gray-500 mb-3">
                                Upload foto outfit sebelum Check In. Hanya bisa diupload 1x per hari.
                            </p>

                            <div
                                class="border-2 border-dashed border-gray-300 rounded-lg p-6 bg-gray-50 hover:bg-gray-100 transition text-center relative">
                                <input type="file" name="photo_outfit" id="photo_outfit" accept="image/*"
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                    onchange="previewOutfit(event)">

                                <div id="uploadPlaceholder" class="flex flex-col items-center gap-2">
                                    <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M3 16l4-4a3 3 0 014 0l4 4m0 0l4-4a3 3 0 014 0l1 1M3 16v4a1 1 0 001 1h16a1 1 0 001-1v-4" />
                                    </svg>
                                    <p class="text-sm text-gray-600 font-medium">Klik untuk upload foto</p>
                                    <p class="text-xs text-gray-400">JPG / PNG • Maks 2MB</p>
                                </div>

                                <div id="outfitPreviewContainer" class="hidden">
                                    <img id="outfitPreview" class="mx-auto rounded-lg shadow-md max-h-48 object-cover">
                                    <p class="text-xs text-green-600 mt-2 font-medium">Foto berhasil dipilih</p>
                                </div>
                            </div>
                            @endif
                        </div>

                        <input type="hidden" name="latitude" id="latitude">
                        <input type="hidden" name="longitude" id="longitude">
                        <input type="hidden" name="photo" id="photo">
                        <input type="hidden" name="status" id="statusInput">

                        {{-- Kamera --}}
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    Ambil Foto
                                </span>
                            </label>

                            {{-- State: Belum aktif --}}
                            <div id="cameraPlaceholder"
                                class="border-2 border-dashed border-gray-300 rounded-lg bg-gray-50 flex flex-col items-center justify-center py-10 gap-3">
                                <svg class="w-14 h-14 text-gray-300" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <p class="text-sm text-gray-500 font-medium">Kamera belum aktif</p>
                                <p class="text-xs text-gray-400 text-center px-4">Klik tombol di bawah untuk
                                    mengaktifkan kamera.<br>Browser akan meminta izin akses kamera.</p>
                                <button type="button" id="btnAktifkanKamera" onclick="initCamera()"
                                    class="mt-2 inline-flex items-center gap-2 bg-blue-600 text-white px-5 py-2.5 rounded-lg hover:bg-blue-700 transition text-sm font-medium shadow">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 10l4.553-2.07A1 1 0 0121 8.868V15.13a1 1 0 01-1.447.9L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z" />
                                    </svg>
                                    Aktifkan Kamera
                                </button>
                            </div>

                            {{-- State: Error kamera --}}
                            <div id="cameraError"
                                class="hidden border-2 border-red-200 rounded-lg bg-red-50 flex flex-col items-center justify-center py-8 gap-3 px-4">
                                <svg class="w-12 h-12 text-red-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                </svg>
                                <p class="text-sm font-semibold text-red-700" id="cameraErrorTitle">Akses Kamera Ditolak
                                </p>
                                <p class="text-xs text-red-600 text-center" id="cameraErrorMsg">—</p>
                                <div
                                    class="bg-white border border-red-200 rounded-lg p-3 text-xs text-gray-600 w-full mt-1">
                                    <p class="font-semibold text-gray-700 mb-1">💡 Cara mengaktifkan izin kamera:</p>
                                    <ul class="list-disc list-inside space-y-1">
                                        <li>Klik ikon 🔒 atau 📷 di address bar browser</li>
                                        <li>Pilih <strong>Izinkan</strong> pada bagian Kamera</li>
                                        <li>Refresh halaman lalu klik <strong>Aktifkan Kamera</strong> lagi</li>
                                    </ul>
                                </div>
                                <button type="button" onclick="initCamera()"
                                    class="inline-flex items-center gap-2 bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition text-sm font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    Coba Lagi
                                </button>
                            </div>

                            {{-- State: Video aktif --}}
                            <div id="cameraActive"
                                class="hidden border-2 border-green-300 rounded-lg overflow-hidden bg-black relative">
                                <video id="video" width="100%" autoplay playsinline class="block"></video>
                                <div
                                    class="absolute top-2 right-2 bg-green-500 text-white text-xs px-2 py-0.5 rounded-full flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 bg-white rounded-full animate-pulse inline-block"></span>
                                    Live
                                </div>
                            </div>
                            <canvas id="canvas" style="display:none;"></canvas>

                            <p class="text-xs text-gray-500 mt-2">Pastikan wajah Anda terlihat jelas dan berada dalam
                                frame</p>
                        </div>

                        {{-- Status Lokasi --}}
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    Status Lokasi
                                </span>
                            </label>
                            <div id="locationStatus"
                                class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="animate-pulse">
                                    <div class="w-2 h-2 bg-gray-400 rounded-full"></div>
                                </div>
                                <p class="text-sm text-gray-500">Mengambil lokasi...</p>
                            </div>

                            <div id="nearestBranchInfo" class="mt-2 hidden">
                                <div class="flex items-center gap-2 p-2 bg-blue-50 rounded-lg border border-blue-200">
                                    <svg class="w-4 h-4 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-xs text-blue-700" id="nearestBranchText"></p>
                                </div>
                            </div>
                        </div>
                        {{-- Status Verifikasi Wajah --}}
                        <div id="faceVerifyStatus" class="mb-6 hidden"></div>

                        {{-- Tombol Absensi --}}
                        <div class="grid grid-cols-2 gap-4">
                            <button type="button" onclick="prepareSubmit(this, 'CHECK_IN')"
                                class="absen-btn flex items-center justify-center gap-2 bg-green-600 text-white py-4 rounded-lg hover:bg-green-700 transition font-medium"
                                @if($disableCheckIn) disabled @endif>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                </svg>
                                Check In
                            </button>

                            <button type="button" onclick="prepareSubmit(this, 'CHECK_OUT')"
                                class="absen-btn flex items-center justify-center gap-2 bg-red-600 text-white py-4 rounded-lg hover:bg-red-700 transition font-medium"
                                @if($disableCheckOut) disabled @endif>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Check Out
                            </button>

                            {{-- DIBALIK: Out dulu baru In --}}
                            <button type="button" onclick="prepareSubmit(this, 'ISTIRAHAT_OUT')"
                                class="absen-btn flex items-center justify-center gap-2 bg-yellow-600 text-white py-4 rounded-lg hover:bg-yellow-700 transition font-medium"
                                @if($disableIstirahatOut) disabled @endif>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Istirahat Out
                            </button>

                            <button type="button" onclick="prepareSubmit(this, 'ISTIRAHAT_IN')"
                                class="absen-btn flex items-center justify-center gap-2 bg-blue-600 text-white py-4 rounded-lg hover:bg-blue-700 transition font-medium"
                                @if($disableIstirahatIn) disabled @endif>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Istirahat In
                            </button>
                        </div>
                        <div class="mt-4 text-center">
                            <p class="text-xs text-gray-500">
                                Dengan melakukan absensi, Anda menyetujui bahwa data lokasi dan foto Anda akan direkam
                            </p>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    {{-- Loading Overlay --}}
    <div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-sm mx-auto text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mb-4 mx-auto"></div>
            <p class="text-gray-700 font-medium">Memproses absensi...</p>
            <p class="text-sm text-gray-500 mt-2">Mohon tunggu sebentar</p>
        </div>
    </div>
    <script>
        const video              = document.getElementById('video');
    const canvas             = document.getElementById('canvas');
    const photoInput         = document.getElementById('photo');
    const locationStatus     = document.getElementById('locationStatus');
    const faceVerifyStatus   = document.getElementById('faceVerifyStatus');
    const nearestBranchInfo  = document.getElementById('nearestBranchInfo');
    const loadingOverlay     = document.getElementById('loadingOverlay');
    const cameraPlaceholder  = document.getElementById('cameraPlaceholder');
    const cameraError        = document.getElementById('cameraError');
    const cameraActive       = document.getElementById('cameraActive');
    const cameraErrorTitle   = document.getElementById('cameraErrorTitle');
    const cameraErrorMsg     = document.getElementById('cameraErrorMsg');

    const activeBranch = @json($activeBranch);
    const allBranches  = @json($userBranches);

    let cameraReady       = false;
    let smileDetected     = false;
    let modelsLoaded      = false;
    let detectingSmile    = false;
    let naturalConfirmed  = false;
    let naturalFrameCount = 0;
    const NATURAL_FRAMES_NEEDED = 5;

    
function previewOutfit(event) {
    const file = event.target.files[0];
    if (!file) return;

    const preview = document.getElementById('outfitPreview');
    const container = document.getElementById('outfitPreviewContainer');
    const placeholder = document.getElementById('uploadPlaceholder');

    preview.src = URL.createObjectURL(file);

    placeholder.classList.add('hidden');
    container.classList.remove('hidden');
}

    // =====================
    // LOAD MODEL FACE-API
    // =====================
    async function loadFaceModels() {
        let attempts = 0;
        while (typeof faceapi === 'undefined') {
            await new Promise(r => setTimeout(r, 300));
            attempts++;
            if (attempts > 30) throw new Error('face-api.js gagal dimuat.');
        }
        await faceapi.nets.tinyFaceDetector.loadFromUri('/models');
        await faceapi.nets.faceExpressionNet.loadFromUri('/models');
        modelsLoaded = true;
    }

    // =====================
    // HELPER UPDATE STATUS WAJAH
    // =====================
    function setFaceStatus(type, html) {
        faceVerifyStatus.classList.remove('hidden');
        const colors = {
            blue:   'bg-blue-50 border-blue-200',
            green:  'bg-green-50 border-green-200',
            yellow: 'bg-yellow-50 border-yellow-200',
            red:    'bg-red-50 border-red-200',
        };
        faceVerifyStatus.innerHTML = `
            <div class="p-3 rounded-lg border ${colors[type] || colors.blue}">
                ${html}
            </div>`;
    }

    // =====================
    // DETEKSI SENYUM
    // =====================
    async function detectSmile() {
        if (!modelsLoaded || !cameraReady || smileDetected || !detectingSmile) return;

        try {
            const result = await faceapi
                .detectSingleFace(video, new faceapi.TinyFaceDetectorOptions())
                .withFaceExpressions();

            if (result) {
                const happy = result.expressions.happy;
                const pct   = Math.round(happy * 100);

                // ── FASE 1: Tunggu ekspresi natural ──
                if (!naturalConfirmed) {
                    if (happy < 0.3) {
                        naturalFrameCount++;
                        // Tidak tampilkan apa-apa, cukup loading dots kecil
                        setFaceStatus('blue', `
                            <div class="flex items-center gap-2">
                                <div class="flex gap-1">
                                    <span class="w-1.5 h-1.5 bg-blue-400 rounded-full animate-bounce" style="animation-delay:0ms"></span>
                                    <span class="w-1.5 h-1.5 bg-blue-400 rounded-full animate-bounce" style="animation-delay:150ms"></span>
                                    <span class="w-1.5 h-1.5 bg-blue-400 rounded-full animate-bounce" style="animation-delay:300ms"></span>
                                </div>
                                <p class="text-xs text-blue-500">Memverifikasi...</p>
                            </div>`);

                        if (naturalFrameCount >= NATURAL_FRAMES_NEEDED) {
                            naturalConfirmed = true;
                            // Baru tampilkan instruksi senyum
                            setFaceStatus('blue', `
                                <div class="flex items-center gap-2 mb-2">
                                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <p class="text-sm text-blue-700 font-medium">😊 <strong>Sekarang senyum</strong> ke kamera...</p>
                                </div>
                                <div class="w-full bg-blue-100 rounded-full h-2.5">
                                    <div class="bg-blue-200 h-2.5 rounded-full" style="width:0%"></div>
                                </div>
                                <p class="text-xs text-blue-500 mt-1 text-right">0% senyum terdeteksi</p>`);
                        }
                    } else {
                        // Senyum duluan → reset
                        // <p class="text-xs text-yellow-700">Ekspresi normal dulu ya, jangan senyum dulu...</p>
                        naturalFrameCount = 0;
                        setFaceStatus('yellow', `
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-yellow-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-xs text-yellow-700">Kedip dulu ya, hehe...</p>
                                
                            </div>`);
                    }

                // ── FASE 2: Deteksi senyum ──
                } else {
                    setFaceStatus('blue', `
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-5 h-5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-sm text-blue-700 font-medium">😊 <strong>Sekarang senyum</strong> ke kamera...</p>
                        </div>
                        <div class="w-full bg-blue-100 rounded-full h-2.5">
                            <div class="bg-blue-500 h-2.5 rounded-full transition-all duration-200" style="width:${pct}%"></div>
                        </div>
                        <p class="text-xs text-blue-500 mt-1 text-right">${pct}% senyum terdeteksi</p>`);

                    if (happy > 0.7) {
                        smileDetected  = true;
                        detectingSmile = false;
                        setFaceStatus('green', `
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <p class="text-sm text-green-700 font-medium">😊 Verifikasi berhasil! Silakan lakukan absensi.</p>
                            </div>`);
                        return;
                    }
                }

            } else {
                // Wajah tidak terdeteksi
                naturalFrameCount = 0;
                setFaceStatus('yellow', `
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-yellow-600 flex-shrink-0 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm text-yellow-700">😐 Posisikan wajah di depan kamera...</p>
                    </div>`);
            }

        } catch (e) {
            // abaikan error individual
        }

        setTimeout(() => {
            if (detectingSmile && !smileDetected) {
                requestAnimationFrame(detectSmile);
            }
        }, 150);
    }

    // =====================
    // INISIALISASI KAMERA
    // =====================
    async function initCamera() {
        cameraReady       = false;
        smileDetected     = false;
        modelsLoaded      = false;
        detectingSmile    = false;
        naturalConfirmed  = false;
        naturalFrameCount = 0;

        faceVerifyStatus.classList.add('hidden');
        cameraError.classList.add('hidden');
        cameraActive.classList.add('hidden');
        cameraPlaceholder.classList.remove('hidden');
        cameraPlaceholder.innerHTML = `
            <div class="flex flex-col items-center gap-3 py-10">
                <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-600"></div>
                <p class="text-sm text-blue-700 font-medium">Menghubungkan kamera...</p>
                <p class="text-xs text-gray-400">Izinkan akses kamera jika browser meminta</p>
            </div>`;

        try {
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                throw { name: 'NOT_SUPPORTED' };
            }

            const stream = await navigator.mediaDevices.getUserMedia({
                video: { width: { ideal: 640 }, height: { ideal: 480 }, facingMode: 'user' }
            });

            video.srcObject = stream;
            await new Promise(resolve => { video.onloadedmetadata = () => resolve(); });

            cameraPlaceholder.classList.add('hidden');
            cameraActive.classList.remove('hidden');
            cameraReady = true;

            // Tampilkan loading model di locationStatus (tidak ditimpa detectSmile)
            locationStatus.innerHTML = `
                <div class="flex items-center gap-3 p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-yellow-600 flex-shrink-0"></div>
                    <p class="text-sm text-yellow-700 font-medium">⏳ Memuat model deteksi wajah...</p>
                </div>`;

            try {
                await loadFaceModels();

                // Setelah model load, locationStatus kembali ke status GPS biasa
                locationStatus.innerHTML = `
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="animate-pulse">
                            <div class="w-2 h-2 bg-gray-400 rounded-full"></div>
                        </div>
                        <p class="text-sm text-gray-500">Mengambil lokasi...</p>
                    </div>`;

                // Mulai ambil GPS bersamaan dengan deteksi wajah
                getLocationSilent();

                // Tampilkan area verifikasi wajah
                setFaceStatus('blue', `
                    <div class="flex items-center gap-2">
                        <div class="flex gap-1">
                            <span class="w-1.5 h-1.5 bg-blue-400 rounded-full animate-bounce" style="animation-delay:0ms"></span>
                            <span class="w-1.5 h-1.5 bg-blue-400 rounded-full animate-bounce" style="animation-delay:150ms"></span>
                            <span class="w-1.5 h-1.5 bg-blue-400 rounded-full animate-bounce" style="animation-delay:300ms"></span>
                        </div>
                        <p class="text-xs text-blue-500">Memverifikasi...</p>
                    </div>`);

                detectingSmile = true;
                detectSmile();

            } catch (modelErr) {
                console.warn('Model gagal load:', modelErr);
                smileDetected = true;
                setFaceStatus('yellow', `
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-yellow-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm text-yellow-700">⚠️ Deteksi wajah tidak tersedia. Silakan absen.</p>
                    </div>`);
                getLocationSilent();
            }

        } catch (err) {
            cameraPlaceholder.classList.add('hidden');
            cameraError.classList.remove('hidden');
            cameraReady = false;

            const errMap = {
                'NotAllowedError':     ['Izin Kamera Ditolak',      'Anda menolak akses kamera. Ubah izin di browser lalu klik "Coba Lagi".'],
                'PermissionDeniedError':['Izin Kamera Ditolak',     'Anda menolak akses kamera. Ubah izin di browser lalu klik "Coba Lagi".'],
                'NotFoundError':       ['Kamera Tidak Ditemukan',   'Tidak ada perangkat kamera yang terdeteksi.'],
                'DevicesNotFoundError':['Kamera Tidak Ditemukan',   'Tidak ada perangkat kamera yang terdeteksi.'],
                'NotReadableError':    ['Kamera Sedang Digunakan',  'Tutup aplikasi lain yang menggunakan kamera lalu coba lagi.'],
                'TrackStartError':     ['Kamera Sedang Digunakan',  'Tutup aplikasi lain yang menggunakan kamera lalu coba lagi.'],
                'OverconstrainedError':['Kamera Tidak Kompatibel',  'Kamera tidak mendukung resolusi yang diminta.'],
                'NOT_SUPPORTED':       ['Browser Tidak Didukung',   'Gunakan Chrome, Firefox, atau Safari terbaru.'],
            };
            const [title, msg] = errMap[err.name] || ['Gagal Mengakses Kamera', `Error: ${err.message || err.name}`];
            cameraErrorTitle.textContent = title;
            cameraErrorMsg.textContent   = msg;
        }
    }

    // =====================
    // AMBIL LOKASI (silent, tidak timpa face status)
    // =====================
    function getLocationSilent() {
        if (!navigator.geolocation) {
            locationStatus.innerHTML = `
                <div class="flex items-center gap-3 p-3 bg-red-50 rounded-lg border border-red-200">
                    <p class="text-sm text-red-700">Browser tidak mendukung GPS.</p>
                </div>`;
            return;
        }
        navigator.geolocation.getCurrentPosition(
            (position) => {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                document.getElementById('latitude').value  = lat;
                document.getElementById('longitude').value = lng;
                locationStatus.innerHTML = `
                    <div class="flex items-center gap-3 p-3 bg-green-50 rounded-lg border border-green-200">
                        <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-green-700">Lokasi aktif</p>
                            <p class="text-xs text-green-600 mt-1">Lat: ${lat.toFixed(6)} | Long: ${lng.toFixed(6)}</p>
                        </div>
                    </div>`;
                checkNearestBranch(lat, lng);
            },
            (error) => {
                const messages = {
                    1: 'Akses lokasi ditolak. Izinkan akses lokasi di pengaturan browser.',
                    2: 'Informasi lokasi tidak tersedia. Pastikan GPS aktif.',
                    3: 'Waktu pengambilan lokasi habis. Coba lagi.',
                };
                locationStatus.innerHTML = `
                    <div class="flex items-center gap-3 p-3 bg-red-50 rounded-lg border border-red-200">
                        <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <p class="text-sm text-red-700">${messages[error.code] || 'Gagal mengambil lokasi.'}</p>
                    </div>`;
            },
            { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
        );
    }

    // =====================
    // AMBIL LOKASI (dipanggil saat submit)
    // =====================
    function getLocation() {
        return new Promise((resolve, reject) => {
            const lat = document.getElementById('latitude').value;
            const lng = document.getElementById('longitude').value;
            if (lat && lng) {
                resolve({ lat, lng });
                return;
            }
            if (!navigator.geolocation) {
                reject(new Error('Browser tidak mendukung GPS.'));
                return;
            }
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    document.getElementById('latitude').value  = position.coords.latitude;
                    document.getElementById('longitude').value = position.coords.longitude;
                    resolve({ lat: position.coords.latitude, lng: position.coords.longitude });
                },
                (error) => reject(new Error('Gagal mengambil lokasi.')),
                { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
            );
        });
    }

    // =====================
    // CEK BRANCH TERDEKAT
    // =====================
    function checkNearestBranch(lat, lng) {
        if (!allBranches || allBranches.length === 0) return;
        let nearest = null, minDist = Infinity;
        allBranches.forEach(branch => {
            if (!branch.latitude || !branch.longitude) return;
            const d = calculateDistance(lat, lng, parseFloat(branch.latitude), parseFloat(branch.longitude));
            if (d < minDist) { minDist = d; nearest = branch; }
        });
        if (!nearest) return;
        const dist = Math.round(minDist);
        const ok   = minDist <= 150;
        nearestBranchInfo.classList.remove('hidden');
        nearestBranchInfo.innerHTML = ok
            ? `<div class="flex items-center gap-2 p-2 bg-green-50 rounded-lg border border-green-200">
                <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <p class="text-xs text-green-700">Dalam radius <strong>${dist} meter</strong> dari ${nearest.name} ✅</p>
               </div>`
            : `<div class="flex items-center gap-2 p-2 bg-red-50 rounded-lg border border-red-200">
                <svg class="w-4 h-4 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-xs text-red-700"><strong>${dist} meter</strong> dari ${nearest.name}. Melebihi batas 150m ❌</p>
               </div>`;
    }

    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R  = 6371e3;
        const φ1 = lat1 * Math.PI / 180, φ2 = lat2 * Math.PI / 180;
        const Δφ = (lat2 - lat1) * Math.PI / 180, Δλ = (lon2 - lon1) * Math.PI / 180;
        const a  = Math.sin(Δφ/2)**2 + Math.cos(φ1)*Math.cos(φ2)*Math.sin(Δλ/2)**2;
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    }

    // =====================
    // SUBMIT ABSENSI
    // =====================
    async function prepareSubmit(button, statusValue) {
        if (!cameraReady) {
            alert('❌ Kamera belum aktif. Klik tombol "Aktifkan Kamera" terlebih dahulu.');
            return;
        }
        if (!smileDetected) {
            alert('❌ Silakan senyum ke kamera terlebih dahulu sebelum absen.');
            return;
        }

        document.querySelectorAll('.absen-btn').forEach(b => b.disabled = true);
        loadingOverlay.classList.remove('hidden');
        loadingOverlay.classList.add('flex');

        try {
            await capturePhoto();
            await getLocation();

            if (!document.getElementById('latitude').value) {
                throw new Error('Lokasi belum berhasil diambil. Pastikan GPS aktif.');
            }
            if (!photoInput.value) {
                throw new Error('Foto belum berhasil diambil.');
            }

            document.getElementById('statusInput').value = statusValue;
            document.getElementById('attendanceForm').submit();

        } catch (error) {
            loadingOverlay.classList.add('hidden');
            loadingOverlay.classList.remove('flex');
            document.querySelectorAll('.absen-btn').forEach(b => b.disabled = false);
            alert('❌ ' + (error.message || 'Terjadi kesalahan. Silakan coba lagi.'));
        }
    }

    // =====================
    // AMBIL FOTO
    // =====================
    // function capturePhoto() {
    //     return new Promise((resolve, reject) => {
    //         if (!cameraReady || !video.srcObject) {
    //             reject(new Error('Kamera belum aktif.'));
    //             return;
    //         }
    //         if (video.videoWidth === 0) {
    //             reject(new Error('Kamera belum siap, tunggu sebentar lalu coba lagi.'));
    //             return;
    //         }
    //         canvas.width  = video.videoWidth;
    //         canvas.height = video.videoHeight;
    //         const ctx = canvas.getContext('2d');
    //         ctx.translate(canvas.width, 0);
    //         ctx.scale(-1, 1);
    //         ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
    //         photoInput.value = canvas.toDataURL('image/jpeg', 0.8);
    //         resolve();
    //     });
    // }
    function capturePhoto() {
    return new Promise((resolve, reject) => {
        if (!cameraReady || !video.srcObject) {
            reject(new Error('Kamera belum aktif.'));
            return;
        }
        if (video.videoWidth === 0) {
            reject(new Error('Kamera belum siap, tunggu sebentar lalu coba lagi.'));
            return;
        }

        canvas.width  = video.videoWidth;
        canvas.height = video.videoHeight;
        const ctx = canvas.getContext('2d');

        // Mirror (flip horizontal)
        ctx.translate(canvas.width, 0);
        ctx.scale(-1, 1);
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        // Reset transform sebelum gambar watermark
        ctx.setTransform(1, 0, 0, 1, 0, 0);

        // ── Ambil data watermark ──
        const lat = document.getElementById('latitude').value || '-';
        const lng = document.getElementById('longitude').value || '-';
        const now = new Date();
        const jam = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        const tgl = now.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });

        const lines = [
            `📍 ${parseFloat(lat).toFixed(6)}, ${parseFloat(lng).toFixed(6)}`,
            `🕐 ${jam}  |  ${tgl}`,
        ];

        // ── Konfigurasi watermark ──
        const padding  = 10;
        const fontSize = Math.max(14, canvas.width * 0.022); // responsif
        ctx.font       = `bold ${fontSize}px sans-serif`;

        const lineH    = fontSize * 1.5;
        const boxH     = lines.length * lineH + padding * 2;
        const boxY     = canvas.height - boxH;

        // Background semi-transparan hitam
        ctx.fillStyle = 'rgba(0, 0, 0, 0.55)';
        ctx.fillRect(0, boxY, canvas.width, boxH);

        // Teks putih
        ctx.fillStyle = '#FFFFFF';
        ctx.textBaseline = 'top';

        lines.forEach((line, i) => {
            ctx.fillText(line, padding, boxY + padding + i * lineH);
        });

        photoInput.value = canvas.toDataURL('image/jpeg', 0.8);
        resolve();
    });
}

    // Auto refresh lokasi setiap 30 detik
    setInterval(() => {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                pos => checkNearestBranch(pos.coords.latitude, pos.coords.longitude),
                () => {}
            );
        }
    }, 30000);
    </script>
    <style>
        .absen-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        #video {
            transform: scaleX(-1);
            -webkit-transform: scaleX(-1);
        }

        @media (max-width: 640px) {
            .absen-btn {
                padding: 0.75rem;
                font-size: 0.875rem;
            }
        }
    </style>
</x-app-layout>