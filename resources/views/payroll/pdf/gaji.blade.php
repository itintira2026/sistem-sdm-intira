<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Slip Gaji - {{ $gajihPokok->branchUser->user->name }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Calibri:wght@400;700&display=swap');

        @page {
            margin: 10mm 12mm;
            size: A4;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, 'Calibri', sans-serif;
            font-size: 9pt;
            color: #000;
            background: #fff;
        }

        /* ===== HEADER ===== */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        .header-table td {
            vertical-align: middle;
            padding: 4px;
        }

        .logo-cell {
            width: 70px;
            text-align: center;
        }

        .logo-img {
            width: 60px;
            height: 60px;
            object-fit: contain;
        }

        .company-info {
            text-align: center;
        }

        .company-name {
            font-size: 14pt;
            font-weight: bold;
            color: #1a7a1a;
            letter-spacing: 1px;
        }

        .company-address {
            font-size: 7pt;
            color: #444;
            margin-top: 2px;
            line-height: 1.4;
        }

        /* ===== EMPLOYEE INFO ===== */
        .emp-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
            font-size: 9pt;
        }

        .emp-table td {
            padding: 3px 6px;
        }

        .emp-label {
            font-weight: bold;
            width: 130px;
        }

        .emp-colon {
            width: 10px;
        }

        .emp-value {
            font-size: 12pt;
            font-weight: bold;
        }

        .emp-value.jabatan,
        .emp-value.golongan {
            font-size: 10pt;
            font-weight: bold;
        }

        /* ===== MAIN SALARY TABLE ===== */
        .salary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
            font-size: 9pt;
        }

        .salary-table td,
        .salary-table th {
            border: 1px solid #ccc;
            padding: 4px 8px;
        }

        .salary-table .row-label {
            font-weight: normal;
        }

        .salary-table .row-label.bold {
            font-weight: bold;
        }

        .salary-table .num {
            text-align: right;
        }

        .row-green {
            background-color: #92d050;
            font-weight: bold;
        }

        .row-yellow {
            background-color: #ffff00;
            font-weight: bold;
        }

        .row-grand {
            background-color: #92d050;
            font-weight: bold;
        }

        .neg-val {
            /* Negative values shown as normal, dash handled in template */
        }

        /* ===== SIGNATURE AREA ===== */
        .signature-area {
            text-align: center;
            margin: 10px 0;
            float: right;
            width: 250px;
        }

        .signature-area p {
            font-size: 9pt;
            margin-bottom: 60px;
        }

        .signature-area .sig-name {
            font-weight: bold;
            font-size: 9pt;
            border-top: 1px solid #000;
            padding-top: 2px;
            margin-top: 0;
        }

        .signature-area .sig-title {
            font-size: 9pt;
        }

        .clearfix::after {
            content: '';
            display: table;
            clear: both;
        }

        /* ===== DETAIL TABLE ===== */
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin-top: 8px;
            clear: both;
        }

        .detail-table th {
            background-color: #1a7a1a;
            color: #fff;
            font-weight: bold;
            padding: 5px 8px;
            border: 1px solid #1a7a1a;
            text-align: left;
        }

        .detail-table td {
            border: 1px solid #ccc;
            padding: 4px 8px;
        }

        .detail-table tr:nth-child(even) td {
            background: #f9f9f9;
        }

        .detail-table .neg-cell {
            color: #cc0000;
            text-align: right;
        }

        .detail-table .pos-cell {
            text-align: right;
        }

        .detail-table .text-right {
            text-align: right;
        }

        .separator {
            border: none;
            border-top: 2px solid #1a7a1a;
            margin: 6px 0;
        }
    </style>
</head>

<body>

    @php
        // Parse periode format "Januari 2026" → Carbon date
        $bulanId = [
            'Januari' => 1, 'Februari' => 2, 'Maret' => 3, 'April' => 4,
            'Mei' => 5, 'Juni' => 6, 'Juli' => 7, 'Agustus' => 8,
            'September' => 9, 'Oktober' => 10, 'November' => 11, 'Desember' => 12,
        ];
        $periodeArr = explode(' ', $gajihPokok->periode);
        $periodeBulanNum = $bulanId[$periodeArr[0]] ?? 1;
        $periodeTahun = $periodeArr[1] ?? date('Y');
        $periodeCarbon = \Carbon\Carbon::createFromDate($periodeTahun, $periodeBulanNum, 1);
        $periodeTanggal = $periodeCarbon->format('00/m/Y');
    @endphp

    <!-- ===== HEADER ===== -->
    <table class="header-table">
        <tr>
            <td class="logo-cell">
                <!-- Logo kiri -->
                <div style="width:60px;height:60px;border:2px solid #1a7a1a;border-radius:50%;display:flex;align-items:center;justify-content:center;overflow:hidden;">
                    <span style="font-size:7pt;color:#1a7a1a;font-weight:bold;text-align:center;line-height:1.2;">INTIRA<br>SEJAHTERA</span>
                </div>
            </td>
            <td class="company-info">
                <div class="company-name">PT SOLUSI INTIRA SEJAHTERA</div>
                <div class="company-address">
                    Head Office Jl. Komplek Agraria I No.045 RT.025 RW.003, Desa/Kelurahan Telaga Biru, Kec. Banjarmasin Barat,<br>
                    Kota Banjarmasin, Provinsi Kalimantan Selatan, Kode Pos: 70119
                </div>
            </td>
            <td class="logo-cell">
                <!-- Logo kanan -->
                <div style="width:60px;height:60px;border:2px solid #1a7a1a;border-radius:50%;display:flex;align-items:center;justify-content:center;overflow:hidden;">
                    <span style="font-size:7pt;color:#1a7a1a;font-weight:bold;text-align:center;line-height:1.2;">INTIRA<br>SEJAHTERA</span>
                </div>
            </td>
        </tr>
    </table>

    <hr class="separator">

    <!-- ===== INFO KARYAWAN ===== -->
    <table class="emp-table">
        <tr>
            <td class="emp-label">NAMA KARYAWAN</td>
            <td class="emp-colon"></td>
            <td class="emp-value" style="font-size:14pt; font-weight:bold; border:1px solid #999; padding:2px 8px; width:220px;">
                {{ $gajihPokok->branchUser->user->name }}
            </td>
        </tr>
        <tr>
            <td class="emp-label">JABATAN</td>
            <td class="emp-colon"></td>
            <td class="emp-value jabatan">
                @foreach($gajihPokok->branchUser->user->roles as $role){{ strtoupper($role->name) }}{{ !$loop->last ? ', ' : '' }}@endforeach
                @if($gajihPokok->branchUser->is_manager) (AREA MANAGER)@endif
            </td>
        </tr>
        <tr>
            <td class="emp-label">GOLONGAN</td>
            <td class="emp-colon"></td>
            <td class="emp-value golongan">{{ $gajihPokok->golongan ?? 'N/A' }}</td>
        </tr>
    </table>

    <!-- ===== TABEL GAJI UTAMA ===== -->
    <table class="salary-table">
        <tbody>
            <!-- Gaji Pokok -->
            <tr>
                <td class="row-label" style="width:30%;">GAJI POKOK</td>
                <td class="num" style="width:18%;">{{ number_format($gajihPokok->amount, 0, ',', '.') }}</td>
                <td style="width:12%;"></td>
                <td style="width:12%;"></td>
                <td class="num" style="width:18%;">{{ number_format($gajihPokok->amount, 0, ',', '.') }}</td>
            </tr>
            <!-- Transport -->
            <tr>
                <td class="row-label">TRANSPORT</td>
                <td class="num">{{ $gajihPokok->hari_kerja }} HARI  x</td>
                <td class="num">{{ number_format($gajihPokok->tunjangan_transportasi / $gajihPokok->hari_kerja, 0, ',', '.') }}</td>
                <td></td>
                <td class="num">{{ number_format($gajihPokok->tunjangan_transportasi, 0, ',', '.') }}</td>
            </tr>
            <!-- Makan -->
            <tr>
                <td class="row-label">MAKAN</td>
                <td class="num">{{ $gajihPokok->hari_kerja }} HARI  x</td>
                <td class="num">{{ number_format($gajihPokok->tunjangan_makan / $gajihPokok->hari_kerja, 0, ',', '.') }}</td>
                <td></td>
                <td class="num">{{ number_format($gajihPokok->tunjangan_makan, 0, ',', '.') }}</td>
            </tr>
            <!-- Bonus Revenue -->
            <tr>
                <td class="row-label bold">BONUS REVENUE</td>
                <td class="num">{{ number_format($gajihPokok->bonus_revenue / ($gajihPokok->persentase_revenue / 100), 0, ',', '.') }}</td>
                <td class="num">{{ $gajihPokok->persentase_revenue }}</td>
                <td></td>
                <td class="num">{{ number_format($gajihPokok->bonus_revenue, 0, ',', '.') }}</td>
            </tr>
            <!-- T. Jabatan -->
            <tr class="row-green">
                <td class="row-label bold">T. JABATAN</td>
                <td class="num">{{ number_format($gajihPokok->tunjangan_jabatan, 0, ',', '.') }}</td>
                <td></td>
                <td></td>
                <td class="num">{{ number_format($gajihPokok->tunjangan_jabatan, 0, ',', '.') }}</td>
            </tr>
            <!-- Potongan -->
            <tr>
                <td class="row-label">POTONGAN</td>
                <td class="num">-</td>
                <td class="num">{{ number_format($gajihPokok->ptg_bpjs_ketenagakerjaan + $gajihPokok->ptg_bpjs_kesehatan + $totalPotonganTerlambat, 0, ',', '.') }}</td>
                <td></td>
                <td class="num">- {{ number_format($gajihPokok->ptg_bpjs_ketenagakerjaan + $gajihPokok->ptg_bpjs_kesehatan + $totalPotonganTerlambat, 0, ',', '.') }}</td>
            </tr>
            <!-- Simpanan -->
            <tr>
                <td class="row-label">SIMPANAN</td>
                <td class="num">-</td>
                <td class="num">{{ number_format($gajihPokok->simpanan, 0, ',', '.') }}</td>
                <td></td>
                <td class="num">- {{ number_format($gajihPokok->simpanan, 0, ',', '.') }}</td>
            </tr>
            <!-- KPI -->
            <tr>
                <td class="row-label">KPI</td>
                <td class="num">{{ $gajihPokok->persentase_kpi }}%</td>
                <td class="num">{{ number_format($gajihPokok->bonus_kpi, 0, ',', '.') }}</td>
                <td class="num">-</td>
                <td class="num">- {{ number_format($gajihPokok->bonus_kpi - ($gajihPokok->bonus_kpi * ($gajihPokok->persentase_kpi / 100)), 0, ',', '.') }}</td>
            </tr>
            <!-- Grand Total -->
            <tr class="row-grand">
                <td class="row-label bold" colspan="4">GRAND TOTAL</td>
                <td class="num">{{ number_format($gajiKotor, 0, ',', '.') }}</td>
            </tr>
            <!-- Take Home Pay -->
            <tr class="row-yellow">
                <td class="row-label bold" colspan="4">TAKE HOME PAY</td>
                <td class="num">{{ number_format($gajiBersih, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- ===== TANDA TANGAN ===== -->
    <div class="clearfix" style="margin-top:10px;">
        <div class="signature-area">
            <p>Banjarmasin, {{ $tanggalCetak }}</p>
            <div class="sig-name">AULIA USPIHANI YUSRAN</div>
            <div class="sig-title">HO SDM</div>
        </div>
    </div>

    <div style="clear:both; margin-top: 10px;"></div>

    <!-- ===== TABEL DETAIL BREAKDOWN ===== -->
    <table class="detail-table">
        <thead>
            <tr>
                <th style="width:4%;">NO</th>
                <th style="width:16%;">NAMA</th>
                <th style="width:22%;">KATEGORI</th>
                <th style="width:14%;" class="text-right">TUNJANGAN</th>
                <th style="width:14%;" class="text-right">POTONGAN</th>
                <th style="width:18%;">KETERANGAN</th>
                <th style="width:12%;">TANGGAL</th>
            </tr>
        </thead>
        <tbody>
            {{-- Tunjangan Jabatan --}}
            <tr>
                <td>1</td>
                <td>{{ $gajihPokok->branchUser->user->name }}</td>
                <td>Tunjangan JABATAN</td>
                <td class="pos-cell">{{ number_format($gajihPokok->tunjangan_jabatan, 0, ',', '.') }}</td>
                <td class="neg-cell">-</td>
                <td>-</td>
                <td>{{ $periodeTanggal }}</td>
            </tr>
            {{-- KPI --}}
            <tr>
                <td>2</td>
                <td>{{ $gajihPokok->branchUser->user->name }}</td>
                <td>AM KPI</td>
                <td class="pos-cell">{{ number_format($gajihPokok->bonus_kpi, 0, ',', '.') }}</td>
                <td class="neg-cell">-</td>
                <td>-</td>
                <td>{{ $periodeTanggal }}</td>
            </tr>
            {{-- BPJS Ketenagakerjaan --}}
            <tr>
                <td>3</td>
                <td>{{ $gajihPokok->branchUser->user->name }}</td>
                <td>BPJS Ketenagakerjaan</td>
                <td class="pos-cell">-</td>
                <td class="neg-cell">{{ number_format($gajihPokok->ptg_bpjs_ketenagakerjaan, 0, ',', '.') }}</td>
                <td>-</td>
                <td>{{ $periodeTanggal }}</td>
            </tr>
            {{-- Potongan & Tambahan Lain --}}
            @foreach($potongans as $i => $item)
            <tr>
                <td>{{ $i + 4 }}</td>
                <td>{{ $gajihPokok->branchUser->user->name }}</td>
                <td>{{ $item->divisi }}</td>
                <td class="pos-cell">{{ $item->jenis === 'tambahan' ? number_format($item->amount, 0, ',', '.') : '-' }}</td>
                <td class="neg-cell">{{ $item->jenis === 'potongan' ? number_format($item->amount, 0, ',', '.') : '-' }}</td>
                <td>{{ $item->keterangan }}</td>
                <td>{{ $item->tanggal->format('d/m/Y') }}</td>
            </tr>
            @endforeach
            {{-- Keterlambatan --}}
            @foreach($dataPotonganTerlambat as $j => $item)
            <tr>
                <td>{{ count($potongans) + 4 + $j }}</td>
                <td>{{ $gajihPokok->branchUser->user->name }}</td>
                <td>{{ $item['keterangan'] }}</td>
                <td class="pos-cell">-</td>
                <td class="neg-cell">{{ number_format($item['potongan'], 0, ',', '.') }}</td>
                <td>{{ $item['keterangan'] }}</td>
                <td>{{ \Carbon\Carbon::parse($item['tanggal'])->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>