<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportOfficialData extends Model
{
    protected $table = 'report_official_data';

    protected $fillable = [
        'user_id',
        'branch_id',
        'tanggal',
        'omset',
        'revenue',
        'jumlah_akad',
        'nasabah_baru',
        'uploaded_by',
        'uploaded_at',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'uploaded_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
