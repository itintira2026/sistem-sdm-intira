<?php

namespace App\Exports;

use App\Helpers\FormatHelper;
use App\Models\DailyReportFODetail;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class RankingFoExport implements WithMultipleSheets
{
    public function __construct(
        private string $dateFrom,
        private string $dateTo,
        private string $branchId,
        private string $sortBy,
    ) {}

    public function sheets(): array
    {
        return [
            new RankingFoSheetExport(
                dateFrom: $this->dateFrom,
                dateTo: $this->dateTo,
                branchId: $this->branchId,
                sortBy: $this->sortBy,
                mode: 'validated',
                title: 'Validated Only',
            ),
            new RankingFoSheetExport(
                dateFrom: $this->dateFrom,
                dateTo: $this->dateTo,
                branchId: $this->branchId,
                sortBy: $this->sortBy,
                mode: 'all',
                title: 'Include Pending',
            ),
        ];
    }
}


class RankingFoSheetExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithTitle,
    WithStyles,
    WithColumnWidths
{
    const FIELD_CODES = ['mb_omset', 'mb_revenue', 'mb_jumlah_akad'];

    private \Illuminate\Support\Collection $users;

    public function __construct(
        private string $dateFrom,
        private string $dateTo,
        private string $branchId,
        private string $sortBy,
        private string $mode,
        private string $title,
    ) {}

    public function title(): string
    {
        return $this->title;
    }

    // public function collection(): \Illuminate\Support\Collection
    // {
    //     $foUserIds = $this->getFoUserIds();
    //     $statuses  = $this->mode === 'validated'
    //         ? ['approved']
    //         : ['approved', 'pending'];

    //     $rows = DailyReportFODetail::query()
    //         ->selectRaw("
    //             drfo.user_id,
    //             SUM(CASE WHEN rf.code = 'mb_omset'       THEN drfod.value_number ELSE 0 END) as total_omset,
    //             SUM(CASE WHEN rf.code = 'mb_revenue'     THEN drfod.value_number ELSE 0 END) as total_revenue,
    //             SUM(CASE WHEN rf.code = 'mb_jumlah_akad' THEN drfod.value_number ELSE 0 END) as total_akad,
    //             COUNT(DISTINCT drfo.id)                                                       as total_laporan,
    //             SUM(CASE WHEN drfo.validation_status = 'pending'  THEN 1 ELSE 0 END)         as laporan_pending,
    //             SUM(CASE WHEN drfo.validation_status = 'approved' THEN 1 ELSE 0 END)         as laporan_approved,
    //             SUM(CASE WHEN drfo.validation_status = 'rejected' THEN 1 ELSE 0 END)         as laporan_rejected
    //         ")
    //         ->from('daily_report_fo_details as drfod')
    //         ->join('daily_report_fo as drfo', 'drfo.id',  '=', 'drfod.daily_report_fo_id')
    //         ->join('report_fields as rf',     'rf.id',    '=', 'drfod.field_id')
    //         ->whereIn('drfo.user_id', $foUserIds)
    //         ->whereIn('drfo.validation_status', $statuses)
    //         ->whereBetween('drfo.tanggal', [$this->dateFrom, $this->dateTo])
    //         ->whereIn('rf.code', self::FIELD_CODES)
    //         ->groupBy('drfo.user_id')
    //         ->orderByDesc('total_' . $this->sortBy)
    //         ->get();

    //     // Load users
    //     $this->users = User::whereIn('id', $rows->pluck('user_id'))
    //         ->with(['branchAssignments' => fn($q) => $q->where('is_active', true)->with('branch:id,name')])
    //         ->get()
    //         ->keyBy('id');

    //     // Tambah rank
    //     return $rows->map(function ($row, $index) {
    //         $row->rank = $index + 1;
    //         return $row;
    //     });
    // }

    public function collection(): \Illuminate\Support\Collection
    {
        $foUserIds  = $this->getFoUserIds();
        $statuses   = $this->mode === 'validated' ? ['approved'] : ['approved', 'pending'];
        $userIdList = $foUserIds->implode(',');
        $statusList = implode(',', array_map(fn($s) => "'$s'", $statuses));
        $fieldList  = implode(',', array_map(fn($f) => "'$f'", self::FIELD_CODES));

        $sql = "
        SELECT
            metrics.user_id,
            metrics.total_omset,
            metrics.total_revenue,
            metrics.total_akad,
            COALESCE(counts.total_laporan,   0) as total_laporan,
            COALESCE(counts.hari_aktif,       0) as hari_aktif,
            COALESCE(counts.laporan_approved, 0) as laporan_approved,
            COALESCE(counts.laporan_pending,  0) as laporan_pending,
            COALESCE(counts.laporan_rejected, 0) as laporan_rejected
        FROM (
            SELECT
                drfo.user_id,
                SUM(CASE WHEN rf.code = 'mb_omset'       THEN drfod.value_number ELSE 0 END) as total_omset,
                SUM(CASE WHEN rf.code = 'mb_revenue'     THEN drfod.value_number ELSE 0 END) as total_revenue,
                SUM(CASE WHEN rf.code = 'mb_jumlah_akad' THEN drfod.value_number ELSE 0 END) as total_akad
            FROM daily_report_fo_details drfod
            JOIN daily_report_fo drfo ON drfo.id = drfod.daily_report_fo_id
            JOIN report_fields rf     ON rf.id   = drfod.field_id
            WHERE drfo.user_id IN ($userIdList)
              AND drfo.validation_status IN ($statusList)
              AND drfo.tanggal BETWEEN ? AND ?
              AND rf.code IN ($fieldList)
            GROUP BY drfo.user_id
        ) as metrics
        LEFT JOIN (
            SELECT
                drfo.user_id,
                COUNT(drfo.id)                                                        as total_laporan,
                COUNT(DISTINCT drfo.tanggal)                                          as hari_aktif,
                SUM(CASE WHEN drfo.validation_status = 'approved' THEN 1 ELSE 0 END) as laporan_approved,
                SUM(CASE WHEN drfo.validation_status = 'pending'  THEN 1 ELSE 0 END) as laporan_pending,
                SUM(CASE WHEN drfo.validation_status = 'rejected' THEN 1 ELSE 0 END) as laporan_rejected
            FROM daily_report_fo drfo
            WHERE drfo.user_id IN ($userIdList)
              AND drfo.tanggal BETWEEN ? AND ?
            GROUP BY drfo.user_id
        ) as counts ON metrics.user_id = counts.user_id
        ORDER BY metrics.total_{$this->sortBy} DESC
    ";

        $rows = collect(\DB::select($sql, [
            $this->dateFrom,
            $this->dateTo,
            $this->dateFrom,
            $this->dateTo,
        ]));

        // Load users
        $this->users = User::whereIn('id', $rows->pluck('user_id'))
            ->with([
                'branchAssignments' => fn($q) =>
                $q->where('is_active', true)->with('branch:id,name')
            ])
            ->get()
            ->keyBy('id');

        return $rows->map(function ($row, $index) {
            $row->rank = $index + 1;
            return $row;
        });
    }
    // public function headings(): array
    // {
    //     $headings = [
    //         'Rank',
    //         'Nama FO',
    //         'Cabang',
    //         'Total Omset',
    //         'Total Revenue',
    //         'Total Akad',
    //         'Total Laporan',
    //         'Laporan Approved',
    //         'Laporan Rejected',
    //     ];

    //     // Kolom pending hanya di sheet "all"
    //     if ($this->mode === 'all') {
    //         $headings[] = 'Laporan Pending';
    //         $headings[] = 'Keterangan';
    //     }

    //     return $headings;
    // }

    public function headings(): array
    {
        $headings = [
            'Rank',
            'Nama FO',
            'Cabang',
            'Total Omset',
            'Total Revenue',
            'Total Akad',
            'Total Laporan',
            'Hari Aktif',       // ← tambah
            'Laporan Approved',
            'Laporan Rejected',
        ];

        if ($this->mode === 'all') {
            $headings[] = 'Laporan Pending';
            $headings[] = 'Keterangan';
        }

        return $headings;
    }

    public function map($row): array
    {
        $user       = $this->users->get($row->user_id);
        $branchName = $user?->branchAssignments->first()?->branch?->name ?? '-';

        $data = [
            $row->rank,
            $user?->name ?? '-',
            $branchName,
            (float) $row->total_omset,
            (float) $row->total_revenue,
            (int)   $row->total_akad,
            (int)   $row->total_laporan,
            (int)   $row->hari_aktif,      // ← tambah
            (int)   $row->laporan_approved,
            (int)   $row->laporan_rejected,
        ];

        if ($this->mode === 'all') {
            $data[] = (int) $row->laporan_pending;
            $data[] = $row->laporan_pending > 0
                ? '⚠️ Ada ' . $row->laporan_pending . ' laporan belum divalidasi'
                : '-';
        }

        return $data;
    }
    public function columnWidths(): array
    {
        return [
            'A' => 8,   // Rank
            'B' => 28,  // Nama FO
            'C' => 28,  // Cabang
            'D' => 20,  // Omset
            'E' => 20,  // Revenue
            'F' => 12,  // Akad
            'G' => 14,  // Total Laporan
            'H' => 12,  // Hari Aktif   ← tambah
            'I' => 18,  // Approved
            'J' => 16,  // Rejected
            'K' => 16,  // Pending (mode all)
            'L' => 40,  // Keterangan (mode all)
        ];
    }

    // public function map($row): array
    // {
    //     $user       = $this->users->get($row->user_id);
    //     $branchName = $user?->branchAssignments->first()?->branch?->name ?? '-';

    //     $data = [
    //         $row->rank,
    //         $user?->name ?? '-',
    //         $branchName,
    //         (float) $row->total_omset,
    //         (float) $row->total_revenue,
    //         (int)   $row->total_akad,
    //         (int)   $row->total_laporan,
    //         (int)   $row->laporan_approved,
    //         (int)   $row->laporan_rejected,
    //     ];

    //     if ($this->mode === 'all') {
    //         $data[] = (int) $row->laporan_pending;
    //         $data[] = $row->laporan_pending > 0
    //             ? '⚠️ Ada ' . $row->laporan_pending . ' laporan belum divalidasi'
    //             : '-';
    //     }

    //     return $data;
    // }

    // public function columnWidths(): array
    // {
    //     return [
    //         'A' => 8,   // Rank
    //         'B' => 28,  // Nama FO
    //         'C' => 28,  // Cabang
    //         'D' => 20,  // Omset
    //         'E' => 20,  // Revenue
    //         'F' => 12,  // Akad
    //         'G' => 14,  // Total Laporan
    //         'H' => 18,  // Approved
    //         'I' => 16,  // Rejected
    //         'J' => 16,  // Pending (mode all)
    //         'K' => 40,  // Keterangan (mode all)
    //     ];
    // }

    public function styles(Worksheet $sheet): array
    {
        $lastCol  = $this->mode === 'all' ? 'K' : 'I';
        $lastRow  = $sheet->getHighestRow();

        // Header style
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 11,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0F766E'], // teal-700
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Freeze header row
        $sheet->freezePane('A2');

        // Top 3 highlight
        $highlightColors = [
            2 => 'FEF3C7', // amber-100 = rank 1
            3 => 'F3F4F6', // gray-100  = rank 2
            4 => 'FFF7ED', // orange-50 = rank 3
        ];
        foreach ($highlightColors as $rowNum => $color) {
            if ($rowNum <= $lastRow) {
                $sheet->getStyle("A{$rowNum}:{$lastCol}{$rowNum}")
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setRGB($color);
            }
        }

        // Pending rows — highlight orange kalau ada pending (mode all)
        if ($this->mode === 'all') {
            for ($r = 2; $r <= $lastRow; $r++) {
                $pendingVal = $sheet->getCell("J{$r}")->getValue();
                if ($pendingVal > 0) {
                    $sheet->getStyle("J{$r}:K{$r}")
                        ->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setRGB('FFEDD5'); // orange-100
                    $sheet->getStyle("J{$r}:K{$r}")
                        ->getFont()
                        ->setColor(
                            (new \PhpOffice\PhpSpreadsheet\Style\Color())->setRGB('C2410C')
                        );
                }
            }
        }

        // Border seluruh tabel
        $sheet->getStyle("A1:{$lastCol}{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color'       => ['rgb' => 'E5E7EB'],
                ],
            ],
        ]);

        // Format angka rupiah
        $sheet->getStyle("D2:E{$lastRow}")
            ->getNumberFormat()
            ->setFormatCode('#,##0');

        // Alignment kolom angka
        $sheet->getStyle("A2:A{$lastRow}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("F2:I{$lastRow}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return [];
    }

    // -------------------------------------------------------
    // Helpers
    // -------------------------------------------------------
    private function getFoUserIds(): \Illuminate\Support\Collection
    {
        $query = User::role('fo')->where('is_active', true);

        if ($this->branchId !== 'all') {
            $query->whereHas(
                'branchAssignments',
                fn($q) =>
                $q->where('branch_id', $this->branchId)
                    ->where('is_active', true)
            );
        }

        return $query->pluck('id');
    }
}
