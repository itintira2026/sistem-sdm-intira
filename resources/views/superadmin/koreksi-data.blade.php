<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">🛠️ Panel Koreksi Data</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto space-y-8 sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="p-4 text-green-700 bg-green-100 rounded-lg">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="p-4 text-red-700 bg-red-100 rounded-lg">{{ session('error') }}</div>
            @endif

            {{-- ===== Tugas Terhapus ===== --}}
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="flex flex-wrap items-center justify-between gap-3 px-6 py-4 border-b border-gray-100">
                    <div>
                        <h3 class="font-semibold text-gray-800">🗑️ Tugas Terhapus</h3>
                        <p class="mt-1 text-xs text-gray-400">{{ $deletedTasks->total() }} tugas bisa dipulihkan.</p>
                    </div>
                    <form method="GET" action="{{ route('superadmin.koreksi-data.index') }}" class="flex gap-2">
                        <input type="text" name="deleted_search" value="{{ $deletedSearch }}"
                            placeholder="Cari judul tugas..."
                            class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                        <button type="submit"
                            class="px-3 py-1.5 text-sm text-white transition bg-teal-500 rounded-lg hover:bg-teal-600">Cari</button>
                        @if ($deletedSearch)
                            <a href="{{ route('superadmin.koreksi-data.index') }}"
                                class="px-3 py-1.5 text-sm text-gray-600 transition border border-gray-300 rounded-lg hover:bg-gray-50">Reset</a>
                        @endif
                    </form>
                </div>

                @if ($deletedTasks->isEmpty())
                    <p class="py-8 text-sm text-center text-gray-400">
                        {{ $deletedSearch ? 'Tidak ada tugas terhapus yang cocok.' : 'Tidak ada tugas yang dihapus.' }}
                    </p>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach ($deletedTasks as $task)
                            <div class="flex items-center justify-between gap-4 px-6 py-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-800 truncate">{{ $task->title }}</p>
                                    <p class="text-xs text-gray-400">
                                        Dibuat oleh {{ $task->creator->name ?? '—' }} ·
                                        {{ $task->assignees_count }} assignee ·
                                        Dihapus {{ $task->deleted_at->format('d M Y H:i') }}
                                    </p>
                                </div>
                                <form action="{{ route('superadmin.koreksi-data.restore', $task->id) }}" method="POST"
                                    onsubmit="return confirm('Pulihkan tugas &quot;{{ addslashes($task->title) }}&quot;?')">
                                    @csrf
                                    <button type="submit"
                                        class="px-4 py-1.5 text-sm bg-teal-500 text-white rounded-lg hover:bg-teal-600 transition whitespace-nowrap">
                                        ♻️ Pulihkan
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                    @if ($deletedTasks->hasPages())
                        <div class="px-6 py-3 border-t border-gray-100">{{ $deletedTasks->links() }}</div>
                    @endif
                @endif
            </div>

            {{-- ===== Cari Entry ===== --}}
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">🔍 Cari Entry untuk Dikoreksi</h3>
                    <p class="mt-1 text-xs text-gray-400">
                        {{ $hasFilter ? 'Hasil pencarian' : 'Entry KPI aktif terbaru' }} · {{ $entries->total() }}
                        entry.
                        Klik Koreksi untuk membuka halaman override.
                    </p>
                </div>

                <div class="px-6 py-4 border-b border-gray-100">
                    <form method="GET" action="{{ route('superadmin.koreksi-data.index') }}"
                        class="grid gap-3 sm:grid-cols-2">
                        <input type="text" name="entry_search" value="{{ $filters['q'] }}"
                            placeholder="Nama orang atau judul tugas..."
                            class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg sm:col-span-2 focus:outline-none focus:ring-2 focus:ring-teal-500">

                        <select name="status"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                            <option value="">Semua status</option>
                            <option value="self" @selected($filters['status'] === 'self')>Self-record</option>
                            @foreach ($statusOptions as $status)
                                <option value="{{ $status }}" @selected($filters['status'] === $status)>{{ $status }}
                                </option>
                            @endforeach
                        </select>

                        <select name="task_type"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                            <option value="kpi" @selected($filters['task_type'] === 'kpi')>Tugas KPI</option>
                            <option value="additional" @selected($filters['task_type'] === 'additional')>Tugas Tambahan</option>
                            <option value="all" @selected($filters['task_type'] === 'all')>Semua jenis tugas</option>
                        </select>

                        <select name="frozen_mode"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                            <option value="hide" @selected($filters['frozen_mode'] === 'hide')>Sembunyikan yang freeze</option>
                            <option value="include" @selected($filters['frozen_mode'] === 'include')>Termasuk yang freeze</option>
                        </select>

                        <select name="date_basis"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                            <option value="entry_date" @selected($filters['date_basis'] === 'entry_date')>Filter tanggal entry</option>
                            <option value="created_at" @selected($filters['date_basis'] === 'created_at')>Filter tanggal dibuat</option>
                        </select>

                        <select name="date_mode"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                            <option value="all" @selected($filters['date_mode'] === 'all')>Semua entry</option>
                            <option value="with_date" @selected($filters['date_mode'] === 'with_date')>Entry dengan tanggal</option>
                            <option value="without_date" @selected($filters['date_mode'] === 'without_date')>Entry tanpa tanggal</option>
                        </select>

                        <div class="flex items-center gap-2">
                            <input type="date" name="date_from" value="{{ $filters['from'] }}"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                            <span class="text-xs text-gray-400">s/d</span>
                            <input type="date" name="date_to" value="{{ $filters['to'] }}"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>

                        <div class="flex gap-2 sm:col-span-2">
                            <button type="submit"
                                class="px-4 py-2 text-sm text-white transition bg-teal-500 rounded-lg hover:bg-teal-600">Cari</button>
                            @if ($hasFilter)
                                <a href="{{ route('superadmin.koreksi-data.index') }}"
                                    class="px-4 py-2 text-sm text-gray-600 transition border border-gray-300 rounded-lg hover:bg-gray-50">Reset</a>
                            @endif
                        </div>
                    </form>
                </div>

                @if ($entries->isEmpty())
                    <p class="py-8 text-sm text-center text-gray-400">
                        {{ $hasFilter ? 'Tidak ada entry yang cocok dengan filter.' : 'Belum ada entry.' }}
                    </p>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach ($entries as $entry)
                            @php
                                $assignee = $entry->taskAssignee;
                                $task = $assignee?->task;
                            @endphp
                            <div class="flex items-center justify-between gap-4 px-6 py-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-800 truncate">
                                        {{ $task?->title ?? '—' }}
                                        <span
                                            class="ml-1 px-2 py-0.5 text-xs rounded-full {{ $task?->has_weight ? 'text-blue-700 bg-blue-50' : 'text-amber-700 bg-amber-50' }}">
                                            {{ $task?->has_weight ? 'Tugas KPI' : 'Tugas Tambahan' }}
                                        </span>
                                        @if ($task?->trashed())
                                            <span
                                                class="ml-1 px-2 py-0.5 text-xs text-red-600 bg-red-50 rounded-full">terhapus</span>
                                        @endif
                                        @if ($assignee?->isFinalized())
                                            <span
                                                class="ml-1 px-2 py-0.5 text-xs text-gray-600 bg-gray-100 rounded-full">freeze</span>
                                        @endif
                                    </p>
                                    <p class="flex flex-wrap items-center text-xs text-gray-400 gap-x-1">
                                        <span>{{ $entry->taskAssignee?->user?->name ?? '—' }}</span>
                                        @if ($entry->entry_date)
                                            <span>· Tanggal entry {{ $entry->entry_date->format('d M Y') }}</span>
                                        @else
                                            <span>· Tanpa tanggal entry</span>
                                        @endif
                                        <span>· Dibuat {{ $entry->created_at?->format('d M Y H:i') ?? '—' }}</span>
                                        <span>·</span>
                                        <span class="px-2 py-0.5 text-xs text-gray-600 bg-gray-100 rounded-full">
                                            {{ $entry->validation_status ?? 'Self-record' }}
                                        </span>
                                    </p>
                                </div>
                                <a href="{{ route('task-entries.override.edit', $entry->id) }}"
                                    class="px-4 py-1.5 text-sm bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition whitespace-nowrap">
                                    🛠️ Koreksi
                                </a>
                            </div>
                        @endforeach
                    </div>
                    @if ($entries->hasPages())
                        <div class="px-6 py-3 border-t border-gray-100">{{ $entries->links() }}</div>
                    @endif
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
