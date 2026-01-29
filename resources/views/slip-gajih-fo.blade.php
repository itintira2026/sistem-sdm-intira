<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Dashboard - Slip Gaji Anda
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    {{ $gajihPokok->branchUser->branch->name }} • {{ $gajihPokok->periode }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content - Left Side -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- User Information Card -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Karyawan</h3>
                            <div class="flex items-start gap-4">
                                <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white font-bold text-2xl shadow-lg">
                                    {{ strtoupper(substr($gajihPokok->branchUser->user->name, 0, 2)) }}
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-xl font-bold text-gray-900">{{ $gajihPokok->branchUser->user->name }}</h4>
                                    <p class="text-gray-600 mt-1">{{ $gajihPokok->branchUser->user->email }}</p>
                                    <div class="flex items-center gap-2 mt-3">
                                        @foreach($gajihPokok->branchUser->user->roles as $role)
                                            <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-sm font-medium">
                                                {{ $role->name }}
                                            </span>
                                        @endforeach
                                        @if($gajihPokok->branchUser->is_manager)
                                            <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-sm font-medium">
                                                Manager
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Cabang</label>
                                    <p class="text-gray-900 font-semibold mt-1">{{ $gajihPokok->branchUser->branch->name }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Status</label>
                                    <p class="text-gray-900 font-semibold mt-1">{{ $gajihPokok->branchUser->is_manager ? 'Manager' : 'Staff' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Salary Details Card -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Detail Gaji Pokok & Tunjangan</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                    <span class="text-gray-600 font-medium">Gaji Pokok</span>
                                    <span class="text-gray-900 font-bold">Rp {{ number_format($gajihPokok->amount, 0, ',', '.') }}</span>
                                </div>
                                
                                <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                                    <p class="text-sm font-semibold text-gray-700 mb-2">Tunjangan:</p>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Tunjangan Makan</span>
                                        <span class="text-sm text-gray-900 font-semibold">Rp {{ number_format($gajihPokok->tunjangan_makan, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Tunjangan Transportasi</span>
                                        <span class="text-sm text-gray-900 font-semibold">Rp {{ number_format($gajihPokok->tunjangan_transportasi, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Tunjangan Jabatan</span>
                                        <span class="text-sm text-gray-900 font-semibold">Rp {{ number_format($gajihPokok->tunjangan_jabatan, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Tunjangan Komunikasi</span>
                                        <span class="text-sm text-gray-900 font-semibold">Rp {{ number_format($gajihPokok->tunjangan_komunikasi, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between items-center pt-3 border-t border-gray-200">
                                        <span class="text-sm font-semibold text-gray-700">Total Tunjangan</span>
                                        <span class="text-sm text-blue-600 font-bold">Rp {{ number_format($gajihPokok->total_tunjangan, 0, ',', '.') }}</span>
                                    </div>
                                </div>

                                <div class="flex justify-between items-center py-3 border-t-2 border-gray-300">
                                    <span class="text-gray-700 font-bold">Gaji Kotor (Pokok + Tunjangan)</span>
                                    <span class="text-blue-600 font-bold text-lg">Rp {{ number_format($gajiKotor, 0, ',', '.') }}</span>
                                </div>

                                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                    <span class="text-gray-600 font-medium">Periode</span>
                                    <span class="text-gray-900 font-semibold">{{ $gajihPokok->periode }}</span>
                                </div>
                                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                    <span class="text-gray-600 font-medium">Tanggal Input</span>
                                    <span class="text-gray-900 font-semibold">{{ $gajihPokok->created_at->format('d F Y, H:i') }}</span>
                                </div>
                                <div class="flex justify-between items-center py-3">
                                    <span class="text-gray-600 font-medium">Terakhir Update</span>
                                    <span class="text-gray-900 font-semibold">{{ $gajihPokok->updated_at->format('d F Y, H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Potongan & Tambahan -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Potongan & Tambahan</h3>
                        </div>

                        <div class="p-6 overflow-x-auto">
                            @if($potongans->isEmpty())
                                <div class="text-center py-8">
                                    <p class="text-gray-500">Belum ada potongan atau tambahan untuk periode ini</p>
                                </div>
                            @else
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Divisi</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keterangan</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Nominal</th>
                                        </tr>
                                    </thead>

                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($potongans as $item)
                                            <tr>
                                                <td class="px-4 py-3 text-sm text-gray-900">{{ $item->tanggal->format('d M Y') }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-700">{{ $item->divisi }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-700">{{ $item->keterangan }}</td>
                                                <td class="px-4 py-3">
                                                    @if($item->jenis === 'potongan')
                                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-600">
                                                            Potongan
                                                        </span>
                                                    @else
                                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-600">
                                                            Tambahan
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-sm text-right font-semibold {{ $item->jenis === 'potongan' ? 'text-red-600' : 'text-green-600' }}">
                                                    {{ $item->jenis === 'potongan' ? '-' : '+' }} Rp {{ number_format($item->amount, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    
                                    <tfoot class="bg-gray-50">
                                        <tr>
                                            <td colspan="4" class="px-4 py-3 text-sm font-semibold text-gray-700 text-right">Total Potongan:</td>
                                            <td class="px-4 py-3 text-sm font-bold text-red-600 text-right">- Rp {{ number_format($totalPotongan, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" class="px-4 py-3 text-sm font-semibold text-gray-700 text-right">Total Tambahan:</td>
                                            <td class="px-4 py-3 text-sm font-bold text-green-600 text-right">+ Rp {{ number_format($totalTambahan, 0, ',', '.') }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            @endif
                        </div>
                    </div>

                    <!-- Notes Card -->
                    @if($gajihPokok->keterangan)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900">Keterangan</h3>
                            </div>
                            <div class="p-6">
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-gray-700">{{ $gajihPokok->keterangan }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar - Right Side -->
                <div class="space-y-6">
                    <!-- Amount Card -->
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 overflow-hidden shadow-lg sm:rounded-lg text-white">
                        <div class="p-6">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-sm font-medium opacity-90">Total Gaji Diterima</span>
                            </div>
                            <div class="text-4xl font-bold mb-1">
                                Rp {{ number_format($gajiBersih, 0, ',', '.') }}
                            </div>
                            <p class="text-sm opacity-75">Per Bulan</p>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Statistik</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Status</p>
                                        <p class="font-semibold text-gray-900">{{ $gajihPokok->branchUser->user->is_active ? 'Aktif' : 'Tidak Aktif' }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Cabang</p>
                                        <p class="font-semibold text-gray-900">{{ $gajihPokok->branchUser->branch->name }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Periode</p>
                                        <p class="font-semibold text-gray-900">{{ $gajihPokok->periode }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Riwayat Gaji Pokok -->
                    {{-- <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Riwayat Gaji Pokok</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3">
                                @foreach($riwayatGaji as $index => $riwayat)
                                    <div class="flex items-center justify-between p-4 rounded-lg border {{ $index === 0 ? 'bg-blue-50 border-blue-200' : 'bg-gray-50 border-gray-200' }}">
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $riwayat->periode }}</p>
                                            <p class="text-sm text-gray-600">{{ $index === 0 ? 'Periode Aktif' : ($index === 1 ? 'Periode Sebelumnya' : $index . ' bulan lalu') }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-bold {{ $index === 0 ? 'text-blue-600' : 'text-gray-700' }}">
                                                Rp {{ number_format($riwayat->total_gaji_kotor, 0, ',', '.') }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div> --}}
<!-- Riwayat Gaji Pokok -->
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Riwayat Gaji Pokok</h3>
        <p class="text-sm text-gray-500 mt-1">Klik untuk melihat detail periode</p>
    </div>
    <div class="p-6">
        <div class="space-y-3">
            @foreach($riwayatGaji as $index => $riwayat)
                <a href="{{ route('gaji.fo', ['bulan' => $riwayat->bulan, 'tahun' => $riwayat->tahun]) }}" 
                   class="flex items-center justify-between p-4 rounded-lg border transition-all hover:shadow-md {{ ($riwayat->bulan == $gajihPokok->bulan && $riwayat->tahun == $gajihPokok->tahun) ? 'bg-blue-50 border-blue-300 ring-2 ring-blue-200' : 'bg-gray-50 border-gray-200 hover:border-blue-300' }}">
                    <div>
                        <p class="font-semibold {{ ($riwayat->bulan == $gajihPokok->bulan && $riwayat->tahun == $gajihPokok->tahun) ? 'text-blue-900' : 'text-gray-900' }}">
                            {{ $riwayat->periode }}
                        </p>
                        <p class="text-sm {{ ($riwayat->bulan == $gajihPokok->bulan && $riwayat->tahun == $gajihPokok->tahun) ? 'text-blue-600' : 'text-gray-600' }}">
                            {{ ($riwayat->bulan == $gajihPokok->bulan && $riwayat->tahun == $gajihPokok->tahun) ? 'Periode Aktif' : ($index === 1 ? 'Periode Sebelumnya' : $index . ' bulan lalu') }}
                        </p>
                    </div>
                    <div class="text-right flex items-center gap-2">
                        <div>
                            <p class="font-bold {{ ($riwayat->bulan == $gajihPokok->bulan && $riwayat->tahun == $gajihPokok->tahun) ? 'text-blue-600' : 'text-gray-700' }}">
                                Rp {{ number_format($riwayat->total_gaji_kotor, 0, ',', '.') }}
                            </p>
                        </div>
                        @if($riwayat->bulan == $gajihPokok->bulan && $riwayat->tahun == $gajihPokok->tahun)
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        @else
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</div>
                    <!-- Actions Card -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Aksi</h3>
                        </div>
                        <div class="p-6 space-y-3">
                            <button onclick="window.print()" class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                                Print Slip Gaji
                            </button>
                        </div>
                    </div>

                    <!-- Info Card -->
                    <div class="bg-gradient-to-br from-purple-500 to-purple-600 overflow-hidden shadow-lg sm:rounded-lg text-white">
                        <div class="p-6">
                            <div class="flex items-start gap-3">
                                <svg class="w-6 h-6 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <h4 class="font-semibold mb-1">Informasi</h4>
                                    <p class="text-sm opacity-90">Ini adalah slip gaji Anda untuk periode berjalan. Jika ada pertanyaan, silakan hubungi bagian HR.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
<script>
    // Smooth scroll ke top saat memilih periode
    document.addEventListener('DOMContentLoaded', function() {
        const params = new URLSearchParams(window.location.search);
        if (params.has('bulan') || params.has('tahun')) {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });
</script>
@endpush
</x-app-layout>