<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Slip Gaji - {{ $gajihPokok->branchUser->user->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9pt;
            color: #1a1a2e;
            background: #fff;
        }

        .top-strip { height: 7px; background: #e94560; }

        .header { background: #0f3460; padding: 18px 28px; }
        .header-table { width: 100%; border-collapse: collapse; }
        .header-table td { vertical-align: middle; padding: 0; }

        .company-name {
            font-size: 14pt; font-weight: bold;
            color: #ffffff; letter-spacing: 1px; text-transform: uppercase;
        }
        .company-tagline { font-size: 7.5pt; color: #8899bb; margin-top: 3px; }
        .company-address { font-size: 7pt; color: #667799; margin-top: 5px; line-height: 1.6; }

        .periode-badge {
            background: #e94560; color: #fff; font-size: 8pt; font-weight: bold;
            letter-spacing: 1px; padding: 5px 14px; border-radius: 20px;
            text-transform: uppercase; text-align: center;
        }
        .slip-label {
            font-size: 7.5pt; color: #667799; letter-spacing: 3px;
            text-transform: uppercase; text-align: center; margin-top: 5px;
        }

        .emp-band { background: #16213e; padding: 12px 28px; }
        .emp-table { width: 100%; border-collapse: collapse; }
        .emp-table td { vertical-align: middle; padding: 0; }

        .emp-avatar {
            width: 36px; height: 36px; background: #e94560;
            border-radius: 18px; text-align: center; line-height: 36px;
            font-size: 14pt; font-weight: bold; color: #fff;
        }
        .emp-fullname { font-size: 11pt; font-weight: bold; color: #ffffff; padding-left: 10px; }
        .emp-role { font-size: 7.5pt; color: #8899bb; margin-top: 2px; padding-left: 10px; }
        .emp-meta { font-size: 7.5pt; color: #8899bb; line-height: 1.8; text-align: right; }
        .emp-meta strong { color: #aabbcc; }

        .body-wrap { padding: 18px 28px; }

        .section-title {
            font-size: 7pt; font-weight: bold; letter-spacing: 2px;
            text-transform: uppercase; color: #e94560;
            border-bottom: 1px solid #e0e3ec;
            padding-bottom: 4px; margin-bottom: 10px; margin-top: 16px;
        }

        .two-col-table { width: 100%; border-collapse: collapse; }
        .two-col-table td { vertical-align: top; padding: 0; }
        .col-left-td  { width: 49%; padding-right: 8px; }
        .col-right-td { width: 49%; padding-left: 8px; }

        .card {
            background: #f7f9fc; border: 1px solid #e2e6f0;
            border-radius: 6px; padding: 10px 12px;
        }
        .card-income { border-left: 3px solid #0a8a5c; }
        .card-deduct { border-left: 3px solid #cc2244; }

        .card-title {
            font-size: 7pt; font-weight: bold; letter-spacing: 1.5px;
            text-transform: uppercase; padding-bottom: 6px; margin-bottom: 6px;
            border-bottom: 1px solid #e0e3ec;
        }
        .ct-income { color: #0a8a5c; }
        .ct-deduct { color: #cc2244; }

        .item-table { width: 100%; border-collapse: collapse; }
        .item-table td { padding: 5px 0; border-bottom: 1px solid #f0f2f7; vertical-align: top; }
        .item-table tr:last-child td { border-bottom: none; }
        .item-label { color: #5a6070; font-size: 8.5pt; width: 65%; }
        .item-sub   { font-size: 7pt; color: #a0a8b8; }
        .item-value { text-align: right; font-weight: bold; font-size: 8.5pt; color: #1a1a2e; }
        .val-green  { color: #0a8a5c; }
        .val-red    { color: #cc2244; }
        .val-blue   { color: #0f3460; }

        .info-box {
            background: #fff5f6; border: 1px solid #ffd0d8; border-radius: 4px;
            padding: 7px 10px; margin-top: 10px;
            font-size: 7pt; color: #885060; line-height: 1.7;
        }
        .info-box-title { color: #cc2244; font-weight: bold; margin-bottom: 3px; }

        .summary-box { background: #0f3460; border-radius: 8px; padding: 14px 20px; margin-top: 14px; }
        .summary-table { width: 100%; border-collapse: collapse; }
        .summary-table td { padding: 4px 0; vertical-align: middle; }
        .sum-label { color: #8899bb; font-size: 8pt; }
        .sum-val   { text-align: right; font-weight: bold; font-size: 8pt; color: #ccd6ee; }
        .sum-green { color: #4ade80; }
        .sum-red   { color: #f87171; }
        .sum-divider { border: none; border-top: 1px solid #1e3a6e; margin: 6px 0; }
        .thp-cell  { text-align: right; border-left: 1px solid #1e3a6e; padding-left: 20px; width: 42%; vertical-align: middle; }
        .thp-label { color: #8899bb; font-size: 7pt; letter-spacing: 2px; text-transform: uppercase; }
        .thp-value { color: #e94560; font-size: 18pt; font-weight: bold; line-height: 1.2; margin-top: 4px; }

        .detail-table { width: 100%; border-collapse: collapse; font-size: 7.5pt; margin-top: 6px; }
        .detail-table th { background: #0f3460; color: #fff; padding: 7px 8px; text-align: left; font-weight: bold; }
        .detail-table th.r { text-align: right; }
        .detail-table td { padding: 5px 8px; border-bottom: 1px solid #eef0f5; color: #2a2a3e; }
        .detail-table td.r { text-align: right; font-weight: bold; }
        .row-even td { background: #f7f9fc; }
        .td-red { color: #cc2244; }
        .td-grn { color: #0a8a5c; }
        .tfoot-total td { background: #16213e; color: #fff; font-weight: bold; font-size: 8pt; padding: 6px 8px; border: none; }
        .tfoot-net td   { background: #e94560; color: #fff; font-weight: bold; font-size: 8pt; padding: 6px 8px; border: none; }

        .badge-late {
            background: #fff3e0; color: #e65100; border: 1px solid #ffb74d;
            border-radius: 8px; padding: 1px 6px; font-size: 7pt; font-weight: bold;
        }

        .sig-wrap { margin-top: 16px; padding-top: 14px; border-top: 1px dashed #d0d4e0; }
        .sig-table { width: 100%; border-collapse: collapse; }
        .sig-table td { text-align: center; padding: 0 10px; }
        .sig-title { font-size: 8pt; color: #8890a4; }
        .sig-space { height: 44px; }
        .sig-line  { border-top: 1.5px solid #c0c4d0; padding-top: 5px; }
        .sig-name  { font-weight: bold; font-size: 8.5pt; color: #1a1a2e; }
        .sig-role  { font-size: 7.5pt; color: #8890a4; margin-top: 2px; }

        .doc-footer {
            text-align: center; font-size: 7pt; color: #b0b8cc;
            padding: 8px 28px; border-top: 1px solid #f0f2f7; margin-top: 10px;
        }
        .bottom-strip { height: 5px; background: #e94560; }
    </style>
</head>
<body>

@php $initials = strtoupper(substr($gajihPokok->branchUser->user->name, 0, 1)); @endphp

<div class="top-strip"></div>

<div class="header">
    <table class="header-table">
        <tr>
            <td>
                <div class="company-name">PT Solusi Intira Sejahtera</div>
                <div class="company-tagline">Human Resources &middot; Payroll Division</div>
                <div class="company-address">
                    Jl. Komplek Agraria I No.045, Telaga Biru, Banjarmasin Barat<br>
                    Kota Banjarmasin, Kalimantan Selatan 70119
                </div>
            </td>
            <td style="width: 160px; text-align: right; vertical-align: middle;">
                <div class="periode-badge">{{ $gajihPokok->periode }}</div>
                <div class="slip-label">Slip Gaji</div>
            </td>
        </tr>
    </table>
</div>

<div class="emp-band">
    <table class="emp-table">
        <tr>
            {{-- <td style="width: 36px;">
                <div class="emp-avatar">{{ $initials }}</div>
            </td> --}}
            <td>
                <div class="emp-fullname">{{ $gajihPokok->branchUser->user->name }}</div>
                <div class="emp-role">
                    @foreach($gajihPokok->branchUser->user->roles as $role)
                        {{ $role->name }}{{ !$loop->last ? ', ' : '' }}
                    @endforeach
                    @if($gajihPokok->branchUser->is_manager)
                        &middot; Area Manager
                    @endif
                </div>
            </td>
            <td style="width: 165px;">
                <div class="emp-meta">
                    <strong>Cabang</strong> {{ $gajihPokok->branchUser->branch->name }}<br>
                    <strong>Golongan</strong> {{ $gajihPokok->golongan ?? 'N/A' }}<br>
                    <strong>Status</strong> {{ $gajihPokok->branchUser->user->is_active ? 'Aktif' : 'Non-Aktif' }}
                </div>
            </td>
        </tr>
    </table>
</div>

<div class="body-wrap">

    <table class="two-col-table" style="border-collapse: separate; border-spacing: 20px 0;">
        <tr>
            <td class="col-left-td"  >
                <div class="card card-income">
                    <div class="card-title ct-income">Pendapatan</div>
                    <table class="item-table">
                        <tr>
                            <td class="item-label">Gaji Pokok</td>
                            <td class="item-value val-blue">{{ number_format($gajihPokok->amount, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="item-label">T. Makan<br><span class="item-sub">{{ $gajihPokok->hari_kerja }} hari</span></td>
                            <td class="item-value">{{ number_format($gajihPokok->tunjangan_makan, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="item-label">T. Transportasi<br><span class="item-sub">{{ $gajihPokok->hari_kerja }} hari</span></td>
                            <td class="item-value">{{ number_format($gajihPokok->tunjangan_transportasi, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="item-label">T. Komunikasi</td>
                            <td class="item-value">{{ number_format($gajihPokok->tunjangan_komunikasi, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="item-label">T. Jabatan</td>
                            <td class="item-value">{{ number_format($gajihPokok->tunjangan_jabatan, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="item-label">Bonus Revenue<br><span class="item-sub">{{ $gajihPokok->persentase_revenue }}%</span></td>
                            <td class="item-value val-green">{{ number_format($gajihPokok->bonus_revenue, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="item-label">Bonus KPI<br><span class="item-sub">{{ $gajihPokok->persentase_kpi }}%</span></td>
                            <td class="item-value val-green">{{ number_format($gajihPokok->total_kpi, 0, ',', '.') }}</td>
                        </tr>
                        @if($totalTambahan > 0)
                        <tr>
                            <td class="item-label">Tambahan Lain</td>
                            <td class="item-value val-green">+ {{ number_format($totalTambahan, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </td>
            <td class="col-right-td">
                <div class="card card-deduct">
                    <div class="card-title ct-deduct">Potongan</div>
                    <table class="item-table">
                        <tr>
                            <td class="item-label">BPJS Kesehatan</td>
                            <td class="item-value val-red">{{ number_format($gajihPokok->ptg_bpjs_kesehatan, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="item-label">BPJS Ketenagakerjaan</td>
                            <td class="item-value val-red">{{ number_format($gajihPokok->ptg_bpjs_ketenagakerjaan, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="item-label">Simpanan</td>
                            <td class="item-value val-red">{{ number_format($gajihPokok->simpanan, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="item-label">Potongan KPI<br><span class="item-sub">{{ $gajihPokok->persentase_kpi }}%</span></td>
                            <td class="item-value val-red">{{ number_format($gajihPokok->bonus_kpi, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="item-label">Keterlambatan<br><span class="item-sub">{{ count($dataPotonganTerlambat) }}x kejadian</span></td>
                            <td class="item-value val-red">{{ number_format($totalPotonganTerlambat, 0, ',', '.') }}</td>
                        </tr>
                        @if($totalPotonganLain > 0)
                        <tr>
                            <td class="item-label">Potongan Lain</td>
                            <td class="item-value val-red">{{ number_format($totalPotonganLain, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                    </table>
                    {{-- <div class="info-box">
                        <div class="info-box-title">Info Potongan Keterlambatan</div>
                        Shift 1 (08:00-12:00): Rp 15.000/kejadian<br>
                        Shift 2 (13:00-21:00): Rp 15.000/kejadian
                    </div> --}}
                </div>
            </td>
        </tr>
    </table>

    <div class="summary-box">
        <table class="summary-table">
            <tr>
                <td style="width: 56%; vertical-align: middle;">
                    <table class="summary-table">
                        <tr>
                            <td class="sum-label">Gaji Kotor</td>
                            <td class="sum-val">Rp {{ number_format($gajiKotor, 0, ',', '.') }}</td>
                        </tr>
                        @if($totalTambahan > 0)
                        <tr>
                            <td class="sum-label">Total Tambahan</td>
                            <td class="sum-val sum-green">+ Rp {{ number_format($totalTambahan, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="sum-label">Total Potongan</td>
                            <td class="sum-val sum-red">- Rp {{ number_format($totalPotongan, 0, ',', '.') }}</td>
                        </tr>
                        <tr><td colspan="2"><hr class="sum-divider"></td></tr>
                        <tr>
                            <td class="sum-label" style="font-size: 7pt; color: #445577;">Dicetak: {{ $tanggalCetak }} WIB</td>
                            <td class="sum-val"   style="font-size: 7pt; color: #445577;">Dok. Otomatis</td>
                        </tr>
                    </table>
                </td>
                <td class="thp-cell">
                    <div class="thp-label">Take Home Pay</div>
                    <div class="thp-value">Rp {{ number_format($gajiBersih, 0, ',', '.') }}</div>
                </td>
            </tr>
        </table>
    </div>

    @if(count($dataPotonganTerlambat) > 0 || count($potongans) > 0)
    <div class="section-title">Rincian Potongan &amp; Tambahan</div>
    <table class="detail-table">
        <thead>
            <tr>
                <th style="width: 4%; text-align: center;">#</th>
                <th style="width: 20%;">Nama</th>
                <th style="width: 18%;">Kategori</th>
                <th style="width: 22%;">Keterangan</th>
                <th class="r" style="width: 14%;">Tambahan (Rp)</th>
                <th class="r" style="width: 14%;">Potongan (Rp)</th>
                <th style="width: 10%;">Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($potongans as $i => $item)
            <tr class="{{ $i % 2 == 1 ? 'row-even' : '' }}">
                <td style="text-align: center; color: #8890a4;">{{ $i + 1 }}</td>
                <td>{{ $gajihPokok->branchUser->user->name }}</td>
                <td>{{ $item->divisi }}</td>
                <td>{{ $item->keterangan }}</td>
                <td class="r td-grn">{{ $item->jenis === 'tambahan' ? number_format($item->amount, 0, ',', '.') : '-' }}</td>
                <td class="r td-red">{{ $item->jenis === 'potongan' ? number_format($item->amount, 0, ',', '.') : '-' }}</td>
                <td>{{ $item->tanggal->format('d/m/Y') }}</td>
            </tr>
            @endforeach
            @foreach($dataPotonganTerlambat as $j => $item)
            @php $rowIdx = count($potongans) + $j; @endphp
            <tr class="{{ $rowIdx % 2 == 1 ? 'row-even' : '' }}">
                <td style="text-align: center; color: #8890a4;">{{ $rowIdx + 1 }}</td>
                <td>{{ $gajihPokok->branchUser->user->name }}</td>
                <td><span class="badge-late">Keterlambatan</span></td>
                <td>{{ $item['keterangan'] }}</td>
                <td class="r td-grn">-</td>
                <td class="r td-red">{{ number_format($item['potongan'], 0, ',', '.') }}</td>
                <td>{{ \Carbon\Carbon::parse($item['tanggal'])->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="tfoot-total">
                <td colspan="4" style="text-align: right; letter-spacing: 1px;">TOTAL</td>
                <td class="r" style="color: #4ade80;">{{ number_format($totalTambahan, 0, ',', '.') }}</td>
                <td class="r" style="color: #f87171;">{{ number_format($totalPotongan, 0, ',', '.') }}</td>
                <td></td>
            </tr>
            <tr class="tfoot-net">
                <td colspan="4" style="text-align: right; letter-spacing: 1px;">NET (TAMBAHAN - POTONGAN)</td>
                <td colspan="2" class="r">
                    {{ ($totalTambahan - $totalPotongan) >= 0 ? '' : '- ' }}{{ number_format(abs($totalTambahan - $totalPotongan), 0, ',', '.') }}
                </td>
                <td></td>
            </tr>
        </tfoot>
    </table>
    @endif

    <div class="sig-wrap">
        <table class="sig-table">
            <tr>
                {{-- <td>
                    <div class="sig-title">Dibuat oleh</div>
                    <div class="sig-space"></div>
                    <div class="sig-line">
                        <div class="sig-name">Vini Amalia</div>
                        <div class="sig-role">PT Solusi Intira Sejahtera</div>
                    </div>
                </td> --}}
                <td>
                    <div class="sig-title">Disetujui oleh</div>
                    <div class="sig-space"></div>
                    <div class="sig-line">
                        <div class="sig-name">HRD</div>
                        <div class="sig-role">PT Solusi Intira Sejahtera</div>
                    </div>
                </td>
                <td>
                    <div class="sig-title">Diterima oleh</div>
                    <div class="sig-space"></div>
                    <div class="sig-line">
                        <div class="sig-name">{{ $gajihPokok->branchUser->user->name }}</div>
                        <div class="sig-role">Karyawan</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

</div>

<div class="doc-footer">
    Dokumen ini digenerate secara otomatis &nbsp;&middot;&nbsp; {{ $tanggalCetak }} WIB &nbsp;&middot;&nbsp; PT Solusi Intira Sejahtera &nbsp;&middot;&nbsp; Bersifat Rahasia
</div>

<div class="bottom-strip"></div>

</body>
</html>