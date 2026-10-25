<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'tracking_mode',
        'unit',
        'unit_type',
        'has_weight',
        'repeat_mode',
        'period_start',
        'period_end',
        'reference_task_id',
        'created_by',
        'division_scope',
        'status',
    ];

    protected $casts = [
        'has_weight' => 'boolean',
        'period_start' => 'date',
        'period_end' => 'date',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function division()
    {
        return $this->belongsTo(Division::class, 'division_scope');
    }

    public function referenceTask()
    {
        return $this->belongsTo(Task::class, 'reference_task_id');
    }

    public function assignees()
    {
        return $this->hasMany(TaskAssignee::class);
    }

    public function attachments()
    {
        return $this->hasMany(TaskAttachment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFrozen($query)
    {
        return $query->where('status', 'frozen');
    }

    public function isSelfRecord(): bool
    {
        return ! $this->has_weight;
    }

    public function formatValue(float|string|null $value): string
    {
        if ($value === null || $value === '') {
            return '—';
        }

        $n = (float) $value;

        return match ($this->unit_type) {
            'currency' => 'Rp ' . number_format($n, 0, ',', '.'),
            'percent'  => rtrim(rtrim(number_format($n, 2, ',', '.'), '0'), ',') . '%',
            default    => number_format($n, fmod($n, 1) == 0 ? 0 : 2, ',', '.')
                . ($this->unit ? ' ' . $this->unit : ''),
        };
    }
}
