<div class="divide-y divide-gray-100">
    @forelse ($group['paginator'] as $row)
        @php
            $u = $row['user'];
            $pct = $row['percentage'];
            $color = $pct >= 80 ? 'text-green-600' : ($pct >= 50 ? 'text-yellow-600' : 'text-red-600');
        @endphp
        <div x-show="@js(strtolower($u->name)).includes(search.toLowerCase())"
            class="flex items-center justify-between gap-3 py-2.5">
            <a href="{{ route('tasks.team-member-detail', ['user' => $u->id, 'month' => $month, 'year' => $year]) }}"
                class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-800 truncate hover:text-teal-600">
                    {{ $u->name }}
                    @if ($u->visibility_scope === 'preview')
                        <span class="ml-1 text-xs font-normal text-gray-400">(preview)</span>
                    @endif
                </p>
                <p class="text-xs text-gray-400 truncate">
                    {{ implode(', ', $u->getRoleNames()->toArray()) }}
                    @if ($u->division)
                        · {{ $u->division->name }}
                    @endif
                </p>
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
