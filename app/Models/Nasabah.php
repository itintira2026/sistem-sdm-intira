<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Nasabah extends Model
{
    use HasFactory;

    protected $table = 'nasabahs'; // atau 'nasabah' jika tidak plural

    protected $fillable = [
        'branch_id',
        'branch_user_id', // admin yang input (dari kolom admin di excel)
        'three_hour_report_manager_id', // relasi ke laporan 3 jam
        'tangal_registrasi', // sesuai typo di Excel atau rename jadi tanggal_registrasi
        'status_anggota',
        'no_member',
        'nik',
        'nama',
        'tanggal_lahir',
        'alamat',
        'provinsi',
        'kab_kota',
        'kecamatan',
        'kelurahan',
        'email',
        'no_telepon',
        'agama',
        'pekerjaan',
    ];

    protected $casts = [
        'tangal_registrasi' => 'date',
        'tanggal_lahir' => 'date',
    ];

    // ===============================
    // RELATIONSHIPS
    // ===============================

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

    // ===============================
    // ACCESSORS
    // ===============================

    public function getUmurAttribute()
    {
        return $this->tanggal_lahir ? 
            Carbon::parse($this->tanggal_lahir)->age : null;
    }

    public function getNoTeleponFormattedAttribute()
    {
        // Format: 0821-1557-6664
        $phone = preg_replace('/[^0-9]/', '', $this->no_telepon);
        
        if (strlen($phone) >= 10) {
            return substr($phone, 0, 4) . '-' . 
                   substr($phone, 4, 4) . '-' . 
                   substr($phone, 8);
        }
        
        return $this->no_telepon;
    }

    // ===============================
    // SCOPES
    // ===============================

    public function scopeAktif($query)
    {
        return $query->where('status_anggota', 'Aktif');
    }

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
        return $query->whereDate('tangal_registrasi', today());
    }
}