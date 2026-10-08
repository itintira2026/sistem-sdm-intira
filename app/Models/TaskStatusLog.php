<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskStatusLog extends Model
{
    protected $fillable = [
        'task_id',
        'task_entry_id',
        'task_assignee_id', // BARU (prasyarat G4)
        'action',
        'old_value',
        'new_value',
        'performed_by',
        'reason',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function taskEntry()
    {
        return $this->belongsTo(TaskEntry::class);
    }

    public function taskAssignee()
    {
        return $this->belongsTo(TaskAssignee::class);
    }

    public function performer()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    /**
     * Helper buat nyatet log dari mana pun di kode nanti.
     *
     * DIUBAH (prasyarat G4): tambah parameter $taskAssigneeId — SENGAJA
     * ditaruh PALING BELAKANG (opsional, default null) biar SEMUA
     * pemanggilan lama (TaskValidationService, TaskEntryController,
     * TaskEntryOverrideController — yang masih pakai 7 argumen tanpa ini)
     * TETAP JALAN tanpa perlu diubah satupun.
     */
    public static function record(
        ?int $taskId,
        ?int $taskEntryId,
        string $action,
        ?string $oldValue,
        ?string $newValue,
        ?int $performedBy,
        ?string $reason = null,
        ?int $taskAssigneeId = null
    ): self {
        return self::create([
            'task_id' => $taskId,
            'task_entry_id' => $taskEntryId,
            'task_assignee_id' => $taskAssigneeId,
            'action' => $action,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'performed_by' => $performedBy,
            'reason' => $reason,
        ]);
    }
}
