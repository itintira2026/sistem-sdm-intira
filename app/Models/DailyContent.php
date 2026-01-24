<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'tanggal',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    // ==========================
    // RELATIONSHIP
    // ==========================
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
