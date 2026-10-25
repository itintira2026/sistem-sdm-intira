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
     * Ukuran file buat tampilan. File lama (sebelum kolom file_size ada)
     * balikin "—", bukan "0 B", biar gak nyesatin.
     */
    public function formattedSize(): string
    {
        return $this->file_size === null ? '—' : self::formatBytes($this->file_size);
    }

    public static function formatBytes(int|float $bytes): string
    {
        if ($bytes >= 1073741824) {
            return round($bytes / 1073741824, 2) . ' GB';
        }

        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 1) . ' MB';
        }

        if ($bytes >= 1024) {
            return round($bytes / 1024, 1) . ' KB';
        }

        return $bytes . ' B';
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
