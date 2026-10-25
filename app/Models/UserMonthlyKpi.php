<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMonthlyKpi extends Model
{
    protected $table = 'user_monthly_kpi';

    protected $fillable = [
        'user_id',
        'month',
        'year',
        'total_percentage',
        'finalized_at',
    ];

    protected $casts = [
        'total_percentage' => 'decimal:2',
        'finalized_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
