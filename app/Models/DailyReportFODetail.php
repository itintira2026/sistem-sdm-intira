<?php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;

// class DailyReportFODetail extends Model
// {
//     use HasFactory;

//     protected $table = 'daily_report_fo_details';

//     protected $fillable = [
//         'daily_report_fo_id',
//         'field_id',
//         'value_boolean',
//         'value_number',
//         'value_text',
//         'value_time',
//     ];

//     protected $casts = [
//         'value_boolean' => 'boolean',
//         'value_number' => 'decimal:2',
//     ];

//     // Relationships
//     public function report()
//     {
//         return $this->belongsTo(DailyReportFO::class, 'daily_report_fo_id');
//     }

//     public function field()
//     {
//         return $this->belongsTo(ReportField::class, 'field_id');
//     }

//     public function photos()
//     {
//         return $this->hasMany(DailyReportFOPhoto::class, 'daily_report_fo_detail_id', 'id');
//     }

//     // Get value based on field type
//     public function getValue()
//     {
//         $field = $this->field;

//         switch ($field->input_type) {
//             case 'checkbox':
//                 return $this->value_boolean;
//             case 'number':
//             case 'photo_number':
//                 return $this->value_number;
//             case 'text':
//                 return $this->value_text;
//             case 'time':
//                 return $this->value_time;
//             default:
//                 return null;
//         }
//     }
// }

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyReportFODetail extends Model
{
    use HasFactory;

    protected $table = 'daily_report_fo_details';

    protected $fillable = [
        'daily_report_fo_id',
        'field_id',
        'value_boolean',
        'value_number',
        'value_text',
        'value_time',
    ];

    protected $casts = [
        'value_boolean' => 'boolean',
        'value_number' => 'decimal:2',
    ];

    // =========================================================
    // RELATIONSHIPS
    // =========================================================

    public function report()
    {
        return $this->belongsTo(DailyReportFo::class, 'daily_report_fo_id');
    }

    public function field()
    {
        return $this->belongsTo(ReportField::class, 'field_id');
    }

    /**
     * Relasi ke Photos
     * PENTING: cukup 1 parameter foreign key saja
     * Parameter ke-3 (local key) TIDAK diisi → default pakai 'id'
     */
    public function photos()
    {
        return $this->hasMany(DailyReportFoPhoto::class, 'daily_report_fo_detail_id');
    }

    // =========================================================
    // HELPER
    // =========================================================

    /**
     * Ambil nilai sesuai tipe field
     */
    public function getValue()
    {
        if (! $this->field) {
            return null;
        }

        switch ($this->field->input_type) {
            case 'checkbox':
                return $this->value_boolean;
            case 'number':
            case 'photo_number':
                return $this->value_number;
            case 'text':
                return $this->value_text;
            case 'time':
                return $this->value_time;
            default:
                return null;
        }
    }
}
