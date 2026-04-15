<?php
// app/Exports/DailyReportFoValidationExport.php

namespace App\Exports;

use App\Models\DailyReportFo;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithProperties;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Illuminate\Support\Carbon;

class DailyReportFoValidationExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    WithProperties,
    WithChunkReading
{
    private int $rowNumber = 0;

    public function __construct(
        private string $dateFrom,
        private string $dateTo,
        private ?int $branchId,          // null = semua cabang
        private array $accessibleBranchIds, // security gate
        private string $exportedByName,
    ) {}

    // -------------------------------------------------------
    // Query — dengan chunk untuk handle data besar
    // -------------------------------------------------------

    public function query()
    {
        return DailyReportFo::query()
            ->whereBetween('tanggal', [$this->dateFrom, $this->dateTo])
            ->whereIn('branch_id', $this->accessibleBranchIds) // security gate
            ->when($this->branchId, fn($q) => $q->where('branch_id', $this->branchId))
            ->with([
                'user:id,name',
                'branch:id,name',
                'validation.manager:id,name',
                'validation.actions:id,name',
                'details' => function ($q) {
                    $q->whereHas('field', function ($q) {
                        $q->whereIn('code', ['mb_omset', 'mb_revenue', 'mb_jumlah_akad']);
                    })->with('field:id,code');
                },
            ])
            ->orderBy('tanggal', 'asc')
            ->orderBy('branch_id', 'asc')
            ->orderBy('uploaded_at', 'asc');
    }

    public function chunkSize(): int
    {
        return 200;
    }

    // -------------------------------------------------------
    // Headings
    // -------------------------------------------------------

    public function headings(): array
    {
        return [
            'No',
            'Nama FO',
            'Cabang',
            'Tanggal',
            'Shift',
            'Slot',
            'Upload At',
            'Omset (Rp)',
            'Revenue (Rp)',
            'Akad',
            'Status Validasi',
            'Divalidasi Oleh',
            'Tindakan',
            'Catatan',
        ];
    }

    // -------------------------------------------------------
    // Mapping per row
    // -------------------------------------------------------

    public function map($report): array
    {
        $this->rowNumber++;

        $details  = $report->details->keyBy(fn($d) => $d->field->code);
        $omset    = $details->get('mb_omset')?->value_number ?? 0;
        $revenue  = $details->get('mb_revenue')?->value_number ?? 0;
        $akad     = $details->get('mb_jumlah_akad')?->value_number ?? 0;

        $statusLabel = match ($report->validation_status) {
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default    => 'Pending',
        };

        $tindakan = $report->validation
            ? $report->validation->actions->pluck('name')->join(', ')
            : '-';

        return [
            $this->rowNumber,
            $report->user->name ?? '-',
            $report->branch->name ?? '-',
            Carbon::parse($report->tanggal)->format('d/m/Y'),
            ucfirst($report->shift),
            $report->slot,
            Carbon::parse($report->uploaded_at)->format('H:i:s'),
            $omset,
            $revenue,
            $akad,
            $statusLabel,
            $report->validation?->manager?->name ?? '-',
            $tindakan,
            $report->validation?->catatan ?? '-',
        ];
    }

    // -------------------------------------------------------
    // Styling
    // -------------------------------------------------------

    public function styles(Worksheet $sheet): array
    {
        return [
            // Header row — bold + background abu
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 11,
                ],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F3F4F6'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    // -------------------------------------------------------
    // Sheet title
    // -------------------------------------------------------

    public function title(): string
    {
        return 'Validasi Laporan FO';
    }

    // -------------------------------------------------------
    // File properties
    // -------------------------------------------------------

    public function properties(): array
    {
        return [
            'creator'     => $this->exportedByName,
            'title'       => 'Export Validasi Laporan FO',
            'description' => "Range: {$this->dateFrom} s/d {$this->dateTo}",
            'created'     => now()->format('Y-m-d H:i:s'),
        ];
    }
}
