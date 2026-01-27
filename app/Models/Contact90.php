<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact90 extends Model
{
    use HasFactory;

    protected $table = 'contact90s';

    protected $fillable = [
        'user_id',
        'nama_nasabah',
        'akun_or_notelp',
        'sosmed',
        'situasi',
        'validasi_manager',
        'keterangan',
        'tanggal',
    ];

    protected $casts = [
        'validasi_manager' => 'boolean',
        'tanggal' => 'date',
    ];

    // 🔥 RELATIONSHIP
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 🔥 ACCESSOR - Label Sosmed
    public function getSosmedLabelAttribute()
    {
        return match ($this->sosmed) {
            'DM_IG' => 'DM Instagram',
            'CHAT_WA' => 'Chat WhatsApp',
            'INBOX_FB' => 'Inbox Facebook',
            'MRKT_PLACE_FB' => 'Marketplace Facebook',
            'TIKTOK' => 'TikTok',
            default => $this->sosmed,
        };
    }

    // 🔥 ACCESSOR - Label Situasi
    public function getSituasiLabelAttribute()
    {
        return match ($this->situasi) {
            'tdk_merespon' => 'Tidak Merespon',
            'merespon' => 'Merespon',
            'tertarik' => 'Tertarik',
            'closing' => 'Closing',
            default => $this->situasi,
        };
    }

    // 🔥 ACCESSOR - Badge Color Situasi
    public function getSituasiBadgeAttribute()
    {
        return match ($this->situasi) {
            'tdk_merespon' => ['color' => 'gray', 'text' => 'Tidak Merespon'],
            'merespon' => ['color' => 'blue', 'text' => 'Merespon'],
            'tertarik' => ['color' => 'yellow', 'text' => 'Tertarik'],
            'closing' => ['color' => 'green', 'text' => 'Closing'],
            default => ['color' => 'gray', 'text' => 'Unknown'],
        };
    }
}
