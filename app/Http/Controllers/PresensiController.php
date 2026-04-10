<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use App\Models\User;
use Carbon\Carbon;
// use Illuminate\Container\Attributes\Storage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class PresensiController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        $tanggal  = $request->input('tanggal', now()->toDateString());
        $search   = $request->input('search');
        $perPage  = (int) $request->input('per_page', 10);
        $statusFilter = $request->input('status_presensi');

        $query = User::where('is_active', true);

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $query->with(['presensis' => function ($q) use ($tanggal) {
            $q->whereDate('tanggal', $tanggal);
        }]);

        // 🔥 FILTER STATUS SEBELUM PAGINATION
        if ($statusFilter) {
            $allUsers = $query->get();

            $filtered = $allUsers->filter(function ($user) use ($statusFilter) {
                $grouped = $user->presensis->keyBy('status');
                $required = ['CHECK_IN', 'ISTIRAHAT_OUT', 'ISTIRAHAT_IN', 'CHECK_OUT'];

                // 🔥 CEK APAKAH SEMUA STATUS ADA
                $allExists = collect($required)->every(fn($status) => $grouped->has($status));

                // 🔥 JIKA SEMUA STATUS ADA, CEK KETERANGAN
                if ($allExists) {
                    $firstKeterangan = $grouped->first()->keterangan ?? '';

                    // Jika keterangan mengandung "Sakit"
                    if (stripos($firstKeterangan, 'Sakit') !== false) {
                        $status = 'SAKIT';
                    }
                    // Jika keterangan mengandung "Izin" atau "Cuti"
                    elseif (stripos($firstKeterangan, 'Izin') !== false || stripos($firstKeterangan, 'Cuti') !== false) {
                        $status = 'IZIN_CUTI';
                    }
                    // Semua jam 00:00:00 tapi bukan sakit/izin
                    elseif (collect($required)->every(fn($s) => ($grouped[$s]->jam ?? null) === '00:00:00')) {
                        $status = 'IZIN_CUTI';
                    }
                    // Status lengkap normal
                    else {
                        $status = 'LENGKAP';
                    }
                }
                // Belum ada presensi sama sekali
                elseif ($grouped->isEmpty()) {
                    $status = 'BELUM_ABSEN';
                }
                // Ada presensi tapi tidak lengkap
                else {
                    $status = 'TIDAK_LENGKAP';
                }

                return $status === $statusFilter;
            });

            // MANUAL PAGINATION
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $currentItems = $filtered->slice(($currentPage - 1) * $perPage, $perPage)->values();

            $users = new LengthAwarePaginator(
                $currentItems,
                $filtered->count(),
                $perPage,
                $currentPage,
                ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
            );
        } else {
            $users = $query->orderBy('name')->paginate($perPage)->withQueryString();
        }

        // 🔥 TRANSFORM COLLECTION UNTUK SEMUA USER
        $users->getCollection()->transform(function ($user) {
            $grouped = $user->presensis->keyBy('status');
            $required = ['CHECK_IN', 'ISTIRAHAT_OUT', 'ISTIRAHAT_IN', 'CHECK_OUT'];
            $missing = collect($required)->diff($grouped->keys());

            // 🔥 DETEKSI STATUS BERDASARKAN KETERANGAN
            if ($grouped->isEmpty()) {
                $status = 'BELUM_ABSEN';
            } elseif ($missing->isEmpty()) {
                // Semua status ada, cek keterangan
                $firstKeterangan = $grouped->first()->keterangan ?? '';

                if (stripos($firstKeterangan, 'Sakit') !== false) {
                    $status = 'SAKIT';
                } elseif (stripos($firstKeterangan, 'Izin') !== false || stripos($firstKeterangan, 'Cuti') !== false) {
                    $status = 'IZIN_CUTI';
                } elseif (collect($required)->every(fn($s) => ($grouped[$s]->jam ?? null) === '00:00:00')) {
                    $status = 'IZIN_CUTI';
                } else {
                    $status = 'LENGKAP';
                }
            } else {
                $status = 'TIDAK_LENGKAP';
            }
            $jam = [
                'CHECK_IN'      => $grouped->get('CHECK_IN')?->jam ? \Carbon\Carbon::parse($grouped->get('CHECK_IN')->jam)->format('H:i:s') : null,
                'ISTIRAHAT_OUT' => $grouped->get('ISTIRAHAT_OUT')?->jam ? \Carbon\Carbon::parse($grouped->get('ISTIRAHAT_OUT')->jam)->format('H:i:s') : null,
                'ISTIRAHAT_IN'  => $grouped->get('ISTIRAHAT_IN')?->jam ? \Carbon\Carbon::parse($grouped->get('ISTIRAHAT_IN')->jam)->format('H:i:s') : null,
                'CHECK_OUT'     => $grouped->get('CHECK_OUT')?->jam ? \Carbon\Carbon::parse($grouped->get('CHECK_OUT')->jam)->format('H:i:s') : null,
            ];

            $telat = [];
            if (!empty($jam['CHECK_IN']) && $jam['CHECK_IN'] > '08:00:00') {
                $telat[] = 'CHECK_IN';
            }

            if (!empty($jam['ISTIRAHAT_IN'])) {
                $hari = Carbon::parse($user->presensis->first()->tanggal)->dayOfWeek;
                $batasIstirahat = ($hari === Carbon::FRIDAY) ? '14:00:00' : '13:00:00';

                if ($jam['ISTIRAHAT_IN'] > $batasIstirahat) {
                    $telat[] = 'ISTIRAHAT_IN';
                }
            }

            $user->presensi_status = $status;
            $user->presensi_jam    = $jam;
            $user->presensi_telat  = $telat;

            return $user;
        });

        return view('presensi.index', compact('users', 'tanggal'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
   public function show(Request $request, $userId)
{
    $bulan = $request->input('bulan', now()->format('Y-m'));

    $user = User::where('is_active', true)
        ->with('branches')
        ->findOrFail($userId);

    $branch = $user->branches->first();

    $presensis = Presensi::where('user_id', $user->id)
        ->whereYear('tanggal', substr($bulan, 0, 4))
        ->whereMonth('tanggal', substr($bulan, 5, 2))
        ->orderBy('tanggal')
        ->orderBy('jam')
        ->get();

    // Group per tanggal
    $grouped = $presensis->groupBy(fn($p) => \Carbon\Carbon::parse($p->tanggal)->format('Y-m-d'));

    // Summary
    $summary = [
        'total_hadir'     => 0,
        'total_terlambat' => 0,
        'total_izin'      => 0,
        'total_alpha'     => 0,
    ];

    foreach ($grouped as $tanggal => $entries) {
        $checkIn = $entries->where('status', 'CHECK_IN')->first();

        if (!$checkIn) {
            $summary['total_alpha']++;
        } elseif ($checkIn->keterangan && (
            str_contains(strtolower($checkIn->keterangan), 'izin') ||
            str_contains(strtolower($checkIn->keterangan), 'sakit')
        )) {
            $summary['total_izin']++;
        } else {
            $jamCI = \Carbon\Carbon::parse($checkIn->jam);
            $hour  = $jamCI->hour;
            $late  = false;
            if ($hour >= 8 && $hour < 12) {
                $late = $jamCI->gt(\Carbon\Carbon::parse($tanggal . ' 08:00:00'));
            } elseif ($hour > 13 && $hour <= 21) {
                $late = $jamCI->gt(\Carbon\Carbon::parse($tanggal . ' 13:00:00'));
            }
            $late ? $summary['total_terlambat']++ : $summary['total_hadir']++;
        }
    }

    return view('presensi.show', compact('user', 'bulan', 'branch', 'grouped', 'summary'));
}
    // public function show(Request $request, $userId)
    // {
    //     $tanggal = $request->input('tanggal', now()->toDateString());

    //     $user = User::where('is_active', true)
    //         ->with('branches') // ← tambah ini
    //         ->findOrFail($userId);

    //     $branch = $user->branches->first(); // ← ambil cabang

    //     $presensis = Presensi::where('user_id', $user->id)
    //         ->whereDate('tanggal', $tanggal)
    //         ->get()
    //         ->keyBy('status');
    //     // dd($presensis);

    //     $statuses = ['CHECK_IN', 'ISTIRAHAT_OUT', 'ISTIRAHAT_IN', 'CHECK_OUT'];

    //     $rows = collect($statuses)->map(function ($status) use ($presensis) {
    //         return [
    //             'status'     => $status,
    //             'id'         => $presensis->get($status)?->id, // ← tambah id untuk delete
    //             'jam'        => $presensis->get($status)?->jam
    //                 ? \Carbon\Carbon::parse($presensis->get($status)->jam)->format('H:i:s')
    //                 : null,
    //             'wilayah'    => $presensis->get($status)?->wilayah ?? null,
    //             'keterangan' => $presensis->get($status)?->keterangan ?? null,
    //             'photo'      => $presensis->get($status)?->photo ?? null,
    //         ];
    //     });
    //     // dd($rows);
    //     // ← Ambil outfit photo dari CHECK_IN
    // $checkIn     = $presensis->get('CHECK_IN');
    // $outfitPhoto = $checkIn?->photo_outfit;


    //     return view('presensi.show', compact('user', 'tanggal', 'rows', 'branch', 'outfitPhoto', 'checkIn'));
    // }

    // public function destroy($id)
    // {
    //     $presensi = Presensi::findOrFail($id);

    //     // Hapus foto jika ada
    //     if ($presensi->photo && Storage::exists($presensi->photo)) {
    //         Storage::delete($presensi->photo);
    //     }

    //     // Hapus data
    //     $presensi->delete();

    //     return back()->with('success', 'Data presensi berhasil dihapus');
    // }
    public function destroy($id)
    {
        $presensi = Presensi::findOrFail($id);

        // Hapus foto dari storage
        if ($presensi->photo && Storage::disk('public')->exists($presensi->photo)) {
            Storage::disk('public')->delete($presensi->photo);
        }

        // Hapus data database
        $presensi->delete();

        return back()->with('success', 'Data presensi berhasil dihapus');
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Presensi $presensi)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, $userId)
    {
        $request->validate([
            'tanggal'    => 'required|date',
            'status'     => 'required|string',
            'jam'        => 'nullable',
            'keterangan' => 'nullable|string',
        ]);

        // dd($request->all());

        Presensi::updateOrCreate(
            [
                'user_id' => $userId,
                'tanggal' => $request->tanggal,
                'status'  => $request->status,
                'wilayah'  => 'none', // Wilayah default, bisa diubah sesuai kebutuhan
            ],
            [
                'jam'        => $request->jam,
                'keterangan' => $request->keterangan,
            ]
        );

        return back()->with('success', 'Presensi berhasil diperbarui');
    }


    /**
     * Remove the specified resource from storage.
     */

    // 🔥 METHOD IZIN/CUTI - OTOMATIS
    public function storeIzin(Request $request, $userId)
    {
        // 🔥 AMBIL TANGGAL DARI URL PARAMETER (dari filter tanggal di index)
        $tanggal = $request->input('tanggal', now()->toDateString());

        $statuses = ['CHECK_IN', 'ISTIRAHAT_OUT', 'ISTIRAHAT_IN', 'CHECK_OUT'];

        foreach ($statuses as $status) {
            Presensi::updateOrCreate(
                [
                    'user_id' => $userId,
                    'tanggal' => $tanggal,
                    'status'  => $status,
                ],
                [
                    'jam'        => '00:00:00',
                    'wilayah'    => 'WIB/WITA/WIT',
                    'keterangan' => 'Izin/Cuti',
                ]
            );
        }

        return back()->with('success', 'Data izin/cuti berhasil disimpan untuk semua status presensi');
    }

    // 🔥 METHOD SAKIT - OTOMATIS
    public function storeSakit(Request $request, $userId)
    {
        // 🔥 AMBIL TANGGAL DARI URL PARAMETER (dari filter tanggal di index)
        $tanggal = $request->input('tanggal', now()->toDateString());

        $statuses = ['CHECK_IN', 'ISTIRAHAT_OUT', 'ISTIRAHAT_IN', 'CHECK_OUT'];

        foreach ($statuses as $status) {
            Presensi::updateOrCreate(
                [
                    'user_id' => $userId,
                    'tanggal' => $tanggal,
                    'status'  => $status,
                ],
                [
                    'jam'        => '00:00:00',
                    'wilayah'    => 'none',
                    'keterangan' => 'Sakit',
                ]
            );
        }

        return back()->with('success', 'Data sakit berhasil disimpan untuk semua status presensi');
    }
}
