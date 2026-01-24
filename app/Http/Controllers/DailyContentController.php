<?php

namespace App\Http\Controllers;

use App\Models\DailyContent;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Branch;

class DailyContentController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        $tanggal = $request->input('tanggal', now()->toDateString());
        $perPage = (int) $request->input('per_page', 10);

        $statusFilter = $request->input('status_konten'); // 0,1,2

        $targetKonten = 2;

        $query = Branch::query();

        $search = $request->input('search');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }


        // if ($kodeCabang) {
        //     $query->where('kode', 'like', "%{$kodeCabang}%");
        // }

        // if ($namaCabang) {
        //     $query->where('nama', 'like', "%{$namaCabang}%");
        // }

        $branches = $query
            ->with(['dailyContents' => function ($q) use ($tanggal) {
                $q->whereDate('tanggal', $tanggal);
            }])
            ->orderBy('code')
            ->paginate($perPage)
            ->withQueryString();

        $branches->getCollection()->transform(function ($branch) use ($targetKonten) {

            $jumlahKonten = $branch->dailyContents->count();

            // 🔥 STATUS LOGIKA
            if ($jumlahKonten === 0) {
                $status = 'BELUM_ADA';
            } elseif ($jumlahKonten < $targetKonten) {
                $status = 'BARU_' . $jumlahKonten;
            } else {
                $status = 'TERPENUHI';
            }

            // 🔥 INJECT KE OBJECT
            $branch->konten_jumlah = $jumlahKonten;
            $branch->konten_status = $status;

            return $branch;
        });

        if ($statusFilter !== null && $statusFilter !== '') {
            $filtered = $branches->getCollection()->filter(function ($branch) use ($statusFilter) {
                return $branch->konten_jumlah == $statusFilter;
            })->values();

            $branches->setCollection($filtered);
        }
        // $branchesForInput = Branch::select('id', 'code', 'name')->get();

        return view('daily_content.index', compact(
            // 'branchesForInput',
            'branches',
            'tanggal',
            'targetKonten'
        ));
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
        // dd($request);
        $request->validate([
            'branch_id'  => 'required|exists:branches,id',
            'tanggal'    => 'required|date',
            'keterangan' => 'nullable|string',
        ]);


        DailyContent::create([
            'branch_id'  => $request->branch_id,
            'tanggal'    => $request->tanggal,
            'keterangan' => $request->keterangan,
        ]);

        return back()->with('success', 'Konten berhasil ditambahkan');
    }
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'branch_id'     => 'required|exists:branches,id',
    //         'tanggal'       => 'required|date',
    //         'jumlah_konten' => 'required|integer|min:0',
    //         'keterangan'    => 'nullable|string',
    //     ]);

    //     // ==========================
    //     // SIMPAN / UPDATE DATA
    //     // 1 CABANG = 1 DATA PER HARI
    //     // ==========================
    //     DailyContent::updateOrCreate(
    //         [
    //             'branch_id' => $request->branch_id,
    //             'tanggal'   => $request->tanggal,
    //         ],
    //         [
    //             'jumlah_konten' => $request->jumlah_konten,
    //             'keterangan'    => $request->keterangan,
    //         ]
    //     );

    //     return back()->with('success', 'Data konten harian berhasil disimpan');
    // }



    /**
     * Display the specified resource.
     */
    public function show(DailyContent $dailyContent)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DailyContent $dailyContent)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DailyContent $dailyContent)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DailyContent $dailyContent)
    {
        //
    }
}
