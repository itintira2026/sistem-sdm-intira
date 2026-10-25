@props(['title' => null])

<div x-data="{ open: false }" {{ $attributes }}>
    {{ $trigger ?? '' }}

    <template x-teleport="body">
        <div x-show="open" x-cloak class="fixed inset-0 z-50" @keydown.escape.window="open = false">
            <div x-show="open" x-transition.opacity @click="open = false" class="absolute inset-0 bg-black/40"></div>

            <div class="absolute inset-x-0 bottom-0 flex justify-center sm:inset-0 sm:items-center sm:p-4">
                <div x-show="open" @click.stop x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative w-full max-w-md max-h-[85vh] overflow-y-auto bg-white rounded-t-2xl sm:rounded-2xl p-5 sm:p-6 shadow-xl">

                    <div class="flex items-center justify-between mb-4">
                        @if ($title)
                            <h3 class="text-sm font-semibold text-gray-800">{{ $title }}</h3>
                        @endif
                        <button type="button" @click="open = false"
                            class="p-1 ml-auto text-gray-400 rounded-lg hover:text-gray-600 hover:bg-gray-100">✕</button>
                    </div>

                    {{ $slot }}
                </div>
            </div>
        </div>
    </template>
</div>
