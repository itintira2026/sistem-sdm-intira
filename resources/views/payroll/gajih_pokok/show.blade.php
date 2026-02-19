<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Detail Gaji - {{ $gajihPokok->branchUser->user->name }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $gajihPokok->branchUser->branch->name }} • {{ $gajihPokok->periode }}
                </p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('gaji-pokok.detail', ['branch' => $gajihPokok->branchUser->branch_id, 'bulan' => $gajihPokok->bulan, 'tahun' => $gajihPokok->tahun]) }}"
                    class="flex items-center gap-2 px-4 py-2 text-white transition bg-gray-500 rounded-lg hover:bg-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Main Content - Left Side -->
                <div class="space-y-6 lg:col-span-2">
                    <!-- User Information Card -->
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="mb-4 text-lg font-semibold text-gray-900">Informasi Karyawan</h3>
                            <div class="flex items-start gap-4">
                                <div
                                    class="flex items-center justify-center w-20 h-20 text-2xl font-bold text-white rounded-full shadow-lg bg-gradient-to-br from-blue-500 to-blue-600">
                                    {{ strtoupper(substr($gajihPokok->branchUser->user->name, 0, 2)) }}
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-xl font-bold text-gray-900">{{ $gajihPokok->branchUser->user->name
                                        }}</h4>
                                    <p class="mt-1 text-gray-600">{{ $gajihPokok->branchUser->user->email }}</p>
                                    <div class="flex items-center gap-2 mt-3">
                                        @foreach($gajihPokok->branchUser->user->roles as $role)
                                        <span
                                            class="px-3 py-1 text-sm font-medium text-purple-700 bg-purple-100 rounded-full">
                                            {{ $role->name }}
                                        </span>
                                        @endforeach
                                        @if($gajihPokok->branchUser->is_manager)
                                        <span
                                            class="px-3 py-1 text-sm font-medium text-amber-700 bg-amber-100 rounded-full">
                                            Manager
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-6">
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Cabang</label>
                                    <p class="mt-1 font-semibold text-gray-900">{{ $gajihPokok->branchUser->branch->name
                                        }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Status</label>
                                    <p class="mt-1 font-semibold text-gray-900">
                                        {{ $gajihPokok->golongan ? $gajihPokok->golongan : 'N/A' }}
                                    </p>

                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Salary Details Card -->
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Detail Gaji Pokok & Tunjangan</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                    <span class="font-medium text-gray-600">Gaji Pokok</span>
                                    <span class="font-bold text-gray-900">Rp {{ number_format($gajihPokok->amount, 0,
                                        ',', '.') }}</span>
                                </div>

                                <div class="p-4 space-y-3 bg-gray-50 rounded-lg">
                                    <p class="mb-2 text-sm font-semibold text-gray-700">Tunjangan:</p>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">Tunjangan Makan</span>
                                        <span class="text-sm font-semibold text-gray-900">Rp {{
                                            number_format($gajihPokok->tunjangan_makan, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">Tunjangan Transportasi</span>
                                        <span class="text-sm font-semibold text-gray-900">Rp {{
                                            number_format($gajihPokok->tunjangan_transportasi, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">Tunjangan Jabatan</span>
                                        <span class="text-sm font-semibold text-gray-900">Rp {{
                                            number_format($gajihPokok->tunjangan_jabatan, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">Tunjangan Komunikasi</span>
                                        <span class="text-sm font-semibold text-gray-900">Rp {{
                                            number_format($gajihPokok->tunjangan_komunikasi, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex items-center justify-between pt-3 border-t border-gray-200">
                                        <span class="text-sm font-semibold text-gray-700">Total Tunjangan</span>
                                        <span class="text-sm font-bold text-blue-600">Rp {{
                                            number_format($gajihPokok->total_tunjangan, 0, ',', '.') }}</span>
                                    </div>
                                    {{-- <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">Potongan BPJS Ketenagakerjaan</span>
                                        <span class="text-sm font-semibold text-gray-900">Rp {{
                                            number_format($gajihPokok->ptg_bpjs_ketenagakerjaan, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">Potongan BPJS Kesehatan</span>
                                        <span class="text-sm font-semibold text-gray-900">Rp {{
                                            number_format($gajihPokok->ptg_bpjs_kesehatan, 0, ',', '.') }}</span>
                                    </div> --}}
                                </div>

                                <div class="p-4 space-y-3 bg-gray-50 rounded-lg">
                                    <p class="mb-2 text-sm font-semibold text-gray-700">Revenue:</p>

                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">Bonus Revenue</span>
                                        <span class="text-sm font-semibold text-gray-900">Rp {{
                                            number_format($gajihPokok->total_revenue, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">persentasi Revenue</span>
                                        <span class="text-sm font-semibold text-gray-900">{{
                                            $gajihPokok->persentase_revenue}}%</span>
                                    </div>
                                    <div class="flex items-center justify-between pt-3 border-t border-gray-200">
                                        <span class="text-sm font-semibold text-gray-700">Total Revenue</span>
                                        <span class="text-sm font-bold text-blue-600">Rp {{
                                            number_format($gajihPokok->bonus_revenue, 0, ',', '.') }}</span>
                                    </div>
                                </div>

                                <div class="p-4 space-y-3 bg-gray-50 rounded-lg">
                                    <p class="mb-2 text-sm font-semibold text-gray-700">KPI:</p>

                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">Bonus KPI</span>
                                        <span class="text-sm font-semibold text-gray-900">Rp {{
                                            number_format($gajihPokok->total_kpi, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">persentasi KPI</span>
                                        <span class="text-sm font-semibold text-gray-900">{{
                                            $gajihPokok->persentase_kpi}}%</span>
                                    </div>
                                    <div class="flex items-center justify-between pt-3 border-t border-gray-200">
                                        <span class="text-sm font-semibold text-gray-700">Total KPI</span>
                                        <span class="text-sm font-bold text-red-600">Rp {{
                                            number_format($gajihPokok->bonus_kpi, 0, ',', '.') }}</span>
                                    </div>
                                </div>

                                <div class="p-4 space-y-3 bg-gray-50 rounded-lg">
                                    <p class="mb-2 text-sm font-semibold text-gray-700">Simpanan:</p>


                                    <div class="flex items-center justify-between pt-3 border-t border-gray-200">
                                        <span class="text-sm font-semibold text-gray-700">Total Simpanan</span>
                                        <span class="text-sm font-bold text-red-600">Rp {{
                                            number_format($gajihPokok->simpanan, 0, ',', '.') }}</span>
                                    </div>
                                </div>

                                <div class="p-4 space-y-3 bg-gray-50 rounded-lg">
                                    <p class="mb-2 text-sm font-semibold text-gray-700">Potongan Kesehatan:</p>

                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">Potongan BPJS Ketenagakerjaan</span>
                                        <span class="text-sm font-semibold text-gray-900">Rp {{
                                            number_format($gajihPokok->ptg_bpjs_ketenagakerjaan, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">Potongan BPJS Kesehatan</span>
                                        <span class="text-sm font-semibold text-gray-900">Rp {{
                                            number_format($gajihPokok->ptg_bpjs_kesehatan, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex items-center justify-between pt-3 border-t border-gray-200">
                                        <span class="text-sm font-semibold text-gray-700">Total Potongan</span>
                                        <span class="text-sm font-bold text-red-600">Rp {{
                                            number_format($gajihPokok->total_potongan_bpjs, 0, ',', '.') }}</span>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between py-3 border-t-2 border-gray-300">
                                    <span class="font-bold text-gray-700">Gaji Kotor (Pokok + Tunjangan)</span>
                                    <span class="text-lg font-bold text-blue-600">Rp {{ number_format($gajiKotor, 0,
                                        ',', '.') }}</span>
                                </div>

                                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                    <span class="font-medium text-gray-600">Periode</span>
                                    <span class="font-semibold text-gray-900">{{ $gajihPokok->periode }}</span>
                                </div>
                                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                    <span class="font-medium text-gray-600">Tanggal Input</span>
                                    <span class="font-semibold text-gray-900">{{ $gajihPokok->created_at->format('d F Y,
                                        H:i') }}</span>
                                </div>
                                <div class="flex items-center justify-between py-3">
                                    <span class="font-medium text-gray-600">Terakhir Update</span>
                                    <span class="font-semibold text-gray-900">{{ $gajihPokok->updated_at->format('d F Y,
                                        H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Potongan Keterlambatan -->
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="flex items-center justify-between p-6 border-b border-gray-200">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Potongan Keterlambatan</h3>
                                <p class="text-sm text-gray-500">Berdasarkan data presensi</p>
                            </div>
                            <span class="px-3 py-1 text-sm font-semibold text-orange-700 bg-orange-100 rounded-full">
                                {{ count($dataPotonganTerlambat) }} Keterlambatan
                            </span>
                        </div>

                        <div class="p-6 overflow-x-auto">
                            @if(empty($dataPotonganTerlambat))
                            <div class="py-8 text-center">
                                <svg class="w-12 h-12 mx-auto text-green-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="mt-2 text-gray-500">Tidak ada keterlambatan untuk periode ini</p>
                                <p class="mt-1 text-sm text-green-600">Karyawan selalu tepat waktu! 🎉</p>
                            </div>
                            @else
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-xs font-medium text-left text-gray-500 uppercase">
                                            Tanggal</th>
                                        <th class="px-4 py-3 text-xs font-medium text-left text-gray-500 uppercase">Jam
                                            Check In</th>
                                        <th class="px-4 py-3 text-xs font-medium text-left text-gray-500 uppercase">
                                            Keterlambatan</th>
                                        <th class="px-4 py-3 text-xs font-medium text-left text-gray-500 uppercase">
                                            Keterangan</th>
                                        <th class="px-4 py-3 text-xs font-medium text-right text-gray-500 uppercase">
                                            Potongan</th>
                                    </tr>
                                </thead>

                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($dataPotonganTerlambat as $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            {{ Carbon\Carbon::parse($item['tanggal'])->format('d M Y') }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-700">
                                            {{ $item['jam_check_in'] }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <span
                                                class="inline-flex px-2 py-1 text-xs font-semibold text-orange-600 bg-orange-100 rounded-full">
                                                {{ $item['menit_terlambat'] }} menit
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-700">
                                            {{ $item['keterangan'] }}
                                        </td>
                                        <td class="px-4 py-3 text-sm font-semibold text-right text-red-600">
                                            - Rp {{ number_format($item['potongan'], 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>

                                <tfoot class="bg-red-50">
                                    <tr>
                                        <td colspan="4"
                                            class="px-4 py-3 text-sm font-semibold text-right text-gray-700">
                                            Total Potongan Keterlambatan:
                                        </td>
                                        <td class="px-4 py-3 text-sm font-bold text-right text-red-600">
                                            - Rp {{ number_format($totalPotonganTerlambat, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>

                            <!-- Info potongan per menit -->
                            <div class="p-4 mt-4 bg-blue-50 rounded-lg">
                                <div class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div>
                                        <p class="text-sm font-medium text-blue-900">Informasi Potongan</p>
                                        <p class="mt-1 text-xs text-blue-700">
                                            • Shift 1 (08:00 - 12:00): Potongan Rp 15.000/keterlambatan<br>
                                            • Shift 2 (13:00 - 21:00): Potongan Rp 15.000/keterlambatan
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Tabel Potongan & Tambahan dari Model Potongan -->
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="flex items-center justify-between p-6 border-b border-gray-200">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Potongan & Tambahan Lainnya</h3>
                                <p class="text-sm text-gray-500">Bonus, denda, atau adjustment lainnya</p>
                            </div>
                            <a href="{{ route('potongan.create', ['branch' => $gajihPokok->branchUser->branch_id, 'branch_user_id' => $gajihPokok->branchUser->id, 'bulan' => $gajihPokok->bulan, 'tahun' => $gajihPokok->tahun]) }}"
                                class="flex items-center gap-1 px-3 py-2 text-sm font-medium text-white transition bg-teal-600 rounded-lg hover:bg-teal-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                Tambah
                            </a>
                        </div>

                        <div class="p-6 overflow-x-auto">
                            @if($potongans->isEmpty())
                            <div class="py-8 text-center">
                                <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="mt-2 text-gray-500">Belum ada potongan atau tambahan lainnya</p>
                                <p class="mt-1 text-sm text-gray-400">Klik tombol "Tambah" untuk menambahkan data</p>
                            </div>
                            @else
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-xs font-medium text-left text-gray-500 uppercase">
                                            Tanggal</th>
                                        <th class="px-4 py-3 text-xs font-medium text-left text-gray-500 uppercase">
                                            Divisi</th>
                                        <th class="px-4 py-3 text-xs font-medium text-left text-gray-500 uppercase">
                                            Keterangan</th>
                                        <th class="px-4 py-3 text-xs font-medium text-left text-gray-500 uppercase">
                                            Jenis</th>
                                        <th class="px-4 py-3 text-xs font-medium text-right text-gray-500 uppercase">
                                            Nominal</th>
                                    </tr>
                                </thead>

                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($potongans as $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $item->tanggal->format('d M Y')
                                            }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ $item->divisi }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ $item->keterangan }}</td>
                                        <td class="px-4 py-3">
                                            @if($item->jenis === 'potongan')
                                            <span
                                                class="inline-flex px-2 py-1 text-xs font-semibold text-red-600 bg-red-100 rounded-full">
                                                Potongan
                                            </span>
                                            @else
                                            <span
                                                class="inline-flex px-2 py-1 text-xs font-semibold text-green-600 bg-green-100 rounded-full">
                                                Tambahan
                                            </span>
                                            @endif
                                        </td>
                                        <td
                                            class="px-4 py-3 text-sm font-semibold text-right {{ $item->jenis === 'potongan' ? 'text-red-600' : 'text-green-600' }}">
                                            {{ $item->jenis === 'potongan' ? '-' : '+' }} Rp {{
                                            number_format($item->amount, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>

                                <tfoot class="bg-gray-50">
                                    @if($totalPotonganLain > 0)
                                    <tr>
                                        <td colspan="4"
                                            class="px-4 py-3 text-sm font-semibold text-right text-gray-700">Total
                                            Potongan Lain:</td>
                                        <td class="px-4 py-3 text-sm font-bold text-right text-red-600">- Rp {{
                                            number_format($totalPotonganLain, 0, ',', '.') }}</td>
                                    </tr>
                                    @endif
                                    @if($totalTambahan > 0)
                                    <tr>
                                        <td colspan="4"
                                            class="px-4 py-3 text-sm font-semibold text-right text-gray-700">Total
                                            Tambahan:</td>
                                        <td class="px-4 py-3 text-sm font-bold text-right text-green-600">+ Rp {{
                                            number_format($totalTambahan, 0, ',', '.') }}</td>
                                    </tr>
                                    @endif
                                </tfoot>
                            </table>
                            @endif
                        </div>
                    </div>

                    <!-- Summary Card -->
                    <div class="overflow-hidden shadow-sm bg-gradient-to-r from-blue-500 to-blue-600 sm:rounded-lg">
                        <div class="p-6 text-white">
                            <h3 class="mb-4 text-lg font-semibold">Ringkasan Perhitungan Gaji</h3>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between pb-2 border-b border-blue-400">
                                    <span class="font-medium">Gaji Kotor</span>
                                    <span class="font-bold">Rp {{ number_format($gajiKotor, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm">Potongan Keterlambatan</span>
                                    <span class="text-sm font-semibold text-red-200">- Rp {{
                                        number_format($totalPotonganTerlambat, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm">Potongan Lainnya</span>
                                    <span class="text-sm font-semibold text-red-200">- Rp {{
                                        number_format($totalPotonganLain, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex items-center justify-between pb-2 border-b border-blue-400">
                                    <span class="text-sm">Tambahan</span>
                                    <span class="text-sm font-semibold text-green-200">+ Rp {{
                                        number_format($totalTambahan, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex items-center justify-between pt-2">
                                    <span class="text-lg font-bold">Gaji Bersih</span>
                                    <span class="text-2xl font-bold">Rp {{ number_format($gajiBersih, 0, ',', '.')
                                        }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notes Card -->
                    @if($gajihPokok->keterangan)
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Keterangan</h3>
                        </div>
                        <div class="p-6">
                            <div class="p-4 bg-gray-50 rounded-lg">
                                <p class="text-gray-700">{{ $gajihPokok->keterangan }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Sidebar - Right Side -->
                <div class="space-y-6">
                    <!-- Amount Card -->
                    <div
                        class="overflow-hidden text-white shadow-lg bg-gradient-to-br from-blue-500 to-blue-600 sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-sm font-medium opacity-90">Total Gaji Diterima</span>
                            </div>
                            <div class="mb-1 text-4xl font-bold">
                                Rp {{ number_format($gajiBersih, 0, ',', '.') }}
                            </div>
                            <p class="text-sm opacity-75">Per Bulan</p>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Statistik</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center justify-center w-10 h-10 bg-green-100 rounded-lg">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Status</p>
                                        <p class="font-semibold text-gray-900">{{
                                            $gajihPokok->branchUser->user->is_active ? 'Aktif' : 'Tidak Aktif' }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center justify-center w-10 h-10 bg-blue-100 rounded-lg">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Cabang</p>
                                        <p class="font-semibold text-gray-900">{{ $gajihPokok->branchUser->branch->name
                                            }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center justify-center w-10 h-10 bg-purple-100 rounded-lg">
                                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
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
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Riwayat Gaji Pokok</h3>
                            <p class="mt-1 text-sm text-gray-500">Klik untuk melihat detail periode</p>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3">
                                @foreach($riwayatGaji as $index => $riwayat)
                                <a href="{{ route('gaji-pokok.show', ['gajihPokok' => $initialGajihPokok->id, 'bulan' => $riwayat->bulan, 'tahun' => $riwayat->tahun]) }}"
                                    class="block transition-all hover:shadow-md">
                                    <div
                                        class="flex items-center justify-between p-4 border rounded-lg {{ ($riwayat->bulan == $gajihPokok->bulan && $riwayat->tahun == $gajihPokok->tahun) ? 'bg-blue-50 border-blue-300 ring-2 ring-blue-200' : 'bg-gray-50 border-gray-200 hover:border-blue-300' }}">
                                        <div>
                                            <p
                                                class="font-semibold {{ ($riwayat->bulan == $gajihPokok->bulan && $riwayat->tahun == $gajihPokok->tahun) ? 'text-blue-900' : 'text-gray-900' }}">
                                                {{ $riwayat->periode }}
                                            </p>
                                            <p
                                                class="text-sm {{ ($riwayat->bulan == $gajihPokok->bulan && $riwayat->tahun == $gajihPokok->tahun) ? 'text-blue-600' : 'text-gray-600' }}">
                                                {{ ($riwayat->bulan == $gajihPokok->bulan && $riwayat->tahun ==
                                                $gajihPokok->tahun) ? 'Periode Aktif' : ($index === 1 ? 'Periode
                                                Sebelumnya' : $index . ' bulan lalu') }}
                                            </p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            {{-- <div class="text-right">
                                                <p
                                                    class="font-bold {{ ($riwayat->bulan == $gajihPokok->bulan && $riwayat->tahun == $gajihPokok->tahun) ? 'text-blue-600' : 'text-gray-700' }}">
                                                    Rp {{ number_format($riwayat->total_gaji_kotor, 0, ',', '.') }}
                                                </p>
                                            </div> --}}
                                            @if($riwayat->bulan == $gajihPokok->bulan && $riwayat->tahun ==
                                            $gajihPokok->tahun)
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            @else
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7" />
                                            </svg>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Actions Card -->
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Aksi</h3>
                        </div>
                        <div class="p-6 space-y-3">
                            {{-- <button
                                class="flex items-center justify-center w-full gap-2 px-4 py-2 text-white transition bg-green-500 rounded-lg hover:bg-green-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                Export PDF
                            </button> --}}

                            <a href="{{ route('gaji-pokok.export-pdf', ['gajihPokok' => $initialGajihPokok->id, 'bulan' => $gajihPokok->bulan, 'tahun' => $gajihPokok->tahun]) }}"
                                class="flex items-center justify-center w-full gap-2 px-4 py-2 text-white transition bg-green-500 rounded-lg hover:bg-green-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                Export PDF
                            </a>

                            <button
                                class="flex items-center justify-center w-full gap-2 px-4 py-2 text-gray-700 transition bg-gray-100 rounded-lg hover:bg-gray-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                                Print
                            </button>
                        </div>
                    </div>

                    <!-- Info Card -->
                    <div
                        class="overflow-hidden text-white shadow-lg bg-gradient-to-br from-purple-500 to-purple-600 sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-start gap-3">
                                <svg class="flex-shrink-0 w-6 h-6 mt-1" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <h4 class="mb-1 font-semibold">Informasi</h4>
                                    <p class="text-sm opacity-90">Data gaji pokok ini akan digunakan untuk perhitungan
                                        payroll bulan berjalan. Pastikan data sudah benar sebelum periode penggajian.
                                    </p>
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
            if (params.has('bulan') && params.has('tahun')) {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });
    </script>
    @endpush
</x-app-layout>