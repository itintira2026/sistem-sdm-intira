<?php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Model;

// class DailyReportFoPhoto extends Model
// {
//     //
// }

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyReportFoPhoto extends Model
{
    use HasFactory;

    protected $table = 'daily_report_fo_photos';

    // protected $fillable = [
    //     'daily_report_fo_id',
    //     'kategori',
    //     'file_path',
    //     'file_name',
    // ];

    protected $fillable = [
        'daily_report_fo_detail_id', // ⬅️ UBAH dari daily_report_fo_id
        // 'kategori', // ⬅️ HAPUS (tidak perlu lagi)
        'file_path',
        'file_name',
    ];

    /**
     * Relasi ke Detail (UBAH)
     */
    public function detail() // ⬅️ UBAH dari dailyReportFO()
    {
        return $this->belongsTo(DailyReportFODetail::class, 'daily_report_fo_detail_id');
    }

    /**
     * Relasi ke DailyReportFO
     */
    // public function dailyReportFO()
    // {
    //     return $this->belongsTo(DailyReportFO::class);
    // }

    /**
     * Get kategori label
     *
     * @return string
     */
    public function getKategoriLabelAttribute()
    {
        $categories = config('daily_report_fo.photo_categories');

        return $categories[$this->kategori] ?? ucfirst($this->kategori);
    }

    /**
     * Get full URL path
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return asset('storage/'.$this->file_path);
    }

    /**
     * Boot method - auto delete file when photo deleted
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($photo) {
            \App\Helpers\ImageHelper::delete($photo->file_path);
        });
    }
}
