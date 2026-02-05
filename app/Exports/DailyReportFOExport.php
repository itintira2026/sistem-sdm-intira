<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DailyReportFOExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $query;

    protected $title;

    public function __construct($query, $title = 'Daily Report FO')
    {
        $this->query = $query;
        $this->title = $title;
    }

    /**
     * Get the collection of data
     */
    public function collection()
    {
        return $this->query->with(['user', 'branch', 'photos'])->get();
    }

    /**
     * Define headings
     */
    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'FO Name',
            'Email',
            'Cabang',
            'Shift',
            'Slot',
            'Waktu Slot',
            'Waktu Upload',
            'Total Foto',
            'Like FB',
            'Comment FB',
            'Like IG',
            'Comment IG',
            'Like TikTok',
            'Comment TikTok',
            'Keterangan',
        ];
    }

    /**
     * Map data to rows
     */
    public function map($report): array
    {
        static $counter = 0;
        $counter++;

        $categories = config('daily_report_fo.photo_categories');
        $photoCounts = [];

        foreach (array_keys($categories) as $key) {
            $photoCounts[$key] = $report->getPhotoCategoryCount($key);
        }

        return [
            $counter,
            $report->tanggal->format('d/m/Y'),
            $report->user->name,
            $report->user->email,
            $report->branch->name,
            $report->shift_label,
            'Slot '.$report->slot,
            $report->formatted_slot_time,
            $report->uploaded_at->format('d/m/Y H:i:s'),
            $report->photos->count(),
            $photoCounts['like_fb'],
            $photoCounts['comment_fb'],
            $photoCounts['like_ig'],
            $photoCounts['comment_ig'],
            $photoCounts['like_tiktok'],
            $photoCounts['comment_tiktok'],
            $report->keterangan ?? '-',
        ];
    }

    /**
     * Apply styles
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style header row
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '0D9488'],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }

    /**
     * Set sheet title
     */
    public function title(): string
    {
        return substr($this->title, 0, 31); // Excel sheet title max 31 chars
    }
}
