<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskAssignee extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'user_id',
        'individual_target',
        'weight_percent',
        'final_percentage',
        'finalized_at',
        'removed_reason', // BARU: diisi kalau dinonaktifkan manual oleh atasan
        'removed_by',     // BARU
    ];

    protected $casts = [
        'individual_target' => 'decimal:2',
        'weight_percent' => 'decimal:2',
        'final_percentage' => 'decimal:2',
        'finalized_at' => 'datetime',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function entries()
    {
        return $this->hasMany(TaskEntry::class);
    }

    public function remover()
    {
        return $this->belongsTo(User::class, 'removed_by');
    }

    /**
     * Terkunci karena SALAH SATU dari dua sebab: mutasi jabatan, atau
     * dinonaktifkan manual dari tugas ini. Keduanya sama-sama mengunci
     * final_percentage dan mengeluarkan dari perhitungan bobot & antrian.
     */
    public function isFinalized(): bool
    {
        return $this->finalized_at !== null;
    }

    /**
     * BARU: khusus dinonaktifkan manual oleh atasan (bukan mutasi jabatan).
     * Dibedakan dari isFinalized() lewat ada-tidaknya removed_reason.
     */
    public function isRemoved(): bool
    {
        return $this->removed_reason !== null;
    }
}
