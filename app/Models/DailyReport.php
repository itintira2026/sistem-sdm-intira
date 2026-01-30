<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DailyReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'user_id',
        'tanggal',
        'shift',
        'pencairan_jumlah_barang',
        'pencairan_nominal',
        'pelunasan_jumlah_barang',
        'pelunasan_nominal',
        'final_jumlah_barang',      // 🔥 TAMBAHKAN
        'final_nominal',              // 🔥 TAMBAHKAN
        'keterangan',
        'validasi_manager',
        'validated_by',
        'validated_at',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'pencairan_nominal' => 'decimal:2',
        'pelunasan_nominal' => 'decimal:2',
        'final_nominal' => 'decimal:2',  // 🔥 TAMBAHKAN
        'validasi_manager' => 'boolean',
        'validated_at' => 'datetime',
    ];

    // 🔥 TAMBAHKAN ACCESSOR
    public function getFinalNominalFormattedAttribute()
    {
        return 'Rp ' . number_format($this->final_nominal, 0, ',', '.');
    }

    // 🔥 METHOD UNTUK GET STOK AWAL
    public static function getStokAwal($branchId, $tanggal)
    {
        // Cari laporan terakhir sebelum tanggal ini
        $lastReport = self::where('branch_id', $branchId)
            ->where('tanggal', '<', $tanggal)
            ->orderBy('tanggal', 'desc')
            ->orderBy('shift', 'desc')
            ->first();

        // Jika tidak ada laporan sebelumnya, cek shift pagi di hari yang sama
        if (!$lastReport) {
            $lastReport = self::where('branch_id', $branchId)
                ->whereDate('tanggal', $tanggal)
                ->where('shift', 'pagi')
                ->first();
        }

        return [
            'jumlah_barang' => $lastReport->final_jumlah_barang ?? 0,
            'nominal' => $lastReport->final_nominal ?? 0,
        ];
    }

    // 🔥 METHOD UNTUK HITUNG STOK AKHIR
    public function hitungStokAkhir()
    {
        $stokAwal = self::getStokAwal($this->branch_id, $this->tanggal);

        // Jika shift siang, ambil dari shift pagi hari ini
        if ($this->shift === 'siang') {
            $shiftPagi = self::where('branch_id', $this->branch_id)
                ->whereDate('tanggal', $this->tanggal)
                ->where('shift', 'pagi')
                ->first();

            if ($shiftPagi) {
                $stokAwal = [
                    'jumlah_barang' => $shiftPagi->final_jumlah_barang,
                    'nominal' => $shiftPagi->final_nominal,
                ];
            }
        }

        $this->final_jumlah_barang = $stokAwal['jumlah_barang'] + $this->pencairan_jumlah_barang - $this->pelunasan_jumlah_barang;
        $this->final_nominal = $stokAwal['nominal'] + $this->pencairan_nominal - $this->pelunasan_nominal;
    }

    // ===============================
    // RELATIONSHIPS
    // ===============================

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function photos()
    {
        return $this->hasMany(DailyReportPhoto::class);
    }

    // ===============================
    // ACCESSORS
    // ===============================

    public function getShiftLabelAttribute()
    {
        return match ($this->shift) {
            'pagi' => 'Shift Pagi (08:00 - 16:00)',
            'siang' => 'Shift Siang (14:00 - 22:00)',
            default => $this->shift,
        };
    }

    public function getStatusBadgeAttribute()
    {
        return $this->validasi_manager
            ? ['color' => 'green', 'text' => 'Sudah Divalidasi']
            : ['color' => 'orange', 'text' => 'Belum Divalidasi'];
    }

    public function getPencairanNominalFormattedAttribute()
    {
        return 'Rp ' . number_format($this->pencairan_nominal, 0, ',', '.');
    }

    public function getPelunasanNominalFormattedAttribute()
    {
        return 'Rp ' . number_format($this->pelunasan_nominal, 0, ',', '.');
    }

    // ===============================
    // SCOPES
    // ===============================

    public function scopeByBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeByTanggal($query, $tanggal)
    {
        return $query->whereDate('tanggal', $tanggal);
    }

    public function scopeByShift($query, $shift)
    {
        return $query->where('shift', $shift);
    }

    public function scopeValidated($query)
    {
        return $query->where('validasi_manager', true);
    }

    public function scopeNotValidated($query)
    {
        return $query->where('validasi_manager', false);
    }

    public function scopeBelumLengkap($query, $tanggal)
    {
        // Cabang yang belum lengkap 2 shift di tanggal tertentu
        return $query->selectRaw('branch_id, COUNT(*) as total_laporan')
            ->whereDate('tanggal', $tanggal)
            ->groupBy('branch_id')
            ->having('total_laporan', '<', 2);
    }

    // ===============================
    // METHODS
    // ===============================

    public function validate($validatorId)
    {
        $this->update([
            'validasi_manager' => true,
            'validated_by' => $validatorId,
            'validated_at' => now(),
        ]);
    }

    public function hasPhotos()
    {
        return $this->photos()->count() > 0;
    }

    public function deleteWithPhotos()
    {
        // Delete photos from storage
        foreach ($this->photos as $photo) {
            Storage::disk('public')->delete($photo->file_path);
        }

        // Delete record (cascade will delete photos table)
        return $this->delete();
    }

    // ===============================
    // BOOT
    // ===============================

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($report) {
            // Auto delete photos from storage when report is deleted
            foreach ($report->photos as $photo) {
                Storage::disk('public')->delete($photo->file_path);
            }
        });
    }
}
