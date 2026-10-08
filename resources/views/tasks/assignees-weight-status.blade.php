<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            ⚖️ Atur Bobot KPI — {{ $task->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="p-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">

                @if (session('success'))
                    <div class="p-4 mb-6 text-green-700 bg-green-100 rounded-lg">{{ session('success') }}</div>
                @endif

                <p class="mb-4 text-sm text-gray-500">
                    Tugas ini di-assign ke {{ $assignees->count() }} orang. Klik "Set Bobot" untuk masing-masing —
                    setiap orang punya portofolio tugas berbeda, jadi diatur satu-satu.
                </p>

                <div class="border border-gray-200 divide-y divide-gray-100 rounded-lg">
                    @foreach ($assignees as $assignee)
                        <div class="flex items-center justify-between px-4 py-3">
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $assignee->user->name }}</p>
                                <p class="text-xs text-gray-400">
                                    @if ($assignee->weight_percent !== null)
                                        Bobot: {{ $assignee->weight_percent }}%
                                    @else
                                        <span class="text-yellow-600">⚠️ Belum diatur</span>
                                    @endif
                                </p>
                            </div>
                            <a href="{{ route('tasks.weight-reinput', $assignee->user_id) }}"
                                class="px-4 py-1.5 text-sm bg-teal-500 text-white rounded-lg hover:bg-teal-600 transition">
                                Set Bobot
                            </a>
                        </div>
                    @endforeach
                </div>

                <div class="flex justify-end mt-6">
                    <a href="{{ route('dashboard') }}"
                        class="px-6 py-2 text-gray-700 transition bg-gray-200 rounded-lg hover:bg-gray-300">
                        Selesai
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
