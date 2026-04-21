<?php

namespace App\Providers;

use App\Models\DailyReportFo;
use App\Observers\DailyReportFoObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        DailyReportFo::observe(DailyReportFoObserver::class);
    }
}
