<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\ClosingCabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FotoClosingController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware(['auth', 'role:hr|superadmin']);
    // }

    public function index(Request $request)
    {
        $tanggal  = $request->input('tanggal', now()->toDateString());
        $branchId = $request->input('branch_id');

        // Ambil semua branch aktif untuk dropdown filter
        $branches = Branch::where('is_active', true)
            ->orderBy('name')
            ->get();

        // Query closing cabang berdasarkan tanggal presensi
        $query = ClosingCabang::with([
            'presensi',
            'presensi.user',
            'presensi.user.branches',
            'presensi.branch',
        ])
            ->whereHas('presensi', function ($q) use ($tanggal) {
                $q->whereDate('tanggal', $tanggal)
                    ->where('status', 'CHECK_OUT');
            })
            ->whereHas('presensi.user', function ($q) {
                $q->where('is_active', true);
            });

        // Filter by cabang
        if ($branchId) {
            $query->whereHas('presensi.user.branches', function ($q) use ($branchId) {
                $q->where('branches.id', $branchId);
            });
        }

        $closings = $query->get();

        // ==========================
        // TRANSFORM & GROUP
        // ==========================
        // Urutan kategori yang diinginkan
        $kategoriOrder = ['laci', 'gudang', 'ruang_pelayanan', 'gembok'];

        $kategoriLabel = [
            'laci'           => 'Laci',
            'gudang'         => 'Gudang',
            'ruang_pelayanan' => 'Ruang Pelayanan',
            'gembok'         => 'Gembok',
        ];

        $kategoriColor = [
            'laci'           => 'bg-blue-100 text-blue-700',
            'gudang'         => 'bg-yellow-100 text-yellow-700',
            'ruang_pelayanan' => 'bg-green-100 text-green-700',
            'gembok'         => 'bg-red-100 text-red-700',
        ];

        // Group: branch → user → kategori
        // Struktur: $grouped[branch_name][user_id] = ['user' => ..., 'branch' => ..., 'fotos' => [kategori => closing|null]]
        $grouped = [];

        foreach ($closings as $closing) {
            $presensi = $closing->presensi;
            if (!$presensi || !$presensi->user) continue;

            $user   = $presensi->user;
            $branch = $presensi->branch ?? $user->branches->first();

            if (!$branch) continue;

            $branchName = $branch->name;
            $userId     = $user->id;

            if (!isset($grouped[$branchName])) {
                $grouped[$branchName] = [
                    'branch' => $branch,
                    'users'  => [],
                ];
            }

            if (!isset($grouped[$branchName]['users'][$userId])) {
                $grouped[$branchName]['users'][$userId] = [
                    'user'   => $user,
                    'branch' => $branch,
                    'jam'    => $presensi->jam
                        ? \Carbon\Carbon::parse($presensi->jam)->format('H:i')
                        : '-',
                    'fotos'  => array_fill_keys($kategoriOrder, null),
                ];
            }

            // Assign foto ke kategori yang sesuai
            $kat = $closing->kategori;
            if ($kat && in_array($kat, $kategoriOrder)) {
                $grouped[$branchName]['users'][$userId]['fotos'][$kat] = [
                    'id'       => $closing->id,
                    'foto_url' => Storage::url($closing->foto),
                    'kategori' => $kat,
                    'label'    => $kategoriLabel[$kat] ?? $kat,
                    'color'    => $kategoriColor[$kat] ?? 'bg-gray-100 text-gray-600',
                ];
            }
        }

        // Urutkan grouped by nama cabang (abjad)
        ksort($grouped);

        // Kalau filter cabang aktif, pastikan hanya cabang itu yang tampil
        if ($branchId) {
            $grouped = array_filter($grouped, function ($data) use ($branchId) {
                return $data['branch']->id == $branchId;
            });
        }

        return view('foto-closing.index', compact(
            'grouped',
            'branches',
            'tanggal',
            'branchId',
            'kategoriOrder',
            'kategoriLabel',
            'kategoriColor'
        ));
    }
}
