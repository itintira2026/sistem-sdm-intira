<x-app-layout>
    <x-slot name="header">
        <div class="grid items-center justify-between grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Laporan Per 3 Jam Area Manager
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Wajib 3 laporan per hari (Pada Jam tertera di toleransi lambat mengisi laporan selama 1 jam di jam terakhir)
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            {{-- Alert Success --}}
            @if (session('success'))
                <div class="p-4 mb-6 text-green-700 bg-green-100 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Alert Error --}}
            @if (session('error'))
                <div class="p-4 mb-6 text-red-700 bg-red-100 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Main Container --}}
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                
                {{-- LEFT COLUMN - Form Input --}}
                <div class="lg:col-span-2">
                    <form action="{{ route('daily-reports.3hour-manager.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- Pilih Waktu Card --}}
                        <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="mb-4 text-lg font-semibold text-gray-800">
                                    Pilih Waktu <span class="text-red-500">*</span>
                                </h3>
                                
                                <input type="hidden" name="time_slot" id="time_slot" required>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="p-4 transition border-2 border-gray-300 rounded-lg cursor-pointer time-slot hover:border-teal-500 hover:bg-teal-50" 
                                         onclick="selectTime(this, '12:00')" 
                                         data-time="12:00">
                                        <div class="text-center">
                                            <p class="text-2xl font-bold text-gray-800">12</p>
                                            <p class="mt-1 text-xs text-gray-600">Jam</p>
                                        </div>
                                    </div>
                                    
                                    <div class="p-4 transition border-2 border-gray-300 rounded-lg cursor-pointer time-slot hover:border-teal-500 hover:bg-teal-50" 
                                         onclick="selectTime(this, '16:00')" 
                                         data-time="16:00">
                                        <div class="text-center">
                                            <p class="text-2xl font-bold text-gray-800">16</p>
                                            <p class="mt-1 text-xs text-gray-600">Jam</p>
                                        </div>
                                    </div>
                                    
                                    <div class="col-span-2">
                                        <div class="p-4 transition border-2 border-gray-300 rounded-lg cursor-pointer time-slot hover:border-teal-500 hover:bg-teal-50" 
                                             onclick="selectTime(this, '20:00')" 
                                             data-time="20:00">
                                            <div class="text-center">
                                                <p class="text-2xl font-bold text-gray-800">20</p>
                                                <p class="mt-1 text-xs text-gray-600">Jam</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @error('time_slot')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Tanggal Card --}}
                        <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="flex items-center justify-between p-4 border-2 border-gray-300 rounded-lg">
                                    <div>
                                        <p class="text-sm font-medium text-gray-700">Tanggal</p>
                                        <p class="mt-1 text-2xl font-bold text-gray-800">
                                            {{ now()->format('d-m-Y') }}
                                        </p>
                                    </div>
                                    <div class="text-6xl text-gray-300">
                                        📅
                                    </div>
                                </div>
                            </div>
                        </div>


                        {{-- Action Buttons --}}
                        <div class="flex justify-end gap-3">
                            <a href="{{ route('daily-reports.3hour-manager.index') }}" 
                               class="px-6 py-2 text-sm font-medium text-gray-700 transition bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                                Batal
                            </a>
                            <button type="submit" 
                                    class="px-6 py-2 text-sm font-medium text-white transition bg-teal-600 rounded-lg hover:bg-teal-700">
                                Simpan Laporan
                            </button>
                        </div>

                    </form>
                </div>

                {{-- RIGHT COLUMN - OMS & PT Info --}}
                <div class="lg:col-span-1">
                    
                    {{-- OMS Card --}}
                    <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="mb-4 text-lg font-semibold text-gray-800">OMS</h3>
                            
                            <div class="space-y-3">
                                <div class="p-3 border border-gray-300 rounded-lg">
                                    <p class="text-xs text-gray-500">Cabang</p>
                                    <p class="mt-1 text-sm font-medium text-gray-800">-</p>
                                </div>
                                
                                <div class="p-3 border border-gray-300 rounded-lg">
                                    <p class="text-xs text-gray-500">Omset</p>
                                    <p class="mt-1 text-sm font-medium text-gray-800">Rp 0</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- PT 1 Card --}}
                    <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="mb-4 text-lg font-semibold text-gray-800">PT 1</h3>
                            
                            <div class="space-y-3">
                                <div class="p-3 border border-gray-300 rounded-lg">
                                    <p class="text-xs text-gray-500">Cabang</p>
                                    <p class="mt-1 text-sm font-medium text-gray-800">-</p>
                                </div>
                                
                                <div class="p-3 border border-gray-300 rounded-lg">
                                    <p class="text-xs text-gray-500">Omset</p>
                                    <p class="mt-1 text-sm font-medium text-gray-800">Rp 0</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- PT 2 Card --}}
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="mb-4 text-lg font-semibold text-gray-800">PT 2</h3>
                            
                            <div class="space-y-3">
                                <div class="p-3 border border-gray-300 rounded-lg">
                                    <p class="text-xs text-gray-500">Cabang</p>
                                    <p class="mt-1 text-sm font-medium text-gray-800">-</p>
                                </div>
                                
                                <div class="p-3 border border-gray-300 rounded-lg">
                                    <p class="text-xs text-gray-500">Omset</p>
                                    <p class="mt-1 text-sm font-medium text-gray-800">Rp 0</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>

    <script>
        function selectTime(element, time) {
            // Remove active class from all time slots
            document.querySelectorAll('.time-slot').forEach(slot => {
                slot.classList.remove('border-teal-500', 'bg-teal-50');
                slot.classList.add('border-gray-300');
            });
            
            // Add active class to clicked slot
            element.classList.add('border-teal-500', 'bg-teal-50');
            element.classList.remove('border-gray-300');
            
            // Set hidden input value
            document.getElementById('time_slot').value = time;
            
            console.log('Selected time:', time);
        }

        function handleFileSelect(event, type) {
            const file = event.target.files[0];
            if (file) {
                // Update file name
                const nameElement = document.getElementById('name-' + type);
                nameElement.textContent = file.name;
                nameElement.classList.remove('text-gray-600');
                nameElement.classList.add('text-gray-800', 'font-medium');
                
                // Update status dot
                const dotElement = document.getElementById('dot-' + type);
                dotElement.classList.remove('bg-gray-400');
                dotElement.classList.add('bg-green-500');
                
                console.log('File selected for', type + ':', file.name);
            }
        }
    </script>
</x-app-layout>