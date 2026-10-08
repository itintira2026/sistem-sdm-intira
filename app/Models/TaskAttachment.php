<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TaskAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'task_entry_id',
        'file_path',
        'file_name',
        'file_size', // BARU — diisi saat upload, biar gak perlu Storage::size() satu-satu tiap nampilin total ukuran
        'uploaded_by',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function taskEntry()
    {
        return $this->belongsTo(TaskEntry::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * BARU — Cegah file yatim di storage (Part lampiran, poin 1).
     *
     * Begitu baris TaskAttachment dihapus (dari fitur hapus manual
     * nanti, atau dari mana pun di masa depan), file fisiknya OTOMATIS
     * ikut dihapus. Logic hapus file jadi SATU TEMPAT aja di sini,
     * gak perlu ditulis ulang tiap ada fitur baru yang menghapus
     * attachment — tinggal panggil $attachment->delete().
     */
    protected static function booted(): void
    {
        static::deleting(function (TaskAttachment $attachment) {
            if ($attachment->file_path && Storage::disk('public')->exists($attachment->file_path)) {
                Storage::disk('public')->delete($attachment->file_path);
            }
        });
    }
}
