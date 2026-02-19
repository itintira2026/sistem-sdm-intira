<?php

namespace App\Http\Controllers;

use App\Exports\DailyReportFOExport;
use App\Helpers\ImageHelper;
use App\Helpers\ShiftHelper;
use App\Helpers\TimeHelper;
use App\Models\Branch;
use App\Models\DailyReportFO;
use App\Models\DailyReportFODetail;
use App\Models\DailyReportFOPhoto;
use App\Models\ReportCategory;
use App\Models\ReportField;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class DailyReportFoController extends Controller
{
    // ==============================
    // FO SECTION
    // ==============================

    /**
     * Dashboard FO - Tampilkan 4 slot dengan status real-time
     */
    public function index()
    {
        $user = Auth::user();

        $userBranches = $user->branches;

        if ($userBranches->isEmpty()) {
            return back()->with('error', 'Anda belum terdaftar di cabang manapun.');
        }

        $branch = $userBranches->first();
        $branchTime = TimeHelper::getBranchTime($branch->id);
        $today = $branchTime->toDateString();

        $selectedShift = ShiftHelper::determineShift($user->id);

        if (! $selectedShift) {
            return view('daily-reports-fo.index', [
                'branch' => $branch,
                'needShiftSelection' => true,
                'branchTime' => $branchTime,
            ]);
        }

        $slots = TimeHelper::getShiftSlots($selectedShift);

        // $existingReports = DailyReportFO::where('user_id', $user->id)
        //     ->whereDate('tanggal', $today)
        //     ->where('shift', $selectedShift)
        //     ->with([
        //         'details' => function ($q) {
        //             $q->with(['field', 'photos']);
        //         },
        //     ])
        //     ->get()
        //     ->each(function ($report) {
        //         // Jumlahkan semua foto dari setiap detail
        //         $report->total_photos = $report->details->sum(function ($detail) {
        //             return $detail->photos->count();
        //         });
        //     })
        //     ->keyBy('slot');

        // Ganti seluruh bagian $existingReports dan $stats di method index() dengan ini:
        $existingReports = DailyReportFO::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->where('shift', $selectedShift)
            ->with([
                'details' => function ($q) {
                    $q->with(['field', 'photos']);
                },
                'validation.action', // ⬅️ TAMBAH: load validasi + nama tindakan
            ])
            ->get()
            ->each(function ($report) {
                $report->total_photos = $report->details->sum(function ($detail) {
                    return $detail->photos->count();
                });
            })
            ->keyBy('slot');

        $slotData = [];
        foreach ($slots as $slot) {
            $slotNumber = $slot['slot_number'];
            $slotTime = $slot['slot_time'];

            $status = TimeHelper::getSlotStatus($slotTime, $branch->id, $today);
            $existingReport = $existingReports->get($slotNumber);

            $slotData[] = [
                'slot_number' => $slotNumber,
                'slot_time' => $slotTime,
                'slot_config' => $slot,
                'status' => $status,
                'window' => TimeHelper::getSlotWindowRange($slotTime, $branch->id, $today),
                'existing_report' => $existingReport,
                'has_report' => $existingReport !== null,
                'can_upload' => $status === 'open' && ! $existingReport,
                'can_edit' => $status === 'open' && $existingReport,
                'time_until_open' => $status === 'waiting' ? TimeHelper::getTimeUntilSlotOpens($slotTime, $branch->id, $today) : null,
                'time_remaining' => $status === 'open' ? TimeHelper::getRemainingTimeInSlot($slotTime, $branch->id, $today) : null,
            ];
        }

        // $stats = [
        //     'total_slots' => 4,
        //     'completed_slots' => $existingReports->count(),
        //     'progress_percentage' => ($existingReports->count() / 4) * 100,
        // ];
        // Update $stats: tambah hitungan per validation_status
        $stats = [
            'total_slots' => 4,
            'completed_slots' => $existingReports->count(),
            'progress_percentage' => ($existingReports->count() / 4) * 100,
            'approved' => $existingReports->where('validation_status', 'approved')->count(),
            'rejected' => $existingReports->where('validation_status', 'rejected')->count(),
            'pending' => $existingReports->where('validation_status', 'pending')->count(),
        ];

        return view('daily-reports-fo.index', [
            'branch' => $branch,
            'branchTime' => $branchTime,
            'selectedShift' => $selectedShift,
            'needShiftSelection' => false,
            'slotData' => $slotData,
            'stats' => $stats,
            'today' => $today,
            'serverTimestamp' => $branchTime->timestamp,
            'branchTimezone' => $branch->timezone,
        ]);
    }

    /**
     * Select Shift (POST)
     */
    public function selectShift(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'shift' => 'required|in:pagi,siang',
        ]);

        if (! ShiftHelper::canChangeShiftToday($user->id)) {
            return back()->with('error', 'Shift sudah terkunci karena Anda sudah membuat laporan hari ini.');
        }

        ShiftHelper::setTodayShift($validated['shift']);

        return redirect()->route('daily-reports-fo.index')
            ->with('success', 'Shift ' . config('daily_report_fo.shifts')[$validated['shift']]['label'] . ' berhasil dipilih!');
    }

    /**
     * Show Slot Form (untuk upload atau edit)
     * DIUBAH: Load categories + fields dari master data
     */
    public function showSlot($slotNumber)
    {
        $user = Auth::user();
        $branch = $user->branches->first();

        if (! $branch) {
            return back()->with('error', 'Anda belum terdaftar di cabang manapun.');
        }

        $branchTime = TimeHelper::getBranchTime($branch->id);
        $today = $branchTime->toDateString();

        $selectedShift = ShiftHelper::determineShift($user->id);

        if (! $selectedShift) {
            return redirect()->route('daily-reports-fo.index')
                ->with('error', 'Silakan pilih shift terlebih dahulu.');
        }

        $slotConfig = TimeHelper::getSlotConfig($selectedShift, $slotNumber);

        if (! $slotConfig) {
            return back()->with('error', 'Slot tidak valid.');
        }

        $slotTime = $slotConfig['slot_time'];

        $status = TimeHelper::getSlotStatus($slotTime, $branch->id, $today);

        if ($status !== 'open') {
            return back()->with('error', 'Slot ini belum dibuka atau sudah ditutup.');
        }

        // Load existing report beserta details dan photos
        $existingReport = DailyReportFO::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->where('shift', $selectedShift)
            ->where('slot', $slotNumber)
            ->with([
                'details' => function ($q) {
                    $q->with('field', 'photos');
                },
            ])
            ->first();

        // DIUBAH: Load dari master data (bukan dari config)
        $categories = ReportCategory::active()
            ->ordered()
            ->with(['fields' => function ($q) {
                $q->active()->ordered();
            }])
            ->get();

        // Jika edit mode, buat map detail yang sudah ada: field_id => detail
        // Memudahkan view untuk pre-fill nilai
        $existingDetails = collect();
        if ($existingReport) {
            $existingDetails = $existingReport->details->keyBy('field_id');
        }

        $window = TimeHelper::getSlotWindowRange($slotTime, $branch->id, $today);

        return view('daily-reports-fo.slot-form', [
            'branch' => $branch,
            'branchTime' => $branchTime,
            'selectedShift' => $selectedShift,
            'slotNumber' => $slotNumber,
            'slotConfig' => $slotConfig,
            'slotTime' => $slotTime,
            'window' => $window,
            'existingReport' => $existingReport,
            'existingDetails' => $existingDetails,
            'isEdit' => $existingReport !== null,
            'categories' => $categories,   // dari master data
        ]);
    }

    /**
     * Store Slot Report (POST)
     * DIUBAH TOTAL: Simpan ke daily_report_fo_details + photos via detail
     */
    public function storeSlot(Request $request, $slotNumber)
    {
        $user = Auth::user();
        $branch = $user->branches->first();

        if (! $branch) {
            return back()->with('error', 'Anda belum terdaftar di cabang manapun.');
        }

        $branchTime = TimeHelper::getBranchTime($branch->id);
        $today = $branchTime->toDateString();

        $selectedShift = ShiftHelper::determineShift($user->id);

        if (! $selectedShift) {
            return redirect()->route('daily-reports-fo.index')
                ->with('error', 'Silakan pilih shift terlebih dahulu.');
        }

        $slotConfig = TimeHelper::getSlotConfig($selectedShift, $slotNumber);

        if (! $slotConfig) {
            return back()->with('error', 'Slot tidak valid.');
        }

        $slotTime = $slotConfig['slot_time'];

        if (! TimeHelper::isSlotOpen($slotTime, $branch->id, $today)) {
            return back()->with('error', 'Window upload untuk slot ini sudah ditutup.');
        }

        $existing = DailyReportFO::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->where('shift', $selectedShift)
            ->where('slot', $slotNumber)
            ->first();

        if ($existing) {
            return back()->with('error', 'Anda sudah melaporkan slot ini.');
        }

        // -------------------------------------------------------
        // Load semua field aktif dari master
        // -------------------------------------------------------
        $fields = ReportField::active()->ordered()->get();

        // -------------------------------------------------------
        // Build validation rules secara dinamis dari master data
        // -------------------------------------------------------
        $validationRules = [
            'keterangan' => 'nullable|string|max:1000',
        ];
        $validationMessages = [];

        foreach ($fields as $field) {
            $inputName = "field_{$field->id}";
            $photoName = "photo_{$field->id}";

            switch ($field->input_type) {
                case 'number':
                    // Wajib diisi (boleh 0), harus angka, tidak boleh negatif
                    $rule = $field->is_required
                        ? 'required|numeric|min:0'
                        : 'nullable|numeric|min:0';
                    $validationRules[$inputName] = $rule;
                    if ($field->is_required) {
                        $validationMessages["{$inputName}.required"] = "{$field->name} wajib diisi (isi 0 jika tidak ada).";
                    }
                    break;

                case 'checkbox':
                    // Checkbox tidak wajib — unchecked = false, checked = true
                    $validationRules[$inputName] = 'nullable|boolean';
                    break;

                case 'photo_number':
                    // Angka: opsional
                    $validationRules[$inputName] = 'nullable|numeric|min:0';
                    // Foto: wajib minimal 1 jika field is_required
                    if ($field->is_required) {
                        $validationRules[$photoName] = 'required|array|min:1';
                        $validationRules["{$photoName}.*"] = 'required|image|mimes:jpg,jpeg,png|max:5120';
                        $validationMessages["{$photoName}.required"] = "Foto bukti untuk {$field->name} wajib diupload.";
                    } else {
                        $validationRules[$photoName] = 'nullable|array';
                        $validationRules["{$photoName}.*"] = 'nullable|image|mimes:jpg,jpeg,png|max:5120';
                    }
                    break;

                case 'text':
                    $rule = $field->is_required
                        ? 'required|string|max:1000'
                        : 'nullable|string|max:1000';
                    $validationRules[$inputName] = $rule;
                    break;
            }
        }

        $request->validate($validationRules, $validationMessages);

        // -------------------------------------------------------
        // Simpan dengan DB transaction
        // -------------------------------------------------------
        DB::transaction(function () use ($request, $fields, $user, $branch, $today, $selectedShift, $slotNumber, $slotTime) {

            // 1. Buat header report
            $report = DailyReportFO::create([
                'user_id' => $user->id,
                'branch_id' => $branch->id,
                'tanggal' => $today,
                'shift' => $selectedShift,
                'slot' => $slotNumber,
                'slot_time' => $slotTime,
                'uploaded_at' => now(),
                'keterangan' => $request->input('keterangan'),
            ]);

            // 2. Loop setiap field, simpan detail + foto
            foreach ($fields as $field) {
                $inputName = "field_{$field->id}";
                $photoName = "photo_{$field->id}";

                // Tentukan value berdasarkan input_type
                $valueBoolean = null;
                $valueNumber = null;
                $valueText = null;

                switch ($field->input_type) {
                    case 'checkbox':
                        // Checkbox: true jika dicentang, false jika tidak
                        $valueBoolean = $request->boolean($inputName);
                        break;

                    case 'number':
                        $valueNumber = $request->input($inputName);
                        break;

                    case 'photo_number':
                        $valueNumber = $request->input($inputName);
                        break;

                    case 'text':
                        $valueText = $request->input($inputName);
                        break;
                }

                // Buat record detail
                $detail = DailyReportFODetail::create([
                    'daily_report_fo_id' => $report->id,
                    'field_id' => $field->id,
                    'value_boolean' => $valueBoolean,
                    'value_number' => $valueNumber,
                    'value_text' => $valueText,
                ]);

                // 3. Upload foto jika field tipe photo / photo_number
                if ($field->requiresPhoto() && $request->hasFile($photoName)) {
                    foreach ($request->file($photoName) as $photo) {
                        $filePath = ImageHelper::compressAndSave(
                            $photo,
                            "daily_reports_fo/{$report->id}/{$field->code}",
                            config('daily_report_fo.image_compression.max_width', 1920),
                            config('daily_report_fo.image_compression.max_height', 1080),
                            config('daily_report_fo.image_compression.quality', 80)
                        );

                        DailyReportFOPhoto::create([
                            'daily_report_fo_detail_id' => $detail->id,
                            'file_path' => $filePath,
                            'file_name' => $photo->getClientOriginalName(),
                        ]);
                    }
                }
            }
        });

        return redirect()->route('daily-reports-fo.index')
            ->with('success', "Laporan Slot {$slotNumber} berhasil diupload!");
    }

    /**
     * Edit Slot (GET)
     */
    public function editSlot($slotNumber)
    {
        return $this->showSlot($slotNumber);
    }

    /**
     * Update Slot (PUT)
     * DIUBAH TOTAL: Update detail + handle foto via detail_id
     */
    public function updateSlot(Request $request, $slotNumber)
    {
        $user = Auth::user();
        $branch = $user->branches->first();

        if (! $branch) {
            return back()->with('error', 'Anda belum terdaftar di cabang manapun.');
        }

        $branchTime = TimeHelper::getBranchTime($branch->id);
        $today = $branchTime->toDateString();

        $selectedShift = ShiftHelper::determineShift($user->id);

        if (! $selectedShift) {
            return redirect()->route('daily-reports-fo.index')
                ->with('error', 'Silakan pilih shift terlebih dahulu.');
        }

        $slotConfig = TimeHelper::getSlotConfig($selectedShift, $slotNumber);

        if (! $slotConfig) {
            return back()->with('error', 'Slot tidak valid.');
        }

        $slotTime = $slotConfig['slot_time'];

        if (! TimeHelper::isSlotOpen($slotTime, $branch->id, $today)) {
            return back()->with('error', 'Window upload untuk slot ini sudah ditutup. Tidak bisa edit.');
        }

        $report = DailyReportFO::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->where('shift', $selectedShift)
            ->where('slot', $slotNumber)
            ->with([
                'details' => function ($q) {
                    $q->with('field', 'photos');
                },
            ])
            ->firstOrFail();

        // -------------------------------------------------------
        // Load semua field aktif dari master
        // -------------------------------------------------------
        $fields = ReportField::active()->ordered()->get();

        // -------------------------------------------------------
        // Build validation rules (saat edit, foto opsional
        // kecuali field is_required dan belum punya foto sama sekali)
        // -------------------------------------------------------
        $validationRules = [
            'keterangan' => 'nullable|string|max:1000',
        ];
        $validationMessages = [];

        // Map existing details: field_id => detail (untuk cek foto existing)
        $existingDetails = $report->details->keyBy('field_id');

        foreach ($fields as $field) {
            $inputName = "field_{$field->id}";
            $photoName = "photo_{$field->id}";
            $deletePhotoName = "delete_photos_{$field->id}";

            switch ($field->input_type) {
                case 'number':
                    $rule = $field->is_required
                        ? 'required|numeric|min:0'
                        : 'nullable|numeric|min:0';
                    $validationRules[$inputName] = $rule;
                    if ($field->is_required) {
                        $validationMessages["{$inputName}.required"] = "{$field->name} wajib diisi (isi 0 jika tidak ada).";
                    }
                    break;

                case 'checkbox':
                    $validationRules[$inputName] = 'nullable|boolean';
                    break;

                case 'photo_number':
                    $validationRules[$inputName] = 'nullable|numeric|min:0';

                    // Cek apakah masih ada foto existing setelah dikurangi yang akan dihapus
                    $existingDetail = $existingDetails->get($field->id);
                    $existingPhotoCount = $existingDetail ? $existingDetail->photos->count() : 0;
                    $deleteCount = count($request->input($deletePhotoName, []));
                    $remainingPhotos = $existingPhotoCount - $deleteCount;

                    // Foto baru wajib jika: field required DAN tidak ada foto yang tersisa
                    if ($field->is_required && $remainingPhotos <= 0) {
                        $validationRules[$photoName] = 'required|array|min:1';
                        $validationRules["{$photoName}.*"] = 'required|image|mimes:jpg,jpeg,png|max:5120';
                        $validationMessages["{$photoName}.required"] = "Foto bukti untuk {$field->name} wajib ada minimal 1.";
                    } else {
                        $validationRules[$photoName] = 'nullable|array';
                        $validationRules["{$photoName}.*"] = 'nullable|image|mimes:jpg,jpeg,png|max:5120';
                    }

                    // Validasi ID foto yang akan dihapus (harus milik detail ini)
                    $validationRules[$deletePhotoName] = 'nullable|array';
                    $validationRules["{$deletePhotoName}.*"] = 'nullable|integer';
                    break;

                case 'text':
                    $rule = $field->is_required
                        ? 'required|string|max:1000'
                        : 'nullable|string|max:1000';
                    $validationRules[$inputName] = $rule;
                    break;
            }
        }

        $request->validate($validationRules, $validationMessages);

        // -------------------------------------------------------
        // Update dengan DB transaction
        // -------------------------------------------------------
        DB::transaction(function () use ($request, $fields, $report) {

            // 1. Update keterangan
            $report->update([
                'keterangan' => $request->input('keterangan'),
            ]);

            // 2. Loop setiap field
            foreach ($fields as $field) {
                $inputName = "field_{$field->id}";
                $photoName = "photo_{$field->id}";
                $deletePhotoName = "delete_photos_{$field->id}";

                // Tentukan value
                $valueBoolean = null;
                $valueNumber = null;
                $valueText = null;

                switch ($field->input_type) {
                    case 'checkbox':
                        $valueBoolean = $request->boolean($inputName);
                        break;
                    case 'number':
                        $valueNumber = $request->input($inputName);
                        break;
                    case 'photo_number':
                        $valueNumber = $request->input($inputName);
                        break;
                    case 'text':
                        $valueText = $request->input($inputName);
                        break;
                }

                // Update atau buat detail (updateOrCreate)
                $detail = DailyReportFODetail::updateOrCreate(
                    [
                        'daily_report_fo_id' => $report->id,
                        'field_id' => $field->id,
                    ],
                    [
                        'value_boolean' => $valueBoolean,
                        'value_number' => $valueNumber,
                        'value_text' => $valueText,
                    ]
                );

                // 3. Hapus foto yang ditandai untuk dihapus
                if ($field->requiresPhoto() && $request->has($deletePhotoName)) {
                    $photoIdsToDelete = $request->input($deletePhotoName, []);

                    if (! empty($photoIdsToDelete)) {
                        // Pastikan foto ini memang milik detail ini (security check)
                        $photosToDelete = DailyReportFOPhoto::whereIn('id', $photoIdsToDelete)
                            ->where('daily_report_fo_detail_id', $detail->id)
                            ->get();

                        foreach ($photosToDelete as $photo) {
                            ImageHelper::delete($photo->file_path);
                            $photo->delete();
                        }
                    }
                }

                // 4. Upload foto baru
                if ($field->requiresPhoto() && $request->hasFile($photoName)) {
                    foreach ($request->file($photoName) as $photo) {
                        $filePath = ImageHelper::compressAndSave(
                            $photo,
                            "daily_reports_fo/{$report->id}/{$field->code}",
                            config('daily_report_fo.image_compression.max_width', 1920),
                            config('daily_report_fo.image_compression.max_height', 1080),
                            config('daily_report_fo.image_compression.quality', 80)
                        );

                        DailyReportFOPhoto::create([
                            'daily_report_fo_detail_id' => $detail->id,
                            'file_path' => $filePath,
                            'file_name' => $photo->getClientOriginalName(),
                        ]);
                    }
                }
            }
        });

        return redirect()->route('daily-reports-fo.index')
            ->with('success', "Laporan Slot {$slotNumber} berhasil diperbarui!");
    }

    /**
     * History FO (30 hari terakhir)
     */
    public function history(Request $request)
    {
        $user = Auth::user();
        $branch = $user->branches->first();

        if (! $branch) {
            return back()->with('error', 'Anda belum terdaftar di cabang manapun.');
        }

        $perPage = (int) $request->input('per_page', 10);
        $shiftFilter = $request->input('shift');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $branchTime = TimeHelper::getBranchTime($branch->id);
        $historyDays = config('daily_report_fo.history_days', 30);

        // $query = DailyReportFO::where('user_id', $user->id)
        //     ->where('branch_id', $branch->id)
        //     ->where('tanggal', '>=', $branchTime->copy()->subDays($historyDays)->toDateString())
        //     // ->with([
        //     //     'details' => function ($q) {
        //     //         $q->with('field.category', 'photos');
        //     //     },
        //     // ]);
        //     // Di controller history(), ganti bagian with:
        //     ->with([
        //         'details' => function ($q) {
        //             $q->with(['field.category', 'photos']);
        //         },
        //         'validation.action', // ← TAMBAH
        //     ]);

        $query = DailyReportFO::where('user_id', $user->id)
            ->where('branch_id', $branch->id)
            ->where('tanggal', '>=', $branchTime->copy()->subDays($historyDays)->toDateString())
            ->with([
                'branch:id,name,timezone',  // ← TAMBAH: eager load branch
                'details' => function ($q) {
                    $q->with(['field.category', 'photos']);
                },
                'validation.action',
            ]);


        if ($shiftFilter) {
            $query->where('shift', $shiftFilter);
        }

        if ($dateFrom) {
            $query->whereDate('tanggal', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('tanggal', '<=', $dateTo);
        }

        $reports = $query->orderBy('tanggal', 'desc')
            ->orderBy('shift', 'asc')
            ->orderBy('slot', 'asc')
            ->paginate($perPage)
            ->withQueryString();

        // Tambah stats total_photos:
        $totalPhotos = $reports->sum(function ($report) {
            return $report->details->sum(fn($detail) => $detail->photos->count());
        });

        $stats = [
            'total_reports' => $query->count(),
            'total_days' => $query->select('tanggal')->distinct()->count(),
            'total_photos' => $totalPhotos,
        ];

        return view('daily-reports-fo.history', [
            'branch' => $branch,
            'branchTime' => $branchTime,
            'reports' => $reports,
            'stats' => $stats,
        ]);
    }

    // ==============================
    // MANAGER SECTION
    // ==============================

    /**
     * Manager Dashboard - Overview semua FO di cabang
     */
    public function managerDashboard(Request $request)
    {
        $user = Auth::user();
        $tanggal = $request->input('tanggal', now()->toDateString());

        if ($user->hasRole('superadmin')) {
            $managedBranches = Branch::orderBy('name')->get();
        } else {
            $managedBranches = $user->managedBranches;
        }

        if ($managedBranches->isEmpty()) {
            return back()->with('error', 'Anda tidak mengelola cabang manapun.');
        }

        $selectedBranchId = $request->input('branch_id', $managedBranches->first()->id);
        $selectedBranch = $managedBranches->find($selectedBranchId);

        if (! $selectedBranch) {
            abort(403, 'Anda tidak memiliki akses ke cabang ini.');
        }

        $foUsers = $selectedBranch->users()
            ->whereHas('roles', function ($q) {
                $q->where('name', 'fo');
            })
            ->with(['branches'])
            ->get();

        $todayReports = DailyReportFO::where('branch_id', $selectedBranchId)
            ->whereDate('tanggal', $tanggal)
            ->with([
                'details' => function ($q) {
                    $q->with('field.category', 'photos');
                },
            ])
            ->get()
            ->groupBy('user_id');

        $foProgress = [];
        foreach ($foUsers as $fo) {
            $userReports = $todayReports->get($fo->id, collect());
            $shift = $userReports->first()->shift ?? null;

            $slots = [1, 2, 3, 4];
            $slotStatus = [];

            foreach ($slots as $slotNumber) {
                $report = $userReports->firstWhere('slot', $slotNumber);
                $slotStatus[$slotNumber] = [
                    'has_report' => $report !== null,
                    'report' => $report,
                    'detail_count' => $report ? $report->details->count() : 0,
                ];
            }

            $completedSlots = $userReports->count();
            $progressPercentage = ($completedSlots / 4) * 100;

            $foProgress[] = [
                'user' => $fo,
                'shift' => $shift,
                'shift_label' => $shift ? config('daily_report_fo.shifts')[$shift]['label'] : 'Belum Pilih',
                'total_reports' => $completedSlots,
                'progress_percentage' => $progressPercentage,
                'slot_status' => $slotStatus,
            ];
        }

        $stats = [
            'total_fo' => $foUsers->count(),
            'total_reports_today' => DailyReportFO::where('branch_id', $selectedBranchId)
                ->whereDate('tanggal', $tanggal)
                ->count(),
            'target_reports' => $foUsers->count() * 4,
            'fo_complete' => collect($foProgress)->where('total_reports', 4)->count(),
            'fo_in_progress' => collect($foProgress)->whereBetween('total_reports', [1, 3])->count(),
            'fo_not_started' => collect($foProgress)->where('total_reports', 0)->count(),
        ];

        return view('daily-reports-fo.manager.dashboard', [
            'managedBranches' => $managedBranches,
            'selectedBranch' => $selectedBranch,
            'tanggal' => $tanggal,
            'foProgress' => $foProgress,
            'stats' => $stats,
        ]);
    }

    /**
     * Manager - List FO
     */
    public function managerFOList(Request $request)
    {
        $user = Auth::user();

        if ($user->hasRole('superadmin')) {
            $managedBranches = Branch::orderBy('name')->get();
        } else {
            $managedBranches = $user->managedBranches;
        }

        $selectedBranchId = $request->input('branch_id', $managedBranches->first()->id);
        $selectedBranch = $managedBranches->find($selectedBranchId);

        if (! $selectedBranch) {
            abort(403, 'Anda tidak memiliki akses ke cabang ini.');
        }

        $foUsers = $selectedBranch->users()
            ->whereHas('roles', function ($q) {
                $q->where('name', 'fo');
            })
            ->with(['branches'])
            ->paginate(20);

        return view('daily-reports-fo.manager.fo-list', [
            'managedBranches' => $managedBranches,
            'selectedBranch' => $selectedBranch,
            'foUsers' => $foUsers,
        ]);
    }

    /**
     * Manager - FO Detail (semua laporan 1 FO)
     */
    public function managerFODetail(Request $request, $userId)
    {
        $user = Auth::user();
        $dateFrom = $request->input('date_from', now()->subDays(7)->toDateString());
        $dateTo = $request->input('date_to', now()->toDateString());

        $foUser = User::findOrFail($userId);

        if (! $user->hasRole('superadmin')) {
            $managedBranchIds = $user->managedBranches->pluck('id');
            $foUserBranchIds = $foUser->branches->pluck('id');

            if ($managedBranchIds->intersect($foUserBranchIds)->isEmpty()) {
                abort(403, 'Anda tidak memiliki akses ke FO ini.');
            }
        }

        $reports = DailyReportFO::where('user_id', $userId)
            ->whereBetween('tanggal', [$dateFrom, $dateTo])
            ->with([
                'branch',
                'details' => function ($q) {
                    $q->with('field.category', 'photos');
                },
            ])
            ->orderBy('tanggal', 'desc')
            ->orderBy('shift', 'asc')
            ->orderBy('slot', 'asc')
            ->paginate(20)
            ->withQueryString();

        // Hitung total foto dari semua detail
        $totalPhotos = DailyReportFOPhoto::whereHas('detail.report', function ($q) use ($userId, $dateFrom, $dateTo) {
            $q->where('user_id', $userId)
                ->whereBetween('tanggal', [$dateFrom, $dateTo]);
        })->count();

        $stats = [
            'total_reports' => DailyReportFO::where('user_id', $userId)
                ->whereBetween('tanggal', [$dateFrom, $dateTo])
                ->count(),
            'total_photos' => $totalPhotos,
            'total_days' => DailyReportFO::where('user_id', $userId)
                ->whereBetween('tanggal', [$dateFrom, $dateTo])
                ->select('tanggal')
                ->distinct()
                ->count(),
        ];

        return view('daily-reports-fo.manager.fo-detail', [
            'foUser' => $foUser,
            'reports' => $reports,
            'stats' => $stats,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }

    /**
     * Manager - Reports List
     */
    public function managerReports(Request $request)
    {
        $user = Auth::user();

        if ($user->hasRole('superadmin')) {
            $managedBranches = Branch::orderBy('name')->get();
        } else {
            $managedBranches = $user->managedBranches;
        }

        $selectedBranchId = $request->input('branch_id', $managedBranches->first()->id);
        $selectedBranch = $managedBranches->find($selectedBranchId);

        if (! $selectedBranch) {
            abort(403, 'Anda tidak memiliki akses ke cabang ini.');
        }

        $tanggal = $request->input('tanggal', now()->toDateString());
        $shiftFilter = $request->input('shift');
        $perPage = (int) $request->input('per_page', 25);

        $query = DailyReportFO::where('branch_id', $selectedBranchId)
            ->whereDate('tanggal', $tanggal)
            ->with([
                'user',
                'branch',
                'details' => function ($q) {
                    $q->with('field.category', 'photos');
                },
            ]);

        if ($shiftFilter) {
            $query->where('shift', $shiftFilter);
        }

        $reports = $query->orderBy('shift', 'asc')
            ->orderBy('slot', 'asc')
            ->orderBy('uploaded_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        $totalPhotos = DailyReportFOPhoto::whereHas('detail.report', function ($q) use ($selectedBranchId, $tanggal) {
            $q->where('branch_id', $selectedBranchId)
                ->whereDate('tanggal', $tanggal);
        })->count();

        $stats = [
            'total_reports' => DailyReportFO::where('branch_id', $selectedBranchId)
                ->whereDate('tanggal', $tanggal)
                ->count(),
            'total_photos' => $totalPhotos,
        ];

        return view('daily-reports-fo.manager.reports', [
            'managedBranches' => $managedBranches,
            'selectedBranch' => $selectedBranch,
            'reports' => $reports,
            'stats' => $stats,
            'tanggal' => $tanggal,
        ]);
    }

    /**
     * Manager - Report Detail
     */
    public function managerReportDetail($reportId)
    {
        $user = Auth::user();
        $report = DailyReportFO::with([
            'user',
            'branch',
            'details' => function ($q) {
                $q->with('field.category', 'photos');
            },
        ])->findOrFail($reportId);

        if (! $user->hasRole('superadmin')) {
            $managedBranchIds = $user->managedBranches->pluck('id');

            if (! $managedBranchIds->contains($report->branch_id)) {
                abort(403, 'Anda tidak memiliki akses ke laporan ini.');
            }
        }

        // Group details by category untuk tampilan
        $detailsByCategory = $report->details->groupBy(function ($detail) {
            return $detail->field->category->name ?? 'Lainnya';
        });

        return view('daily-reports-fo.manager.report-detail', [
            'report' => $report,
            'detailsByCategory' => $detailsByCategory,
        ]);
    }

    /**
     * Manager - Export Excel
     */
    public function managerExport(Request $request)
    {
        $user = Auth::user();

        if ($user->hasRole('superadmin')) {
            $managedBranches = Branch::orderBy('name')->get();
        } else {
            $managedBranches = $user->managedBranches;
        }

        $selectedBranchId = $request->input('branch_id', $managedBranches->first()->id);
        $selectedBranch = $managedBranches->find($selectedBranchId);

        if (! $selectedBranch) {
            abort(403, 'Anda tidak memiliki akses ke cabang ini.');
        }

        $dateFrom = $request->input('date_from', now()->subDays(7)->toDateString());
        $dateTo = $request->input('date_to', now()->toDateString());
        $shiftFilter = $request->input('shift');

        $query = DailyReportFO::where('branch_id', $selectedBranchId)
            ->whereBetween('tanggal', [$dateFrom, $dateTo]);

        if ($shiftFilter) {
            $query->where('shift', $shiftFilter);
        }

        $query->orderBy('tanggal', 'desc')
            ->orderBy('shift', 'asc')
            ->orderBy('slot', 'asc');

        $filename = 'Daily_Report_FO_' . $selectedBranch->name . '_' . $dateFrom . '_to_' . $dateTo . '.xlsx';
        $title = 'Report ' . $selectedBranch->name;

        return Excel::download(new DailyReportFOExport($query, $title), $filename);
    }

    // ==============================
    // MARKETING SECTION
    // ==============================

    /**
     * Marketing Dashboard
     */
    public function marketingDashboard(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->subDays(7)->toDateString());
        $dateTo = $request->input('date_to', now()->toDateString());

        $branches = Branch::orderBy('name')->get();

        $totalPhotos = DailyReportFOPhoto::whereHas('detail.report', function ($q) use ($dateFrom, $dateTo) {
            $q->whereBetween('tanggal', [$dateFrom, $dateTo]);
        })->count();

        $stats = [
            'total_reports' => DailyReportFO::whereBetween('tanggal', [$dateFrom, $dateTo])->count(),
            'total_photos' => $totalPhotos,
            'total_fo' => User::whereHas('roles', function ($q) {
                $q->where('name', 'fo');
            })->count(),
            'total_branches' => $branches->count(),
        ];

        $reportsPerDay = DailyReportFO::whereBetween('tanggal', [$dateFrom, $dateTo])
            ->selectRaw('DATE(tanggal) as date, COUNT(*) as total')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->pluck('total', 'date')
            ->toArray();

        $topFO = DailyReportFO::whereBetween('tanggal', [$dateFrom, $dateTo])
            ->selectRaw('user_id, COUNT(*) as total_reports')
            ->groupBy('user_id')
            ->orderBy('total_reports', 'desc')
            ->limit(10)
            ->with('user')
            ->get();

        return view('daily-reports-fo.marketing.dashboard', [
            'stats' => $stats,
            'reportsPerDay' => $reportsPerDay,
            'topFO' => $topFO,
            'branches' => $branches,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }

    /**
     * Marketing - Analytics Detail
     */
    public function marketingAnalytics(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->subDays(30)->toDateString());
        $dateTo = $request->input('date_to', now()->toDateString());
        $branchId = $request->input('branch_id');

        $query = DailyReportFO::whereBetween('tanggal', [$dateFrom, $dateTo]);
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $reportsByShift = $query->selectRaw('shift, COUNT(*) as total')
            ->groupBy('shift')
            ->get()
            ->pluck('total', 'shift')
            ->toArray();

        $reportsByBranch = DailyReportFO::whereBetween('tanggal', [$dateFrom, $dateTo])
            ->selectRaw('branch_id, COUNT(*) as total')
            ->groupBy('branch_id')
            ->with('branch')
            ->get();

        // Stats per field marketing dari master data
        $marketingCategory = ReportCategory::where('code', 'marketing')->first();
        $marketingStats = [];

        if ($marketingCategory) {
            $marketingFields = $marketingCategory->fields()->active()->ordered()->get();

            foreach ($marketingFields as $field) {
                $totalNilai = DailyReportFODetail::where('field_id', $field->id)
                    ->whereHas('report', function ($q) use ($dateFrom, $dateTo, $branchId) {
                        $q->whereBetween('tanggal', [$dateFrom, $dateTo]);
                        if ($branchId) {
                            $q->where('branch_id', $branchId);
                        }
                    })
                    ->sum('value_number');

                $marketingStats[] = [
                    'field' => $field,
                    'total' => $totalNilai,
                ];
            }
        }

        $branches = Branch::orderBy('name')->get();

        return view('daily-reports-fo.marketing.analytics', [
            'marketingStats' => $marketingStats,
            'reportsByShift' => $reportsByShift,
            'reportsByBranch' => $reportsByBranch,
            'branches' => $branches,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'selectedBranchId' => $branchId,
        ]);
    }

    /**
     * Marketing - All Reports
     */
    public function marketingReports(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->subDays(7)->toDateString());
        $dateTo = $request->input('date_to', now()->toDateString());
        $branchId = $request->input('branch_id');
        $shiftFilter = $request->input('shift');
        $perPage = (int) $request->input('per_page', 25);

        $query = DailyReportFO::whereBetween('tanggal', [$dateFrom, $dateTo])
            ->with([
                'user',
                'branch',
                'details' => function ($q) {
                    $q->with('field.category', 'photos');
                },
            ]);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if ($shiftFilter) {
            $query->where('shift', $shiftFilter);
        }

        $reports = $query->orderBy('tanggal', 'desc')
            ->orderBy('shift', 'asc')
            ->orderBy('slot', 'asc')
            ->paginate($perPage)
            ->withQueryString();

        $branches = Branch::orderBy('name')->get();

        return view('daily-reports-fo.marketing.reports', [
            'reports' => $reports,
            'branches' => $branches,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }

    /**
     * Marketing - Report Detail
     */
    public function marketingReportDetail($reportId)
    {
        $report = DailyReportFO::with([
            'user',
            'branch',
            'details' => function ($q) {
                $q->with('field.category', 'photos');
            },
        ])->findOrFail($reportId);

        // Group details by category untuk tampilan
        $detailsByCategory = $report->details->groupBy(function ($detail) {
            return $detail->field->category->name ?? 'Lainnya';
        });

        return view('daily-reports-fo.marketing.report-detail', [
            'report' => $report,
            'detailsByCategory' => $detailsByCategory,
        ]);
    }

    /**
     * Marketing - Export Excel
     */
    public function marketingExport(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->subDays(7)->toDateString());
        $dateTo = $request->input('date_to', now()->toDateString());
        $branchId = $request->input('branch_id');
        $shiftFilter = $request->input('shift');

        $query = DailyReportFO::whereBetween('tanggal', [$dateFrom, $dateTo]);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if ($shiftFilter) {
            $query->where('shift', $shiftFilter);
        }

        $query->orderBy('tanggal', 'desc')
            ->orderBy('branch_id', 'asc')
            ->orderBy('shift', 'asc')
            ->orderBy('slot', 'asc');

        $branchName = $branchId ? Branch::find($branchId)->name : 'All_Branches';
        $filename = 'Daily_Report_FO_' . $branchName . '_' . $dateFrom . '_to_' . $dateTo . '.xlsx';
        $title = 'Report ' . $branchName;

        return Excel::download(new DailyReportFOExport($query, $title), $filename);
    }
}
