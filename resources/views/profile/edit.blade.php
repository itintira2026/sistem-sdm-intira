<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Edit Profil
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Kelola informasi akun & keamanan Anda
                </p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('dashboard') }}"
                    class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- ===================== PROFIL CARD ===================== --}}
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="h-24 bg-gradient-to-r from-teal-400 to-teal-600"></div>
                <div class="px-6 pb-6">
                    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between -mt-12 gap-4">
                        {{-- Avatar --}}
                        <div class="flex items-end gap-4">
                            <div
                                class="w-24 h-24 rounded-full border-4 border-white shadow-md overflow-hidden bg-teal-100 flex items-center justify-center">
                                @if(auth()->user()->profile_photo)
                                <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}"
                                    alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                                @else
                                <span class="text-3xl font-bold text-teal-600">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                </span>
                                @endif
                            </div>
                            <div class="mb-2">
                                <h3 class="text-xl font-bold text-gray-900">{{ auth()->user()->name }}</h3>
                                <p class="text-sm text-gray-500">{{ auth()->user()->email }}</p>
                            </div>
                        </div>

                        {{-- Status & Role --}}
                        <div class="flex flex-wrap gap-2 mb-2">
                            @if(auth()->user()->is_active)
                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">
                                ● Aktif
                            </span>
                            @else
                            <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold">
                                ● Nonaktif
                            </span>
                            @endif

                            @foreach(auth()->user()->roles as $role)
                            <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-semibold">
                                {{ $role->name }}
                            </span>
                            @endforeach
                        </div>
                    </div>

                    {{-- Info Detail --}}
                    <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4 border-t pt-6">
                        <div>
                            <p class="text-xs text-gray-400 uppercase font-semibold">Username</p>
                            <p class="text-sm text-gray-800 mt-1">{{ auth()->user()->username ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase font-semibold">No. Telepon</p>
                            <p class="text-sm text-gray-800 mt-1">{{ auth()->user()->phone ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase font-semibold">Bergabung</p>
                            <p class="text-sm text-gray-800 mt-1">{{ auth()->user()->created_at->format('d M Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===================== FORM UPDATE PROFIL ===================== --}}
            {{-- <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-teal-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <h4 class="text-base font-semibold text-gray-800">Informasi Profil</h4>
                </div>
                <div class="p-6">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div> --}}



            {{-- ===================== CABANG CARD ===================== --}}
            @php
            $authUser = auth()->user();
            $branches = $authUser->branches ?? collect();
            $managerBranches = $branches->filter(fn($b) => $b->pivot->is_manager);
            $staffBranches = $branches->filter(fn($b) => !$b->pivot->is_manager);
            @endphp

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center justify-between mb-5">
                    <h4 class="text-base font-semibold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Cabang yang Ditugaskan
                    </h4>
                    <span class="text-xs text-gray-400 bg-gray-100 px-2 py-1 rounded-full">
                        {{ $branches->count() }} cabang aktif
                    </span>
                </div>

                @if($branches->isEmpty())
                <div class="flex flex-col items-center justify-center py-10 text-center">
                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5" />
                    </svg>
                    <p class="text-gray-500 text-sm font-medium">Belum ditugaskan ke cabang manapun</p>
                    <p class="text-gray-400 text-xs mt-1">Hubungi admin untuk penugasan cabang</p>
                </div>
                @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($branches as $branch)
                    <div
                        class="border border-gray-200 rounded-xl p-4 hover:border-teal-300 hover:bg-teal-50 transition">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-lg bg-teal-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800 text-sm">{{ $branch->name }}</p>
                                    <p class="text-xs text-gray-400">Kode: {{ $branch->code }}</p>
                                </div>
                            </div>

                            @if($branch->pivot->is_manager)
                            <span
                                class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-semibold flex-shrink-0">
                                👑 Manager
                            </span>
                            @else
                            <span
                                class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold flex-shrink-0">
                                Staff
                            </span>
                            @endif
                        </div>

                        <div class="mt-3 pt-3 border-t border-gray-100 grid grid-cols-2 gap-2 text-xs text-gray-500">
                            @if($branch->address)
                            <div class="flex items-center gap-1 col-span-2">
                                <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                </svg>
                                <span class="truncate">{{ $branch->address }}</span>
                            </div>
                            @endif
                            @if($branch->phone)
                            <div class="flex items-center gap-1">
                                <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.948V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <span>{{ $branch->phone }}</span>
                            </div>
                            @endif
                            <div class="flex items-center gap-1">
                                <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>{{ $branch->timezone ?? 'WIB' }}</span>
                            </div>
                        </div>

                        <p class="text-xs text-gray-400 mt-2">
                            Ditugaskan: {{ $branch->pivot->created_at?->format('d M Y') ?? '-' }}
                        </p>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- ===================== RINGKASAN CABANG ===================== --}}
            @if($managerBranches->count() > 0)
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-yellow-600 text-lg">👑</span>
                    <p class="text-sm font-semibold text-yellow-800">
                        Anda adalah Manager di {{ $managerBranches->count() }} cabang
                    </p>
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach($managerBranches as $branch)
                    <span
                        class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-medium border border-yellow-200">
                        {{ $branch->name }}
                    </span>
                    @endforeach
                </div>
            </div>
            @endif

            @if($staffBranches->count() > 0)
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-blue-600 text-lg">👤</span>
                    <p class="text-sm font-semibold text-blue-800">
                        Staff di {{ $staffBranches->count() }} cabang
                    </p>
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach($staffBranches as $branch)
                    <span
                        class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium border border-blue-200">
                        {{ $branch->name }}
                    </span>
                    @endforeach
                </div>
            </div>
            @endif
            {{-- ===================== FORM UPDATE PASSWORD ===================== --}}
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-teal-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <h4 class="text-base font-semibold text-gray-800">Ubah Password</h4>
                </div>
                <div class="p-6">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>