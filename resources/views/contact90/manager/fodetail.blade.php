<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Detail Kontak - {{ $user->name }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Validasi dan monitoring kontak FO
                </p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('contact90.manager.folist', ['tanggal' => $tanggal]) }}"
                    class="flex items-center gap-2 px-4 py-2 text-white transition bg-gray-500 rounded-lg hover:bg-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Daftar FO
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            {{-- 🔥 TEMPORARY: SUPERADMIN ONLY NOTICE --}}
            {{-- 🔥 TODO: Nanti ganti middleware ke 'manager' juga bisa akses --}}
            <div class="p-4 mb-6 text-purple-700 bg-purple-100 rounded-lg">
                <p class="font-semibold">🔐 Temporary Access: Superadmin Only</p>
                <p class="mt-1 text-sm">
                    Saat ini halaman ini hanya bisa diakses oleh Superadmin.
                    <strong>Nanti akan dibuka untuk role "manager"</strong> dengan menambahkan middleware di route.
                </p>
            </div>

            {{-- Alert Success --}}
            @if (session('success'))
                <div class="p-4 mb-6 text-green-700 bg-green-100 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            {{-- INFO FO --}}
            <div class="p-6 mb-6 bg-white rounded-lg shadow-sm">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <h3 class="mb-4 text-lg font-semibold text-gray-800">Informasi FO</h3>
                        <div class="space-y-2">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span class="text-sm text-gray-600">Nama:</span>
                                <span class="font-medium text-gray-800">{{ $user->name }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <span class="text-sm text-gray-600">Email:</span>
                                <span class="font-medium text-gray-800">{{ $user->email }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <span class="text-sm text-gray-600">Cabang:</span>
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($user->branches as $branch)
                                        <span class="px-2 py-1 text-xs text-blue-700 bg-blue-100 rounded">
                                            {{ $branch->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="mb-4 text-lg font-semibold text-gray-800">Progress Hari Ini</h3>
                        @php
                            $totalToday = $contacts->total();
                            $validatedToday = \App\Models\Contact90::where('user_id', $user->id)
                                ->whereDate('tanggal', $tanggal)
                                ->where('validasi_manager', true)
                                ->count();
                            $pendingToday = $totalToday - $validatedToday;
                        @endphp
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Total Kontak:</span>
                                <span class="text-2xl font-bold text-gray-800">{{ $totalToday }}/90</span>
                            </div>
                            <div class="w-full h-3 bg-gray-200 rounded-full">
                                <div class="h-3 bg-teal-600 rounded-full"
                                    style="width: {{ min(($totalToday / 90) * 100, 100) }}%"></div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="p-3 rounded-lg bg-green-50">
                                    <p class="text-xs text-green-700">✅ Sudah Validasi</p>
                                    <p class="text-xl font-bold text-green-800">{{ $validatedToday }}</p>
                                </div>
                                <div class="p-3 rounded-lg bg-orange-50">
                                    <p class="text-xs text-orange-700">⏳ Belum Validasi</p>
                                    <p class="text-xl font-bold text-orange-800">{{ $pendingToday }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- DAFTAR KONTAK --}}
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-800">
                            Daftar Kontak
                        </h3>

                        {{-- BULK VALIDATE BUTTON --}}
                        @if ($contacts->where('validasi_manager', false)->count() > 0)
                            <button onclick="toggleBulkMode()"
                                class="flex items-center gap-2 px-4 py-2 text-white transition bg-teal-600 rounded-lg hover:bg-teal-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                                Mode Validasi Massal
                            </button>
                        @endif
                    </div>

                    {{-- BULK VALIDATE FORM --}}
                    <form id="bulkValidateForm" action="{{ route('contact90.manager.validate.bulk') }}" method="POST"
                        class="hidden p-4 mb-6 rounded-lg bg-teal-50">
                        @csrf
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-teal-800">Mode Validasi Massal Aktif</p>
                                <p class="text-sm text-teal-700">Pilih kontak yang ingin divalidasi, lalu klik
                                    "Validasi Terpilih"</p>
                            </div>
                            <div class="flex gap-2">
                                <button type="button" onclick="toggleBulkMode()"
                                    class="px-4 py-2 text-gray-700 transition border border-gray-300 rounded-lg hover:bg-gray-50">
                                    Batal
                                </button>
                                <button type="submit" id="bulkSubmitBtn" disabled
                                    class="px-4 py-2 text-white transition bg-teal-600 rounded-lg hover:bg-teal-700 disabled:opacity-50 disabled:cursor-not-allowed">
                                    ✅ Validasi Terpilih (<span id="selectedCount">0</span>)
                                </button>
                            </div>
                        </div>
                    </form>

                    {{-- FILTER --}}
                    <form method="GET" class="flex flex-wrap gap-4 mb-6">
                        <input type="hidden" name="tanggal" value="{{ $tanggal }}">

                        {{-- PER PAGE --}}
                        <div>
                            <select name="per_page" onchange="this.form.submit()"
                                class="px-4 py-2 pr-10 border rounded-lg">
                                @foreach ([10, 25, 50, 100] as $size)
                                    <option value="{{ $size }}"
                                        {{ request('per_page', 25) == $size ? 'selected' : '' }}>
                                        {{ $size }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- SOSMED --}}
                        <div>
                            <select name="sosmed" onchange="this.form.submit()"
                                class="px-4 py-2 pr-10 border rounded-lg">
                                <option value="">Semua Platform</option>
                                <option value="DM_IG" {{ request('sosmed') == 'DM_IG' ? 'selected' : '' }}>
                                    DM Instagram
                                </option>
                                <option value="CHAT_WA" {{ request('sosmed') == 'CHAT_WA' ? 'selected' : '' }}>
                                    Chat WhatsApp
                                </option>
                                <option value="INBOX_FB" {{ request('sosmed') == 'INBOX_FB' ? 'selected' : '' }}>
                                    Inbox Facebook
                                </option>
                                <option value="MRKT_PLACE_FB"
                                    {{ request('sosmed') == 'MRKT_PLACE_FB' ? 'selected' : '' }}>
                                    Marketplace Facebook
                                </option>
                                <option value="TIKTOK" {{ request('sosmed') == 'TIKTOK' ? 'selected' : '' }}>
                                    TikTok
                                </option>
                            </select>
                        </div>

                        {{-- SITUASI --}}
                        <div>
                            <select name="situasi" onchange="this.form.submit()"
                                class="px-4 py-2 pr-10 border rounded-lg">
                                <option value="">Semua Situasi</option>
                                <option value="tdk_merespon"
                                    {{ request('situasi') == 'tdk_merespon' ? 'selected' : '' }}>
                                    Tidak Merespon
                                </option>
                                <option value="merespon" {{ request('situasi') == 'merespon' ? 'selected' : '' }}>
                                    Merespon
                                </option>
                                <option value="tertarik" {{ request('situasi') == 'tertarik' ? 'selected' : '' }}>
                                    Tertarik
                                </option>
                                <option value="closing" {{ request('situasi') == 'closing' ? 'selected' : '' }}>
                                    Closing
                                </option>
                            </select>
                        </div>

                        {{-- STATUS VALIDASI --}}
                        <div>
                            <select name="validasi" onchange="this.form.submit()"
                                class="px-4 py-2 pr-10 border rounded-lg">
                                <option value="">Semua Status</option>
                                <option value="1" {{ request('validasi') === '1' ? 'selected' : '' }}>
                                    Sudah Validasi
                                </option>
                                <option value="0" {{ request('validasi') === '0' ? 'selected' : '' }}>
                                    Belum Validasi
                                </option>
                            </select>
                        </div>

                        <button type="submit" class="px-4 py-2 text-white bg-teal-600 rounded-lg hover:bg-teal-700">
                            Filter
                        </button>
                    </form>

                    {{-- TABLE --}}
                    <div class="relative w-full overflow-x-auto md:overflow-x-visible">
                        <table class="w-full text-sm min-w-max whitespace-nowrap">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="px-4 py-4 min-w-[50px] text-left text-gray-600 uppercase">
                                        <input type="checkbox" id="selectAll" onclick="toggleSelectAll()"
                                            class="hidden bulk-checkbox">
                                        #
                                    </th>
                                    <th class="px-4 py-4 min-w-[180px] text-left text-gray-600 uppercase">Nama Nasabah
                                    </th>
                                    <th class="px-4 py-4 min-w-[140px] text-left text-gray-600 uppercase">Akun/No Telp
                                    </th>
                                    <th class="px-4 py-4 min-w-[120px] text-left text-gray-600 uppercase">Platform</th>
                                    <th class="px-4 py-4 min-w-[120px] text-left text-gray-600 uppercase">Situasi</th>
                                    <th class="px-4 py-4 min-w-[180px] text-left text-gray-600 uppercase">Keterangan
                                    </th>
                                    <th class="px-4 py-4 min-w-[120px] text-left text-gray-600 uppercase">Validasi</th>
                                    <th class="px-4 py-4 min-w-[100px] text-left text-gray-600 uppercase">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($contacts as $index => $contact)
                                    <tr class="border-t hover:bg-gray-50">
                                        <td class="px-4 py-3">
                                            <input type="checkbox" name="contact_ids[]" value="{{ $contact->id }}"
                                                form="bulkValidateForm" class="hidden bulk-checkbox contact-checkbox"
                                                onchange="updateSelectedCount()"
                                                {{ $contact->validasi_manager ? 'disabled' : '' }}>
                                            <span class="bulk-number">
                                                {{ ($contacts->currentPage() - 1) * $contacts->perPage() + $index + 1 }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 font-medium">{{ $contact->nama_nasabah }}</td>
                                        <td class="px-4 py-3">{{ $contact->akun_or_notelp }}</td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 text-xs text-blue-700 bg-blue-100 rounded">
                                                {{ $contact->sosmed_label }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            @php
                                                $badge = $contact->situasi_badge;
                                            @endphp
                                            <span
                                                class="px-2 py-1 text-{{ $badge['color'] }}-700 bg-{{ $badge['color'] }}-100 rounded">
                                                {{ $badge['text'] }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-xs text-gray-600">
                                            {{ $contact->keterangan ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3">
                                            @if ($contact->validasi_manager)
                                                <span class="px-2 py-1 text-green-700 bg-green-100 rounded">
                                                    ✅ Sudah
                                                </span>
                                            @else
                                                <span class="px-2 py-1 text-orange-700 bg-orange-100 rounded">
                                                    ⏳ Belum
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            @if (!$contact->validasi_manager)
                                                <form action="{{ route('contact90.manager.validate', $contact->id) }}"
                                                    method="POST" class="single-validate-form">
                                                    @csrf
                                                    <button type="submit"
                                                        class="flex items-center gap-1 px-3 py-1 text-sm text-white transition bg-green-600 rounded hover:bg-green-700">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        Validasi
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-xs text-gray-500">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                            Tidak ada kontak yang ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <div class="mt-6">
                            {{ $contacts->withQueryString()->links() }}
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    {{-- SCRIPT BULK VALIDATE --}}
    <script>
        let bulkMode = false;

        function toggleBulkMode() {
            bulkMode = !bulkMode;
            const form = document.getElementById('bulkValidateForm');
            const checkboxes = document.querySelectorAll('.bulk-checkbox');
            const numbers = document.querySelectorAll('.bulk-number');
            const singleForms = document.querySelectorAll('.single-validate-form');

            if (bulkMode) {
                form.classList.remove('hidden');
                checkboxes.forEach(cb => cb.classList.remove('hidden'));
                numbers.forEach(num => num.classList.add('hidden'));
                singleForms.forEach(form => form.classList.add('hidden'));
            } else {
                form.classList.add('hidden');
                checkboxes.forEach(cb => {
                    cb.classList.add('hidden');
                    cb.checked = false;
                });
                numbers.forEach(num => num.classList.remove('hidden'));
                singleForms.forEach(form => form.classList.remove('hidden'));
                updateSelectedCount();
            }
        }

        function toggleSelectAll() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.contact-checkbox:not([disabled])');

            checkboxes.forEach(cb => {
                cb.checked = selectAll.checked;
            });

            updateSelectedCount();
        }

        function updateSelectedCount() {
            const checked = document.querySelectorAll('.contact-checkbox:checked').length;
            document.getElementById('selectedCount').textContent = checked;
            document.getElementById('bulkSubmitBtn').disabled = checked === 0;
        }
    </script>

</x-app-layout>
