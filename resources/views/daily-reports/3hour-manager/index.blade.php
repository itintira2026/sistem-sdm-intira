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

            {{-- <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="grid items-center justify-between grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                                Import Tombol
                            </h2>
                            <p class="mt-1 text-sm text-gray-500">
                                Jadwal & Align Satu
                            </p>
                        </div>
                    </div>
                </div>
            </div> --}}
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

            

            <!-- Time Slots Card -->
            <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="mb-4 text-lg font-semibold text-gray-800">Pilih Waktu</h3>
                    
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div class="p-4 transition rounded-lg cursor-pointer bg-teal-50 hover:bg-teal-100 time-slot active" onclick="selectTime(this)" data-time="12:00">
                            <div class="text-center">
                                <p class="text-sm font-medium text-teal-700">Shift Pagi</p>
                                <p class="mt-2 text-3xl font-bold text-teal-800">12:00</p>
                            </div>
                        </div>
                        
                        <div class="p-4 transition bg-gray-100 rounded-lg cursor-pointer hover:bg-teal-100 time-slot" onclick="selectTime(this)" data-time="16:00">
                            <div class="text-center">
                                <p class="text-sm font-medium text-gray-700">Shift Tengah</p>
                                <p class="mt-2 text-3xl font-bold text-gray-800">16:00</p>
                            </div>
                        </div>
                        
                        <div class="p-4 transition bg-gray-100 rounded-lg cursor-pointer hover:bg-teal-100 time-slot" onclick="selectTime(this)" data-time="21:00">
                            <div class="text-center">
                                <p class="text-sm font-medium text-gray-700">Shift Siang</p>
                                <p class="mt-2 text-3xl font-bold text-gray-800">21:00</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Import Files Card -->
            <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="mb-6 text-lg font-semibold text-gray-800">Import Data</h3>
                    
                    <!-- Import Rahn -->
                    <div class="p-4 mb-4 transition rounded-lg bg-gray-50 hover:bg-gray-100">
                        <div class="flex flex-col items-start gap-4 md:flex-row md:items-center">
                            <label class="text-sm font-medium text-gray-700 md:w-40">
                                Import Rahn
                            </label>
                            <div class="flex-1 w-full">
                                <div class="flex items-center justify-between w-full gap-3 p-3 bg-white border-2 border-gray-300 border-dashed rounded-lg cursor-pointer hover:border-teal-500 hover:bg-teal-50" onclick="document.getElementById('file-rahn').click()">
                                    <div class="flex items-center flex-1 gap-2">
                                        <span class="flex-shrink-0 w-2 h-2 bg-gray-400 rounded-full status-dot" id="dot-rahn"></span>
                                        <span class="text-sm text-gray-600 file-name" id="name-rahn">Belum ada file dipilih</span>
                                    </div>
                                    <button type="button" class="px-4 py-2 text-sm font-medium text-white transition bg-teal-600 rounded-lg hover:bg-teal-700">
                                        Import File
                                    </button>
                                </div>
                                <input type="file" id="file-rahn" class="hidden" onchange="handleFileSelect(event, 'rahn')" accept=".xlsx,.xls,.csv">
                            </div>
                        </div>
                    </div>

                    <!-- Import Nasabah -->
                    <div class="p-4 mb-4 transition rounded-lg bg-gray-50 hover:bg-gray-100">
                        <div class="flex flex-col items-start gap-4 md:flex-row md:items-center">
                            <label class="text-sm font-medium text-gray-700 md:w-40">
                                Import Nasabah
                            </label>
                            <div class="flex-1 w-full">
                                <div class="flex items-center justify-between w-full gap-3 p-3 bg-white border-2 border-gray-300 border-dashed rounded-lg cursor-pointer hover:border-teal-500 hover:bg-teal-50" onclick="document.getElementById('file-nasabah').click()">
                                    <div class="flex items-center flex-1 gap-2">
                                        <span class="flex-shrink-0 w-2 h-2 bg-gray-400 rounded-full status-dot" id="dot-nasabah"></span>
                                        <span class="text-sm text-gray-600 file-name" id="name-nasabah">Belum ada file dipilih</span>
                                    </div>
                                    <button type="button" class="px-4 py-2 text-sm font-medium text-white transition bg-teal-600 rounded-lg hover:bg-teal-700">
                                        Import File
                                    </button>
                                </div>
                                <input type="file" id="file-nasabah" class="hidden" onchange="handleFileSelect(event, 'nasabah')" accept=".xlsx,.xls,.csv">
                            </div>
                        </div>
                    </div>

                    <!-- Import Rekening -->
                    <div class="p-4 transition rounded-lg bg-gray-50 hover:bg-gray-100">
                        <div class="flex flex-col items-start gap-4 md:flex-row md:items-center">
                            <label class="text-sm font-medium text-gray-700 md:w-40">
                                Import Rekening
                            </label>
                            <div class="flex-1 w-full">
                                <div class="flex items-center justify-between w-full gap-3 p-3 bg-white border-2 border-gray-300 border-dashed rounded-lg cursor-pointer hover:border-teal-500 hover:bg-teal-50" onclick="document.getElementById('file-rekening').click()">
                                    <div class="flex items-center flex-1 gap-2">
                                        <span class="flex-shrink-0 w-2 h-2 bg-gray-400 rounded-full status-dot" id="dot-rekening"></span>
                                        <span class="text-sm text-gray-600 file-name" id="name-rekening">Belum ada file dipilih</span>
                                    </div>
                                    <button type="button" class="px-4 py-2 text-sm font-medium text-white transition bg-teal-600 rounded-lg hover:bg-teal-700">
                                        Import File
                                    </button>
                                </div>
                                <input type="file" id="file-rekening" class="hidden" onchange="handleFileSelect(event, 'rekening')" accept=".xlsx,.xls,.csv">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Keterangan Card -->
            <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <label class="block mb-3 text-sm font-medium text-gray-700">
                        Keterangan
                    </label>
                    <textarea 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg resize-vertical focus:ring-2 focus:ring-teal-500 focus:border-teal-500" 
                        rows="8"
                        placeholder="Masukkan keterangan di sini..."></textarea>
                    
                    <!-- Action Buttons -->
                    <div class="flex justify-end gap-3 mt-4">
                        <button type="button" class="px-6 py-2 text-sm font-medium text-gray-700 transition bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="button" class="px-6 py-2 text-sm font-medium text-white transition bg-teal-600 rounded-lg hover:bg-teal-700">
                            Simpan Laporan
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

     <script>
        function selectTime(element) {
            // Remove active class from all time slots
            document.querySelectorAll('.time-slot').forEach(slot => {
                slot.classList.remove('active', 'bg-teal-50');
                slot.classList.add('bg-gray-100');
                
                const label = slot.querySelector('p:first-child');
                const time = slot.querySelector('p:last-child');
                label.classList.remove('text-teal-700');
                label.classList.add('text-gray-700');
                time.classList.remove('text-teal-800');
                time.classList.add('text-gray-800');
            });
            
            // Add active class to clicked slot
            element.classList.add('active', 'bg-teal-50');
            element.classList.remove('bg-gray-100');
            
            const label = element.querySelector('p:first-child');
            const time = element.querySelector('p:last-child');
            label.classList.add('text-teal-700');
            label.classList.remove('text-gray-700');
            time.classList.add('text-teal-800');
            time.classList.remove('text-gray-800');
            
            console.log('Selected time:', element.dataset.time);
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