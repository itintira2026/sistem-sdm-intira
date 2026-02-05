{{-- Sama seperti manager/report-detail.blade.php, bisa reuse atau copy --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Detail Laporan
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $report->user->name }} | {{ $report->branch->name }} | {{ $report->tanggal->format('d M Y') }}
                    | {{ $report->shift_label }} | Slot {{ $report->slot }}
                </p>
            </div>
            <div class="flex gap-3 mt-3 md:mt-0">
                <a href="{{ route('daily-reports-fo.marketing.reports') }}"
                    class="flex items-center gap-2 px-4 py-2 text-white transition bg-gray-500 rounded-lg hover:bg-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            {{-- Info Card --}}
            <div class="p-6 mb-6 bg-white rounded-lg shadow-sm">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <h3 class="mb-4 text-lg font-semibold text-gray-800">Informasi Laporan</h3>
                        <dl class="space-y-2">
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600">FO:</dt>
                                <dd class="text-sm font-semibold text-gray-800">{{ $report->user->name }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600">Email:</dt>
                                <dd class="text-sm font-semibold text-gray-800">{{ $report->user->email }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600">Cabang:</dt>
                                <dd class="text-sm font-semibold text-gray-800">{{ $report->branch->name }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600">Tanggal:</dt>
                                <dd class="text-sm font-semibold text-gray-800">
                                    {{ $report->tanggal->format('d M Y') }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600">Shift:</dt>
                                <dd class="text-sm font-semibold text-gray-800">{{ $report->shift_label }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600">Slot:</dt>
                                <dd class="text-sm font-semibold text-gray-800">
                                    Slot {{ $report->slot }} - {{ $report->formatted_slot_time }}
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600">Waktu Upload:</dt>
                                <dd class="text-sm font-semibold text-gray-800">
                                    {{ $report->uploaded_at->format('d M Y H:i:s') }}
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600">Total Foto:</dt>
                                <dd class="text-sm font-semibold text-gray-800">{{ $report->photos->count() }}</dd>
                            </div>
                        </dl>
                    </div>

                    @if ($report->keterangan)
                        <div>
                            <h3 class="mb-4 text-lg font-semibold text-gray-800">Keterangan</h3>
                            <p class="text-sm text-gray-700">{{ $report->keterangan }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Photos by Category --}}
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                @foreach (config('daily_report_fo.photo_categories') as $key => $label)
                    @php
                        $photos = $photosByCategory->get($key, collect());
                    @endphp

                    <div class="p-6 bg-white rounded-lg shadow-sm">
                        <h4 class="mb-4 text-base font-semibold text-gray-800">
                            {{ $label }} ({{ $photos->count() }})
                        </h4>

                        @if ($photos->count() > 0)
                            <div class="grid grid-cols-3 gap-3">
                                @foreach ($photos as $photo)
                                    <a href="{{ $photo->url }}" target="_blank" class="group">
                                        <img src="{{ $photo->url }}" alt="{{ $label }}"
                                            class="object-cover w-full transition rounded-lg h-28 group-hover:opacity-75">
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500">Tidak ada foto</p>
                        @endif
                    </div>
                @endforeach
            </div>

        </div>
    </div>
</x-app-layout>
{{-- ```

---

## ✅ **PHASE 4 & 5 COMPLETE CHECKLIST**

### **Controller:**
- [x] Manager Dashboard
- [x] Manager FO List
- [x] Manager FO Detail
- [x] Manager Reports
- [x] Manager Report Detail
- [x] Marketing Dashboard
- [x] Marketing Analytics
- [x] Marketing Reports
- [x] Marketing Report Detail

### **Views:**
- [x] Manager Dashboard (`manager/dashboard.blade.php`)
- [x] Manager FO Detail (`manager/fo-detail.blade.php`)
- [x] Manager Reports (`manager/reports.blade.php`)
- [x] Manager Report Detail (`manager/report-detail.blade.php`)
- [x] Marketing Dashboard (`marketing/dashboard.blade.php`)
- [x] Marketing Analytics (`marketing/analytics.blade.php`)
- [x] Marketing Reports (`marketing/reports.blade.php`)
- [x] Marketing Report Detail (`marketing/report-detail.blade.php`)

### **Routes:**
- [x] Manager routes (6 routes)
- [x] Marketing routes (5 routes)

---

## 🎯 **FINAL SUMMARY - COMPLETE SYSTEM**

### **✅ WHAT WE'VE BUILT:**
```
PHASE 1: Foundation
├── Migrations (daily_report_fo, daily_report_fo_photos, timezone)
├── Config (slot times, categories, settings)
├── Helpers (TimeHelper, ShiftHelper, ImageHelper)
└── Models (DailyReportFO, DailyReportFOPhoto)

PHASE 2: Controller & Routes (FO Area)
├── Dashboard dengan shift selection
├── Slot management (upload/edit)
├── History 30 hari
└── Routes (7 routes)

PHASE 3: Views (FO Area)
├── Dashboard + Modal shift
├── Slot form (upload/edit)
├── History
└── Real-time countdown & auto-refresh

PHASE 4 & 5: Manager & Marketing
├── Manager Dashboard (monitoring FO)
├── Manager Reports (semua laporan)
├── Marketing Dashboard (analytics)
├── Marketing Analytics (detailed stats)
└── Chart.js integration --}}