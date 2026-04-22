<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Pengguna
            </h2>
            <a href="{{ route('users.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nama Lengkap -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Nama Lengkap <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" 
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500 @error('name') border-red-500 @enderror">
                                @error('name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Username -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Username <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="username" value="{{ old('username', $user->username) }}"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500 @error('username') border-red-500 @enderror">
                                @error('username')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500 @error('email') border-red-500 @enderror">
                                @error('email')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Phone -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Nomor Telepon
                                </label>
                                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500 @error('phone') border-red-500 @enderror">
                                @error('phone')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Role -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Role <span class="text-red-500">*</span>
                                </label>
                                <select name="role"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500 @error('role') border-red-500 @enderror">
                                    <option value="">Pilih Role</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}" 
                                            {{ old('role', $user->roles->first()?->name) == $role->name ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- ===================== CABANG CHECKLIST ===================== --}}
                        @php
                            $selectedBranchIds = old('branches', $user->branches->pluck('id')->toArray());
                        @endphp

                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Cabang <span class="text-red-500">*</span>
                            </label>
                            <p class="text-xs text-gray-400 mb-3">Pilih satu atau lebih cabang yang ditugaskan</p>

                            @error('branches')
                                <p class="text-red-500 text-xs mb-2">{{ $message }}</p>
                            @enderror

                            <!-- Search cabang -->
                            <div class="relative mb-3">
                                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <input type="text" id="branchSearch" placeholder="Cari cabang..."
                                    class="w-full border border-gray-300 rounded-lg pl-9 pr-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                            </div>

                            <!-- Select All -->
                            <div class="flex items-center gap-2 px-3 py-2 mb-2 bg-gray-50 rounded-lg border border-gray-200">
                                <input type="checkbox" id="selectAllBranches"
                                    class="w-4 h-4 rounded border-gray-300 text-teal-600 focus:ring-teal-500 cursor-pointer">
                                <label for="selectAllBranches" class="text-sm font-semibold text-gray-600 cursor-pointer select-none">
                                    Pilih Semua Cabang
                                </label>
                                <span id="selectedCount" class="ml-auto text-xs text-teal-600 font-medium bg-teal-50 px-2 py-0.5 rounded-full">
                                    {{ count($selectedBranchIds) }} dipilih
                                </span>
                            </div>

                            <!-- Daftar Cabang -->
                            <div id="branchList" class="border border-gray-200 rounded-lg divide-y divide-gray-100 max-h-64 overflow-y-auto">
                                @foreach($branches as $branch)
                                    <label class="branch-item flex items-center gap-3 px-4 py-3 hover:bg-teal-50 cursor-pointer transition group"
                                        data-name="{{ strtolower($branch->name) }}">
                                        <input type="checkbox"
                                            name="branches[]"
                                            value="{{ $branch->id }}"
                                            class="branch-checkbox w-4 h-4 rounded border-gray-300 text-teal-600 focus:ring-teal-500"
                                            {{ in_array($branch->id, $selectedBranchIds) ? 'checked' : '' }}>
                                        <div class="flex items-center gap-2 flex-1 min-w-0">
                                            <div class="w-8 h-8 rounded-lg bg-teal-100 flex items-center justify-center flex-shrink-0 group-hover:bg-teal-200 transition">
                                                <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"/>
                                                </svg>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-sm font-medium text-gray-800 truncate">{{ $branch->name }}</p>
                                                <p class="text-xs text-gray-400">Kode: {{ $branch->code }}</p>
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>

                            <!-- Empty state search -->
                            <p id="branchEmptyState" class="hidden text-center text-sm text-gray-400 py-6">
                                Cabang tidak ditemukan
                            </p>
                        </div>

                        {{-- ===================== STATUS MANAGER PER CABANG ===================== --}}
                        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                            <h3 class="font-medium text-gray-700 mb-1">Status Manager per Cabang</h3>
                            <p class="text-sm text-gray-500 mb-4">Tentukan apakah user adalah manager di cabang yang dipilih</p>
                            
                            <div id="branchManagerContainer" class="space-y-3">
                                @foreach($user->branches as $branch)
                                    <div class="branch-manager-item flex items-center justify-between p-3 bg-white rounded-lg border border-gray-200"
                                        data-branch-id="{{ $branch->id }}" data-branch-name="{{ $branch->name }}">
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-lg bg-teal-100 flex items-center justify-center">
                                                <svg class="w-3.5 h-3.5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"/>
                                                </svg>
                                            </div>
                                            <span class="text-sm font-medium text-gray-700">{{ $branch->name }}</span>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" 
                                                name="branch_managers[{{ $branch->id }}]" 
                                                value="1" 
                                                class="sr-only peer" 
                                                {{ old("branch_managers.{$branch->id}", $branch->pivot->is_manager) ? 'checked' : '' }}>
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-teal-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-teal-600"></div>
                                            <span class="ml-3 text-sm font-medium text-gray-700">Manager</span>
                                        </label>
                                    </div>
                                @endforeach

                                @if($user->branches->isEmpty())
                                    <p id="managerEmptyNote" class="text-sm text-gray-400 text-center py-4">
                                        Pilih cabang terlebih dahulu untuk mengatur status manager
                                    </p>
                                @endif
                            </div>
                        </div>

                        <!-- Password Section -->
                        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                            <h3 class="font-medium text-gray-700 mb-1">Ubah Password <span class="text-gray-400 font-normal">(Opsional)</span></h3>
                            <p class="text-sm text-gray-500 mb-4">Kosongkan jika tidak ingin mengubah password</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Password Baru -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Password Baru</label>
                                    <div class="relative">
                                        <input type="password" name="password" id="password"
                                            placeholder="........"
                                            class="w-full border border-gray-300 rounded-lg px-4 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-teal-500 @error('password') border-red-500 @enderror">
                                        <button type="button" onclick="togglePassword('password', 'togglePasswordIcon')" 
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                            <svg id="togglePasswordIcon" class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Minimal 8 karakter</p>
                                    @error('password')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Konfirmasi Password -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password Baru</label>
                                    <div class="relative">
                                        <input type="password" name="password_confirmation" id="password_confirmation"
                                            placeholder="........"
                                            class="w-full border border-gray-300 rounded-lg px-4 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-teal-500 @error('password_confirmation') border-red-500 @enderror">
                                        <button type="button" onclick="togglePassword('password_confirmation', 'togglePasswordConfirmIcon')" 
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                            <svg id="togglePasswordConfirmIcon" class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>
                                    </div>
                                    @error('password_confirmation')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Foto Profil & Preview -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Foto Profil</label>
                                <input type="file" name="profile_photo" id="profile_photo" accept="image/*"
                                    onchange="previewImage(event)"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100">
                                <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, JPEG (Max: 2MB)</p>
                                @error('profile_photo')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Preview</label>
                                <div id="imagePreview" class="w-24 h-24 bg-teal-100 rounded-xl flex items-center justify-center overflow-hidden border-2 border-teal-200">
                                    @if($user->profile_photo)
                                        <img src="{{ asset('storage/' . $user->profile_photo) }}" 
                                            class="w-full h-full object-cover" 
                                            alt="Profile Photo">
                                    @else
                                        <span class="text-teal-600 text-3xl font-bold">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Toggle Aktif -->
                        <div class="mt-6 flex items-center gap-3">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" class="sr-only peer" 
                                    {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-teal-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-teal-600"></div>
                            </label>
                            <span class="font-medium text-gray-700">Aktif</span>
                        </div>

                        <!-- Buttons -->
                        <div class="mt-8 flex justify-end gap-3">
                            <a href="{{ route('users.index') }}" 
                                class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                                Batal
                            </a>
                            <button type="submit" 
                                class="px-6 py-2 bg-teal-500 text-white rounded-lg hover:bg-teal-600 transition flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ======================== PASSWORD TOGGLE ========================
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>`;
            } else {
                input.type = 'password';
                icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`;
            }
        }

        // ======================== IMAGE PREVIEW ========================
        function previewImage(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('imagePreview');
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
                }
                reader.readAsDataURL(file);
            }
        }

        // ======================== BRANCH CHECKLIST ========================
        const checkboxes     = document.querySelectorAll('.branch-checkbox');
        const selectAll      = document.getElementById('selectAllBranches');
        const selectedCount  = document.getElementById('selectedCount');
        const branchSearch   = document.getElementById('branchSearch');
        const branchItems    = document.querySelectorAll('.branch-item');
        const emptyState     = document.getElementById('branchEmptyState');
        const managerContainer = document.getElementById('branchManagerContainer');

        // Existing manager data from server (branch_id => is_manager)
        const existingManagers = @json($user->branches->pluck('pivot.is_manager', 'id'));

        function updateCount() {
            const total = document.querySelectorAll('.branch-checkbox:checked').length;
            selectedCount.textContent = total + ' dipilih';
            // Sync select-all state
            const visible = document.querySelectorAll('.branch-item:not([style*="none"]) .branch-checkbox');
            const visibleChecked = document.querySelectorAll('.branch-item:not([style*="none"]) .branch-checkbox:checked');
            selectAll.indeterminate = visibleChecked.length > 0 && visibleChecked.length < visible.length;
            selectAll.checked = visible.length > 0 && visibleChecked.length === visible.length;
        }

        function syncManagerPanel() {
            const checked = Array.from(document.querySelectorAll('.branch-checkbox:checked'));

            // Remove unchecked branch rows
            document.querySelectorAll('.branch-manager-item').forEach(item => {
                const id = item.dataset.branchId;
                const stillChecked = checked.find(c => c.value == id);
                if (!stillChecked) item.remove();
            });

            // Add newly checked branches
            checked.forEach(cb => {
                const id = cb.value;
                const label = cb.closest('.branch-item');
                const name = label.querySelector('p.text-sm').textContent.trim();

                if (!managerContainer.querySelector(`[data-branch-id="${id}"]`)) {
                    const isManager = existingManagers[id] ?? false;
                    const div = document.createElement('div');
                    div.className = 'branch-manager-item flex items-center justify-between p-3 bg-white rounded-lg border border-gray-200';
                    div.dataset.branchId = id;
                    div.dataset.branchName = name;
                    div.innerHTML = `
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-teal-100 flex items-center justify-center">
                                <svg class="w-3.5 h-3.5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"/>
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-700">${name}</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox"
                                name="branch_managers[${id}]"
                                value="1"
                                class="sr-only peer"
                                ${isManager ? 'checked' : ''}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-teal-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-teal-600"></div>
                            <span class="ml-3 text-sm font-medium text-gray-700">Manager</span>
                        </label>
                    `;
                    managerContainer.appendChild(div);
                }
            });

            // Show/hide empty note
            const emptyNote = document.getElementById('managerEmptyNote');
            const hasItems = managerContainer.querySelectorAll('.branch-manager-item').length > 0;
            if (emptyNote) emptyNote.style.display = hasItems ? 'none' : 'block';
            if (!emptyNote && !hasItems) {
                const p = document.createElement('p');
                p.id = 'managerEmptyNote';
                p.className = 'text-sm text-gray-400 text-center py-4';
                p.textContent = 'Pilih cabang terlebih dahulu untuk mengatur status manager';
                managerContainer.appendChild(p);
            }
        }

        // Per checkbox change
        checkboxes.forEach(cb => {
            cb.addEventListener('change', () => {
                updateCount();
                syncManagerPanel();
            });
        });

        // Select All
        selectAll.addEventListener('change', function() {
            document.querySelectorAll('.branch-item:not([style*="none"]) .branch-checkbox').forEach(cb => {
                cb.checked = this.checked;
            });
            updateCount();
            syncManagerPanel();
        });

        // Search filter
        branchSearch.addEventListener('input', function() {
            const q = this.value.toLowerCase();
            let visibleCount = 0;
            branchItems.forEach(item => {
                const match = item.dataset.name.includes(q);
                item.style.display = match ? '' : 'none';
                if (match) visibleCount++;
            });
            emptyState.classList.toggle('hidden', visibleCount > 0);
            updateCount();
        });

        // Init
        updateCount();
    </script>
</x-app-layout>