<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Omzet extends Model
{
    use HasFactory;

    protected $table = 'omzets';

    protected $fillable = [

        'branch_id',
        'branch_user_id',
        'three_hour_report_manager_id',

        'no_akad',
        'tanggal',

        'lokasi',
        'status',

        'nama',
        'no_telepon',

        'rahn',
        'tunggakan',

        'grade_barang',
        'jenis_barang',

        'merk',
        'type',

        'keterangan',

        'tanggal_angkut',
    ];

    protected $casts = [

        'tanggal' => 'date',
        'tanggal_angkut' => 'date',

        'rahn' => 'decimal:2',
        'tunggakan' => 'decimal:2',

        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function branchUser()
    {
        return $this->belongsTo(BranchUser::class);
    }

    public function threeHourReportManager()
    {
        return $this->belongsTo(ThreeHourReportManager::class);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getRahnFormattedAttribute()
    {
        return $this->rahn
            ? number_format($this->rahn, 0, ',', '.')
            : null;
    }

    public function getTunggakanFormattedAttribute()
    {
        return $this->tunggakan
            ? number_format($this->tunggakan, 0, ',', '.')
            : null;
    }

    public function getNoTeleponFormattedAttribute()
    {
        if (!$this->no_telepon) return null;

        $phone = preg_replace('/[^0-9]/', '', $this->no_telepon);

        if (strlen($phone) >= 10) {

            return substr($phone, 0, 4) . '-' .
                   substr($phone, 4, 4) . '-' .
                   substr($phone, 8);
        }

        return $this->no_telepon;
    }

    public function getTanggalFormattedAttribute()
    {
        return $this->tanggal
            ? Carbon::parse($this->tanggal)->format('d M Y')
            : null;
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeByBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeByBranchUser($query, $branchUserId)
    {
        return $query->where('branch_user_id', $branchUserId);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('tanggal', today());
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

}
