<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
    // ==========================
    // INDEX
    // ==========================

    public function index()
    {
        $user = auth()->user();

        // Ambil absensi hari ini beserta relasi branch (eager load)
        $todayPresensis = Presensi::with('branch')
            ->where('user_id', $user->id)
            ->whereDate('tanggal', now()->toDateString())
            ->orderBy('jam', 'asc')
            ->get();

        // Ambil semua branch aktif user
        $userBranches  = $user->branches()->get();

        // Cabang aktif: ambil dari session jika ada, fallback ke cabang pertama
        $activeBranchId = session('active_branch_id');
        $activeBranch   = $activeBranchId
            ? $userBranches->firstWhere('id', $activeBranchId) ?? $userBranches->first()
            : $userBranches->first();

        return view('attendance.index', compact(
            'user',
            'todayPresensis',
            'userBranches',
            'activeBranch'
        ));
    }

    // ==========================
    // SWITCH BRANCH (opsional, untuk branch switcher di header)
    // ==========================

    public function switchBranch(Request $request, $branchId)
    {
        $user     = auth()->user();
        $branches = $user->branches()->get();

        // Pastikan branch valid untuk user ini
        if (!$branches->contains('id', $branchId)) {
            return back()->with('error', 'Cabang tidak valid.');
        }

        session(['active_branch_id' => $branchId]);

        return back()->with('success', 'Cabang berhasil diganti.');
    }

    // ==========================
    // STORE
    // ==========================

    public function store(Request $request)
    {
        // Validasi
        $request->validate([
            'status'    => 'required|in:CHECK_IN,CHECK_OUT,ISTIRAHAT_IN,ISTIRAHAT_OUT',
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
            'photo'     => 'required|string',
        ]);

        $user = auth()->user();

        // ==========================
        // CEK BRANCH USER
        // ==========================
        $branches = $user->branches()->get();

        if ($branches->isEmpty()) {
            return back()->with('error', 'Anda tidak terdaftar di cabang manapun. Hubungi administrator.');
        }

        // ==========================
        // CARI CABANG TERDEKAT
        // ==========================
        $nearestBranch = null;
        $minDistance   = PHP_FLOAT_MAX;
        $distances     = [];

        foreach ($branches as $branch) {
            if (!$branch->latitude || !$branch->longitude) {
                continue;
            }

            $distance = $this->calculateDistance(
                (float) $request->latitude,
                (float) $request->longitude,
                (float) $branch->latitude,
                (float) $branch->longitude
            );

            $distances[$branch->id] = [
                'branch'   => $branch,
                'distance' => $distance,
            ];

            if ($distance < $minDistance) {
                $minDistance   = $distance;
                $nearestBranch = $branch;
            }
        }

        if (!$nearestBranch) {
            return back()->with('error', 'Data koordinat cabang tidak lengkap. Hubungi administrator.');
        }

        // ==========================
        // VALIDASI RADIUS
        // ==========================
        $maxDistance = 150; // meter

        if ($minDistance > $maxDistance) {
            $branchDistances = [];
            foreach ($distances as $data) {
                $branchDistances[] = '• ' . $data['branch']->name . ': ' . round($data['distance']) . ' m';
            }

            return back()->with(
                'error',
                'Anda berada di luar radius semua cabang.<br>' .
                    implode('<br>', $branchDistances) .
                    '<br>Maksimal radius: ' . $maxDistance . ' meter.'
            );
        }

        $branch = $nearestBranch;

        // ==========================
        // CEK LOGIKA ABSENSI HARI INI
        // ==========================
        $today = Presensi::where('user_id', $user->id)
            ->whereDate('tanggal', now()->toDateString())
            ->get();

        $hasCheckIn      = $today->where('status', 'CHECK_IN')->isNotEmpty();
        $hasCheckOut     = $today->where('status', 'CHECK_OUT')->isNotEmpty();
        $istirahatInCount  = $today->where('status', 'ISTIRAHAT_IN')->count();
        $istirahatOutCount = $today->where('status', 'ISTIRAHAT_OUT')->count();

        switch ($request->status) {
            case 'CHECK_IN':
                if ($hasCheckIn) {
                    return back()->with('error', 'Anda sudah CHECK IN hari ini.');
                }
                break;

            case 'CHECK_OUT':
                if (!$hasCheckIn) {
                    return back()->with('error', 'Anda belum melakukan CHECK IN.');
                }
                if ($hasCheckOut) {
                    return back()->with('error', 'Anda sudah CHECK OUT hari ini.');
                }
                break;

            case 'ISTIRAHAT_IN':
                if (!$hasCheckIn) {
                    return back()->with('error', 'Anda belum melakukan CHECK IN.');
                }
                // Tidak boleh istirahat in kalau sudah istirahat in tapi belum out
                if ($istirahatInCount > $istirahatOutCount) {
                    return back()->with('error', 'Anda sedang dalam ISTIRAHAT. Silakan ISTIRAHAT OUT terlebih dahulu.');
                }
                break;

            case 'ISTIRAHAT_OUT':
                if ($istirahatInCount === 0) {
                    return back()->with('error', 'Anda belum melakukan ISTIRAHAT IN.');
                }
                if ($istirahatOutCount >= $istirahatInCount) {
                    return back()->with('error', 'Anda sudah melakukan ISTIRAHAT OUT.');
                }
                break;
        }

        // ==========================
        // SIMPAN FOTO
        // ==========================
        $photoData = $request->photo;

        // Handle berbagai prefix base64
        if (str_contains($photoData, ';base64,')) {
            $photoData = substr($photoData, strpos($photoData, ',') + 1);
        }
        $photoData = str_replace(' ', '+', $photoData);

        $decoded = base64_decode($photoData, true);
        if ($decoded === false) {
            return back()->with('error', 'Gagal memproses foto. Silakan coba lagi.');
        }

        $dateFolder = now()->format('Y/m/d');
        $imageName  = 'absen_' . now()->format('Y-m-d-H-i-s') . '_' . $user->name . '.jpg';
        $imagePath  = 'absensi/' . $dateFolder . '/' .  $user->name.'/'. $imageName;

        Storage::disk('public')->makeDirectory('absensi/' . $dateFolder);
        Storage::disk('public')->put($imagePath, $decoded);

        // ==========================
        // SIMPAN DATA PRESENSI
        // ==========================
        $presensi = Presensi::create([
            'user_id'    => $user->id,
            'branch_id'  => $branch->id,
            'tanggal'    => now()->toDateString(),
            'status'     => $request->status,
            'jam'        => now()->format('H:i:s'),
            'latitude'   => $request->latitude,
            'longitude'  => $request->longitude,
            'wilayah'    => $branch->name,
            'photo'      => $imagePath,
            'jarak'      => round($minDistance),
            'keterangan' => 'Absensi via mobile di ' . $branch->name,
        ]);

        // ==========================
        // LOG ACTIVITY
        // ==========================
        // activity()
        //     ->performedOn($presensi)
        //     ->causedBy($user)
        //     ->withProperties([
        //         'status' => $request->status,
        //         'branch' => $branch->name,
        //         'jarak'  => round($minDistance) . 'm',
        //     ])
        //     ->log('Melakukan absensi ' . $request->status);

        // ==========================
        // RESPONSE SUKSES
        // ==========================
        $statusMessages = [
            'CHECK_IN'     => 'Selamat bekerja! Check In berhasil.',
            'CHECK_OUT'    => 'Hati-hati di jalan! Check Out berhasil.',
            'ISTIRAHAT_IN' => 'Selamat beristirahat!',
            'ISTIRAHAT_OUT'=> 'Selamat bekerja kembali!',
        ];

        $message = $statusMessages[$request->status] ?? 'Absensi berhasil.';

        return back()->with(
            'success',
            $message . ' (Cabang: ' . $branch->name . ', Jarak: ' . round($minDistance) . 'm)'
        );
    }

    // ==========================
    // HELPER: HITUNG JARAK (Haversine)
    // ==========================

    private function calculateDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371000; // meter

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2)
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    // ==========================
    // HELPER: STATUS ABSENSI HARI INI
    // ==========================

    // private function getTodayAttendanceStatus($userId): array
    // {
    //     $presensis = Presensi::where('user_id', $userId)
    //         ->whereDate('tanggal', now()->toDateString())
    //         ->get();

    //     return [
    //         'has_check_in'      => $presensis->where('status', 'CHECK_IN')->isNotEmpty(),
    //         'has_check_out'     => $presensis->where('status', 'CHECK_OUT')->isNotEmpty(),
    //         'has_istirahat_in'  => $presensis->where('status', 'ISTIRAHAT_IN')->isNotEmpty(),
    //         'has_istirahat_out' => $presensis->where('status', 'ISTIRAHAT_OUT')->isNotEmpty(),
    //         'last_status'       => $presensis->last()?->status,
    //         'last_time'         => $presensis->last()?->jam,
    //         'branch_name'       => $presensis->first()?->branch?->name,
    //     ];
    // }
    public function riwayat(Request $request)
{
    $user  = auth()->user();
    $bulan = $request->get('bulan', now()->format('Y-m')); // format: 2025-03

    [$tahun, $bln] = explode('-', $bulan);

    // Ambil semua presensi bulan tersebut dengan relasi branch
    $presensis = Presensi::with('branch')
        ->where('user_id', $user->id)
        ->whereYear('tanggal', $tahun)
        ->whereMonth('tanggal', $bln)
        ->orderBy('tanggal', 'asc')
        ->orderBy('jam', 'asc')
        ->get();

    // Group per tanggal
    $grouped = $presensis->groupBy(fn($p) => $p->tanggal->format('Y-m-d'));

    // Hitung summary
    $totalHadir     = 0;
    $totalTerlambat = 0;
    $totalIzin      = 0;

    foreach ($grouped as $tanggal => $entries) {
        $checkIn = $entries->where('status', 'CHECK_IN')->first();
        if (!$checkIn) continue;

        $totalHadir++;

        // Cek izin/sakit
        if ($checkIn->keterangan && (
            str_contains(strtolower($checkIn->keterangan), 'izin') ||
            str_contains(strtolower($checkIn->keterangan), 'sakit')
        )) {
            $totalIzin++;
            continue;
        }

        // Cek terlambat
        $jamCI = \Carbon\Carbon::parse($checkIn->jam);
        $hour  = $jamCI->hour;
        $late  = false;

        if ($hour >= 8 && $hour < 12) {
            $late = $jamCI->gt(\Carbon\Carbon::parse($tanggal . ' 08:00:00'));
        } elseif ($hour > 13 && $hour <= 21) {
            $late = $jamCI->gt(\Carbon\Carbon::parse($tanggal . ' 13:00:00'));
        }

        if ($late) $totalTerlambat++;
    }

    $summary = [
        'total_hadir'     => $totalHadir,
        'total_terlambat' => $totalTerlambat,
        'total_izin'      => $totalIzin,
        'total_alpha'     => 0, // bisa dikembangkan dengan data jadwal kerja
    ];

    return view('attendance.riwayat', compact('user', 'bulan', 'grouped', 'summary'));
}

}