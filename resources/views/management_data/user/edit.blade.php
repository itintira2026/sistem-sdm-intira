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

                            <!-- Branches -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Cabang <span class="text-red-500">*</span>
                                </label>
                                <select name="branches[]" multiple
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500 @error('branches') border-red-500 @enderror"
                                    size="5">
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" 
                                            {{ in_array($branch->id, old('branches', $user->branches->pluck('id')->toArray())) ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Tekan Ctrl/Cmd untuk memilih lebih dari satu cabang</p>
                                @error('branches')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Password Section -->
                        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                            <h3 class="font-medium text-gray-700 mb-4">Ubah Password (Opsional)</h3>
                            <p class="text-sm text-gray-500 mb-4">Kosongkan jika tidak ingin mengubah password</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Password Baru -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Password Baru
                                    </label>
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
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Konfirmasi Password Baru
                                    </label>
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
                            <!-- Foto Profil -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Foto Profil
                                </label>
                                <input type="file" name="profile_photo" id="profile_photo" accept="image/*"
                                    onchange="previewImage(event)"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100">
                                <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, JPEG (Max: 2MB)</p>
                                @error('profile_photo')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Preview -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Preview
                                </label>
                                <div id="imagePreview" class="w-24 h-24 bg-blue-100 rounded-lg flex items-center justify-center overflow-hidden">
                                    @if($user->profile_photo)
                                        <img src="{{ asset('storage/' . $user->profile_photo) }}" 
                                            class="w-full h-full object-cover rounded-lg" 
                                            alt="Profile Photo">
                                    @else
                                        <span class="text-blue-500 text-3xl font-bold">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Manager Assignment per Branch -->
                        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                            <h3 class="font-medium text-gray-700 mb-4">Status Manager per Cabang</h3>
                            <p class="text-sm text-gray-500 mb-4">Tentukan apakah user adalah manager di cabang yang dipilih</p>
                            
                            <div id="branchManagerContainer" class="space-y-3">
                                @foreach($user->branches as $branch)
                                    <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-gray-200">
                                        <span class="text-sm font-medium text-gray-700">{{ $branch->name }}</span>
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

        function previewImage(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('imagePreview');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover rounded-lg">`;
                }
                reader.readAsDataURL(file);
            }
        }

        // Update branch manager checkboxes when branches are selected
        document.querySelector('select[name="branches[]"]').addEventListener('change', function() {
            const selectedBranches = Array.from(this.selectedOptions).map(option => ({
                id: option.value,
                name: option.text
            }));
            
            const container = document.getElementById('branchManagerContainer');
            container.innerHTML = '';
            
            selectedBranches.forEach(branch => {
                const div = document.createElement('div');
                div.className = 'flex items-center justify-between p-3 bg-white rounded-lg border border-gray-200';
                div.innerHTML = `
                    <span class="text-sm font-medium text-gray-700">${branch.name}</span>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" 
                            name="branch_managers[${branch.id}]" 
                            value="1" 
                            class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-teal-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-teal-600"></div>
                        <span class="ml-3 text-sm font-medium text-gray-700">Manager</span>
                    </label>
                `;
                container.appendChild(div);
            });
        });
    </script>
</x-app-layout>