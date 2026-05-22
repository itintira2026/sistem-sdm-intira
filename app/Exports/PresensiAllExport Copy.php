<?php

namespace App\Exports;

use App\Models\User;
use App\Models\Presensi;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;

class PresensiAllExport implements FromCollection, WithHeadings, WithStyles, WithTitle, WithColumnWidths, WithEvents
{
    protected $startDate;
    protected $endDate;
    protected $progressKey;
    protected $rows;
    protected $summaryStartRow;

    public function __construct(string $startDate, string $endDate, string $progressKey = null)
    {
        $this->startDate   = Carbon::parse($startDate)->startOfDay();
        $this->endDate     = Carbon::parse($endDate)->endOfDay();
        $this->progressKey = $progressKey;
    }

    public function collection()
    {
        $users = User::where('is_active', true)
            ->with(['branches'])
            ->orderBy('name')
            ->get();

        $period = CarbonPeriod::create($this->startDate, $this->endDate);
        $dates  = collect($period)->map(fn($d) => $d->format('Y-m-d'));

        // Ambil semua presensi dalam range sekaligus (1 query)
        $allPresensis = Presensi::whereBetween('tanggal', [
            $this->startDate->toDateString(),
            $this->endDate->toDateString(),
        ])
            ->whereIn('user_id', $users->pluck('id'))
            ->get()
            ->groupBy(fn($p) => $p->user_id . '_' . $p->tanggal->format('Y-m-d'));

        $rows        = collect();
        $totalUsers  = $users->count();
        $processed   = 0;

        // Rekap summary keseluruhan
        $rekapTotal = [
            'hadir'     => 0,
            'terlambat' => 0,
            'izin'      => 0,
            'sakit'     => 0,
            'alpha'     => 0,
        ];

        foreach ($users as $user) {
            $branch = $user->branches->first()?->name ?? '-';

            foreach ($dates as $date) {
                $key      = $user->id . '_' . $date;
                $dayData  = $allPresensis->get($key, collect());
                $grouped  = $dayData->keyBy('status');

                $checkIn      = $grouped->get('CHECK_IN');
                $istirahatOut = $grouped->get('ISTIRAHAT_OUT');
                $istirahatIn  = $grouped->get('ISTIRAHAT_IN');
                $checkOut     = $grouped->get('CHECK_OUT');

                $carbonDate = Carbon::parse($date);
                $hariNama   = $this->hariIndonesia($carbonDate->dayOfWeek);

                // ========================
                // TENTUKAN STATUS HARI INI
                // ========================
                if ($dayData->isEmpty()) {
                    // Hari sudah lewat & tidak ada absen → Alpha
                    if ($carbonDate->isPast() && !$carbonDate->isToday()) {
                        $statusLabel = 'Tidak Absen';
                        $rekapTotal['alpha']++;
                    } else {
                        // Hari ini atau masa depan → skip
                        $statusLabel = '-';
                    }
                    $rows->push([
                        'nama'          => $user->name,
                        'branch'        => $branch,
                        'tanggal'       => $carbonDate->format('d/m/Y'),
                        'hari'          => $hariNama,
                        'check_in'      => '-',
                        'istirahat_out' => '-',
                        'istirahat_in'  => '-',
                        'check_out'     => '-',
                        'foto_check_in' => '-',
                        'status'        => $statusLabel,
                        'menit_terlambat' => '-',
                        'keterangan'    => 'Tidak ada data absensi',
                    ]);
                    continue;
                }

                // Parse jam
                $jamCI  = $checkIn      ? Carbon::parse($checkIn->jam)->format('H:i')      : '-';
                $jamIO  = $istirahatOut ? Carbon::parse($istirahatOut->jam)->format('H:i')  : '-';
                $jamII  = $istirahatIn  ? Carbon::parse($istirahatIn->jam)->format('H:i')   : '-';
                $jamCO  = $checkOut     ? Carbon::parse($checkOut->jam)->format('H:i')      : '-';
                $foto   = $checkIn?->photo ? 'Ada' : 'Tidak Ada';
                $keterangan = $checkIn?->keterangan ?? '-';

                // Hitung potongan & menit terlambat
                $potonganData   = $checkIn ? $checkIn->hitungPotonganTerlambat() : null;
                $menitTerlambat = $potonganData ? $potonganData['menit_terlambat'] : 0;

                // Tentukan status label
                $ketLower = strtolower($keterangan);
                if (str_contains($ketLower, 'sakit')) {
                    $statusLabel = 'Sakit';
                    $rekapTotal['sakit']++;
                } elseif (str_contains($ketLower, 'izin') || str_contains($ketLower, 'cuti')) {
                    $statusLabel = 'Izin/Cuti';
                    $rekapTotal['izin']++;
                } elseif ($checkIn && $checkOut) {
                    $statusLabel = 'Hadir Lengkap';
                    $rekapTotal['hadir']++;
                    if ($menitTerlambat > 0) $rekapTotal['terlambat']++;
                } else {
                    $statusLabel = 'Tidak Lengkap';
                    $rekapTotal['hadir']++;
                    if ($menitTerlambat > 0) $rekapTotal['terlambat']++;
                }

                $rows->push([
                    'nama'            => $user->name,
                    'branch'          => $branch,
                    'tanggal'         => $carbonDate->format('d/m/Y'),
                    'hari'            => $hariNama,
                    'check_in'        => $jamCI,
                    'istirahat_out'   => $jamIO,
                    'istirahat_in'    => $jamII,
                    'check_out'       => $jamCO,
                    'foto_check_in'   => $foto,
                    'status'          => $statusLabel,
                    'menit_terlambat' => $menitTerlambat > 0 ? $menitTerlambat . ' mnt' : '-',
                    'keterangan'      => $keterangan,
                ]);
            }

            $processed++;

            // Update progress ke cache
            if ($this->progressKey) {
                $percent = (int) round(($processed / $totalUsers) * 90); // max 90%, sisanya untuk write excel
                Cache::put($this->progressKey, $percent, now()->addMinutes(10));
            }
        }

        // Simpan info untuk styling nanti
        $this->rows           = $rows;
        $this->rekapTotal     = $rekapTotal;
        $this->summaryStartRow = $rows->count() + 3; // +2 heading + 1 spacing

        return $rows->map(fn($r) => array_values($r));
    }

    public function headings(): array
    {
        return [
            'Nama',
            'Branch',
            'Tanggal',
            'Hari',
            'Check In',
            'Istirahat Out',
            'Istirahat In',
            'Check Out',
            'Foto Check In',
            'Status',
            'Menit Terlambat',
            'Keterangan',
        ];
    }

    public function title(): string
    {
        return 'Rekap Presensi';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25, // Nama
            'B' => 20, // Branch
            'C' => 13, // Tanggal
            'D' => 12, // Hari
            'E' => 12, // Check In
            'F' => 14, // Istirahat Out
            'G' => 13, // Istirahat In
            'H' => 12, // Check Out
            'I' => 15, // Foto Check In
            'J' => 16, // Status
            'K' => 16, // Menit Terlambat
            'L' => 30, // Keterangan
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header row styling
        $sheet->getStyle('A1:L1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['argb' => 'FFFFFFFF'],
                'size'  => 11,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF1F4E79'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(30);

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet     = $event->sheet->getDelegate();
                $lastRow   = $sheet->getHighestRow();
                $lastDataRow = $lastRow;

                // ================================
                // STYLING TIAP ROW DATA
                // ================================
                for ($row = 2; $row <= $lastDataRow; $row++) {
                    $status = $sheet->getCell('J' . $row)->getValue();

                    // Warna per status
                    $bgColor = match (true) {
                        str_contains((string)$status, 'Tidak Absen') => 'FFFF0000', // merah
                        str_contains((string)$status, 'Sakit')       => 'FFFFC000', // kuning
                        str_contains((string)$status, 'Izin')        => 'FFFFC000', // kuning
                        str_contains((string)$status, 'Tidak Lengkap') => 'FFFF7F00', // oranye
                        str_contains((string)$status, 'Hadir Lengkap') => 'FF00B050', // hijau
                        default                                        => null,
                    };

                    if ($bgColor) {
                        $sheet->getStyle('J' . $row)->applyFromArray([
                            'fill' => [
                                'fillType'   => Fill::FILL_SOLID,
                                'startColor' => ['argb' => $bgColor],
                            ],
                            'font' => [
                                'color' => ['argb' => 'FFFFFFFF'],
                                'bold'  => true,
                            ],
                        ]);
                    }

                    // Zebra striping
                    if ($row % 2 === 0) {
                        foreach (range('A', 'I') as $col) {
                            if ($col === 'J') continue;
                            $sheet->getStyle($col . $row)->applyFromArray([
                                'fill' => [
                                    'fillType'   => Fill::FILL_SOLID,
                                    'startColor' => ['argb' => 'FFF2F2F2'],
                                ],
                            ]);
                        }
                        $sheet->getStyle('K' . $row)->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF2F2F2']],
                        ]);
                        $sheet->getStyle('L' . $row)->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF2F2F2']],
                        ]);
                    }

                    // Border tipis tiap row
                    $sheet->getStyle('A' . $row . ':L' . $row)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color'       => ['argb' => 'FFD0D0D0'],
                            ],
                        ],
                    ]);

                    // Center alignment untuk kolom jam & status
                    foreach (['C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K'] as $col) {
                        $sheet->getStyle($col . $row)->getAlignment()
                            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                            ->setVertical(Alignment::VERTICAL_CENTER);
                    }
                }

                // ================================
                // REKAP SUMMARY DI BAWAH TABEL
                // ================================
                $summaryRow = $lastDataRow + 2;

                $rekapTotal = $this->rekapTotal ?? [];

                $sheet->mergeCells('A' . $summaryRow . ':C' . $summaryRow);
                $sheet->setCellValue('A' . $summaryRow, 'REKAP KESELURUHAN');
                $sheet->getStyle('A' . $summaryRow . ':L' . $summaryRow)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12, 'color' => ['argb' => 'FFFFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1F4E79']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                $summaryRow++;
                $summaryData = [
                    ['Total Hadir',     $rekapTotal['hadir']     ?? 0, 'FF00B050'],
                    ['Total Terlambat', $rekapTotal['terlambat'] ?? 0, 'FFFFC000'],
                    ['Total Izin/Cuti', $rekapTotal['izin']      ?? 0, 'FF0070C0'],
                    ['Total Sakit',     $rekapTotal['sakit']     ?? 0, 'FFFF7F00'],
                    ['Total Alpha',     $rekapTotal['alpha']     ?? 0, 'FFFF0000'],
                ];

                foreach ($summaryData as [$label, $value, $color]) {
                    $sheet->setCellValue('A' . $summaryRow, $label);
                    $sheet->setCellValue('B' . $summaryRow, $value);
                    $sheet->getStyle('A' . $summaryRow)->applyFromArray([
                        'font' => ['bold' => true],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF2F2F2']],
                    ]);
                    $sheet->getStyle('B' . $summaryRow)->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['argb' => 'FF' . ltrim($color, 'FF')]],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF2F2F2']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);
                    $summaryRow++;
                }

                // Freeze pane header
                $sheet->freezePane('A2');

                // Auto filter
                $sheet->setAutoFilter('A1:L1');

                // Update progress 100%
                if ($this->progressKey) {
                    Cache::put($this->progressKey, 100, now()->addMinutes(10));
                }
            },
        ];
    }

    private function hariIndonesia(int $dayOfWeek): string
    {
        return match ($dayOfWeek) {
            0 => 'Minggu',
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
        };
    }
}
