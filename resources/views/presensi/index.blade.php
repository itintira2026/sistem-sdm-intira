<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Manajemen Cabang
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Kelola data cabang perusahaan Anda
                </p>
            </div>

            <a href="{{ route('branches.create') }}"
                class="flex items-center gap-2 px-4 py-2 text-white transition bg-teal-500 rounded-lg hover:bg-teal-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Cabang
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <h1>hello world</h1>
        </div>
    </div>

    {{-- Dropdown Script --}}
    <script>
        function toggleDropdown(id) {
            document.querySelectorAll('[id^="dropdown-"]').forEach(el => {
                if (el.id !== `dropdown-${id}`) {
                    el.classList.add('hidden');
                }
            });

            document.getElementById(`dropdown-${id}`).classList.toggle('hidden');
        }

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.relative')) {
                document.querySelectorAll('[id^="dropdown-"]').forEach(el => {
                    el.classList.add('hidden');
                });
            }
        });
    </script>

</x-app-layout>
