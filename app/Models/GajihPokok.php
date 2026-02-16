<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GajihPokok extends Model
{
    protected $table = 'gajih_pokoks';

    protected $fillable = [
        'user_id',
        'amount',
        'tunjangan_makan',
        'tunjangan_transportasi',
        'tunjangan_jabatan',
        'tunjangan_komunikasi',
        'ptg_bpjs_ketenagakerjaan',
        'ptg_bpjs_kesehatan',
        'total_revenue',
        'persentase_revenue',
        'bonus_revenue',
        'bulan',
        'tahun',
        'hari_kerja',
        'keterangan',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'tunjangan_makan' => 'decimal:2',
        'tunjangan_transportasi' => 'decimal:2',
        'tunjangan_jabatan' => 'decimal:2',
        'tunjangan_komunikasi' => 'decimal:2',
        'ptg_bpjs_ketenagakerjaan' => 'decimal:2',
        'ptg_bpjs_kesehatan' => 'decimal:2',
        'total_revenue' => 'decimal:2',
        'persentase_revenue' => 'integer',
        'bonus_revenue' => 'decimal:2',
        'bulan' => 'integer',
        'tahun' => 'integer',
        'hari_kerja' => 'integer',
    ];

    /**
     * Relasi ke BranchUser
     */
    // public function branchUser()
    // {
    //     return $this->belongsTo(BranchUser::class);
    // }

    public function branchUser()
    {


        return $this->belongsTo(BranchUser::class, 'user_id', 'user_id')
            ->whereHas('user', function ($query) {
                $query->where('is_active', true);
            });
    }

    /**
     * Get user via branch_user
     */
    public function user()
    {
        return $this->hasOneThrough(
            User::class,
            BranchUser::class,
            'id', // Foreign key on branch_user table
            'id', // Foreign key on users table
            'branch_user_id', // Local key on gaji_pokok table
            'user_id' // Local key on branch_user table
        );
    }

    // /**
    //  * Get branch via branch_user
    //  */
    // public function branch()
    // {
    //     return $this->hasOneThrough(
    //         Branch::class,
    //         BranchUser::class,
    //         'id', // Foreign key on branch_user table
    //         'id', // Foreign key on branches table
    //         'branch_user_id', // Local key on gaji_pokok table
    //         'branch_id' // Local key on branch_user table
    //     );
    // }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public function getTotalTunjanganAttribute()
    {
        return $this->tunjangan_makan +
            $this->tunjangan_transportasi +
            $this->tunjangan_jabatan +
            $this->tunjangan_komunikasi;
    }
    public function getTotalPotonganBpjsAttribute()
    {
        return $this->ptg_bpjs_ketenagakerjaan +
            $this->ptg_bpjs_kesehatan;
    }

    //   public function getTotalRevenueAttribute()
    // {
    //     return $this->bonus_revenue *
    //         ($this->persentase_revenue / 100);
    // }
    public function getTotalGajiKotorAttribute()
    {
        return $this->amount + $this->total_tunjangan +
            $this->bonus_revenue - $this->total_potongan_bpjs;
    }

    /**
     * Get nama bulan
     */
    public function getNamaBulanAttribute()
    {
        $bulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];
        return $bulan[$this->bulan] ?? '';
    }

    /**
     * Get periode (Januari 2024)
     */
    public function getPeriodeAttribute()
    {
        return $this->nama_bulan . ' ' . $this->tahun;
    }

    /**
     * Scope untuk bulan tertentu
     */
    public function scopeForMonth($query, $bulan, $tahun)
    {
        return $query->where('bulan', $bulan)->where('tahun', $tahun);
    }

    /**
     * Scope untuk tahun tertentu
     */
    public function scopeForYear($query, $tahun)
    {
        return $query->where('tahun', $tahun);
    }

    /**
     * Ambil USER dari gaji pokok
     */
    // public function user()
    // {
    //     return $this->belongsTo(User::class, 'user_id');
    // }

    /**
     * Ambil BRANCH lewat branch_users
     * (user → branch_user → branch)
     */
    public function branch()
    {
        return $this->hasOneThrough(
            Branch::class,      // Model tujuan akhir
            BranchUser::class,  // Model perantara
            'user_id',          // FK di branch_users
            'id',               // PK di branches
            'user_id',          // FK di gajih_pokoks
            'branch_id'         // FK di branch_users
        );
    }

    /**
     * Ambil branchUser aktif (OPSIONAL tapi SANGAT DISARANKAN)
     */
    public function activeBranchUser()
    {
        return $this->hasOne(BranchUser::class, 'user_id', 'user_id')
            ->where('status', 'active');
    }
}
