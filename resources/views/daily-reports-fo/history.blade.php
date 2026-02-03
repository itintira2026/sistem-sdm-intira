<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    📜 History Laporan Harian FO
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $branch->name }} | {{ config('daily_report_fo.history_days') }} hari terakhir
                </p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('daily-reports-fo.index') }}"
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

            {{-- Stats --}}
            <div class="grid grid-cols-1 gap-6 mb-6 md:grid-cols-2">
                <div class="p-6 bg-white rounded-lg shadow-sm">
                    <p class="text-sm text-gray-600">Total Laporan</p>
                    <p class="text-3xl font-bold text-teal-600">{{ $stats['total_reports'] }}</p>
                </div>
                <div class="p-6 bg-white rounded-lg shadow-sm">
                    <p class="text-sm text-gray-600">Total Hari Lapor</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $stats['total_days'] }}</p>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="mb-6 text-lg font-semibold text-gray-800">
                        Daftar History
                    </h3>

                    {{-- Filter --}}
                    <form method="GET" class="mb-6">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                            {{-- Per Page --}}
                            <div>
                                <select name="per_page" onchange="this.form.submit()"
                                    class="w-full px-4 py-2 border rounded-lg">
                                    @foreach ([10, 25, 50] as $size)
                                        <option value="{{ $size }}"
                                            {{ request('per_page', 10) == $size ? 'selected' : '' }}>
                                            {{ $size }} per halaman
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Shift --}}
                            <div>
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

                            {{-- Date From --}}
                            <div>
                                <input type="date" name="date_from" value="{{ request('date_from') }}"
                                    onchange="this.form.submit()" class="w-full px-4 py-2 border rounded-lg"
                                    placeholder="Dari Tanggal">
                            </div>

                            {{-- Date To --}}
                            <div>
                                <input type="date" name="date_to" value="{{ request('date_to') }}"
                                    onchange="this.form.submit()" class="w-full px-4 py-2 border rounded-lg"
                                    placeholder="Sampai Tanggal">
                            </div>
                        </div>
                    </form>

                    {{-- Table --}}
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="px-4 py-4 text-left text-gray-600 uppercase">#</th>
                                    <th class="px-4 py-4 text-left text-gray-600 uppercase">Tanggal</th>
                                    <th class="px-4 py-4 text-left text-gray-600 uppercase">Shift</th>
                                    <th class="px-4 py-4 text-left text-gray-600 uppercase">Slot</th>
                                    <th class="px-4 py-4 text-left text-gray-600 uppercase">Waktu Upload</th>
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
                                            {{ $report->tanggal->format('d M Y') }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <span
                                                class="px-2 py-1 text-xs rounded {{ $report->shift == 'pagi' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                                                {{ $report->shift_label }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 font-semibold">
                                            Slot {{ $report->slot }} - {{ $report->formatted_slot_time }}
                                        </td>
                                        <td class="px-4 py-3 text-xs text-gray-600">
                                            {{ $report->uploaded_at->format('d M Y H:i:s') }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 text-xs text-teal-700 bg-teal-100 rounded">
                                                📷 {{ $report->photos->count() }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <button onclick="showDetail({{ $report->id }})"
                                                class="px-3 py-1 text-xs text-blue-600 transition hover:text-blue-800">
                                                👁️ Lihat Detail
                                            </button>
                                        </td>
                                    </tr>

                                    {{-- Detail Row (Hidden) --}}
                                    <tr id="detail-{{ $report->id }}" class="hidden bg-gray-50">
                                        <td colspan="7" class="px-4 py-4">
                                            <div class="p-4 bg-white rounded-lg">
                                                <h4 class="mb-3 font-semibold text-gray-800">Detail Laporan</h4>

                                                {{-- Keterangan --}}
                                                @if ($report->keterangan)
                                                    <div class="mb-4">
                                                        <p class="text-sm font-semibold text-gray-700">Keterangan:</p>
                                                        <p class="text-sm text-gray-600">{{ $report->keterangan }}</p>
                                                    </div>
                                                @endif

                                                {{-- Photos by Category --}}
                                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                                    @foreach (config('daily_report_fo.photo_categories') as $key => $label)
                                                        @php
                                                            $photos = $report->getPhotosByCategory($key);
                                                        @endphp
                                                        <div class="p-3 border rounded-lg">
                                                            <p class="mb-2 text-sm font-semibold text-gray-700">
                                                                {{ $label }} ({{ $photos->count() }})
                                                            </p>
                                                            <div class="grid grid-cols-4 gap-2">
                                                                @foreach ($photos as $photo)
                                                                    <a href="{{ $photo->url }}" target="_blank">
                                                                        <img src="{{ $photo->url }}"
                                                                            alt="{{ $label }}"
                                                                            class="object-cover w-full h-16 rounded">
                                                                    </a>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                            Belum ada history laporan.
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

    {{-- JavaScript --}}
    <script>
        function showDetail(id) {
            const row = document.getElementById(`detail-${id}`);
            row.classList.toggle('hidden');
        }
    </script>
</x-app-layout>
{{-- ```

---

## ✅ **PHASE 3 COMPLETE CHECKLIST**

- [x] **View** - `index.blade.php` (Dashboard + Modal shift) ✅
- [x] **View** - `slot-form.blade.php` (Upload/Edit form) ✅
- [x] **View** - `history.blade.php` (History list) ✅
- [x] **JavaScript** - Real-time countdown ✅
- [x] **JavaScript** - Photo preview ✅
- [x] **JavaScript** - Auto refresh ✅
- [x] **JavaScript** - Current time display ✅

---

## 🎯 **KEY FEATURES IMPLEMENTED:**

### **1. Dashboard (index.blade.php)**
```
✅ Modal shift selection (muncul jika belum pilih)
✅ Shift info banner dengan timezone
✅ Real-time current time display
✅ Progress bar (completed/total slots)
✅ 4 slot cards dengan status badge
✅ Countdown timer per slot (waiting/open)
✅ Auto refresh every 5 minutes
```

### **2. Slot Form (slot-form.blade.php)**
```
✅ Window info dengan countdown
✅ 6 kategori upload (each dengan preview)
✅ Edit mode: show existing photos + delete checkbox
✅ Photo preview on select
✅ Upload new photos (unlimited per kategori)
✅ Keterangan text field
✅ Auto refresh saat countdown habis
```

### **3. History (history.blade.php)**
```
✅ Stats cards (total reports, total days)
✅ Filter: per page, shift, date range
✅ Table dengan pagination
✅ Expandable detail row (toggle)
✅ Show photos by category
✅ Click to view full image --}}
