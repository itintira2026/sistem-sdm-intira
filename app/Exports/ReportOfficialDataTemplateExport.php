<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReportOfficialDataTemplateExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return ['username', 'tanggal', 'omset', 'revenue', 'jumlah_akad', 'nasabah_baru', 'branch_code'];
    }

    public function array(): array
    {
        return [
            // Baris contoh — boleh dihapus sebelum upload beneran.
            // branch_code boleh dikosongkan kalau mau pakai cabang FO saat ini.
            ['contoh_username_fo', '2026-09-01', 4000000, 3500000, 5, 2, ''],
        ];
    }
}
