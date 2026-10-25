{{-- resources/views/panduan-sistem/partials/section.blade.php --}}
{{-- State (open, active, toggle, isOpen) datang dari x-data="panduan" di halaman induk --}}
<section id="{{ $section['slug'] }}" data-section
    class="transition-colors duration-200 bg-white border rounded-xl scroll-mt-24 lg:scroll-mt-6"
    :class="active === '{{ $section['slug'] }}' ? 'border-teal-300 shadow-sm' : 'border-gray-200'">

    <button type="button" @click="toggle('{{ $section['slug'] }}')"
        :aria-expanded="isOpen('{{ $section['slug'] }}').toString()" aria-controls="body-{{ $section['slug'] }}"
        class="flex items-center justify-between w-full gap-3 px-4 py-3 text-left rounded-xl sm:px-5 hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500">
        <div class="flex items-center min-w-0 gap-2">
            <h3 class="text-base font-semibold text-gray-800 truncate">{{ $section['title'] }}</h3>
            @if ($section['mode'] === 'context')
                <span class="px-2 py-0.5 text-[11px] text-gray-500 bg-gray-100 rounded-full shrink-0">alur</span>
            @endif
        </div>

        <svg class="w-5 h-5 text-gray-400 transition-transform duration-300 shrink-0 motion-reduce:transition-none"
            :class="isOpen('{{ $section['slug'] }}') ? 'rotate-180 text-teal-600' : ''" viewBox="0 0 20 20"
            fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd"
                d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                clip-rule="evenodd" />
        </svg>
    </button>

    {{-- Trik grid-rows 0fr -> 1fr: animasi tinggi mulus tanpa plugin & tanpa hitung tinggi manual --}}
    <div id="body-{{ $section['slug'] }}"
        class="grid transition-[grid-template-rows] duration-300 ease-in-out motion-reduce:transition-none"
        :class="isOpen('{{ $section['slug'] }}') ? 'grid-rows-[1fr]' : 'grid-rows-[0fr]'"
        :inert="!isOpen('{{ $section['slug'] }}')">
        <div class="min-h-0 overflow-hidden">
            <div class="px-4 pb-5 sm:px-5">
                <div class="pt-3 border-t border-gray-100 panduan-body">
                    @if (view()->exists($section['view']))
                        @include($section['view'])
                    @else
                        <p class="text-gray-400">Bagian ini belum ditulis.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
