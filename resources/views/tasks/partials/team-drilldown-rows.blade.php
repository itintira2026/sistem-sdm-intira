<div class="divide-y divide-gray-100">
    @forelse ($group['paginator'] as $row)
        @php
            $u = $row['user'];
            $pct = $row['percentage'];
            $color = $pct >= 80 ? 'text-green-600' : ($pct >= 50 ? 'text-yellow-600' : 'text-red-600');
            $roles = $u->getRoleNames();
            [$roleLabel, $roleColor] = match (true) {
                $roles->contains('korwil') => ['Korwil', 'bg-amber-100 text-amber-800'],
                $roles->contains('direktur_utama') => ['Direktur Utama', 'bg-indigo-100 text-indigo-800'],
                $roles->contains('direktur_bisnis') => ['Direktur Bisnis', 'bg-indigo-100 text-indigo-800'],
                $roles->contains('direktur_operasional') => ['Direktur Operasional', 'bg-indigo-100 text-indigo-800'],
                $roles->contains('ketua') => ['Ketua', 'bg-indigo-100 text-indigo-800'],
                $roles->contains('kadiv') => ['Kadiv', 'bg-blue-100 text-blue-800'],
                $roles->contains('manager') => ['AM', 'bg-sky-100 text-sky-800'],
                $roles->contains('fo') => ['FO', 'bg-teal-100 text-teal-800'],
                $roles->contains('staff') => ['Staff', 'bg-slate-100 text-slate-700'],
                default => ['Anggota', 'bg-gray-100 text-gray-700'],
            };
        @endphp
        <div x-show="@js(strtolower($u->name)).includes(search.toLowerCase())"
            class="flex items-center justify-between gap-3 py-2.5">
            <a href="{{ route('tasks.team-member-detail', ['user' => $u->id, 'month' => $month, 'year' => $year]) }}"
                class="flex items-center flex-1 min-w-0 gap-3">
                <div
                    class="flex items-center justify-center w-10 h-10 overflow-hidden text-xs font-semibold text-gray-600 bg-gray-100 rounded-full shrink-0">
                    @if ($u->profile_photo)
                        <img src="{{ Storage::url($u->profile_photo) }}" alt="" class="object-cover w-full h-full">
                    @else
                        {{ strtoupper(substr($u->name, 0, 2)) }}
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-1.5">
                        <p class="text-sm font-medium text-gray-800 truncate hover:text-teal-600">{{ $u->name }}
                        </p>
                        <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full {{ $roleColor }}">
                            {{ $roleLabel }}
                        </span>
                        @if ($u->visibility_scope === 'preview')
                            <span
                                class="px-2 py-0.5 text-[10px] font-medium text-gray-500 bg-gray-100 rounded-full">Preview</span>
                        @endif
                    </div>
                    @if ($u->division)
                        <p class="text-xs text-gray-400 truncate">{{ $u->division->name }}</p>
                    @endif
                </div>
            </a>
            <span
                class="text-xs px-2 py-0.5 rounded-full shrink-0 {{ $row['weight_ok'] ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                {{ $row['weight_ok'] ? 'Bobot ✔️' : 'bobot ⚠️' }}
            </span>
            <span class="font-bold shrink-0 {{ $color }}">{{ number_format($pct, 1) }}%</span>
        </div>
    @empty
        <p class="py-6 text-sm text-center text-gray-400">Tidak ada data.</p>
    @endforelse
</div>

@if ($group['paginator']->hasPages())
    <div class="pt-3 mt-2 text-xs border-t border-gray-100">
        {{ $group['paginator']->links() }}
    </div>
@endif
