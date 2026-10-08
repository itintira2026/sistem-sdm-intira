<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\ReportOfficialData;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportReconciliationController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $branches = Branch::orderBy('name')->get();
        $branchId = $request->input('branch_id');
        $startDate = $request->input('start_date', now()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        $foQuery = User::whereHas('roles', fn($q) => $q->where('name', 'fo'));

        if ($branchId) {
            $foQuery->whereHas('branches', fn($q) => $q->where('branches.id', $branchId));
        }

        $foUsers = $foQuery->orderBy('name')->get();

        $foTotals = DB::table('daily_report_fo as r')
            ->join('daily_report_fo_details as d', 'd.daily_report_fo_id', '=', 'r.id')
            ->join('report_fields as f', 'f.id', '=', 'd.field_id')
            ->whereBetween('r.tanggal', [$startDate, $endDate])
            ->where('r.validation_status', 'approved')
            ->whereIn('f.code', ['mb_omset', 'mb_revenue'])
            ->when($branchId, fn($q) => $q->where('r.branch_id', $branchId))
            ->selectRaw('r.user_id, f.code, SUM(d.value_number) as total')
            ->groupBy('r.user_id', 'f.code')
            ->get()
            ->groupBy('user_id');

        $officialTotals = ReportOfficialData::whereBetween('tanggal', [$startDate, $endDate])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->selectRaw('user_id, SUM(omset) as total_omset, SUM(revenue) as total_revenue, COUNT(*) as jumlah_hari_data')
            ->groupBy('user_id')
            ->get()
            ->keyBy('user_id');

        $rows = [];

        foreach ($foUsers as $fo) {
            $userFoTotals = $foTotals->get($fo->id, collect())->pluck('total', 'code');
            $foOmset = round((float) ($userFoTotals->get('mb_omset', 0)), 2);
            $foRevenue = round((float) ($userFoTotals->get('mb_revenue', 0)), 2);

            $official = $officialTotals->get($fo->id);

            $officialOmset = null;
            $officialRevenue = null;

            // BARU: status Omset & Revenue dihitung TERPISAH — masing-masing
            // punya kesimpulan sendiri, gak lagi 1 status gabungan yang bisa
            // menyesatkan (kasus di screenshot: Revenue sama persis tapi
            // ketutup status "Kekurangan" yang sebenarnya cuma soal Omset).
            $omsetStatus = 'no_data';
            $revenueStatus = 'no_data';

            if ($official) {
                $officialOmset = round((float) $official->total_omset, 2);
                $officialRevenue = round((float) $official->total_revenue, 2);

                $omsetStatus = $this->compareStatus($foOmset, $officialOmset);
                $revenueStatus = $this->compareStatus($foRevenue, $officialRevenue);
            }

            // Status ringkasan per baris (dipakai buat kartu stats & warna baris):
            // 'no_data' kalau data resmi belum ada sama sekali,
            // 'sesuai' kalau DUA-duanya cocok,
            // 'selisih' kalau salah satu ATAU keduanya beda.
            $overallStatus = $official === null
                ? 'no_data'
                : (($omsetStatus === 'sesuai' && $revenueStatus === 'sesuai') ? 'sesuai' : 'selisih');

            $rows[] = [
                'user' => $fo,
                'branch' => $fo->branches->first()?->name ?? '—',
                'fo_omset' => $foOmset,
                'fo_revenue' => $foRevenue,
                'official_omset' => $officialOmset,
                'official_revenue' => $officialRevenue,
                'omset_status' => $omsetStatus,
                'revenue_status' => $revenueStatus,
                'overall_status' => $overallStatus,
            ];
        }

        $stats = [
            'total_fo' => count($rows),
            'sesuai' => collect($rows)->where('overall_status', 'sesuai')->count(),
            'selisih' => collect($rows)->where('overall_status', 'selisih')->count(),
            'no_data' => collect($rows)->where('overall_status', 'no_data')->count(),
        ];

        return view('report-official-data.reconciliation', [
            'branches' => $branches,
            'branchId' => $branchId,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'rows' => $rows,
            'stats' => $stats,
        ]);
    }

    /**
     * BARU: helper 3-way comparison, dipakai buat Omset & Revenue secara
     * independen. 'sesuai' | 'lebih' | 'kurang'.
     */
    private function compareStatus(float $foValue, float $officialValue): string
    {
        if ($foValue == $officialValue) {
            return 'sesuai';
        }

        return $foValue > $officialValue ? 'lebih' : 'kurang';
    }
}
