<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskEntry extends Model
{
    use HasFactory;

    /** Persentase di atas target yang dianggap janggal (kemungkinan salah ketik). */
    private const SUSPICIOUS_RATIO = 150;

    protected $fillable = [
        'task_assignee_id',
        'entry_date',
        'value_number',
        'value_checklist',
        'validation_status',
        'checked_by',
        'checked_at',
        'approved_by',
        'approved_at',
        'rejected_reason',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'value_number' => 'decimal:2',
        'value_checklist' => 'boolean',
        'checked_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function taskAssignee()
    {
        return $this->belongsTo(TaskAssignee::class);
    }

    public function checker()
    {
        return $this->belongsTo(User::class, 'checked_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function attachments()
    {
        return $this->hasMany(TaskAttachment::class);
    }

    /**
     * Self-record (Tugas Tambahan) selalu punya validation_status NULL —
     * begitu disimpan langsung final, tanpa alur apapun (§6).
     */
    public function isSelfRecord(): bool
    {
        return $this->validation_status === null;
    }

    /**
     * Sudah ada nilai yang diinput? (numeric atau checklist)
     */
    public function hasSubmittedValue(): bool
    {
        return $this->value_number !== null || $this->value_checklist !== null;
    }

    /**
     * BARU: bandingkan nilai numeric vs target individual.
     * Return null kalau bukan numeric / tidak ada target.
     * status: kurang | sesuai | lebih | janggal
     */
    public function compareToTarget(): ?array
    {
        $task = $this->taskAssignee->task;

        if ($task->tracking_mode !== 'numeric' || $this->value_number === null) {
            return null;
        }

        $target = (float) $this->taskAssignee->individual_target;

        if ($target <= 0) {
            return null;
        }

        $value = (float) $this->value_number;
        $ratio = (int) round($value / $target * 100);
        $diff = $task->formatValue(abs($value - $target));

        if ($value < $target) {
            return ['status' => 'kurang', 'label' => "kurang {$diff} dari target ({$ratio}%)"];
        }

        if ($value == $target) {
            return ['status' => 'sesuai', 'label' => 'sesuai target'];
        }

        if ($ratio >= self::SUSPICIOUS_RATIO) {
            return ['status' => 'janggal', 'label' => "jauh lebih dari target: lebih {$diff} ({$ratio}%), kemungkinan salah input, perlu dicek ulang"];
        }

        return ['status' => 'lebih', 'label' => "melampaui target: lebih {$diff} ({$ratio}%)"];
    }
}
