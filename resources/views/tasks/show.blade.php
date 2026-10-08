<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ $task->title }}</h2>
                <p class="mt-1 text-sm text-gray-500">
                    @if ($task->has_weight)
                        Tugas KPI
                    @else
                        Tugas Tambahan (Self-Record)
                    @endif
                    · {{ ucfirst(str_replace('_', ' ', $task->repeat_mode)) }}
                </p>
            </div>
            @if ($canManage)
                <div class="flex gap-2">
                    <a href="{{ route('tasks.edit', $task->id) }}"
                        class="px-4 py-2 text-sm text-white transition bg-yellow-500 rounded-lg hover:bg-yellow-600">
                        ✏️ Edit
                    </a>
                    <form action="{{ route('tasks.destroy', $task->id) }}" method="POST"
                        onsubmit="return confirm('Hapus tugas ini? (soft delete, masih bisa dipulihkan lewat Superadmin)')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-4 py-2 text-sm text-white transition bg-red-500 rounded-lg hover:bg-red-600">
                            🗑️ Hapus
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto space-y-6 sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="p-4 text-green-700 bg-green-100 rounded-lg">{{ session('success') }}</div>
            @endif

            {{-- Info Tugas --}}
            <div class="p-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                @if ($task->description)
                    <p class="mb-4 text-sm text-gray-600">{{ $task->description }}</p>
                @endif

                <div class="grid grid-cols-2 gap-4 text-sm md:grid-cols-4">
                    <div>
                        <p class="text-xs text-gray-400 uppercase">Cara Tracking</p>
                        <p class="font-medium text-gray-700">{{ ucfirst($task->tracking_mode) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase">Satuan</p>
                        <p class="font-medium text-gray-700">{{ $task->unit ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase">Periode</p>
                        <p class="font-medium text-gray-700">
                            @if ($task->period_start && $task->period_end)
                                {{ $task->period_start->format('d M') }} - {{ $task->period_end->format('d M Y') }}
                            @else
                                —
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase">Dibuat oleh</p>
                        <p class="font-medium text-gray-700">{{ $task->creator->name ?? '—' }}</p>
                    </div>
                    @if ($task->division)
                        <div>
                            <p class="text-xs text-gray-400 uppercase">Divisi</p>
                            <p class="font-medium text-gray-700">{{ $task->division->name }}</p>
                        </div>
                    @endif
                    @if ($task->referenceTask)
                        <div class="col-span-2">
                            <p class="text-xs text-gray-400 uppercase">Referensi</p>
                            <a href="{{ route('tasks.show', $task->referenceTask->id) }}"
                                class="font-medium text-teal-600 hover:underline">
                                {{ $task->referenceTask->title }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Assignee & Progress --}}
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">
                        Assignee ({{ $assigneesData->count() }})
                    </h3>
                </div>

                <div class="divide-y divide-gray-100">
                    @foreach ($assigneesData as $data)
                        @php $assignee = $data['assignee']; @endphp
                        <div class="px-6 py-4">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="font-medium text-gray-800">{{ $assignee->user->name }}</p>
                                    <p class="text-xs text-gray-400">
                                        Target: {{ $task->formatValue($assignee->individual_target) }}
                                        @if ($task->has_weight)
                                            · Bobot:
                                            {{ $assignee->weight_percent !== null ? number_format((float) $assignee->weight_percent, 2) . '%' : '⚠️ belum diatur' }}
                                        @endif
                                    </p>
                                    @if ($assignee->isFinalized())
                                        <p class="mt-0.5 text-xs font-semibold text-blue-600">
                                            🧊 {{ $assignee->isRemoved() ? 'Non-aktif' : 'Dibekukan' }}
                                            sejak {{ $assignee->finalized_at->format('d M Y') }}
                                        </p>
                                    @endif
                                </div>
                                <div class="flex items-center gap-3">
                                    <span
                                        class="text-lg font-bold text-teal-600">{{ number_format($data['percentage'], 1) }}%</span>
                                </div>
                            </div>

                            <x-task-daily-detail :assignee="$assignee" :show-fill-link="true" />
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
