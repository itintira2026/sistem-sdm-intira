<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PresensiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     return view('presensi.index');
    // }

    public function index(Request $request)
    {
        $tanggal  = $request->input('tanggal', now()->toDateString());
        $search   = $request->input('search');
        $perPage  = (int) $request->input('per_page', 10);

        // 🔥 [BARU] FILTER STATUS PRESENSI
        $statusFilter = $request->input('status_presensi');

        $query = User::where('is_active', true);
        // $query = User::where('is_active', true)
        //     ->withCount([
        //         'presensis as total_presensi' => function ($q) use ($tanggal) {
        //             $q->whereDate('tanggal', $tanggal);
        //         },
        //         'presensis as check_in' => function ($q) use ($tanggal) {
        //             $q->whereDate('tanggal', $tanggal)
        //                 ->where('status', 'CHECK_IN');
        //         },
        //         'presensis as istirahat_out' => function ($q) use ($tanggal) {
        //             $q->whereDate('tanggal', $tanggal)
        //                 ->where('status', 'ISTIRAHAT_OUT');
        //         },
        //         'presensis as istirahat_in' => function ($q) use ($tanggal) {
        //             $q->whereDate('tanggal', $tanggal)
        //                 ->where('status', 'ISTIRAHAT_IN');
        //         },
        //         'presensis as check_out' => function ($q) use ($tanggal) {
        //             $q->whereDate('tanggal', $tanggal)
        //                 ->where('status', 'CHECK_OUT');
        //         },
        //     ]);

        // if ($statusFilter === 'BELUM_ABSEN') {
        //     $query->having('total_presensi', '=', 0);
        // }

        // if ($statusFilter === 'LENGKAP') {
        //     $query->havingRaw('
        //         check_in = 1 AND
        //         istirahat_out = 1 AND
        //         istirahat_in = 1 AND
        //         check_out = 1
        //     ');
        // }

        // if ($statusFilter === 'TIDAK_LENGKAP') {
        //     $query->having('total_presensi', '>', 0)
        //         ->havingRaw('
        //         NOT (
        //         check_in = 1 AND
        //         istirahat_out = 1 AND
        //         istirahat_in = 1 AND
        //         check_out = 1
        //         )
        //     ');
        // }




        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $users = $query
            ->with(['presensis' => function ($q) use ($tanggal) {
                $q->whereDate('tanggal', $tanggal);
            }])
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();
        // $users = $query
        //     ->orderBy('name')
        //     ->paginate($perPage)
        //     ->withQueryString();


        $users->getCollection()->transform(function ($user) {

            // GROUP BY STATUS
            $grouped = $user->presensis->keyBy('status');

            $required = [
                'CHECK_IN',
                'ISTIRAHAT_OUT',
                'ISTIRAHAT_IN',
                'CHECK_OUT',
            ];

            $missing = collect($required)->diff($grouped->keys());

            // STATUS PRESENSI
            if ($grouped->isEmpty()) {
                $status = 'BELUM_ABSEN';
            } elseif ($missing->isEmpty()) {
                $status = 'LENGKAP';
            } else {
                $status = 'TIDAK_LENGKAP';
            }

            // RINGKASAN JAM
            $jam = [
                'CHECK_IN'      => $grouped['CHECK_IN']->jam ?? null,
                'ISTIRAHAT_OUT' => $grouped['ISTIRAHAT_OUT']->jam ?? null,
                'ISTIRAHAT_IN'  => $grouped['ISTIRAHAT_IN']->jam ?? null,
                'CHECK_OUT'     => $grouped['CHECK_OUT']->jam ?? null,
            ];

            // DETEKSI TELAT
            $telat = [];

            if (!empty($jam['CHECK_IN']) && $jam['CHECK_IN'] > '08:00:00') {
                $telat[] = 'CHECK_IN';
            }

            // ISTIRAHAT IN (BEDA JUMAT)
            if (!empty($jam['ISTIRAHAT_IN'])) {

                // Ambil hari dari tanggal presensi
                $hari = Carbon::parse($user->presensis->first()->tanggal)->dayOfWeek;

                // Default jam masuk istirahat
                $batasIstirahat = '13:00:00';

                // 🔥 Khusus Jumat
                if ($hari === Carbon::FRIDAY) {
                    $batasIstirahat = '14:00:00';
                }

                if ($jam['ISTIRAHAT_IN'] > $batasIstirahat) {
                    $telat[] = 'ISTIRAHAT_IN';
                }
            }

            // INJECT KE OBJECT USER (BUKAN RETURN OBJECT BARU)
            $user->presensi_status = $status;
            $user->presensi_jam    = $jam;
            $user->presensi_telat  = $telat;

            return $user;
        });

        if ($statusFilter) {
            $filtered = $users->getCollection()
                ->filter(fn($user) => $user->presensi_status === $statusFilter)
                ->values();

            // replace collection pagination
            $users->setCollection($filtered);
        }

        // dd($users, $tanggal);

        return view('presensi.index', compact(
            'users',
            'tanggal'
        ));
    }



    // public function index(Request $request)
    // {
    //     // ==========================
    //     // TANGGAL (DEFAULT HARI INI)
    //     // ==========================
    //     $tanggal = $request->input('tanggal', now()->toDateString());

    //     // ==========================
    //     // AMBIL SEMUA USER AKTIF
    //     // + PRESENSI DI TANGGAL ITU
    //     // ==========================
    //     $users = User::where('is_active', true)
    //         ->with(['presensis' => function ($q) use ($tanggal) {
    //             $q->whereDate('tanggal', $tanggal);
    //         }])
    //         ->orderBy('name')
    //         ->get()
    //         ->map(function ($user) {

    //             // ==========================
    //             // GROUP BY STATUS
    //             // ==========================
    //             $grouped = $user->presensis->keyBy('status');

    //             $required = [
    //                 'CHECK_IN',
    //                 'ISTIRAHAT_OUT',
    //                 'ISTIRAHAT_IN',
    //                 'CHECK_OUT',
    //             ];

    //             $missing = collect($required)->diff($grouped->keys());

    //             // ==========================
    //             // STATUS PRESENSI
    //             // ==========================
    //             if ($grouped->isEmpty()) {
    //                 $status = 'BELUM_ABSEN';
    //             } elseif ($missing->isEmpty()) {
    //                 $status = 'LENGKAP';
    //             } else {
    //                 $status = 'TIDAK_LENGKAP';
    //             }

    //             // ==========================
    //             // RINGKASAN JAM
    //             // ==========================
    //             $jam = [
    //                 'CHECK_IN'      => $grouped['CHECK_IN']->jam ?? null,
    //                 'ISTIRAHAT_OUT' => $grouped['ISTIRAHAT_OUT']->jam ?? null,
    //                 'ISTIRAHAT_IN'  => $grouped['ISTIRAHAT_IN']->jam ?? null,
    //                 'CHECK_OUT'     => $grouped['CHECK_OUT']->jam ?? null,
    //             ];

    //             // ==========================
    //             // DETEKSI TELAT
    //             // ==========================
    //             $telat = [];

    //             if (!empty($jam['CHECK_IN']) && $jam['CHECK_IN'] > '08:00:00') {
    //                 $telat[] = 'CHECK_IN';
    //             }

    //             if (!empty($jam['ISTIRAHAT_IN']) && $jam['ISTIRAHAT_IN'] > '13:00:00') {
    //                 $telat[] = 'ISTIRAHAT_IN';
    //             }

    //             // ==========================
    //             // FINAL OBJECT KE VIEW
    //             // ==========================
    //             return (object) [
    //                 'user'   => $user,
    //                 'status' => $status,
    //                 'jam'    => $jam,
    //                 'telat'  => $telat,
    //             ];
    //         });

    //     return view('presensi.index', compact('users', 'tanggal'));
    // }
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
    // public function show(Presensi $presensi)
    // {
    //     //
    // }

    public function show(Request $request, $userId)
    {
        // ==========================
        // TANGGAL (DEFAULT HARI INI)
        // ==========================
        $tanggal = $request->input('tanggal', now()->toDateString());

        // ==========================
        // USER
        // ==========================
        $user = User::where('is_active', true)->findOrFail($userId);

        // ==========================
        // PRESENSI TANGGAL TERPILIH
        // ==========================
        $presensis = Presensi::where('user_id', $user->id)
            ->whereDate('tanggal', $tanggal)
            ->get()
            ->keyBy('status');

        // ==========================
        // TEMPLATE STATUS WAJIB
        // ==========================
        $statuses = [
            'CHECK_IN',
            'ISTIRAHAT_OUT',
            'ISTIRAHAT_IN',
            'CHECK_OUT',
        ];

        // ==========================
        // SUSUN DATA KE VIEW
        // ==========================
        $rows = collect($statuses)->map(function ($status) use ($presensis) {
            return [
                'status'     => $status,
                'jam'        => $presensis[$status]->jam ?? null,
                'wilayah'    => $presensis[$status]->wilayah ?? null,
                'keterangan' => $presensis[$status]->keterangan ?? null,
            ];
        });

        return view('presensi.show', compact(
            'user',
            'tanggal',
            'rows'
        ));
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
            'wilayah'    => 'required|in:WIB,WITA,WIT',
            'keterangan' => 'nullable|string',
        ]);

        // dd($request->all());

        Presensi::updateOrCreate(
            [
                'user_id' => $userId,
                'tanggal' => $request->tanggal,
                'status'  => $request->status,
            ],
            [
                'jam'        => $request->jam,
                'wilayah'    => $request->wilayah,
                'keterangan' => $request->keterangan,
            ]
        );

        return back()->with('success', 'Presensi berhasil diperbarui');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Presensi $presensi)
    {
        //
    }
}
