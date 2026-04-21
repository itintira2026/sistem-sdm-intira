<?php

namespace App\Observers;

use App\Models\DailyReportFo;
use App\Services\RankingCacheService;

class DailyReportFoObserver
{
    public function __construct(
        private RankingCacheService $cacheService
    ) {}

    public function updated(DailyReportFo $report): void
    {
        if ($report->isDirty('validation_status')) {
            $this->cacheService->flush();
        }
    }

    public function created(DailyReportFo $report): void
    {
        $this->cacheService->flush();
    }

    public function deleted(DailyReportFo $report): void
    {
        $this->cacheService->flush();
    }
}
