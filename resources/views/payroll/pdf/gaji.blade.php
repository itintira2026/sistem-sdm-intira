<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji - {{ $gajihPokok->branchUser->user->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #2563eb;
        }

        .header h1 {
            font-size: 24px;
            color: #1e40af;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 11px;
            color: #666;
        }

        .info-box {
            background-color: #f3f4f6;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }

        .info-row:last-child {
            margin-bottom: 0;
        }

        .info-label {
            display: table-cell;
            width: 35%;
            font-weight: bold;
            color: #555;
        }

        .info-value {
            display: table-cell;
            width: 65%;
            color: #333;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #1e40af;
            margin-top: 25px;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #ddd;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th {
            background-color: #e5e7eb;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
            color: #374151;
            border: 1px solid #d1d5db;
        }

        table td {
            padding: 8px 10px;
            border: 1px solid #e5e7eb;
            font-size: 11px;
        }

        table tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .summary-box {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-top: 30px;
        }

        .summary-row {
            display: table;
            width: 100%;
            margin-bottom: 10px;
            padding-bottom: 10px;
        }

        .summary-row.total {
            border-top: 2px solid rgba(255, 255, 255, 0.3);
            padding-top: 15px;
            margin-top: 5px;
        }

        .summary-label {
            display: table-cell;
            width: 60%;
            font-size: 13px;
        }

        .summary-value {
            display: table-cell;
            width: 40%;
            text-align: right;
            font-weight: bold;
            font-size: 13px;
        }

        .summary-row.total .summary-label {
            font-size: 16px;
            font-weight: bold;
        }

        .summary-row.total .summary-value {
            font-size: 20px;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }

        .badge-green {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-red {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .badge-orange {
            background-color: #fed7aa;
            color: #92400e;
        }

        .amount-positive {
            color: #059669;
            font-weight: bold;
        }

        .amount-negative {
            color: #dc2626;
            font-weight: bold;
        }

        .detail-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .detail-row {
            display: table-row;
        }

        .detail-cell {
            display: table-cell;
            padding: 8px 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .detail-cell.label {
            width: 60%;
            color: #6b7280;
        }

        .detail-cell.value {
            width: 40%;
            text-align: right;
            font-weight: bold;
        }

        .highlight-box {
            background-color: #eff6ff;
            border-left: 4px solid #2563eb;
            padding: 12px;
            margin: 15px 0;
        }

        .no-data {
            text-align: center;
            padding: 30px;
            color: #9ca3af;
            font-style: italic;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>SLIP GAJI KARYAWAN</h1>
        <p>Periode: {{ $gajihPokok->periode }}</p>
        <p style="margin-top: 5px;">Dicetak pada: {{ $tanggalCetak }}</p>
    </div>

    <!-- Informasi Karyawan -->
    <div class="info-box">
        <div class="info-row">
            <div class="info-label">Nama Karyawan:</div>
            <div class="info-value">{{ $gajihPokok->branchUser->user->name }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Email:</div>
            <div class="info-value">{{ $gajihPokok->branchUser->user->email }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Cabang:</div>
            <div class="info-value">{{ $gajihPokok->branchUser->branch->name }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Jabatan:</div>
            <div class="info-value">
                @foreach($gajihPokok->branchUser->user->roles as $role)
                    {{ $role->name }}{{ !$loop->last ? ', ' : '' }}
                @endforeach
                @if($gajihPokok->branchUser->is_manager)
                    (Manager)
                @endif
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Periode Gaji:</div>
            <div class="info-value">{{ $gajihPokok->periode }}</div>
        </div>
    </div>

    <!-- Detail Gaji Pokok & Tunjangan -->
    <h2 class="section-title">Gaji Pokok & Tunjangan</h2>
    <div class="detail-grid">
        <div class="detail-row">
            <div class="detail-cell label">Gaji Pokok</div>
            <div class="detail-cell value">Rp {{ number_format($gajihPokok->amount, 0, ',', '.') }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-cell label">Tunjangan Makan</div>
            <div class="detail-cell value">Rp {{ number_format($gajihPokok->tunjangan_makan, 0, ',', '.') }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-cell label">Tunjangan Transportasi</div>
            <div class="detail-cell value">Rp {{ number_format($gajihPokok->tunjangan_transportasi, 0, ',', '.') }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-cell label">Tunjangan Jabatan</div>
            <div class="detail-cell value">Rp {{ number_format($gajihPokok->tunjangan_jabatan, 0, ',', '.') }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-cell label">Tunjangan Komunikasi</div>
            <div class="detail-cell value">Rp {{ number_format($gajihPokok->tunjangan_komunikasi, 0, ',', '.') }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-cell label"><strong>Total Tunjangan</strong></div>
            <div class="detail-cell value" style="color: #2563eb;">Rp {{ number_format($gajihPokok->total_tunjangan, 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Bonus Revenue -->
    <h2 class="section-title">Bonus Revenue</h2>
    <div class="detail-grid">
        <div class="detail-row">
            <div class="detail-cell label">Total Revenue</div>
            <div class="detail-cell value">Rp {{ number_format($gajihPokok->total_revenue, 0, ',', '.') }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-cell label">Persentase Revenue</div>
            <div class="detail-cell value">{{ $gajihPokok->persentase_revenue }}%</div>
        </div>
        <div class="detail-row">
            <div class="detail-cell label"><strong>Bonus Revenue</strong></div>
            <div class="detail-cell value amount-positive">+ Rp {{ number_format($gajihPokok->bonus_revenue, 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Potongan BPJS -->
    <h2 class="section-title">Potongan Kesehatan</h2>
    <div class="detail-grid">
        <div class="detail-row">
            <div class="detail-cell label">BPJS Ketenagakerjaan</div>
            <div class="detail-cell value amount-negative">- Rp {{ number_format($gajihPokok->ptg_bpjs_ketenagakerjaan, 0, ',', '.') }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-cell label">BPJS Kesehatan</div>
            <div class="detail-cell value amount-negative">- Rp {{ number_format($gajihPokok->ptg_bpjs_kesehatan, 0, ',', '.') }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-cell label"><strong>Total Potongan BPJS</strong></div>
            <div class="detail-cell value amount-negative">- Rp {{ number_format($gajihPokok->total_potongan_bpjs, 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Potongan Keterlambatan -->
    <h2 class="section-title">Potongan Keterlambatan ({{ count($dataPotonganTerlambat) }} Keterlambatan)</h2>
    @if(empty($dataPotonganTerlambat))
        <div class="no-data">✓ Tidak ada keterlambatan untuk periode ini</div>
    @else
        <table>
            <thead>
                <tr>
                    <th width="20%">Tanggal</th>
                    <th width="15%">Check In</th>
                    <th width="20%">Keterlambatan</th>
                    <th width="30%">Keterangan</th>
                    <th width="15%" class="text-right">Potongan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dataPotonganTerlambat as $item)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($item['tanggal'])->format('d M Y') }}</td>
                    <td>{{ $item['jam_check_in'] }}</td>
                    <td>
                        <span class="badge badge-orange">{{ $item['menit_terlambat'] }} menit</span>
                    </td>
                    <td>{{ $item['keterangan'] }}</td>
                    <td class="text-right amount-negative">- Rp {{ number_format($item['potongan'], 0, ',', '.') }}</td>
                </tr>
                @endforeach
                <tr style="background-color: #fee2e2; font-weight: bold;">
                    <td colspan="4" class="text-right">Total Potongan Keterlambatan:</td>
                    <td class="text-right amount-negative">- Rp {{ number_format($totalPotonganTerlambat, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
        
        <div class="highlight-box">
            <strong>Informasi Potongan:</strong><br>
            • Shift 1 (08:00 - 12:00): Potongan Rp 15.000/keterlambatan<br>
            • Shift 2 (13:00 - 21:00): Potongan Rp 15.000/keterlambatan
        </div>
    @endif

    <!-- Potongan & Tambahan Lainnya -->
    <h2 class="section-title">Potongan & Tambahan Lainnya</h2>
    @if($potongans->isEmpty())
        <div class="no-data">Tidak ada potongan atau tambahan lainnya</div>
    @else
        <table>
            <thead>
                <tr>
                    <th width="15%">Tanggal</th>
                    <th width="15%">Divisi</th>
                    <th width="40%">Keterangan</th>
                    <th width="12%">Jenis</th>
                    <th width="18%" class="text-right">Nominal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($potongans as $item)
                <tr>
                    <td>{{ $item->tanggal->format('d M Y') }}</td>
                    <td>{{ $item->divisi }}</td>
                    <td>{{ $item->keterangan }}</td>
                    <td>
                        @if($item->jenis === 'potongan')
                            <span class="badge badge-red">Potongan</span>
                        @else
                            <span class="badge badge-green">Tambahan</span>
                        @endif
                    </td>
                    <td class="text-right {{ $item->jenis === 'potongan' ? 'amount-negative' : 'amount-positive' }}">
                        {{ $item->jenis === 'potongan' ? '-' : '+' }} Rp {{ number_format($item->amount, 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach
                @if($totalPotonganLain > 0)
                <tr style="background-color: #fee2e2; font-weight: bold;">
                    <td colspan="4" class="text-right">Total Potongan Lain:</td>
                    <td class="text-right amount-negative">- Rp {{ number_format($totalPotonganLain, 0, ',', '.') }}</td>
                </tr>
                @endif
                @if($totalTambahan > 0)
                <tr style="background-color: #d1fae5; font-weight: bold;">
                    <td colspan="4" class="text-right">Total Tambahan:</td>
                    <td class="text-right amount-positive">+ Rp {{ number_format($totalTambahan, 0, ',', '.') }}</td>
                </tr>
                @endif
            </tbody>
        </table>
    @endif

    <!-- Summary -->
    <div class="summary-box">
        <div class="summary-row">
            <div class="summary-label">Gaji Kotor (Pokok + Tunjangan + Bonus - BPJS)</div>
            <div class="summary-value">Rp {{ number_format($gajiKotor, 0, ',', '.') }}</div>
        </div>
        <div class="summary-row">
            <div class="summary-label">Potongan Keterlambatan</div>
            <div class="summary-value">- Rp {{ number_format($totalPotonganTerlambat, 0, ',', '.') }}</div>
        </div>
        <div class="summary-row">
            <div class="summary-label">Potongan Lainnya</div>
            <div class="summary-value">- Rp {{ number_format($totalPotonganLain, 0, ',', '.') }}</div>
        </div>
        <div class="summary-row">
            <div class="summary-label">Tambahan</div>
            <div class="summary-value">+ Rp {{ number_format($totalTambahan, 0, ',', '.') }}</div>
        </div>
        <div class="summary-row total">
            <div class="summary-label">TOTAL GAJI BERSIH</div>
            <div class="summary-value">Rp {{ number_format($gajiBersih, 0, ',', '.') }}</div>
        </div>
    </div>

    @if($gajihPokok->keterangan)
    <div class="highlight-box" style="margin-top: 20px;">
        <strong>Keterangan:</strong><br>
        {{ $gajihPokok->keterangan }}
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Dokumen ini dicetak secara otomatis oleh sistem</p>
        <p>{{ $gajihPokok->branchUser->branch->name }} • {{ $tanggalCetak }}</p>
        <p style="margin-top: 5px; font-size: 9px; color: #999;">
            Data ini bersifat rahasia dan hanya untuk keperluan internal karyawan yang bersangkutan
        </p>
    </div>
</body>
</html>