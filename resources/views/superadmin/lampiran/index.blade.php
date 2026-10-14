<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">🗂️ Kelola Lampiran</h2>
        <p class="mt-1 text-sm text-gray-500">Lihat ukuran lampiran per rentang tanggal upload. Khusus Superadmin.</p>
    </x-slot>

    <div class="py-8 sm:py-12">
        <div class="max-w-5xl px-4 mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">
                    <ul class="ml-4 list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Filter --}}
            <form method="GET" action="{{ route('superadmin.lampiran.index') }}"
                class="p-4 mb-4 bg-white border border-gray-200 rounded-xl sm:p-5">
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-[1fr_1fr_auto] sm:items-end">
                    <div>
                        <label class="block mb-1 text-xs font-medium text-gray-500">Diupload dari</label>
                        <input type="date" name="from" value="{{ $from }}"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>
                    <div>
                        <label class="block mb-1 text-xs font-medium text-gray-500">Sampai</label>
                        <input type="date" name="to" value="{{ $to }}"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>
                    <button type="submit"
                        class="w-full px-5 py-2 text-sm text-white transition bg-teal-500 rounded-lg sm:w-auto hover:bg-teal-600">
                        Terapkan
                    </button>
                </div>

                <div class="flex flex-wrap items-center gap-2 mt-3 text-xs">
                    <span class="text-gray-400">Pintasan:</span>
                    @foreach ([3 => 'Lebih dari 3 bulan', 6 => 'Lebih dari 6 bulan', 12 => 'Lebih dari 1 tahun'] as $months => $label)
                        <a href="{{ route('superadmin.lampiran.index', ['to' => now()->subMonths($months)->toDateString()]) }}"
                            class="px-2.5 py-1 text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </form>

            {{-- Ringkasan --}}
            <div class="grid grid-cols-1 gap-3 mb-4 sm:grid-cols-3">
                <div class="p-4 bg-white border border-gray-200 rounded-xl">
                    <p class="text-xs text-gray-400">Jumlah file</p>
                    <p class="text-2xl font-bold text-gray-700">{{ number_format($stats->total) }}</p>
                </div>
                <div class="p-4 border border-teal-200 rounded-xl bg-teal-50">
                    <p class="text-xs text-teal-700">Total ukuran</p>
                    <p class="text-2xl font-bold text-teal-700">
                        {{ \App\Models\TaskAttachment::formatBytes($stats->total_size) }}
                    </p>
                    @if ($stats->unknown_size > 0)
                        <p class="mt-0.5 text-[11px] text-teal-600">
                            belum termasuk {{ number_format($stats->unknown_size) }} file lama tanpa data ukuran
                        </p>
                    @endif
                </div>
                <div class="p-4 bg-white border border-gray-200 rounded-xl">
                    <p class="text-xs text-gray-400">Rentang</p>
                    <p class="text-sm font-semibold text-gray-700">
                        {{ $from ? \Carbon\Carbon::parse($from)->format('d M Y') : 'Awal' }}
                        –
                        {{ $to ? \Carbon\Carbon::parse($to)->format('d M Y') : 'Sekarang' }}
                    </p>
                </div>
            </div>

            {{-- Hapus --}}
            <div
                class="flex flex-col gap-3 p-4 mb-4 border border-red-200 rounded-xl bg-red-50 sm:flex-row sm:items-center sm:justify-between sm:p-5">
                <div>
                    <p class="text-sm font-semibold text-red-800">Hapus lampiran di rentang ini</p>
                    <p class="text-xs text-red-700">
                        @if (!$to)
                            Isi tanggal "sampai" dulu dan terapkan filter sebelum bisa menghapus.
                        @elseif ($stats->total === 0)
                            Tidak ada lampiran di rentang ini.
                        @else
                            Penghapusan permanen: baris data dan file fisiknya, tidak bisa dikembalikan.
                        @endif
                    </p>
                </div>

                @if ($to && $stats->total > 0)
                    <x-modal-sheet title="Hapus {{ number_format($stats->total) }} lampiran?">
                        <x-slot:trigger>
                            <button type="button" @click="open = true"
                                class="w-full px-4 py-2 text-sm text-white transition bg-red-600 rounded-lg sm:w-auto hover:bg-red-700">
                                🗑️ Hapus lampiran
                            </button>
                        </x-slot:trigger>

                        <form method="POST" action="{{ route('superadmin.lampiran.destroy-range') }}">
                            @csrf
                            <input type="hidden" name="from" value="{{ $from }}">
                            <input type="hidden" name="to" value="{{ $to }}">

                            <div class="p-3 mb-3 text-xs text-red-700 border border-red-200 rounded-lg bg-red-50">
                                Akan menghapus permanen <strong>{{ number_format($stats->total) }} file</strong>
                                ({{ \App\Models\TaskAttachment::formatBytes($stats->total_size) }})
                                yang diupload
                                {{ $from ? \Carbon\Carbon::parse($from)->format('d M Y') : 'dari awal' }}
                                s/d {{ \Carbon\Carbon::parse($to)->format('d M Y') }}.
                                File tidak bisa dikembalikan.
                            </div>

                            @if ($isRecentRange)
                                <div
                                    class="p-3 mb-3 text-xs text-yellow-800 border border-yellow-300 rounded-lg bg-yellow-50">
                                    ⚠️ Rentang ini mencakup lampiran dalam {{ $recentMonths }} bulan terakhir. Bisa
                                    jadi
                                    masih dibutuhkan sebagai bukti KPI kalau ada sengketa.
                                </div>

                                <label class="flex items-start gap-2 mb-3 text-xs text-gray-700 cursor-pointer">
                                    <input type="checkbox" name="confirm_recent" value="1" required
                                        class="mt-0.5 text-red-600 border-gray-300 rounded focus:ring-red-500">
                                    <span>Saya paham dan tetap ingin menghapus lampiran yang masih baru.</span>
                                </label>
                            @endif

                            <div class="mb-4">
                                <label class="block mb-1 text-xs font-medium text-gray-600">
                                    Alasan <span class="text-red-500">*</span>
                                </label>
                                <textarea name="reason" rows="2" required maxlength="500"
                                    placeholder="misal: pembersihan rutin storage, lampiran lebih dari 1 tahun"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-400">{{ old('reason') }}</textarea>
                            </div>

                            <div class="flex justify-end gap-2">
                                <button type="button" @click="open = false"
                                    class="px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100">
                                    Batal
                                </button>
                                <button type="submit"
                                    class="px-4 py-2 text-sm text-white transition bg-red-600 rounded-lg hover:bg-red-700">
                                    Hapus Permanen
                                </button>
                            </div>
                        </form>
                    </x-modal-sheet>
                @endif
            </div>

            {{-- Daftar --}}
            <div class="overflow-hidden bg-white border border-gray-200 rounded-xl">
                <div class="px-4 py-3 border-b border-gray-100 sm:px-5">
                    <h3 class="text-sm font-semibold text-gray-800">Daftar lampiran</h3>
                </div>

                <div class="divide-y divide-gray-100">
                    @forelse ($attachments as $att)
                        @php
                            $assignee = $att->taskEntry?->taskAssignee;
                            $taskTitle = $assignee?->task?->title ?? '—';
                            $workerName = $assignee?->user?->name ?? '—';
                        @endphp
                        <div
                            class="flex flex-col gap-1 px-4 py-3 sm:flex-row sm:items-center sm:justify-between sm:px-5">
                            <div class="min-w-0">
                                <a href="{{ Storage::url($att->file_path) }}" target="_blank"
                                    class="block text-sm font-medium text-teal-700 truncate hover:underline">
                                    📎 {{ $att->file_name }}
                                </a>
                                <p class="text-xs text-gray-400 truncate">
                                    {{ $taskTitle }} · {{ $workerName }}
                                </p>
                            </div>
                            <div class="flex items-center gap-3 text-xs text-gray-500 shrink-0">
                                <span>{{ $att->created_at->format('d M Y') }}</span>
                                <span class="font-semibold text-gray-700">{{ $att->formattedSize() }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="py-10 text-sm text-center text-gray-400">Tidak ada lampiran di rentang ini.</p>
                    @endforelse
                </div>

                @if ($attachments->hasPages())
                    <div class="px-4 py-3 text-xs border-t border-gray-100 sm:px-5">
                        {{ $attachments->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
