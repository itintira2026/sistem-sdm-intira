<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClosingCabang extends Model
{
    protected $fillable = [
        'presensi_id',
        'kategori',
        'foto'

    ];
    public function presensi()
    {
        return $this->belongsTo(Presensi::class);
    }
}
