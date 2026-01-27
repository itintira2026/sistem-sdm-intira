<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\BranchUser;
use App\Models\Contact90;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class Contact90Controller extends Controller
{
    // ==============================
    // FO (FRONT OFFICE) SECTION
    // ==============================

    /**
     * Dashboard & List Kontak FO
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $tanggal = $request->input('tanggal', now()->toDateString());
        $search = $request->input('search');
        $sosmedFilter = $request->input('sosmed');
        $situasiFilter = $request->input('situasi');
        $validasiFilter = $request->input('validasi');
        $perPage = (int) $request->input('per_page', 10);

        // Query untuk FO (hanya lihat kontak sendiri)
        // Superadmin bisa lihat semua dengan pilih user
        if ($user->hasRole('superadmin')) {
            $selectedUserId = $request->input('user_id', $user->id);
            $query = Contact90::where('user_id', $selectedUserId);
        } else {
            $query = Contact90::where('user_id', $user->id);
        }

        $query->whereDate('tanggal', $tanggal);

        // Filter Search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_nasabah', 'like', "%{$search}%")
                    ->orWhere('akun_or_notelp', 'like', "%{$search}%");
            });
        }

        // Filter Sosmed
        if ($sosmedFilter) {
            $query->where('sosmed', $sosmedFilter);
        }

        // Filter Situasi
        if ($situasiFilter) {
            $query->where('situasi', $situasiFilter);
        }

        // Filter Validasi
        if ($validasiFilter !== null) {
            $query->where('validasi_manager', $validasiFilter === '1');
        }

        $contacts = $query->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        // $allData = Contact90::all();
        // dd($allData);

        // Statistik Dashboard
        $stats = [
            'total' => Contact90::where('user_id', $user->id)
                ->whereDate('tanggal', $tanggal)
                ->count(),
            'target' => 90,
            'validated' => Contact90::where('user_id', $user->id)
                ->whereDate('tanggal', $tanggal)
                ->where('validasi_manager', true)
                ->count(),
            'pending' => Contact90::where('user_id', $user->id)
                ->whereDate('tanggal', $tanggal)
                ->where('validasi_manager', false)
                ->count(),
            'closing' => Contact90::where('user_id', $user->id)
                ->whereDate('tanggal', $tanggal)
                ->where('situasi', 'closing')
                ->count(),
            'tertarik' => Contact90::where('user_id', $user->id)
                ->whereDate('tanggal', $tanggal)
                ->where('situasi', 'tertarik')
                ->count(),
        ];

        // Untuk superadmin: list semua FO
        $foList = null;
        if ($user->hasRole('superadmin')) {
            $foList = User::role('fo')->orderBy('name')->get();
        }

        // dd($contacts);

        return view('contact90.index', compact(
            'contacts',
            'tanggal',
            'stats',
            'foList'
        ));
    }

    /**
     * Form Create Kontak
     */
    public function create()
    {
        $user = Auth::user();

        // Untuk superadmin: tampilkan dropdown FO
        $foList = null;
        if ($user->hasRole('superadmin')) {
            $foList = User::role('fo')->orderBy('name')->get();
        }

        return view('contact90.create', compact('foList'));
    }

    /**
     * Store Kontak Baru
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Validasi
        $validated = $request->validate([
            'user_id' => $user->hasRole('superadmin') ? 'required|exists:users,id' : 'nullable',
            'nama_nasabah' => [
                'required',
                'string',
                'max:255',
                Rule::unique('contact90s')
                    ->where('tanggal', $request->input('tanggal', now()->toDateString()))
            ],
            'akun_or_notelp' => 'required|string|max:255',
            'sosmed' => 'required|in:DM_IG,CHAT_WA,INBOX_FB,MRKT_PLACE_FB,TIKTOK',
            'situasi' => 'required|in:tdk_merespon,merespon,tertarik,closing',
            'keterangan' => 'nullable|string|max:500',
            'tanggal' => $user->hasRole('superadmin') ? 'required|date' : 'nullable',
        ], [
            'nama_nasabah.unique' => 'Nasabah "' . $request->nama_nasabah . '" sudah diinput pada tanggal ' . Carbon::parse($request->input('tanggal', now()->toDateString()))->format('d M Y') . '. Tidak boleh duplikat.',
        ]);

        // Tentukan user_id dan tanggal
        $userId = $user->hasRole('superadmin') ? $request->user_id : $user->id;
        $tanggal = $user->hasRole('superadmin') ? $request->tanggal : now()->toDateString();

        // Create
        Contact90::create([
            'user_id' => $userId,
            'nama_nasabah' => $validated['nama_nasabah'],
            'akun_or_notelp' => $validated['akun_or_notelp'],
            'sosmed' => $validated['sosmed'],
            'situasi' => $validated['situasi'],
            'keterangan' => $validated['keterangan'],
            'tanggal' => $tanggal,
        ]);

        return redirect()->route('contact90.index', ['tanggal' => $tanggal])
            ->with('success', 'Kontak berhasil ditambahkan!');
    }

    /**
     * Form Edit Kontak
     */
    public function edit(Contact90 $contact90)
    {
        $user = Auth::user();

        // Cek permission: hanya bisa edit kontak sendiri dan belum divalidasi
        // Kecuali superadmin
        if (!$user->hasRole('superadmin')) {
            if ($contact90->user_id !== $user->id || $contact90->validasi_manager) {
                abort(403, 'Anda tidak memiliki akses untuk edit kontak ini.');
            }
        }

        return view('contact90.edit', compact('contact90'));
    }

    /**
     * Update Kontak
     */
    public function update(Request $request, Contact90 $contact90)
    {
        $user = Auth::user();

        // Cek permission
        if (!$user->hasRole('superadmin')) {
            if ($contact90->user_id !== $user->id || $contact90->validasi_manager) {
                abort(403, 'Anda tidak memiliki akses untuk edit kontak ini.');
            }
        }

        // Validasi
        $validated = $request->validate([
            'nama_nasabah' => [
                'required',
                'string',
                'max:255',
                Rule::unique('contact90s')
                    ->where('tanggal', $contact90->tanggal)
                    ->ignore($contact90->id)
            ],
            'akun_or_notelp' => 'required|string|max:255',
            'sosmed' => 'required|in:DM_IG,CHAT_WA,INBOX_FB,MRKT_PLACE_FB,TIKTOK',
            'situasi' => 'required|in:tdk_merespon,merespon,tertarik,closing',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $contact90->update($validated);

        return redirect()->route('contact90.index', ['tanggal' => $contact90->tanggal])
            ->with('success', 'Kontak berhasil diperbarui!');
    }

    /**
     * Delete Kontak
     */
    public function destroy(Contact90 $contact90)
    {
        $user = Auth::user();

        // Cek permission
        if (!$user->hasRole('superadmin')) {
            if ($contact90->user_id !== $user->id || $contact90->validasi_manager) {
                abort(403, 'Anda tidak memiliki akses untuk hapus kontak ini.');
            }
        }

        $tanggal = $contact90->tanggal;
        $contact90->delete();

        return redirect()->route('contact90.index', ['tanggal' => $tanggal])
            ->with('success', 'Kontak berhasil dihapus!');
    }

    // ==============================
    // MANAGER SECTION
    // ==============================

    /**
     * Dashboard Tim untuk Manager
     */
    // public function managerDashboard(Request $request)
    // {
    //     $tanggal = $request->input('tanggal', now()->toDateString());

    //     // Total FO
    //     $totalFo = User::role('fo')->count();

    //     // Total Kontak Hari Ini
    //     $totalKontak = Contact90::whereDate('tanggal', $tanggal)->count();

    //     // Target (90 x jumlah FO)
    //     $target = $totalFo * 90;

    //     // Belum Validasi
    //     $belumValidasi = Contact90::whereDate('tanggal', $tanggal)
    //         ->where('validasi_manager', false)
    //         ->count();

    //     // Sudah Validasi
    //     $sudahValidasi = Contact90::whereDate('tanggal', $tanggal)
    //         ->where('validasi_manager', true)
    //         ->count();

    //     // Breakdown Situasi
    //     $closing = Contact90::whereDate('tanggal', $tanggal)->where('situasi', 'closing')->count();
    //     $tertarik = Contact90::whereDate('tanggal', $tanggal)->where('situasi', 'tertarik')->count();
    //     $merespon = Contact90::whereDate('tanggal', $tanggal)->where('situasi', 'merespon')->count();
    //     $tdkMerespon = Contact90::whereDate('tanggal', $tanggal)->where('situasi', 'tdk_merespon')->count();

    //     return view('contact90.manager.dashboard', compact(
    //         'tanggal',
    //         'totalFo',
    //         'totalKontak',
    //         'target',
    //         'belumValidasi',
    //         'sudahValidasi',
    //         'closing',
    //         'tertarik',
    //         'merespon',
    //         'tdkMerespon'
    //     ));
    // }

    /**
     * Daftar FO untuk Manager
     */
    // public function managerFoList(Request $request)
    // {
    //     $tanggal = $request->input('tanggal', now()->toDateString());
    //     $search = $request->input('search');

    //     $query = User::role('fo');

    //     if ($search) {
    //         $query->where('name', 'like', "%{$search}%");
    //     }

    //     $foList = $query->orderBy('name')->get()->map(function ($fo) use ($tanggal) {
    //         $total = Contact90::where('user_id', $fo->id)
    //             ->whereDate('tanggal', $tanggal)
    //             ->count();

    //         $validated = Contact90::where('user_id', $fo->id)
    //             ->whereDate('tanggal', $tanggal)
    //             ->where('validasi_manager', true)
    //             ->count();

    //         $pending = $total - $validated;

    //         $fo->kontak_total = $total;
    //         $fo->kontak_validated = $validated;
    //         $fo->kontak_pending = $pending;

    //         return $fo;
    //     });

    //     return view('contact90.manager.folist', compact('foList', 'tanggal'));
    // }

    // /**
    //  * Detail Kontak per FO untuk Manager
    //  */
    // public function managerFoDetail(Request $request, User $user)
    // {
    //     $tanggal = $request->input('tanggal', now()->toDateString());
    //     $sosmedFilter = $request->input('sosmed');
    //     $situasiFilter = $request->input('situasi');
    //     $validasiFilter = $request->input('validasi');
    //     $perPage = (int) $request->input('per_page', 25);

    //     $query = Contact90::where('user_id', $user->id)
    //         ->whereDate('tanggal', $tanggal);

    //     if ($sosmedFilter) {
    //         $query->where('sosmed', $sosmedFilter);
    //     }

    //     if ($situasiFilter) {
    //         $query->where('situasi', $situasiFilter);
    //     }

    //     if ($validasiFilter !== null) {
    //         $query->where('validasi_manager', $validasiFilter === '1');
    //     }

    //     $contacts = $query->orderBy('validasi_manager')
    //         ->orderBy('created_at', 'desc')
    //         ->paginate($perPage)
    //         ->withQueryString();

    //     return view('contact90.manager.fodetail', compact('user', 'contacts', 'tanggal'));
    // }

    // /**
    //  * Validasi Kontak (Single)
    //  */
    // public function validate(Contact90 $contact90)
    // {
    //     $contact90->update(['validasi_manager' => true]);

    //     return back()->with('success', 'Kontak berhasil divalidasi!');
    // }

    // /**
    //  * Validasi Kontak (Bulk)
    //  */
    // public function validateBulk(Request $request)
    // {
    //     $request->validate([
    //         'contact_ids' => 'required|array',
    //         'contact_ids.*' => 'exists:contact90s,id',
    //     ]);

    //     Contact90::whereIn('id', $request->contact_ids)
    //         ->update(['validasi_manager' => true]);

    //     return back()->with('success', count($request->contact_ids) . ' kontak berhasil divalidasi!');
    // }

    // ==============================
    // MANAGER SECTION
    // ==============================

    /**
     * Dashboard Tim untuk Manager
     * 🔥 Manager hanya bisa lihat FO di cabang yang dia manage
     */
    public function managerDashboard(Request $request)
    {
        $user = Auth::user();
        $tanggal = $request->input('tanggal', now()->toDateString());

        // 🔥 SUPERADMIN: Lihat semua cabang
        // 🔥 MANAGER: Hanya cabang yang dia manage (is_manager = true)
        if ($user->hasRole('superadmin')) {
            $managedBranches = Branch::all();
        } else {
            $managedBranches = $user->managedBranches; // Dari User model
        }

        // 🔥 Get semua FO dari cabang yang di-manage
        $managedBranchIds = $managedBranches->pluck('id');

        $foUserIds = BranchUser::whereIn('branch_id', $managedBranchIds)
            ->whereHas('user', function ($q) {
                $q->role('fo'); // Hanya ambil yang role FO
            })
            ->pluck('user_id')
            ->unique();

        // Total FO (yang di-manage)
        $totalFo = $foUserIds->count();

        // Total Kontak Hari Ini (dari FO yang di-manage)
        $totalKontak = Contact90::whereIn('user_id', $foUserIds)
            ->whereDate('tanggal', $tanggal)
            ->count();

        // Target (90 x jumlah FO)
        $target = $totalFo * 90;

        // Belum Validasi
        $belumValidasi = Contact90::whereIn('user_id', $foUserIds)
            ->whereDate('tanggal', $tanggal)
            ->where('validasi_manager', false)
            ->count();

        // Sudah Validasi
        $sudahValidasi = Contact90::whereIn('user_id', $foUserIds)
            ->whereDate('tanggal', $tanggal)
            ->where('validasi_manager', true)
            ->count();

        // Breakdown Situasi
        $closing = Contact90::whereIn('user_id', $foUserIds)
            ->whereDate('tanggal', $tanggal)
            ->where('situasi', 'closing')
            ->count();

        $tertarik = Contact90::whereIn('user_id', $foUserIds)
            ->whereDate('tanggal', $tanggal)
            ->where('situasi', 'tertarik')
            ->count();

        $merespon = Contact90::whereIn('user_id', $foUserIds)
            ->whereDate('tanggal', $tanggal)
            ->where('situasi', 'merespon')
            ->count();

        $tdkMerespon = Contact90::whereIn('user_id', $foUserIds)
            ->whereDate('tanggal', $tanggal)
            ->where('situasi', 'tdk_merespon')
            ->count();

        return view('contact90.manager.dashboard', compact(
            'tanggal',
            'totalFo',
            'totalKontak',
            'target',
            'belumValidasi',
            'sudahValidasi',
            'closing',
            'tertarik',
            'merespon',
            'tdkMerespon',
            'managedBranches' // 🔥 Kirim ke view untuk info
        ));
    }

    /**
     * Daftar FO untuk Manager
     * 🔥 Manager hanya bisa lihat FO di cabang yang dia manage
     */
    public function managerFoList(Request $request)
    {
        $user = Auth::user();
        $tanggal = $request->input('tanggal', now()->toDateString());
        $search = $request->input('search');
        $branchFilter = $request->input('branch_id'); // 🔥 Filter by cabang

        // 🔥 SUPERADMIN: Lihat semua cabang
        // 🔥 MANAGER: Hanya cabang yang dia manage
        if ($user->hasRole('superadmin')) {
            $managedBranches = Branch::all();
        } else {
            $managedBranches = $user->managedBranches;
        }

        $managedBranchIds = $managedBranches->pluck('id');

        // 🔥 Query FO dari cabang yang di-manage
        $query = User::role('fo')
            ->whereHas('branchAssignments', function ($q) use ($managedBranchIds) {
                $q->whereIn('branch_id', $managedBranchIds);
            });

        // 🔥 Filter by Cabang (jika dipilih)
        if ($branchFilter) {
            $query->whereHas('branchAssignments', function ($q) use ($branchFilter) {
                $q->where('branch_id', $branchFilter);
            });
        }

        // Search
        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $foList = $query->orderBy('name')->get()->map(function ($fo) use ($tanggal) {
            $total = Contact90::where('user_id', $fo->id)
                ->whereDate('tanggal', $tanggal)
                ->count();

            $validated = Contact90::where('user_id', $fo->id)
                ->whereDate('tanggal', $tanggal)
                ->where('validasi_manager', true)
                ->count();

            $pending = $total - $validated;

            $fo->kontak_total = $total;
            $fo->kontak_validated = $validated;
            $fo->kontak_pending = $pending;

            return $fo;
        });

        return view('contact90.manager.folist', compact(
            'foList',
            'tanggal',
            'managedBranches' // 🔥 Untuk dropdown filter cabang
        ));
    }

    /**
     * Detail Kontak per FO untuk Manager
     * 🔥 Manager hanya bisa lihat FO di cabang yang dia manage
     */
    public function managerFoDetail(Request $request, User $user)
    {
        $authUser = Auth::user();
        $tanggal = $request->input('tanggal', now()->toDateString());
        $sosmedFilter = $request->input('sosmed');
        $situasiFilter = $request->input('situasi');
        $validasiFilter = $request->input('validasi');
        $perPage = (int) $request->input('per_page', 25);

        // 🔥 CEK PERMISSION: Manager hanya bisa akses FO di cabangnya
        if (!$authUser->hasRole('superadmin')) {
            $managedBranchIds = $authUser->managedBranches->pluck('id');

            // Cek apakah FO ini ada di cabang yang di-manage
            $foInManagedBranch = BranchUser::where('user_id', $user->id)
                ->whereIn('branch_id', $managedBranchIds)
                ->exists();

            if (!$foInManagedBranch) {
                abort(403, 'Anda tidak memiliki akses untuk melihat kontak FO ini.');
            }
        }

        $query = Contact90::where('user_id', $user->id)
            ->whereDate('tanggal', $tanggal);

        if ($sosmedFilter) {
            $query->where('sosmed', $sosmedFilter);
        }

        if ($situasiFilter) {
            $query->where('situasi', $situasiFilter);
        }

        if ($validasiFilter !== null) {
            $query->where('validasi_manager', $validasiFilter === '1');
        }

        $contacts = $query->orderBy('validasi_manager')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        return view('contact90.manager.fodetail', compact('user', 'contacts', 'tanggal'));
    }

    /**
     * Validasi Kontak (Single)
     * 🔥 Manager hanya bisa validasi kontak FO di cabangnya
     */
    public function validate(Contact90 $contact90)
    {
        $user = Auth::user();

        // 🔥 CEK PERMISSION: Manager hanya bisa validasi FO di cabangnya
        if (!$user->hasRole('superadmin')) {
            $managedBranchIds = $user->managedBranches->pluck('id');

            $foInManagedBranch = BranchUser::where('user_id', $contact90->user_id)
                ->whereIn('branch_id', $managedBranchIds)
                ->exists();

            if (!$foInManagedBranch) {
                abort(403, 'Anda tidak memiliki akses untuk validasi kontak ini.');
            }
        }

        $contact90->update(['validasi_manager' => true]);

        return back()->with('success', 'Kontak berhasil divalidasi!');
    }

    /**
     * Validasi Kontak (Bulk)
     * 🔥 Manager hanya bisa validasi kontak FO di cabangnya
     */
    public function validateBulk(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'contact_ids' => 'required|array',
            'contact_ids.*' => 'exists:contact90s,id',
        ]);

        // 🔥 CEK PERMISSION: Filter hanya kontak dari FO di cabang yang di-manage
        if (!$user->hasRole('superadmin')) {
            $managedBranchIds = $user->managedBranches->pluck('id');

            $foUserIds = BranchUser::whereIn('branch_id', $managedBranchIds)
                ->pluck('user_id');

            // Update hanya kontak yang user_id-nya ada di cabang yang di-manage
            $validatedCount = Contact90::whereIn('id', $request->contact_ids)
                ->whereIn('user_id', $foUserIds)
                ->update(['validasi_manager' => true]);
        } else {
            // Superadmin bisa validasi semua
            $validatedCount = Contact90::whereIn('id', $request->contact_ids)
                ->update(['validasi_manager' => true]);
        }

        return back()->with('success', $validatedCount . ' kontak berhasil divalidasi!');
    }
}
