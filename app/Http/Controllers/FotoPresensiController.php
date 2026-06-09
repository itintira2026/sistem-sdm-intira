<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FotoPresensiController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware(['auth', 'role:hr|superadmin']);
    // }

    public function index(Request $request)
    {
        $tanggal   = $request->input('tanggal', now()->toDateString());
        $branchId  = $request->input('branch_id');
        $tipeFilter = $request->input('tipe', 'CHECK_IN'); // CHECK_IN default
        $search    = $request->input('search');

        // Ambil semua branch aktif untuk dropdown filter
        $branches = Branch::where('is_active', true)
            ->orderBy('name')
            ->get();

        // Build query presensi
        $query = Presensi::with(['user', 'user.branches'])
            ->whereDate('tanggal', $tanggal)
            ->whereHas('user', function ($q) use ($search) {
                $q->where('is_active', true);
                if ($search) {
                    $q->where(function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
                }
            });

        // Filter by cabang (via relasi user → branches pivot)
        if ($branchId) {
            $query->whereHas('user.branches', function ($q) use ($branchId) {
                $q->where('branches.id', $branchId);
            });
        }

        // Filter berdasarkan tipe foto
        if ($tipeFilter === 'OUTFIT') {
            // Outfit: ambil CHECK_IN yang punya photo_outfit
            $query->where('status', 'CHECK_IN')
                ->whereNotNull('photo_outfit')
                ->where('photo_outfit', '!=', '');
        } else {
            // Status presensi normal: CHECK_IN, ISTIRAHAT_OUT, ISTIRAHAT_IN, CHECK_OUT
            $query->where('status', $tipeFilter)
                ->whereNotNull('photo')
                ->where('photo', '!=', '');
        }

        $presensis = $query->orderBy('jam')->get();

        // Transform untuk view
        $fotos = $presensis->map(function ($p) use ($tipeFilter) {
            $user   = $p->user;
            $branch = $user?->branches->first();

            // Tentukan URL foto
            $fotoPath = $tipeFilter === 'OUTFIT' ? $p->photo_outfit : $p->photo;
            $fotoUrl  = $fotoPath ? Storage::url($fotoPath) : null;

            if (!$fotoUrl) return null;

            return [
                'id'         => $p->id,
                'foto_url'   => $fotoUrl,
                'nama'       => $user?->name ?? '-',
                'email'      => $user?->email ?? '-',
                'cabang'     => $branch?->name ?? 'Tidak ada cabang',
                'jam'        => $p->jam ? \Carbon\Carbon::parse($p->jam)->format('H:i') : '-',
                'status'     => $p->status,
                'tipe_label' => $tipeFilter === 'OUTFIT' ? 'Outfit' : str_replace('_', ' ', $p->status),
                'tanggal'    => \Carbon\Carbon::parse($p->tanggal)->translatedFormat('d F Y'),
            ];
        })->filter()->values();

        return view('foto-presensi.index', compact(
            'fotos',
            'branches',
            'tanggal',
            'branchId',
            'tipeFilter',
            'search'
        ));
    }
}
