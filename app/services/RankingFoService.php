<?php

namespace App\Services;

use App\Models\DailyReportFODetail;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class RankingFoService
{
    // Field codes yang kita butuhkan
    const FIELD_CODES = ['mb_omset', 'mb_revenue', 'mb_jumlah_akad'];

    public function __construct(
        private RankingCacheService $cacheService
    ) {}

    // -------------------------------------------------------
    // Main ranking query — dipaginate
    // -------------------------------------------------------
    // public function getRanking(
    //     string $dateFrom,
    //     string $dateTo,
    //     string $branchId,
    //     string $sortBy,
    //     string $mode,
    //     int $page,
    //     int $perPage
    // ): LengthAwarePaginator {
    //     $cacheKey = $this->cacheService->buildKey(
    //         $dateFrom,
    //         $dateTo,
    //         $branchId,
    //         $sortBy,
    //         $mode,
    //         $page,
    //         $perPage
    //     );

    //     return $this->cacheService->remember($cacheKey, function () use (
    //         $dateFrom,
    //         $dateTo,
    //         $branchId,
    //         $sortBy,
    //         $mode,
    //         $page,
    //         $perPage
    //     ) {
    //         $foUserIds = $this->getFoUserIds($branchId);
    //         $statuses  = $this->resolveStatuses($mode);

    //         return DailyReportFODetail::query()
    //             ->selectRaw("
    //                 drfo.user_id,
    //                 SUM(CASE WHEN rf.code = 'mb_omset'         THEN drfod.value_number ELSE 0 END) as total_omset,
    //                 SUM(CASE WHEN rf.code = 'mb_revenue'       THEN drfod.value_number ELSE 0 END) as total_revenue,
    //                 SUM(CASE WHEN rf.code = 'mb_jumlah_akad'   THEN drfod.value_number ELSE 0 END) as total_akad,
    //                 COUNT(DISTINCT drfo.id)                                                         as total_laporan,
    //                 SUM(CASE WHEN drfo.validation_status = 'pending'  THEN 1 ELSE 0 END)            as laporan_pending,
    //                 SUM(CASE WHEN drfo.validation_status = 'approved' THEN 1 ELSE 0 END)            as laporan_approved,
    //                 SUM(CASE WHEN drfo.validation_status = 'rejected' THEN 1 ELSE 0 END)            as laporan_rejected
    //             ")
    //             ->from('daily_report_fo_details as drfod')
    //             ->join('daily_report_fo as drfo',   'drfo.id',  '=', 'drfod.daily_report_fo_id')
    //             ->join('report_fields as rf',        'rf.id',   '=', 'drfod.field_id')
    //             ->whereIn('drfo.user_id', $foUserIds)
    //             ->whereIn('drfo.validation_status', $statuses)
    //             ->whereBetween('drfo.tanggal', [$dateFrom, $dateTo])
    //             ->whereIn('rf.code', self::FIELD_CODES)
    //             ->groupBy('drfo.user_id')
    //             ->orderByDesc('total_' . $sortBy)
    //             ->paginate($perPage, ['*'], 'page', $page);
    //     });
    // }
    public function getRanking(
        string $dateFrom,
        string $dateTo,
        string $branchId,
        string $sortBy,
        string $mode,
        int $page,
        int $perPage
    ): LengthAwarePaginator {
        $cacheKey = $this->cacheService->buildKey(
            $dateFrom,
            $dateTo,
            $branchId,
            $sortBy,
            $mode,
            $page,
            $perPage
        );

        return $this->cacheService->remember($cacheKey, function () use (
            $dateFrom,
            $dateTo,
            $branchId,
            $sortBy,
            $mode,
            $page,
            $perPage
        ) {
            $foUserIds = $this->getFoUserIds($branchId);
            $statuses  = $this->resolveStatuses($mode);

            // Flatten user ids untuk binding
            $userIdList   = $foUserIds->implode(',');
            $statusList   = implode(',', array_map(fn($s) => "'$s'", $statuses));
            $fieldList    = implode(',', array_map(fn($f) => "'$f'", self::FIELD_CODES));

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
                -- Subquery 1: metrics ikut mode
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
                -- Subquery 2: counts selalu semua status
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
            ORDER BY metrics.total_{$sortBy} DESC
        ";

            // Binding hanya untuk parameter ? (tanggal)
            $bindings = [$dateFrom, $dateTo, $dateFrom, $dateTo];

            // Hitung total untuk paginator
            $totalSql = "SELECT COUNT(*) as total FROM ($sql) as sub";
            $total    = \DB::selectOne($totalSql, $bindings)->total;

            // Ambil data dengan offset
            $offset  = ($page - 1) * $perPage;
            $dataSql = $sql . " LIMIT $perPage OFFSET $offset";
            $items   = collect(\DB::select($dataSql, $bindings));

            return new \Illuminate\Pagination\LengthAwarePaginator(
                items: $items,
                total: $total,
                perPage: $perPage,
                currentPage: $page,
                options: [
                    'path'  => \Illuminate\Pagination\Paginator::resolveCurrentPath(),
                    'query' => request()->query(),
                ]
            );
        });
    }

    // -------------------------------------------------------
    // Top 3 — selalu dari approved only untuk fairness
    // -------------------------------------------------------
    public function getTop3(
        string $dateFrom,
        string $dateTo,
        string $branchId,
        string $sortBy
    ): Collection {
        $cacheKey = $this->cacheService->buildTop3Key(
            $dateFrom,
            $dateTo,
            $branchId,
            $sortBy
        );

        return $this->cacheService->remember($cacheKey, function () use (
            $dateFrom,
            $dateTo,
            $branchId,
            $sortBy
        ) {
            $foUserIds = $this->getFoUserIds($branchId);

            return DailyReportFODetail::query()
                ->selectRaw("
                    drfo.user_id,
                    SUM(CASE WHEN rf.code = 'mb_omset'       THEN drfod.value_number ELSE 0 END) as total_omset,
                    SUM(CASE WHEN rf.code = 'mb_revenue'     THEN drfod.value_number ELSE 0 END) as total_revenue,
                    SUM(CASE WHEN rf.code = 'mb_jumlah_akad' THEN drfod.value_number ELSE 0 END) as total_akad
                ")
                ->from('daily_report_fo_details as drfod')
                ->join('daily_report_fo as drfo', 'drfo.id', '=', 'drfod.daily_report_fo_id')
                ->join('report_fields as rf',      'rf.id',  '=', 'drfod.field_id')
                ->whereIn('drfo.user_id', $foUserIds)
                ->where('drfo.validation_status', 'approved')
                ->whereBetween('drfo.tanggal', [$dateFrom, $dateTo])
                ->whereIn('rf.code', self::FIELD_CODES)
                ->groupBy('drfo.user_id')
                ->orderByDesc('total_' . $sortBy)
                ->limit(3)
                ->get();
        });
    }

    // -------------------------------------------------------
    // Load user info untuk hasil query
    // -------------------------------------------------------
    public function loadUsers(Collection $userIds): Collection
    {
        return User::whereIn('id', $userIds)
            ->with([
                'branches' => fn($q) => $q->wherePivot('is_active', true)->limit(1),
            ])
            ->get()
            ->keyBy('id');
    }

    private function getFoUserIds(string $branchId): Collection
    {
        $query = User::role('fo')->where('is_active', true);

        if ($branchId !== 'all') {
            $query->whereHas(
                'branchAssignments',
                fn($q) =>
                $q->where('branch_id', $branchId)
                    ->where('is_active', true)  // ← langsung ke tabel branch_users
            );
        }

        return $query->pluck('id');
    }

    private function resolveStatuses(string $mode): array
    {
        return $mode === 'validated'
            ? ['approved']
            : ['approved', 'pending'];
    }
}
