<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">🛠️ Panel Koreksi Data</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto space-y-8 sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="p-4 text-green-700 bg-green-100 rounded-lg">{{ session('success') }}</div>
            @endif

            {{-- Restore Tugas Terhapus --}}
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">🗑️ Tugas Terhapus (Restore)</h3>
                </div>

                @if ($deletedTasks->isEmpty())
                    <p class="py-8 text-sm text-center text-gray-400">Tidak ada tugas yang dihapus.</p>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach ($deletedTasks as $task)
                            <div class="flex items-center justify-between px-6 py-3">
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ $task->title }}</p>
                                    <p class="text-xs text-gray-400">
                                        Dibuat oleh {{ $task->creator->name ?? '—' }} ·
                                        Dihapus {{ $task->deleted_at->format('d M Y H:i') }}
                                    </p>
                                </div>
                                <form action="{{ route('superadmin.koreksi-data.restore', $task->id) }}" method="POST"
                                    onsubmit="return confirm('Pulihkan tugas ini?')">
                                    @csrf
                                    <button type="submit"
                                        class="px-4 py-1.5 text-sm bg-teal-500 text-white rounded-lg hover:bg-teal-600 transition">
                                        ♻️ Pulihkan
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                    <div class="px-6 py-3 border-t border-gray-100">{{ $deletedTasks->links() }}</div>
                @endif
            </div>

            {{-- Cari Entry buat Koreksi (link ke D5) --}}
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">🔍 Cari Entry untuk Dikoreksi</h3>
                    <p class="mt-1 text-xs text-gray-400">Cari nama orang atau judul tugas — hasil diarahkan ke halaman
                        koreksi langsung (Part D5).</p>
                </div>

                <div class="px-6 py-4 border-b border-gray-100">
                    <form method="GET" action="{{ route('superadmin.koreksi-data.index') }}">
                        <input type="text" name="entry_search" value="{{ $entrySearch }}"
                            placeholder="Cari nama atau judul tugas..."
                            class="w-full max-w-sm px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </form>
                </div>

                @if ($entrySearch)
                    @if ($entries->isEmpty())
                        <p class="py-8 text-sm text-center text-gray-400">Tidak ada entry ditemukan untuk
                            "{{ $entrySearch }}".</p>
                    @else
                        <div class="divide-y divide-gray-100">
                            @foreach ($entries as $entry)
                                <div class="flex items-center justify-between px-6 py-3">
                                    <div>
                                        <p class="text-sm font-medium text-gray-800">
                                            {{ $entry->taskAssignee->task->title }}</p>
                                        <p class="text-xs text-gray-400">
                                            {{ $entry->taskAssignee->user->name }}
                                            @if ($entry->entry_date)
                                                · {{ $entry->entry_date->format('d M Y') }}
                                            @endif
                                            · Status: {{ $entry->validation_status ?? 'Self-record' }}
                                        </p>
                                    </div>
                                    <a href="{{ route('task-entries.override.edit', $entry->id) }}"
                                        class="px-4 py-1.5 text-sm bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                                        🛠️ Koreksi
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
